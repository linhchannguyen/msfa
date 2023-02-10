<?php

namespace App\Services;

use App\Repositories\TargetFacilityPersonSettingJob\TargetFacilityPersonSettingJobRepositoryInterface;

class TargetFacilityPersonSettingServiceJob
{
    private $repo, $serviceAccessLog;

    public function __construct(TargetFacilityPersonSettingJobRepositoryInterface $repo, LogService $serviceAccessLog)
    {
        $this->repo = $repo;
        $this->serviceAccessLog = $serviceAccessLog;
    }
    /**
     * get list target product base on date of system
     * screen : G01-B01_基本設計書【ターゲット施設個人確定】
     * document : https://docs.google.com/spreadsheets/d/1irmw8vP2PKjGLHnxQVcwzrbo81w3o1hq/edit#gid=917942248
     * 
     * @author huynh
     */
    public function actionTargetFacilityIndividualConfirmation($dateSystem, $jobId, $keyBatchJob)
    {
        // get list target product base on date of system
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Select confirmed target product in m_target_product");
        $targetProductCds = $this->repo->getTargetProductCd($dateSystem);
        $targetProductCds = $this->repo->convertStringToWhereIn($targetProductCds);
        // remove data m_target_facility_person base on list product cd
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Deleted out of date target facility person in  m_target_facility_person");
        $this->repo->deleteTargetFacilityPersionBaseOnProductCd($targetProductCds, $jobId, $keyBatchJob);
        // register data m_target_facility_person base on list product cd from table t_target_facility_person_selection
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Inserted confirmed target facility person into  m_target_facility_person");
        $this->repo->registerDataTargetFacilityPersionBaseOnProductCd($targetProductCds, $dateSystem, $jobId, $keyBatchJob);
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End  selecting  to confirmed targets");
        // detele data m_target_facility_person base on foreign key not exist on table main
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start  deleting  to invalid confirmed targets");
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Deleted out of date target facility person in  m_target_facility_person");
        $this->repo->deleteTargetFacilityPersionBaseOnCheckForeignNotExistOnTableMainOther($dateSystem, $jobId, $keyBatchJob);
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End   deleting  to invalid confirmed targets");
    }
}
