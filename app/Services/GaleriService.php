<?php

namespace App\Services;

use App\Contracts\Repositories\GaleriRepository;

class GaleriService extends BaseService
{
    public function __construct(GaleriRepository $repository)
    {
        parent::__construct($repository);
    }
}
