<?php

namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TiketRepository extends Repository
{
    public function filtered(array $filters = [], int $perPage = 20): LengthAwarePaginator;
}
