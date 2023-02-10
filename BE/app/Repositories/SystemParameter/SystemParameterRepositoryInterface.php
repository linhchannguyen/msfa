<?php

namespace App\Repositories\SystemParameter;

use App\Repositories\BaseRepositoryInterface;

interface SystemParameterRepositoryInterface extends BaseRepositoryInterface
{
    public function getTimezone();
}
