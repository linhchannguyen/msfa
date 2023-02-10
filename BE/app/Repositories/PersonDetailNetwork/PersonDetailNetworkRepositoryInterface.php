<?php

namespace App\Repositories\PersonDetailNetwork;

use App\Repositories\BaseRepositoryInterface;

interface PersonDetailNetworkRepositoryInterface extends BaseRepositoryInterface
{
    public function personalDetail($person_cd);
    public function getEra($era);
    public function search($conditions,  $limit, $offset);
    public function getWorkplaceHistoryList($person_cd);
}
