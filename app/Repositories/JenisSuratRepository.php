<?php

namespace App\Repositories;

use App\Contracts\Repositories\JenisSuratRepository as JenisSuratRepositoryContract;
use App\Models\JenisSurat;

class JenisSuratRepository extends BaseRepository implements JenisSuratRepositoryContract
{
    public function __construct(JenisSurat $model)
    {
        $this->model = $model;
    }
}
