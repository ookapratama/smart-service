<?php

namespace App\Services;

use App\Enums\TiketStatus;
use App\Repositories\TiketRepository;
use Illuminate\Support\Facades\Auth;

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
