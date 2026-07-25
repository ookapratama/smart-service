<?php

namespace App\Contracts\Repositories;

interface MenuRepository extends Repository
{
    public function getTree();
}
