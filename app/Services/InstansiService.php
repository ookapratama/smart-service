<?php

namespace App\Services;

use App\Repositories\InstansiRepository;

class InstansiService extends BaseService
{
    public function __construct(InstansiRepository $repository)
    {
        parent::__construct($repository);
    }
}
