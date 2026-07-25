<?php

namespace App\Repositories;

use App\Contracts\Repositories\MenuRepository as MenuRepositoryContract;
use App\Models\Menu;

class MenuRepository extends BaseRepository implements MenuRepositoryContract
{
    public function __construct(Menu $model)
    {
        parent::__construct($model);
    }

    public function getTree()
    {
        return $this->model->whereNull('parent_id')
            ->with('children')
            ->orderBy('order_no')
            ->get();
    }
}
