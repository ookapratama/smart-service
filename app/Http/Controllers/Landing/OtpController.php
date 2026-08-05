<?php

namespace App\Http\Controllers\Landing;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Pemohon;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Endpoint AJAX OTP untuk form publik (session + CSRF, bukan API stateless).
 */
class OtpController extends Controller
{
    public const PURPOSE_PERSURATAN = 'persuratan';

    public const SESSION_KEY = 'otp_verified';

    /**
     * Probe "Cek NIK" (wizard step 1): dibatasi jauh lebih longgar daripada
     * limiter pengiriman OTP di OtpService, karena tujuannya cuma memperlambat
     * mass-scan NIK, bukan mengamankan pengiriman kode.
     */
    public const NIK_CHECK_MAX_PER_IP = 20;

    public const NIK_CHECK_DECAY = 600;

    public function __construct(protected OtpService $otpService) {}

    /**
     * Wizard step 1 ("Cek NIK") + kirim OTP.
     *
     * `phone` sekarang opsional:
     * - NIK tidak ditemukan & tanpa phone → murni probe "Cek NIK", tidak ada
     *   OTP yang dikirim (found:false), limiter pengiriman OtpService tidak
     *   tersentuh.
     * - NIK tidak ditemukan & phone diisi → jalur lama: pemohon baru mengisi
     *   form manual, OTP dikirim ke nomor yang diketik sendiri.
     * - NIK ditemukan → phone client TIDAK PERNAH dipakai. OTP selalu
     *   dikirim ke Pemohon::phone tersimpan di server; client tidak pernah
     *   melihat/mengirim nomor ini.
     *
     * Catatan anti-enumeration: PII (nama/nomor/alamat) TIDAK PERNAH terbuka
     * di respons ini — itu jaminan yang tetap dipegang. Status pendaftaran
     * (found true/false) sendiri **sengaja** terbuka di sini (keputusan
     * produk untuk UX wizard) dan dibatasi lewat throttle IP terpisah di
     * bawah supaya tidak dipakai mass-scan NIK.
     */
    public function request(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|digits:16',
            'phone' => 'nullable|string|max:50',
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus persis 16 digit angka.',
        ]);

        $ip = $request->ip();
        $nikCheckKey = "nik-check:ip:{$ip}";

        if (RateLimiter::tooManyAttempts($nikCheckKey, self::NIK_CHECK_MAX_PER_IP)) {
            return ResponseHelper::error(
                'Terlalu banyak permintaan. Silakan coba lagi beberapa saat lagi.',
                429,
                ['retry_after' => RateLimiter::availableIn($nikCheckKey)]
            );
        }

        RateLimiter::hit($nikCheckKey, self::NIK_CHECK_DECAY);

        $pemohon = Pemohon::where('nik', $validated['nik'])->first();

        // Step-1 murni "Cek NIK": tidak ada pemohon & client tidak menyertakan
        // phone sendiri → tidak ada yang bisa/boleh dikirimi OTP.
        if (! $pemohon && ! ($validated['phone'] ?? null)) {
            return ResponseHelper::success(['found' => false], 'NIK belum terdaftar.');
        }

        // Keamanan: saat pemohon DITEMUKAN, targetPhone HARUS selalu nomor
        // tersimpan di server — jangan pernah nomor kiriman client, apa pun
        // isinya. Sebelum perbaikan ini, `$validated['phone'] ?? $pemohon?->phone`
        // memprioritaskan input client bahkan saat pemohon ditemukan: siapa pun
        // yang tahu NIK korban (NIK tidak rahasia, §3) tinggal menyisipkan
        // `phone` miliknya sendiri di body request untuk membajak pengiriman
        // OTP ke nomornya — begitu lolos verifikasi, endpoint verify()
        // membocorkan nama/nomor/alamat/kelurahan pemohon ke penyerang.
        $targetPhone = $pemohon ? $pemohon->phone : ($validated['phone'] ?? null);

        if (! $targetPhone) {
            return ResponseHelper::error(
                'Data kontak pemohon tidak lengkap, silakan hubungi petugas kecamatan.',
                422
            );
        }

        $purpose = $request->input('purpose', self::PURPOSE_PERSURATAN);

        $result = $this->otpService->requestCode(
            $purpose,
            $validated['nik'],
            $targetPhone,
            $ip,
            // Jalur "ditemukan": client tidak pernah tahu targetPhone → verify()
            // tidak boleh menuntutnya mengirim balik nomor yang tak pernah ia
            // lihat. Jalur "tidak ditemukan": client sendiri yang mengetik
            // nomor ini → wajib dicocokkan lagi saat verifikasi.
            requirePhoneAtVerify: ! $pemohon
        );

        if (! $result['ok']) {
            $message = $result['reason'] === 'cooldown'
                ? 'Tunggu '.($result['retry_after'] ?? OtpService::RESEND_COOLDOWN).' detik sebelum meminta kode baru.'
                : 'Terlalu banyak permintaan OTP. Silakan coba lagi beberapa saat lagi.';

            return ResponseHelper::error($message, 429, ['retry_after' => $result['retry_after'] ?? null]);
        }

        return ResponseHelper::success(
            ['found' => (bool) $pemohon, 'phone_masked' => $result['masked_phone']]
                + (isset($result['debug_code']) ? ['debug_code' => $result['debug_code']] : []),
            'Kode OTP telah dikirim via WhatsApp.'
        );
    }

    /**
     * `phone` opsional: jalur "NIK ditemukan" hanya mengirim {nik, code} —
     * client tidak pernah tahu nomor pemohon sehingga tidak bisa
     * mengirimkannya balik. `OtpService::verifyCode` melewati pengecekan
     * pasangan nomor saat phone null, dan selalu mengembalikan nomor target
     * yang sesungguhnya (state server) untuk disimpan ke flag session —
     * bukan phone dari input client.
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|digits:16',
            'phone' => 'nullable|string|max:50',
            'code' => 'required|digits:6',
            'purpose' => 'nullable|string',
        ], [
            'code.required' => 'Kode OTP wajib diisi.',
            'code.digits' => 'Kode OTP terdiri dari 6 digit angka.',
        ]);

        $purpose = $validated['purpose'] ?? self::PURPOSE_PERSURATAN;

        $result = $this->otpService->verifyCode(
            $purpose,
            $validated['nik'],
            $validated['phone'] ?? null,
            $validated['code']
        );

        if (! $result['ok']) {
            $message = match ($result['reason']) {
                'expired' => 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.',
                'locked' => 'Terlalu banyak percobaan salah. Silakan minta kode baru.',
                'phone_required' => 'Nomor WhatsApp wajib disertakan untuk verifikasi ini.',
                default => 'Kode OTP salah. Sisa percobaan: '.($result['attempts_left'] ?? 0).'.',
            };

            return ResponseHelper::error($message, 422);
        }

        $sessionData = [
            'purpose' => $purpose,
            'nik' => $validated['nik'],
            'phone' => $result['phone'],
            'expires_at' => now()->addSeconds(OtpService::SESSION_TTL)->getTimestamp(),
        ];

        $request->session()->put("otp_verified_{$purpose}", $sessionData);
        $request->session()->put(self::SESSION_KEY, $sessionData);

        $pemohon = Pemohon::where('nik', $validated['nik'])->first();

        // Auto-fill hanya setelah identitas terbukti (S3_MVP_DESIGN.md §3):
        // PII pemohon baru ikut di respons di sini, setelah OTP lolos.
        $data = $pemohon ? [
            'pemohon' => [
                'name' => $pemohon->name,
                'phone' => $pemohon->phone,
                'alamat' => $pemohon->alamat,
                'kelurahan_id' => $pemohon->kelurahan_id,
            ],
        ] : null;

        return ResponseHelper::success($data, 'Nomor WhatsApp berhasil diverifikasi.');
    }
}
