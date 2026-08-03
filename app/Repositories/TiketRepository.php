<?php

namespace App\Repositories;

use App\Contracts\Repositories\TiketRepository as TiketRepositoryContract;
use App\Models\PengajuanSurat;
use App\Models\Tiket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TiketRepository extends BaseRepository implements TiketRepositoryContract
{
    public function __construct(Tiket $model)
    {
        $this->model = $model;
    }

    /**
     * Antrian tiket berpaginasi dengan filter opsional:
     * status, channel, detail_type (morph alias), jenis_surat_id,
     * q (nomor tiket / nama / NIK pemohon). Diurutkan terbaru.
     */
    public function filtered(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->with(['pemohon', 'detail'])
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['channel'] ?? null, fn ($q, $channel) => $q->where('channel', $channel))
            ->when($filters['detail_type'] ?? null, fn ($q, $type) => $q->where('detail_type', $type))
            ->when($filters['jenis_surat_id'] ?? null, function ($q, $jenisSuratId) {
                $q->where('detail_type', 'pengajuan_surat')
                    ->whereIn('detail_id', PengajuanSurat::where('jenis_surat_id', $jenisSuratId)->select('id'));
            })
            ->when($filters['q'] ?? null, function ($q, $keyword) {
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('nomor_tiket', 'like', "%{$keyword}%")
                        ->orWhereHas('pemohon', function ($pemohon) use ($keyword) {
                            $pemohon->where('name', 'like', "%{$keyword}%")
                                ->orWhere('nik', 'like', "%{$keyword}%");
                        });
                });
            })
            ->latest()
            ->paginate($perPage);
    }
}
