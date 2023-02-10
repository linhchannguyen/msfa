<?php

namespace App\Repositories\PersonGroupDetail;

use App\Repositories\BaseRepositoryInterface;

interface PersonGroupDetailRepositoryInterface extends BaseRepositoryInterface
{
    public function getPersonGroupDetail();
    public function deletePersonGroupDetail($facility_cd, $person_cd, $jobId, $keyBatchJob);
}
