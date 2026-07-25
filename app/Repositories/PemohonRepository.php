<?php

namespace App\Repositories;

use App\Contracts\Repositories\PemohonRepository as PemohonRepositoryContract;
use App\Models\Pemohon;

class PemohonRepository extends BaseRepository implements PemohonRepositoryContract
{
    public function __construct(Pemohon $model)
    {
        $this->model = $model;
    }
}
