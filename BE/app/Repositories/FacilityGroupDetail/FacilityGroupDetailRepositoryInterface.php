<?php

namespace App\Repositories\FacilityGroupDetail;

use App\Repositories\BaseRepositoryInterface;

interface FacilityGroupDetailRepositoryInterface extends BaseRepositoryInterface
{
    public function deleteFacilityGroupDetail($jobId, $keyBatchJob);
}
