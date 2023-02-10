<?php

namespace App\Repositories\FacilityUser;

use App\Repositories\BaseRepositoryInterface;

interface FacilityUserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * add user reponsible area
     * screen : UI04-I03-B03_基本設計書【施設担当者マスタ反映】
     * @return response
     */
    public function addMasterEquipmentHistoryPerson($jobId, $keyBatchJob);
    /**
     * update table h_facility_user from h_enclave_user
     * screen : UI04-I03-B03_基本設計書【施設担当者マスタ反映】
     * @return response
     */
    public function addMasterEquipmentHistoryPersonFromEnclave($jobId, $keyBatchJob);
    public function getAllMasterEquipmentPerson();
    public function updateMasterEquipmentPerson($data, $jobId, $keyBatchJob);
    public function deleteMasterEquipmentPerson($facility_cd);
    public function updateUserFacilityMaster($date, $jobId, $keyBatchJob);
    public function emptyTable($table, $jobId, $keyBatchJob);
    public function listAttendee($userCd);
    public function getExchangeDataRetentionTime($text);
    public function deleteExchangeDataRetentionTime($dateCompare, $jobId, $keyBatchJob);
    public function deleteDataTutorial($dateCompare, $jobId, $keyBatchJob);
    public function deleteDataPresent($dateCompare, $jobId, $keyBatchJob);
    public function deleteDataOtherEvent($dateCompare, $jobId, $keyBatchJob);
    public function deleteDataMessage($dateCompare, $jobId, $keyBatchJob);
    public function deleteDataNotification($dateCompare, $jobId, $keyBatchJob);
    public function deleteDataStock($dateCompare, $jobId, $keyBatchJob);
    public function deleteDataOperationLog($dateCompare, $jobId, $keyBatchJob);
    public function outputDataToFileOperationLog($date, $jobId, $keyBatchJob);
    public function getConventionName($idConvention);
}
