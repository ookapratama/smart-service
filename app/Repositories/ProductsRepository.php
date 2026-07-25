<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProductsRepository as ProductsRepositoryContract;
use App\Models\Products;

class ProductsRepository extends BaseRepository implements ProductsRepositoryContract
{
    public function __construct(Products $model)
    {
        $this->model = $model;
    }
}