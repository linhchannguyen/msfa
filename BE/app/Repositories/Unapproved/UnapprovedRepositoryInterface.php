<?php

namespace App\Repositories\Unapproved;

use App\Repositories\BaseRepositoryInterface;

interface UnapprovedRepositoryInterface extends BaseRepositoryInterface
{
    public function allData($request);
}
