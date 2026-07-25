<?php

namespace App\Repositories;

use App\Contracts\Repositories\KategoriPengaduanRepository as KategoriPengaduanRepositoryContract;
use App\Models\KategoriPengaduan;

class KategoriPengaduanRepository extends BaseRepository implements KategoriPengaduanRepositoryContract
{
    public function __construct(KategoriPengaduan $model)
    {
        $this->model = $model;
    }
}
