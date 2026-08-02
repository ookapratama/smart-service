<?php

namespace App\Services;

use App\Contracts\Repositories\AgendaRepository;

class AgendaService extends BaseService
{
    public function __construct(AgendaRepository $repository)
    {
        parent::__construct($repository);
    }
}
