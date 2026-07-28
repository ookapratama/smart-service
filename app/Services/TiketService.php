<?php

namespace App\Services;

use App\Enums\TiketStatus;
use App\Models\Setting;
use App\Models\Tiket;
use App\Models\TiketCounter;
use App\Repositories\TiketRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TiketService extends BaseService
{
    public function __construct(TiketRepository $repository)
    {
        parent::__construct($repository);
    }

    public function filtered(array $filters = [])
    {
        return $this->repository->filtered($filters);
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
        $tiket = $this->find($id);
        $statusFrom = $tiket->status;
        $newStatus = TiketStatus::from($statusTo);

        if (! in_array($newStatus, $statusFrom->transitions(), true)) {
            throw new \InvalidArgumentException(
                "Transisi status dari {$statusFrom->label()} ke {$newStatus->label()} tidak diizinkan."
            );
        }

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

        return $tiket;
    }
}
