<?php

namespace App\Contracts\Repositories;

interface UserRepository extends Repository
{
  public function paginated(array $params);
}
