<?php

namespace App\Services;

use App\Repositories\JenisSuratRepository;

class JenisSuratService extends BaseService
{
    public function __construct(JenisSuratRepository $repository)
    {
        parent::__construct($repository);
    }
}
