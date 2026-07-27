<?php

namespace App\Repositories;

use App\Contracts\Repositories\JadwalPelayananRepository as JadwalPelayananRepositoryContract;
use App\Models\JadwalPelayanan;

class JadwalPelayananRepository extends BaseRepository implements JadwalPelayananRepositoryContract
{
    public function __construct(JadwalPelayanan $model)
    {
        $this->model = $model;
    }
}
