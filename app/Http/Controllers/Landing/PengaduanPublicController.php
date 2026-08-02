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
    public function create()
    {
        $kategoriList = KategoriPengaduan::where('is_active', true)->get();
        $kelurahanList = Kelurahan::where('is_active', true)->orderBy('nama')->get();

        return view('home.pengaduan.create', compact('kategoriList', 'kelurahanList'));
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
            'message' => 'NIK belum terdaftar di sistem. Silakan lengkapi data pemohon.',
        ]);
    }

    /**
     * Process & Store Public Pengaduan Form
     */
    public function store(Request $request)
    {
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
            // 1. Find or Create Pemohon — jika eksis, gunakan data tersimpan / perbarui jika diisi
            if ($existingPemohon) {
                $pemohon = $existingPemohon;
                $updateData = array_filter([
                    'name' => $validated['nama'] ?? null,
                    'email' => $validated['email'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'alamat' => $validated['alamat'] ?? null,
                    'kelurahan_id' => $validated['kelurahan_id'] ?? null,
                ]);
                if (!empty($updateData)) {
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

            // 2. Create Detail Pengaduan
            $pengaduan = Pengaduan::create([
                'kategori_pengaduan_id' => $validated['kategori_pengaduan_id'],
                'deskripsi' => $validated['deskripsi'],
                'lokasi' => $validated['lokasi'],
                'is_anonim' => $request->boolean('is_anonim'),
                'jenis_laporan' => $validated['jenis_laporan'],
                'tanggal_kejadian' => $validated['tanggal_kejadian'] ?? null,
            ]);

            // Handle Media attachment upload if present
            if ($request->hasFile('lampiran')) {
                $media = $this->fileUploadService->upload($request->file('lampiran'), 'pengaduan', 'public');
                $pengaduan->media()->save($media);
            }

            // 3. Generate Thread-safe Nomor Tiket
            $nomorTiket = $this->tiketService->generateNomorTiket();

            // 4. Create Tiket via morph relation
            $tiket = $pengaduan->tiket()->create([
                'nomor_tiket' => $nomorTiket,
                'pemohon_id' => $pemohon->id,
                'status' => TiketStatus::Baru,
                'channel' => TiketChannel::Web,
                'judul' => $validated['judul'],
                'keterangan' => $validated['deskripsi'],
            ]);

            // Create initial status log
            $tiket->statusLogs()->create([
                'status_from' => TiketStatus::Baru,
                'status_to' => TiketStatus::Baru,
                'user_id' => null,
                'catatan' => 'Pengaduan publik baru diterima via portal 3S.',
            ]);

            return $tiket;
        });

        // Kirim notifikasi WhatsApp via Fonnte / WhatsAppNotifier
        if ($tiket && $tiket->pemohon && !empty($tiket->pemohon->phone)) {
            $this->kirimNotifikasiTiket($tiket, $tiket->pemohon->phone);
        }

        return redirect()->route('pengaduan.sukses', $tiket->nomor_tiket)
            ->with('success', 'Laporan pengaduan Anda berhasil terkirim!');
    }

    /**
     * Kirim WA nomor tiket + info pengaduan, catat hasilnya di notifikasi_wa.
     */
    protected function kirimNotifikasiTiket(Tiket $tiket, string $phone): void
    {
        $pesan = sprintf(
            "Halo, %s!\n\n" .
            "Laporan pengaduan Anda telah kami terima di portal Soreang Smart Service (3S).\n\n" .
            "RINCIAN LAPORAN PENGADUAN\n" .
            "Nomor Tiket: %s\n" .
            "Judul: %s\n" .
            "Tanggal: %s WIB\n" .
            "Status: Baru / Diterima\n\n" .
            "PANTAU PROGRES STATUS\n" .
            "Gunakan nomor tiket di atas untuk mengecek progres tindakan petugas melalui link berikut:\n" .
            "%s\n\n" .
            "Pemerintah Kecamatan Soreang",
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
