<?php

namespace App\Services;

use App\Contracts\Repositories\BeritaRepository;

class BeritaService extends BaseService
{
    public function __construct(BeritaRepository $repository)
    {
        parent::__construct($repository);
    }
}
