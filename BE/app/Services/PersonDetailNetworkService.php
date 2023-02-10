<?php

namespace App\Services;

use App\Repositories\PersonDetailNetwork\PersonDetailNetworkRepositoryInterface;

class PersonDetailNetworkService
{
    private $repo;

    public function __construct(PersonDetailNetworkRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function personalDetail($person_cd)
    {
        return $this->repo->personalDetail($person_cd);
    }

    public function getEra($era)
    {
        return $this->repo->getEra($era);
    }

    public function search($conditions, $limit, $offset)
    {
        return $this->repo->search($conditions, $limit, $offset);
    }

    public function getWorkplaceHistoryList($person_cd)
    {
        return $this->repo->getWorkplaceHistoryList($person_cd);
    }
}