<?php

namespace App\Http\Controllers\Landing;

use App\Enums\TiketChannel;
use App\Enums\TiketStatus;
use App\Http\Controllers\Controller;
use App\Models\KategoriPengaduan;
use App\Models\Kelurahan;
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
        protected TiketService $tiketService
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
     * Process & Store Public Pengaduan Form
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|digits:16',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
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

        return DB::transaction(function () use ($request, $validated) {
            // 1. Find or Create Pemohon — nama asli selalu tersimpan; is_anonim
            //    hanya mengatur penyamaran identitas di sisi tampilan petugas.
            $pemohon = Pemohon::firstOrCreate(
                ['nik' => $validated['nik']],
                [
                    'name' => $validated['nama'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'alamat' => $validated['alamat'] ?? null,
                    'kelurahan_id' => $validated['kelurahan_id'] ?? null,
                ]
            );

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

            // 4. Create Tiket via morph relation (alias 'pengaduan' dari morph map)
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

            return redirect()->route('pengaduan.sukses', $tiket->nomor_tiket)
                ->with('success', 'Laporan pengaduan Anda berhasil terkirim!');
        });
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
