<?php

namespace App\Services;

use App\Repositories\GetSystemParameter\GetSystemParameterRepositoryInterface;

class GetSystemParameterService
{
    private $repo;

    public function __construct(GetSystemParameterRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getSystemParameter()
    {
        $result['system_name'] = $this->repo->getSystemParameter();
        return $result;
    }

    public function getAllDataFromSystemParameterByName($parameterName)
    {
        return $this->repo->getAllDataFromSystemParameterByName($parameterName);
    }
}
