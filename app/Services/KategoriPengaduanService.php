<?php

namespace App\Services;

use App\Repositories\KategoriPengaduanRepository;

class KategoriPengaduanService extends BaseService
{
    public function __construct(KategoriPengaduanRepository $repository)
    {
        parent::__construct($repository);
    }
}
