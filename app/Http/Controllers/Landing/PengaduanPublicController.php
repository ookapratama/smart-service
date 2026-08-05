<?php

namespace App\Http\Controllers\Landing;

use App\Contracts\Services\WhatsAppNotifier;
use App\Enums\NotifikasiWaStatus;
use App\Enums\TiketChannel;
use App\Enums\TiketStatus;
use App\Http\Controllers\Controller;
use App\Models\KategoriPengaduan;
use App\Models\Kelurahan;
use App\Models\NotifikasiWa;
use App\Models\Pemohon;
use App\Models\Pengaduan;
use App\Models\Tiket;
use App\Services\FileUploadService;
use App\Services\TiketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PengaduanPublicController extends Controller
{
    public function __construct(
        protected FileUploadService $fileUploadService,
        protected TiketService $tiketService,
        protected WhatsAppNotifier $whatsAppNotifier
    ) {}

    /**
     * Halaman Publik Pengaduan Landing & Info
     */
    public function index()
    {
        $kategoriList = KategoriPengaduan::where('is_active', true)->get();
        $totalPengaduan = Tiket::whereHasMorph('detail', [Pengaduan::class])->count();
        $pengaduanSelesai = Tiket::whereHasMorph('detail', [Pengaduan::class])
            ->where('status', TiketStatus::Selesai)
            ->count();

        return view('home.pengaduan.index', compact('kategoriList', 'totalPengaduan', 'pengaduanSelesai'));
    }

    /**
     * Halaman Form Pengaduan Publik
     */
    public function create(Request $request): View
    {
        $kategoriList = KategoriPengaduan::where('is_active', true)->orderBy('nama')->get();
        $kelurahanList = Kelurahan::where('is_active', true)->orderBy('nama')->get();

        $flag = $request->session()->get(OtpController::SESSION_KEY);
        $otpVerifiedNik = null;

        if (is_array($flag)
            && ($flag['purpose'] ?? null) === OtpController::PURPOSE_PERSURATAN
            && ($flag['expires_at'] ?? 0) >= now()->getTimestamp()) {
            $otpVerifiedNik = $flag['nik'] ?? null;
        }

        $autofillPemohon = $request->user()?->pemohon;
        if ($autofillPemohon && ! $otpVerifiedNik) {
            $otpVerifiedNik = $autofillPemohon->nik;
        }

        return view('home.pengaduan.create', compact('kategoriList', 'kelurahanList', 'otpVerifiedNik', 'autofillPemohon'));
    }

    /**
     * AJAX Endpoint untuk verifikasi NIK pemohon di tabel pemohon
     */
    public function cekNik(Request $request)
    {
        $request->validate([
            'nik' => 'required|digits:16',
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus 16 digit angka.',
        ]);

        $pemohon = Pemohon::where('nik', $request->nik)->first();

        if ($pemohon) {
            return response()->json([
                'exists' => true,
                'message' => 'NIK terverifikasi di sistem.',
                'pemohon' => [
                    'id' => $pemohon->id,
                    'nik' => $pemohon->nik,
                    'name' => $pemohon->name,
                    'email' => $pemohon->email,
                    'phone' => $pemohon->phone,
                    'alamat' => $pemohon->alamat,
                    'kelurahan_id' => $pemohon->kelurahan_id,
                ],
            ]);
        }

        return response()->json([
            'exists' => false,
            'message' => 'NIK belum terdaftar.',
        ]);
    }

    /**
     * Process & Store Public Pengaduan Form
     */
    public function store(Request $request)
    {
        if ($request->user()?->pemohon) {
            $pemohonAuth = $request->user()->pemohon;
            $request->merge([
                'nik' => $request->input('nik') ?: $pemohonAuth->nik,
                'nama' => $request->input('nama') ?: $pemohonAuth->name,
                'email' => $request->input('email') ?: $pemohonAuth->email,
                'phone' => $request->input('phone') ?: $pemohonAuth->phone,
                'kelurahan_id' => $request->input('kelurahan_id') ?: $pemohonAuth->kelurahan_id,
                'alamat' => $request->input('alamat') ?: $pemohonAuth->alamat,
            ]);
        }

        $existingPemohon = Pemohon::where('nik', $request->nik)->first();

        $validated = $request->validate([
            'nik' => 'required|digits:16',
            'nama' => $existingPemohon ? 'nullable|string|max:255' : 'required|string|max:255',
            'email' => $existingPemohon ? 'nullable|email|max:255' : 'required|email|max:255',
            'phone' => $existingPemohon ? 'nullable|string|max:50' : 'required|string|max:50',
            'kelurahan_id' => 'nullable|exists:kelurahan,id',
            'alamat' => 'nullable|string',
            'jenis_laporan' => 'required|string|max:50',
            'kategori_pengaduan_id' => 'required|exists:kategori_pengaduan,id',
            'judul' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'tanggal_kejadian' => 'nullable|date',
            'deskripsi' => 'required|string|min:15',
            'is_anonim' => 'nullable|boolean',
            'lampiran' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:3072',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi 16 digit.',
            'nik.digits' => 'NIK harus persis 16 digit angka.',
            'email.required' => 'Alamat email wajib diisi.',
            'phone.required' => 'Nomor Telepon / WhatsApp wajib diisi.',
            'kategori_pengaduan_id.required' => 'Pilih kategori pengaduan.',
            'judul.required' => 'Judul pengaduan wajib diisi.',
            'lokasi.required' => 'Lokasi kejadian wajib diisi.',
            'deskripsi.required' => 'Rincian pengaduan wajib diisi minimal 15 karakter.',
        ]);

        $tiket = DB::transaction(function () use ($request, $validated, $existingPemohon) {
            if ($existingPemohon) {
                $pemohon = $existingPemohon;
                $updateData = array_filter([
                    'name' => $validated['nama'] ?? null,
                    'email' => $validated['email'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'alamat' => $validated['alamat'] ?? null,
                    'kelurahan_id' => $validated['kelurahan_id'] ?? null,
                ]);
                if (! empty($updateData)) {
                    $pemohon->update($updateData);
                }
            } else {
                $pemohon = Pemohon::create([
                    'nik' => $validated['nik'],
                    'name' => $validated['nama'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'alamat' => $validated['alamat'] ?? null,
                    'kelurahan_id' => $validated['kelurahan_id'] ?? null,
                ]);
            }

            $pengaduan = Pengaduan::create([
                'kategori_pengaduan_id' => $validated['kategori_pengaduan_id'],
                'deskripsi' => $validated['deskripsi'],
                'lokasi' => $validated['lokasi'],
                'is_anonim' => $request->boolean('is_anonim'),
                'jenis_laporan' => $validated['jenis_laporan'],
                'tanggal_kejadian' => $validated['tanggal_kejadian'] ?? null,
            ]);

            if ($request->hasFile('lampiran')) {
                $media = $this->fileUploadService->upload($request->file('lampiran'), 'pengaduan', 'public');
                $pengaduan->media()->save($media);
            }

            $nomorTiket = $this->tiketService->generateNomorTiket();

            $tiket = $pengaduan->tiket()->create([
                'nomor_tiket' => $nomorTiket,
                'pemohon_id' => $pemohon->id,
                'status' => TiketStatus::Baru,
                'channel' => TiketChannel::Web,
                'judul' => $validated['judul'],
                'keterangan' => $validated['deskripsi'],
            ]);

            $tiket->statusLogs()->create([
                'status_from' => TiketStatus::Baru,
                'status_to' => TiketStatus::Baru,
                'user_id' => null,
                'catatan' => 'Pengaduan publik baru diterima via portal 3S.',
            ]);

            return $tiket;
        });

        if ($tiket && $tiket->pemohon && ! empty($tiket->pemohon->phone)) {
            $this->kirimNotifikasiTiket($tiket, $tiket->pemohon->phone);
        }

        return redirect()->route('pengaduan.sukses', $tiket->nomor_tiket)
            ->with('success', 'Laporan pengaduan Anda berhasil terkirim!');
    }

    /**
     * Submit hanya sah jika session menyimpan flag OTP yang masih berlaku.
     */
    protected function assertOtpVerified(Request $request): void
    {
        $flag = $request->session()->get(OtpController::SESSION_KEY);

        $valid = is_array($flag)
            && ($flag['purpose'] ?? null) === OtpController::PURPOSE_PERSURATAN
            && ($flag['expires_at'] ?? 0) >= now()->getTimestamp()
            && ($flag['nik'] ?? null) === $request->input('nik')
            && ($flag['phone'] ?? null) === $request->input('phone');

        if (! $valid) {
            throw ValidationException::withMessages([
                'otp' => 'Verifikasi OTP WhatsApp diperlukan sebelum mengirim laporan pengaduan. Silakan verifikasi ulang.',
            ]);
        }
    }

    /**
     * Kirim WA nomor tiket + info pengaduan, catat hasilnya di notifikasi_wa.
     */
    protected function kirimNotifikasiTiket(Tiket $tiket, string $phone): void
    {
        $pesan = sprintf(
            "Halo, %s!\n\n".
            "Laporan pengaduan Anda telah kami terima di portal Soreang Smart Service (3S).\n\n".
            "*RINCIAN LAPORAN PENGADUAN*\n".
            "📋 *Nomor Tiket (Salin)*:\n```%s```\n".
            "📝 *Judul*: %s\n".
            "📅 *Tanggal*: %s WIB\n".
            "📌 *Status*: Baru / Diterima\n\n".
            "🔗 *Pantau Progres Status*:\n%s\n\n".
            '_Pemerintah Kecamatan Soreang_',
            $tiket->pemohon->name ?? 'Warga',
            $tiket->nomor_tiket,
            $tiket->judul,
            $tiket->created_at->format('d M Y H:i'),
            route('cek-status.index')
        );

        $error = null;

        try {
            $terkirim = $this->whatsAppNotifier->send($phone, $pesan);
        } catch (\Throwable $e) {
            $terkirim = false;
            $error = $e->getMessage();
        }

        NotifikasiWa::create([
            'tiket_id' => $tiket->id,
            'phone' => $phone,
            'pesan' => $pesan,
            'status' => $terkirim ? NotifikasiWaStatus::Terkirim : NotifikasiWaStatus::Gagal,
            'error' => $error,
            'sent_at' => $terkirim ? now() : null,
        ]);
    }

    /**
     * Halaman Sukses Pengaduan
     */
    public function sukses(string $nomor_tiket)
    {
        $tiket = Tiket::with(['pemohon', 'detail', 'detail.kategori'])->where('nomor_tiket', $nomor_tiket)->firstOrFail();

        return view('home.pengaduan.sukses', compact('tiket'));
    }
}
