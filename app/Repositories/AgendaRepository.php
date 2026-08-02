<?php

namespace App\Repositories;

use App\Contracts\Repositories\AgendaRepository as AgendaRepositoryContract;
use App\Models\Agenda;

class AgendaRepository extends BaseRepository implements AgendaRepositoryContract
{
    public function __construct(Agenda $model)
    {
        $this->model = $model;
    }
}
