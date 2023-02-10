<?php

namespace App\Repositories\TargetFacilityPersonSettingJob;

use App\Repositories\BaseRepositoryInterface;

interface TargetFacilityPersonSettingJobRepositoryInterface extends BaseRepositoryInterface
{
    public function getTargetProductCd($date_system);
    public function deleteTargetFacilityPersionBaseOnProductCd($targetProductCds, $jobId, $keyBatchJob);
    public function registerDataTargetFacilityPersionBaseOnProductCd($targetProductCds, $dateSystem, $jobId, $keyBatchJob);
    public function deleteTargetFacilityPersionBaseOnCheckForeignNotExistOnTableMainOther($dateSystem, $jobId, $keyBatchJob);
    public function convertStringToWhereIn($prdCds);
}
