<?php

namespace App\Repositories\GetSystemParameter;

use App\Repositories\BaseRepositoryInterface;

interface GetSystemParameterRepositoryInterface extends BaseRepositoryInterface
{
    public function getSystemParameter();
    public function getAllDataFromSystemParameterByName($parameterName);
}