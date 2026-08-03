<?php

namespace App\Services;

use App\Contracts\Services\WhatsAppNotifier;
use App\Enums\NotifikasiWaStatus;
use App\Enums\TiketStatus;
use App\Models\NotifikasiWa;
use App\Models\Setting;
use App\Models\Tiket;
use App\Models\TiketCounter;
use App\Repositories\TiketRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TiketService extends BaseService
{
    /**
     * Pesan peringatan bila PDF surat gagal dibuat pada transisi selesai
     * terakhir. Side-effect pasca-commit tidak boleh menggagalkan perubahan
     * status (§7) — controller membaca properti ini untuk menampilkan warning.
     */
    public ?string $pdfWarning = null;

    protected WhatsAppNotifier $whatsAppNotifier;

    public function __construct(
        TiketRepository $repository,
        protected SuratService $suratService,
        ?WhatsAppNotifier $whatsAppNotifier = null
    ) {
        parent::__construct($repository);
        $this->whatsAppNotifier = $whatsAppNotifier ?? app(WhatsAppNotifier::class);
    }

    public function filtered(array $filters = [], int $perPage = 20)
    {
        return $this->repository->filtered($filters, $perPage);
    }

    /**
     * Generate nomor_tiket berikutnya secara race-safe.
     * HARUS dipanggil di dalam DB::transaction(). Upsert atomik: MySQL
     * INSERT..ON DUPLICATE KEY UPDATE, SQLite INSERT..ON CONFLICT DO UPDATE
     * (grammar Laravel menangani keduanya). lockForUpdate saat baca balik
     * agar baris tidak berubah lagi sebelum nomor dipakai.
     */
    public function generateNomorTiket(): string
    {
        $periode = now()->format('ym');

        TiketCounter::upsert(
            [['periode' => $periode, 'last_seq' => 1]],
            ['periode'],
            ['last_seq' => DB::raw('last_seq + 1')]
        );

        $seq = TiketCounter::where('periode', $periode)->lockForUpdate()->value('last_seq');
        $prefix = Setting::getByKey('tiket_prefix') ?: Tiket::NOMOR_PREFIX;

        return sprintf('%s-%s-%05d', $prefix, $periode, $seq);
    }

    /**
     * Transisikan status tiket dan catat riwayatnya di status_log.
     *
     * @throws \InvalidArgumentException jika transisi tidak diizinkan
     */
    public function updateStatus(int $id, string $statusTo, ?string $catatan = null)
    {
        $this->pdfWarning = null;

        $tiket = $this->find($id);
        $statusFrom = $tiket->status;
        $newStatus = TiketStatus::from($statusTo);

        if (! in_array($newStatus, $statusFrom->transitions(), true)) {
            throw new \InvalidArgumentException(
                "Transisi status dari {$statusFrom->label()} ke {$newStatus->label()} tidak diizinkan."
            );
        }

        $terbitkanSurat = $newStatus === TiketStatus::Selesai
            && $tiket->detail_type === 'pengajuan_surat';

        // Status + status_log + nomor surat dalam SATU transaksi (§7 Atomicity):
        // nomor surat resmi hanya terbit bersama status selesai yang ter-commit.
        DB::transaction(function () use ($tiket, $statusFrom, $newStatus, $catatan, $terbitkanSurat) {
            $tiket->status = $newStatus;
            if ($newStatus === TiketStatus::Selesai) {
                $tiket->selesai_at = now();
            }
            $tiket->save();

            $tiket->statusLogs()->create([
                'status_from' => $statusFrom,
                'status_to' => $newStatus,
                'user_id' => Auth::id(),
                'catatan' => $catatan,
            ]);

            if ($terbitkanSurat) {
                $this->suratService->assignNomorSurat($tiket->detail);
            }
        });

        // Tulis file PDF ketat SETELAH commit (§7 Durability). Gagal PDF tidak
        // membatalkan status — dicatat + petugas bisa generate ulang dari detail tiket.
        if ($terbitkanSurat) {
            try {
                $this->suratService->generatePdf($tiket->detail);
            } catch (\Throwable $e) {
                report($e);
                $this->pdfWarning = 'Status tersimpan dan nomor surat terbit, tetapi PDF surat gagal dibuat. '
                    .'Gunakan tombol "Generate Ulang PDF" pada detail tiket.';
            }
        }

        // Kirim notifikasi WhatsApp ke pemohon via Fonnte / WhatsAppNotifier jika no HP tersedia
        if ($tiket->pemohon && ! empty($tiket->pemohon->phone)) {
            $this->kirimNotifikasiUpdateStatus($tiket, $newStatus, $catatan);
        }

        return $tiket;
    }

    /**
     * Kirim notifikasi WA saat petugas memperbarui status dan/atau catatan tiket.
     */
    protected function kirimNotifikasiUpdateStatus(Tiket $tiket, TiketStatus $status, ?string $catatan): void
    {
        $phone = $tiket->pemohon->phone;
        if (empty($phone)) {
            return;
        }

        $pesan = $this->buatPesanUpdateStatus($tiket, $status, $catatan);

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
     * Isi pesan mengikuti jenis layanan tiket (morph alias), dan pengajuan
     * surat yang selesai menyertakan tautan unduh langsung — gerbang unduh
     * tetap nomor tiket + NIK, keduanya milik penerima pesan ini.
     */
    protected function buatPesanUpdateStatus(Tiket $tiket, TiketStatus $status, ?string $catatan): string
    {
        $jenisLayanan = $tiket->detail_type === 'pengajuan_surat' ? 'pengajuan surat' : 'pengaduan';
        $pesanCatatan = ! empty($catatan) ? "\n\nCatatan Petugas:\n".$catatan : '';

        $pesan = sprintf(
            "Halo, %s!\n\n".
            "Status tiket %s #%s Anda telah diperbarui menjadi [%s].%s\n\n".
            "PANTAU PROGRES STATUS\n".
            "Cek perkembangan detail %s Anda di link berikut:\n".
            '%s',
            $tiket->pemohon->name ?? 'Warga',
            $jenisLayanan,
            $tiket->nomor_tiket,
            $status->label(),
            $pesanCatatan,
            $jenisLayanan,
            route('cek-status.index')
        );

        if ($status === TiketStatus::Selesai && $tiket->detail_type === 'pengajuan_surat') {
            $pesan .= "\n\nSurat resmi Anda sudah terbit dan dapat diunduh di:\n"
                .route('surat.unduh', $tiket->nomor_tiket).'?nik='.$tiket->pemohon->nik;
        }

        return $pesan."\n\nPemerintah Kecamatan Soreang";
    }
}
