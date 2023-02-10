<?php

namespace App\Repositories\PersonalDetailsWorkingInformation;

use App\Repositories\BaseRepositoryInterface;

interface PersonalDetailsWorkingInformationRepositoryInterface extends BaseRepositoryInterface
{
    public function getWorkingInformation($person_cd,$limit,$offset);
    public function getPhaseName($date_system);
}
