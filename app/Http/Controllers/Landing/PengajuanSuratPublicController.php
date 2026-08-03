<?php

namespace App\Http\Controllers\Landing;

use App\Contracts\Services\WhatsAppNotifier;
use App\Enums\NotifikasiWaStatus;
use App\Enums\TiketChannel;
use App\Enums\TiketStatus;
use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use App\Models\Kelurahan;
use App\Models\NotifikasiWa;
use App\Models\Pemohon;
use App\Models\PengajuanSurat;
use App\Models\Tiket;
use App\Services\FileUploadService;
use App\Services\SuratService;
use App\Services\TiketService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PengajuanSuratPublicController extends Controller
{
    public function __construct(
        protected FileUploadService $fileUploadService,
        protected TiketService $tiketService,
        protected SuratService $suratService,
        protected WhatsAppNotifier $whatsAppNotifier
    ) {}

    /**
     * Daftar jenis surat aktif yang bisa diajukan warga.
     */
    public function index()
    {
        $jenisSuratList = JenisSurat::where('is_active', true)->orderBy('nama')->get();

        return view('home.surat.index', compact('jenisSuratList'));
    }

    /**
     * Form dinamis: blok identitas + field dari jenis_surat.fields JSON + OTP.
     */
    public function create(Request $request, JenisSurat $jenisSurat)
    {
        abort_unless($jenisSurat->is_active, 404);

        $kelurahanList = Kelurahan::where('is_active', true)->orderBy('nama')->get();

        // Submit yang gagal validasi tidak menghanguskan flag OTP — beri tahu
        // front-end NIK mana yang masih terverifikasi supaya wizard tidak
        // kembali ke keadaan awal (yang membuat tombol submit terkunci lagi).
        $flag = $request->session()->get(OtpController::SESSION_KEY);
        $otpVerifiedNik = null;

        if (is_array($flag)
            && ($flag['purpose'] ?? null) === OtpController::PURPOSE_PERSURATAN
            && ($flag['expires_at'] ?? 0) >= now()->getTimestamp()) {
            $otpVerifiedNik = $flag['nik'] ?? null;
        }

        return view('home.surat.create', compact('jenisSurat', 'kelurahanList', 'otpVerifiedNik'));
    }

    /**
     * Proses submit pengajuan surat publik (wajib lolos OTP WA — §4).
     */
    public function store(Request $request)
    {
        $request->validate(['jenis_surat_id' => 'required|integer|exists:jenis_surat,id']);

        $jenisSurat = JenisSurat::where('is_active', true)->findOrFail($request->integer('jenis_surat_id'));

        // Gerbang OTP lebih dulu (§4): tanpa bukti kepemilikan nomor WA,
        // tidak ada gunanya memproses isian form lebih jauh.
        $this->assertOtpVerified($request);

        $validated = $request->validate(array_merge([
            'nama' => 'required|string|max:255',
            'nik' => 'required|digits:16',
            'phone' => 'required|string|max:50',
            'kelurahan_id' => 'nullable|exists:kelurahan,id',
            'alamat' => 'nullable|string',
            'keperluan' => 'nullable|string|max:500',
        ], $this->buildDynamicRules($jenisSurat)), [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi 16 digit.',
            'nik.digits' => 'NIK harus persis 16 digit angka.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
        ], $this->buildDynamicAttributes($jenisSurat));

        $fields = collect($jenisSurat->fields ?? []);
        $fileFieldNames = $fields->where('type', 'file')->pluck('name')->all();
        $dataFieldNames = $fields->where('type', '!=', 'file')->pluck('name')->all();

        // Kolom keperluan NOT NULL (§6) sementara input form opsional —
        // tanpa fallback, submit tanpa keperluan gagal dengan QueryException.
        $keperluan = $validated['keperluan'] ?? 'Pengajuan '.$jenisSurat->nama.' via portal 3S.';

        // Semua tulisan DB submission dalam SATU transaksi (§7 Atomicity).
        $tiket = DB::transaction(function () use ($validated, $jenisSurat, $dataFieldNames, $keperluan) {
            // 1. Registrasi implisit by NIK; lolos OTP = nomor WA terbukti milik
            //    pengaju → data form (termasuk nomor baru) menggantikan yang lama.
            $pemohon = Pemohon::updateOrCreate(
                ['nik' => $validated['nik']],
                [
                    'name' => $validated['nama'],
                    'phone' => $validated['phone'],
                    'alamat' => $validated['alamat'] ?? null,
                    'kelurahan_id' => $validated['kelurahan_id'] ?? null,
                    'phone_verified_at' => now(),
                ]
            );

            // 2. Detail pengajuan (field dinamis non-file → kolom data JSON)
            $pengajuanSurat = PengajuanSurat::create([
                'jenis_surat_id' => $jenisSurat->id,
                'keperluan' => $keperluan,
                'data' => Arr::only($validated['data'] ?? [], $dataFieldNames),
            ]);

            // 3. Nomor tiket race-safe (upsert atomik + lockForUpdate)
            $nomorTiket = $this->tiketService->generateNomorTiket();

            // 4. Tiket via morph relation (alias 'pengajuan_surat' dari morph map)
            $tiket = $pengajuanSurat->tiket()->create([
                'nomor_tiket' => $nomorTiket,
                'pemohon_id' => $pemohon->id,
                'status' => TiketStatus::Baru,
                'channel' => TiketChannel::Web,
                'judul' => 'Pengajuan '.$jenisSurat->nama,
                'keterangan' => $keperluan,
            ]);

            $tiket->statusLogs()->create([
                'status_from' => TiketStatus::Baru,
                'status_to' => TiketStatus::Baru,
                'user_id' => null,
                'catatan' => 'Pengajuan surat baru diterima via portal 3S.',
            ]);

            return $tiket;
        });

        // --- Side effects hanya setelah commit (§7 Durability) ---
        $pengajuanSurat = $tiket->detail;

        foreach ($fileFieldNames as $fieldName) {
            if ($request->hasFile("data.{$fieldName}")) {
                $media = $this->fileUploadService->upload(
                    $request->file("data.{$fieldName}"),
                    'pengajuan-surat',
                    'public',
                    ['collection' => $fieldName]
                );
                $pengajuanSurat->media()->save($media);
            }
        }

        $this->kirimNotifikasiTiket($tiket, $validated['phone']);
        $request->session()->forget(OtpController::SESSION_KEY);

        return redirect()->route('surat.sukses', $tiket->nomor_tiket)
            ->with('success', 'Pengajuan surat Anda berhasil terkirim!');
    }

    /**
     * Halaman sukses pengajuan surat.
     */
    public function sukses(string $nomor_tiket)
    {
        $tiket = Tiket::with(['pemohon', 'detail', 'detail.jenisSurat'])
            ->where('nomor_tiket', $nomor_tiket)
            ->where('detail_type', 'pengajuan_surat')
            ->firstOrFail();

        // Surat siap diunduh hanya bila tiket selesai DAN PDF-nya sudah ada.
        $suratSiap = $tiket->status === TiketStatus::Selesai
            && $this->suratService->pdfMedia($tiket->detail) !== null;

        return view('home.surat.sukses', compact('tiket', 'suratSiap'));
    }

    /**
     * Unduh PDF surat jadi. Gerbang ganda §3: nomor tiket (path) + NIK (input)
     * — NIK saja bukan otentikasi. Hanya untuk tiket selesai yang PDF-nya ada.
     */
    public function unduh(Request $request, string $nomor_tiket)
    {
        $validated = $request->validate([
            'nik' => 'required|digits:16',
        ], [
            'nik.required' => 'Masukkan NIK Anda untuk mengunduh surat.',
            'nik.digits' => 'NIK harus persis 16 digit angka.',
        ]);

        $tiket = Tiket::with(['pemohon', 'detail'])
            ->where('nomor_tiket', $nomor_tiket)
            ->where('detail_type', 'pengajuan_surat')
            ->firstOrFail();

        if (! $tiket->pemohon || ! hash_equals($tiket->pemohon->nik, $validated['nik'])) {
            throw ValidationException::withMessages([
                'nik' => 'NIK tidak cocok dengan data pemohon tiket ini.',
            ]);
        }

        abort_unless($tiket->status === TiketStatus::Selesai, 404);

        $media = $this->suratService->pdfMedia($tiket->detail);

        abort_unless($media, 404);

        return $this->fileUploadService->download($media);
    }

    /**
     * Submit hanya sah jika session menyimpan flag OTP yang masih berlaku
     * dan cocok dengan pasangan NIK + nomor WA yang dikirim form.
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
                'otp' => 'Verifikasi OTP WhatsApp diperlukan sebelum mengirim pengajuan. Silakan verifikasi ulang.',
            ]);
        }
    }

    /**
     * Rules validasi field dinamis dari jenis_surat.fields JSON.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function buildDynamicRules(JenisSurat $jenisSurat): array
    {
        $rules = [];

        foreach ($jenisSurat->fields ?? [] as $field) {
            $name = $field['name'] ?? null;

            if (! $name) {
                continue;
            }

            $rule = [($field['required'] ?? false) ? 'required' : 'nullable'];

            $rules["data.{$name}"] = array_merge($rule, match ($field['type'] ?? 'text') {
                'textarea' => ['string'],
                'number' => ['numeric'],
                'date' => ['date'],
                'select' => ['string', Rule::in($field['options'] ?? [])],
                'file' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
                default => ['string', 'max:255'],
            });
        }

        return $rules;
    }

    /**
     * Nama atribut ramah-manusia untuk pesan error field dinamis.
     *
     * @return array<string, string>
     */
    protected function buildDynamicAttributes(JenisSurat $jenisSurat): array
    {
        $attributes = [];

        foreach ($jenisSurat->fields ?? [] as $field) {
            if (! empty($field['name'])) {
                $attributes['data.'.$field['name']] = $field['label'] ?? Str::headline($field['name']);
            }
        }

        return $attributes;
    }

    /**
     * Kirim WA nomor tiket + link cek status, catat hasilnya di notifikasi_wa.
     * Kegagalan WA tidak boleh menggagalkan submission yang sudah ter-commit.
     */
    protected function kirimNotifikasiTiket(Tiket $tiket, string $phone): void
    {
        $pesan = sprintf(
            'Pengajuan surat Anda telah diterima. Nomor tiket: %s. Pantau prosesnya di %s',
            $tiket->nomor_tiket,
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
}
