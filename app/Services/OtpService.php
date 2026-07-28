<?php

namespace App\Services;

use App\Contracts\Services\WhatsAppNotifier;
use App\Models\Pemohon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

/**
 * OTP engine sesuai S3_MVP_DESIGN.md §4: kode 6 digit di-hash di cache
 * (TTL 5 menit), maks 5 percobaan, cooldown resend 60 detik, throttle
 * per NIK & per IP, dan aturan nomor tujuan anti-pembajakan riwayat.
 */
class OtpService
{
    public const CODE_TTL = 300;

    public const MAX_ATTEMPTS = 5;

    public const RESEND_COOLDOWN = 60;

    public const SESSION_TTL = 900;

    public const MAX_REQUESTS_PER_NIK = 5;

    public const MAX_REQUESTS_PER_IP = 15;

    public const THROTTLE_DECAY = 600;

    public function __construct(protected WhatsAppNotifier $notifier) {}

    /**
     * Minta kode OTP baru. Respons identik untuk NIK terdaftar/tidak
     * (anti-enumeration) — pembeda hanya nomor tujuan pengiriman:
     * pemohon dengan nomor WA TERVERIFIKASI yang berbeda dari input form
     * menerima OTP di nomor lama tersimpan (§4, anti-pembajakan riwayat).
     *
     * $requirePhoneAtVerify: true kecuali caller (controller) sudah memastikan
     * nomor tujuan diresolusi sepenuhnya di server tanpa pernah ditunjukkan ke
     * client (jalur "NIK ditemukan" wizard) — lihat verifyCode().
     *
     * @return array{ok: bool, reason?: string, retry_after?: int, masked_phone?: string}
     */
    public function requestCode(string $purpose, string $nik, string $phone, string $ip, bool $requirePhoneAtVerify = true): array
    {
        $limits = [
            "otp-req:nik:{$nik}" => self::MAX_REQUESTS_PER_NIK,
            "otp-req:ip:{$ip}" => self::MAX_REQUESTS_PER_IP,
        ];

        foreach ($limits as $key => $max) {
            if (RateLimiter::tooManyAttempts($key, $max)) {
                return ['ok' => false, 'reason' => 'throttled', 'retry_after' => RateLimiter::availableIn($key)];
            }
        }

        $target = $this->resolveTargetPhone($nik, $phone);
        $cooldownKey = $this->cooldownKey($purpose, $target);

        if (Cache::has($cooldownKey)) {
            return ['ok' => false, 'reason' => 'cooldown', 'retry_after' => self::RESEND_COOLDOWN];
        }

        foreach ($limits as $key => $max) {
            RateLimiter::hit($key, self::THROTTLE_DECAY);
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put($this->codeKey($purpose, $nik), [
            'hash' => Hash::make($code),
            'phone' => $target,
            'form_phone' => $phone,
            'requires_phone' => $requirePhoneAtVerify,
            'attempts' => 0,
            'expires_at' => now()->addSeconds(self::CODE_TTL)->getTimestamp(),
        ], self::CODE_TTL);

        Cache::put($cooldownKey, true, self::RESEND_COOLDOWN);

        $this->notifier->send(
            $target,
            "Kode OTP layanan {$purpose} Kecamatan Soreang: {$code}. Berlaku 5 menit. Jangan bagikan kode ini kepada siapa pun."
        );

        return ['ok' => true, 'masked_phone' => $this->maskPhone($target)];
    }

    /**
     * Verifikasi kode OTP. Sukses = kode hangus (sekali pakai).
     *
     * $phone opsional HANYA untuk jalur "NIK ditemukan" (wizard Cek NIK):
     * client tidak pernah diberi tahu nomor pemohon sehingga tidak bisa
     * mengirimnya balik — saat null DAN request asalnya ditandai
     * requires_phone=false, pengecekan pasangan nomor dilewati dan cakupan
     * tetap terjaga oleh cache key (purpose+nik) + hash kode + limit 5
     * percobaan. Untuk jalur "NIK tidak ditemukan" (requires_phone=true,
     * default) — client sendiri yang mengetik nomor di form sebelum
     * verifikasi — nomor WAJIB disertakan dan dicocokkan; mengosongkannya
     * bukan jalan pintas, tetap ditolak (lihat OtpServiceTest &
     * OtpControllerWizardTest untuk regresi ini).
     *
     * @return array{ok: bool, reason?: string, attempts_left?: int, phone?: string}
     */
    public function verifyCode(string $purpose, string $nik, ?string $phone, string $code): array
    {
        $key = $this->codeKey($purpose, $nik);
        $payload = Cache::get($key);

        if (! is_array($payload)) {
            return ['ok' => false, 'reason' => 'expired'];
        }

        if ($phone === null && ($payload['requires_phone'] ?? true)) {
            return ['ok' => false, 'reason' => 'phone_required'];
        }

        $phoneMatches = $phone === null || hash_equals($payload['form_phone'], $phone);
        $valid = $phoneMatches && Hash::check($code, $payload['hash']);

        if (! $valid) {
            $payload['attempts']++;

            if ($payload['attempts'] >= self::MAX_ATTEMPTS) {
                Cache::forget($key);

                return ['ok' => false, 'reason' => 'locked'];
            }

            $remaining = max($payload['expires_at'] - now()->getTimestamp(), 1);
            Cache::put($key, $payload, $remaining);

            return ['ok' => false, 'reason' => 'invalid', 'attempts_left' => self::MAX_ATTEMPTS - $payload['attempts']];
        }

        Cache::forget($key);

        return ['ok' => true, 'phone' => $payload['form_phone']];
    }

    /**
     * §4: nomor WA tersimpan + TERVERIFIKASI + berbeda dari input form →
     * OTP ke nomor lama (bukti kepemilikan sebelum nomor baru menggantikan).
     * Belum pernah terverifikasi → ke nomor input form (last-write-wins).
     */
    protected function resolveTargetPhone(string $nik, string $phone): string
    {
        $pemohon = Pemohon::where('nik', $nik)->first();

        if ($pemohon && $pemohon->phone_verified_at && $pemohon->phone && $pemohon->phone !== $phone) {
            return $pemohon->phone;
        }

        return $phone;
    }

    public function maskPhone(string $phone): string
    {
        $len = strlen($phone);

        if ($len <= 6) {
            return str_repeat('*', max($len - 2, 0)).substr($phone, -2);
        }

        return substr($phone, 0, 4).str_repeat('*', $len - 6).substr($phone, -2);
    }

    protected function codeKey(string $purpose, string $nik): string
    {
        return "otp:code:{$purpose}:{$nik}";
    }

    protected function cooldownKey(string $purpose, string $phone): string
    {
        return "otp:cooldown:{$purpose}:{$phone}";
    }
}
