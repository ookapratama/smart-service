<?php

namespace App\Repositories;

use App\Contracts\Repositories\RoleRepository as RoleRepositoryContract;
use App\Models\Role;

class RoleRepository extends BaseRepository implements RoleRepositoryContract
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }
}
