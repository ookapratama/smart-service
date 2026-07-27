<?php

namespace App\Services;

use App\Contracts\Repositories\JadwalPelayananRepository;

class JadwalPelayananService extends BaseService
{
    public function __construct(JadwalPelayananRepository $repository)
    {
        parent::__construct($repository);
    }
}
