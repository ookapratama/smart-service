<?php

namespace App\Http\Controllers;

use App\Enums\TiketChannel;
use App\Enums\TiketStatus;
use App\Http\Requests\BaseRequest;
use App\Models\Instansi;
use App\Models\KategoriPengaduan;
use App\Models\Media;
use App\Models\Pemohon;
use App\Models\Pengaduan;
use App\Models\Tiket;
use App\Models\TiketCounter;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PengaduanPublicController extends Controller
{
    public function __construct(
        protected FileUploadService $fileUploadService
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
        $kelurahanList = [
            'Mekarjaya',
            'Sorean',
            'Tanjungsari',
            'Sukadamai',
            'Cikatomas',
            'Cikadu',
            'Margarahayu'
        ];

        return view('home.pengaduan.create', compact('kategoriList', 'kelurahanList'));
    }

    /**
     * Process & Store Public Pengaduan Form
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|size:16',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'kelurahan' => 'nullable|string|max:100',
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
            'nik.size' => 'NIK harus persis 16 digit angka.',
            'email.required' => 'Alamat email wajib diisi.',
            'phone.required' => 'Nomor Telepon / WhatsApp wajib diisi.',
            'kategori_pengaduan_id.required' => 'Pilih kategori pengaduan.',
            'judul.required' => 'Judul pengaduan wajib diisi.',
            'lokasi.required' => 'Lokasi kejadian wajib diisi.',
            'deskripsi.required' => 'Rincian pengaduan wajib diisi minimal 15 karakter.',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            // 1. Get Default Instansi (Kecamatan Sorean)
            $instansi = Instansi::first();
            $instansiId = $instansi ? $instansi->id : 1;
            $kodeInstansi = $instansi && $instansi->kode ? $instansi->kode : 'SRG';

            // 2. Find or Create Pemohon
            $isAnonim = $request->boolean('is_anonim');
            $pemohonNama = $isAnonim ? 'Anonim (Pelapor Rahasia)' : $validated['nama'];
            
            $pemohon = Pemohon::firstOrCreate(
                ['nik' => $validated['nik']],
                [
                    'instansi_id' => $instansiId,
                    'name' => $pemohonNama,
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'alamat' => ($validated['alamat'] ?? '') . ($validated['kelurahan'] ? " (Kel. {$validated['kelurahan']})" : ''),
                ]
            );

            // 3. Create Detail Pengaduan
            $pengaduan = Pengaduan::create([
                'kategori_pengaduan_id' => $validated['kategori_pengaduan_id'],
                'deskripsi' => "[$validated[jenis_laporan]] " . $validated['deskripsi'] . ($validated['tanggal_kejadian'] ? " (Tanggal Kejadian: {$validated['tanggal_kejadian']})" : ''),
                'lokasi' => $validated['lokasi'],
            ]);

            // Handle Media attachment upload if present
            if ($request->hasFile('lampiran')) {
                $media = $this->fileUploadService->upload($request->file('lampiran'), 'pengaduan', 'public');
                $pengaduan->media()->save($media);
            }

            // 4. Generate Thread-safe Nomor Tiket
            $periode = date('ym');
            $counter = TiketCounter::where('instansi_id', $instansiId)
                ->where('periode', $periode)
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                $counter = TiketCounter::create([
                    'instansi_id' => $instansiId,
                    'periode' => $periode,
                    'last_seq' => 1
                ]);
            } else {
                $counter->increment('last_seq');
            }

            $nomorTiket = sprintf('%s-%s-%05d', $kodeInstansi, $periode, $counter->last_seq);

            // 5. Create Tiket Record
            $tiket = Tiket::create([
                'instansi_id' => $instansiId,
                'nomor_tiket' => $nomorTiket,
                'pemohon_id' => $pemohon->id,
                'detail_type' => Pengaduan::class,
                'detail_id' => $pengaduan->id,
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
