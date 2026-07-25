<?php

namespace App\Repositories;

use App\Contracts\Repositories\TiketRepository as TiketRepositoryContract;
use App\Models\Tiket;

class TiketRepository extends BaseRepository implements TiketRepositoryContract
{
    public function __construct(Tiket $model)
    {
        $this->model = $model;
    }

    /**
     * Antrian tiket dengan filter opsional (status, channel), diurutkan terbaru.
     */
    public function filtered(array $filters = [])
    {
        return $this->model->with(['pemohon', 'detail'])
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['channel'] ?? null, fn ($q, $channel) => $q->where('channel', $channel))
            ->latest()
            ->get();
    }
}
