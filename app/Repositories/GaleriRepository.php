<?php

namespace App\Repositories;

use App\Contracts\Repositories\GaleriRepository as GaleriRepositoryContract;
use App\Models\Galeri;

class GaleriRepository extends BaseRepository implements GaleriRepositoryContract
{
    public function __construct(Galeri $model)
    {
        $this->model = $model;
    }
}
