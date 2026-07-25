<?php

namespace App\Services;

use App\Repositories\PemohonRepository;

class PemohonService extends BaseService
{
    public function __construct(PemohonRepository $repository)
    {
        parent::__construct($repository);
    }
}
