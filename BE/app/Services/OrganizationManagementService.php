<?php

namespace App\Services;

use App\Repositories\OrganizationManagement\OrganizationManagementRepositoryInterface;
use App\Repositories\Organization\OrganizationRepositoryInterface;
use Illuminate\Support\Facades\Log;
use App\Services\LogService;

class OrganizationManagementService
{
    private $repo_manage;
    private $repo;
    protected $serviceAccessLog;

    public function __construct(OrganizationManagementRepositoryInterface $repo_manage, OrganizationRepositoryInterface $repo, LogService $serviceAccessLog)
    {
        $this->repo_manage = $repo_manage;
        $this->repo = $repo;
        $this->serviceAccessLog = $serviceAccessLog;
    }

    public function getListData($data)
    {
        $result = [];
        $org_obj = [];
        $title = '組織階層名';
        $title_org = $this->repo->allTitleOrg($title);
        $date = $data->date;
        $layer_all = $this->repo_manage->allOrganizationLayer1($date, 1);
        foreach ($layer_all as $item_layer_1) {
            $obj_layer_2 = [];
            $layer_2 = $this->repo_manage->allOrganizationLayer2($date, 2, $item_layer_1->org_cd);
            foreach ($layer_2 as $item_layer_2) {
                $obj_layer_3 = [];
                $layer_3 = $this->repo_manage->allOrganizationLayer3($date, 3, $item_layer_1->org_cd, $item_layer_2->org_cd);
                foreach ($layer_3 as $item_layer_3) {
                    $layer_4 = $this->repo_manage->allOrganizationLayer4($date, 4, $item_layer_1->org_cd, $item_layer_2->org_cd, $item_layer_3->org_cd);
                    $obj_layer_4 = [];
                    foreach ($layer_4 as $item_layer_4) {
                        $layer_5 = $this->repo_manage->allOrganizationLayer5($date, 5, $item_layer_1->org_cd, $item_layer_2->org_cd, $item_layer_3->org_cd, $item_layer_4->org_cd);
                        $item_layer_4->children = $layer_5;
                        $obj_layer_4[] = $item_layer_4;
                    }
                    $item_layer_3->children = $obj_layer_4;
                    $obj_layer_3[] = $item_layer_3;
                }
                $item_layer_2->children = $obj_layer_3;
                $obj_layer_2[] = $item_layer_2;
            }
            $item_layer_1->children = $obj_layer_2;
            $org_obj[] = $item_layer_1;
        }
        $result['title'] = $title_org;
        $result['org_obj'] = $org_obj;
        return $result;
    }

    public function getListUser($data)
    {
        $date = $data->date;
        $org_cd = $data->org_cd;
        return $this->repo_manage->allUser($date, $org_cd);
    }
    /**
     * update again data table m_user from table i_user
     * 
     * @param date | date system or date setting by enter on terminal
     * @author huynh
     */
    public function excuteUserMaster ($date, $jobId, $keyBatchJob) {
        // empty table m_user
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "deleting  m_user data"); // add line
        $this->repo_manage->emptyTable('m_user', $jobId, $keyBatchJob);
        // insert table m_user from i_user
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "inserting  m_user data"); // add line
        $this->repo_manage->updateUserMaster($date, $jobId, $keyBatchJob);
        return true;
    }

    /**
     * update again data table m_org from table i_org
     * 
     * @param date | date system or date setting by enter on terminal
     * @author huynh
     */
    public function excuteOrganizationMaster ($date, $jobId, $keyBatchJob) {
        // empty table m_user
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "deleting  m_org data");
        $this->repo_manage->emptyTable('m_org', $jobId, $keyBatchJob);
        // insert table m_user from i_user
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "inserting  m_org data");
        $this->repo_manage->updateOrganizationMaster($date, $jobId, $keyBatchJob);
    }

    /**
     * update again data table m_user_org from table i_user_org
     * 
     * @param date | date system or date setting by enter on terminal
     * @author huynh
     */
    public function excuteUserOrganizationMaster ($date, $jobId, $keyBatchJob) {
        // empty table m_user
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "deleting  m_user_org data");
        $this->repo_manage->emptyTable('m_user_org', $jobId, $keyBatchJob);
        // insert table m_user from i_user
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "inserting  m_user_org data");
        $this->repo_manage->updateUserOrganizationMaster($date, $jobId, $keyBatchJob);
    }
}
