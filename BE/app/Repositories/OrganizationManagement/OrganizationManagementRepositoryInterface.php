<?php

namespace App\Repositories\OrganizationManagement;

use App\Repositories\BaseRepositoryInterface;

interface OrganizationManagementRepositoryInterface extends BaseRepositoryInterface
{
    public function allOrganizationLayer1($date,$layer_num);
    public function allOrganizationLayer2($date,$layer_num,$layer_1);
    public function allOrganizationLayer3($date,$layer_num,$layer_1,$layer_2);
    public function allOrganizationLayer4($date,$layer_num,$layer_1,$layer_2,$layer_3);
    public function allOrganizationLayer5($date,$layer_num,$layer_1,$layer_2,$layer_3,$layer_4);
    public function allUser($date,$org_cd);
    public function updateUserMaster($date, $jobId, $keyBatchJob);
    public function updateOrganizationMaster($date, $jobId, $keyBatchJob);
    public function updateUserOrganizationMaster($date, $jobId, $keyBatchJob);
    public function emptyTable($table, $jobId, $keyBatchJob);
    
}
