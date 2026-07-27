<?php

namespace App\Repositories;

use App\Contracts\Repositories\BeritaRepository as BeritaRepositoryContract;
use App\Models\Berita;

class BeritaRepository extends BaseRepository implements BeritaRepositoryContract
{
    public function __construct(Berita $model)
    {
        $this->model = $model;
    }
}
