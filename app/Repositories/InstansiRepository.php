<?php

namespace App\Repositories;

use App\Contracts\Repositories\InstansiRepository as InstansiRepositoryContract;
use App\Models\Instansi;

class InstansiRepository extends BaseRepository implements InstansiRepositoryContract
{
    public function __construct(Instansi $model)
    {
        $this->model = $model;
    }
}
