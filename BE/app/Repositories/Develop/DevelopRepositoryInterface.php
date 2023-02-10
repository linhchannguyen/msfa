<?php

namespace App\Repositories\Develop;

use App\Repositories\BaseRepositoryInterface;

interface DevelopRepositoryInterface extends BaseRepositoryInterface
{
    public function login($develop_cd);
}
