<?php

namespace App\Http\Controllers\Landing;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Pemohon;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Login warga passwordless by NIK (S3_MVP_DESIGN.md §4). Berbeda dari wizard
 * persuratan (OtpController) yang sengaja membuka found:true/false untuk UX,
 * endpoint login TIDAK BOLEH membocorkan status pendaftaran sebuah NIK:
 * respons untuk NIK terdaftar dan tidak terdaftar identik byte-per-byte.
 */
class WargaAuthController extends Controller
{
    public const PURPOSE_LOGIN = 'login';

    protected const PESAN_OTP_TERKIRIM = 'Jika NIK terdaftar, kode OTP telah dikirim ke nomor WhatsApp yang tersimpan.';

    protected const PESAN_VERIFIKASI_GAGAL = 'Kode OTP salah atau sudah kedaluwarsa. Silakan periksa kembali atau minta kode baru.';

    public function __construct(protected OtpService $otpService) {}

    public function showLogin(): View
    {
        return view('home.masuk');
    }

    /**
     * Kirim OTP login. Respons SELALU pesan generik yang sama — throttle,
     * cooldown, NIK tak dikenal, dan pemohon tanpa nomor WA semuanya tidak
     * dibedakan, karena setiap perbedaan adalah oracle enumerasi NIK.
     * Limiter per-NIK/per-IP di OtpService tetap menghentikan penyalahgunaan
     * pengiriman; throttle route menahan flood endpoint-nya sendiri.
     */
    public function requestOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nik' => 'required|digits:16',
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus persis 16 digit angka.',
        ]);

        $pemohon = Pemohon::where('nik', $validated['nik'])->first();
        $data = null;

        if ($pemohon && $pemohon->phone) {
            $result = $this->otpService->requestCode(
                self::PURPOSE_LOGIN,
                $validated['nik'],
                $pemohon->phone,
                $request->ip(),
                requirePhoneAtVerify: false
            );

            // debug_code hanya pernah terisi saat APP_DEBUG + driver log
            // (dev/testing) — di production respons tetap identik byte-per-byte.
            if (isset($result['debug_code'])) {
                $data = ['debug_code' => $result['debug_code']];
            }
        }

        return ResponseHelper::success($data, self::PESAN_OTP_TERKIRIM);
    }

    /**
     * Verifikasi OTP → login. Semua kegagalan memakai satu pesan generik
     * (tanpa attempts_left) — membedakan "kode tidak pernah diterbitkan"
     * dari "kode salah" akan membocorkan status pendaftaran NIK.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nik' => 'required|digits:16',
            'code' => 'required|digits:6',
        ], [
            'code.required' => 'Kode OTP wajib diisi.',
            'code.digits' => 'Kode OTP terdiri dari 6 digit angka.',
        ]);

        $result = $this->otpService->verifyCode(self::PURPOSE_LOGIN, $validated['nik'], null, $validated['code']);

        if (! $result['ok']) {
            return ResponseHelper::error(self::PESAN_VERIFIKASI_GAGAL, 422);
        }

        $pemohon = Pemohon::where('nik', $validated['nik'])->first();

        if (! $pemohon) {
            return ResponseHelper::error(self::PESAN_VERIFIKASI_GAGAL, 422);
        }

        $user = $this->provisionUser($pemohon);

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return ResponseHelper::success(
            ['redirect' => route('tiket-saya.index')],
            'Login berhasil. Selamat datang, '.$pemohon->name.'!'
        );
    }

    /**
     * OTP lolos = bukti kepemilikan NIK+nomor → akun warga dibuat implisit
     * pada login pertama (tidak ada halaman register, §3) dan riwayat tiket
     * lama dengan NIK sama otomatis ikut lewat pemohon.user_id.
     */
    protected function provisionUser(Pemohon $pemohon): User
    {
        return $pemohon->provisionWargaUser();
    }
}
