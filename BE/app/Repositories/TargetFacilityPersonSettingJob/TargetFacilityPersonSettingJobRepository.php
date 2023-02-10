<?php

namespace App\Repositories\TargetFacilityPersonSettingJob;

use App\Enums\LogBatchJob;
use App\Repositories\BaseRepository;
use App\Repositories\TargetFacilityPersonSettingJob\TargetFacilityPersonSettingJobRepositoryInterface;

class TargetFacilityPersonSettingJobRepository extends BaseRepository implements TargetFacilityPersonSettingJobRepositoryInterface
{
    protected $useAutoMeta = false;
    /**
     * get list target product base on date of system
     * screen : G01-B01_基本設計書【ターゲット施設個人確定】
     * action : 1-1, 1-2
     * document : https://docs.google.com/spreadsheets/d/1irmw8vP2PKjGLHnxQVcwzrbo81w3o1hq/edit#gid=917942248
     * 
     * @author huynh
     */
    public function getTargetProductCd($date_system)
    {
        $sql = "SELECT GROUP_CONCAT(DISTINCT target_product_cd) AS target_product_cds 
                FROM m_target_product 
                WHERE end_date >= :date_system AND 
                (selection_start_date > :date_system_1 OR 
                selection_end_date < :date_system_2)  
                ORDER BY sort_order ASC;";
        $results = $this->_find($sql, [
            'date_system' => $date_system,
            'date_system_1' => $date_system,
            'date_system_2' => $date_system
        ]);
        return $results[0]->target_product_cds;
    }
    /**
     * remove data m_target_facility_person base on list product cd
     * screen : G01-B01_基本設計書【ターゲット施設個人確定】
     * action : 1-3
     * document : https://docs.google.com/spreadsheets/d/1irmw8vP2PKjGLHnxQVcwzrbo81w3o1hq/edit#gid=917942248
     * @param : targetProductCds : list product cd target
     * 
     * @author huynh
     */
    public function deleteTargetFacilityPersionBaseOnProductCd($targetProductCds, $jobId, $keyBatchJob)
    {
        $sql1 = "SET FOREIGN_KEY_CHECKS = 0";
        $this->_destroy($sql1, []);
        $sql2 = "DELETE FROM m_target_facility_person 
                WHERE target_product_cd IN ($targetProductCds)";
        $this->_destroy($sql2, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
        $sql3 = "SET FOREIGN_KEY_CHECKS = 1";
        $this->_destroy($sql3, []);
    }
    /**
     * register data m_target_facility_person base on list product cd from table t_target_facility_person_selection
     * screen : G01-B01_基本設計書【ターゲット施設個人確定】
     * action : 1-4
     * document : https://docs.google.com/spreadsheets/d/1irmw8vP2PKjGLHnxQVcwzrbo81w3o1hq/edit#gid=917942248
     * @param : targetProductCds : list product cd target
     * 
     * @author huynh
     */
    public function registerDataTargetFacilityPersionBaseOnProductCd($targetProductCds, $dateSystem, $jobId, $keyBatchJob)
    {
        $sql = "INSERT INTO m_target_facility_person (facility_cd ,person_cd ,target_product_cd ,created_by ,proxy_created_by ,created_at ,updated_by ,proxy_updated_by ,updated_at)
                SELECT T1.facility_cd
                        ,T1.person_cd
                        ,T1.target_product_cd
                        ,COALESCE('G01-B01') AS created_by
                        ,COALESCE(NULL) AS proxy_created_by
                        ,COALESCE('" . $dateSystem . "') AS created_at
                        ,COALESCE('G01-B01') AS updated_by
                        ,COALESCE(NULL) AS proxy_updated_by
                        ,COALESCE('" . $dateSystem . "') AS updated_at 
                FROM t_target_facility_person_selection T1 
                JOIN m_facility M1 ON M1.facility_cd = T1.facility_cd 
                JOIN m_person M2 ON M2.person_cd = T1.person_cd 
                JOIN m_facility_person M3 ON M3.facility_cd = T1.facility_cd AND M3.person_cd = T1.person_cd 
                WHERE T1.target_product_cd IN ($targetProductCds)";
        $this->_create($sql, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
    /**
     * detele data m_target_facility_person base on foreign key not exist on table main
     * screen : G01-B01_基本設計書【ターゲット施設個人確定】
     * action : 2-1
     * document : https://docs.google.com/spreadsheets/d/1irmw8vP2PKjGLHnxQVcwzrbo81w3o1hq/edit#gid=917942248
     * 
     * @author huynh
     */
    public function deleteTargetFacilityPersionBaseOnCheckForeignNotExistOnTableMainOther($dateSystem, $jobId, $keyBatchJob)
    {
        $sql1 = "SET FOREIGN_KEY_CHECKS = 0";
        $this->_destroy($sql1, []);
        $sql2 = "DELETE FROM m_target_facility_person  
                WHERE m_target_facility_person.facility_cd NOT IN (SELECT facility_cd FROM m_facility) 
                OR m_target_facility_person.person_cd NOT IN (SELECT person_cd FROM m_person) 
                OR m_target_facility_person.target_product_cd NOT IN (SELECT target_product_cd FROM m_target_product) 
                OR m_target_facility_person.target_product_cd IN (SELECT target_product_cd FROM m_target_product WHERE start_date >= :date_system_1 OR end_date <= :date_system_2) 
                OR m_target_facility_person.facility_cd NOT IN (SELECT m_facility_person.facility_cd FROM m_facility_person WHERE m_facility_person.facility_cd = m_target_facility_person.facility_cd AND m_facility_person.person_cd = m_target_facility_person.person_cd)";
        $this->_destroy($sql2, [
            'date_system_1' => $dateSystem,
            'date_system_2' => $dateSystem
        ]);
        $sql3 = "SET FOREIGN_KEY_CHECKS = 1";
        $this->_destroy($sql3, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
    /**
     * convert string to list to use WhereIn
     */
    public function convertStringToWhereIn($prdCds)
    {
        $prdCds = explode(",", $prdCds);
        $listPrdCds = '';
        foreach ($prdCds as $key => $prdCd) {
            $listPrdCds .= "'" . $prdCd . "'";
            if ($key != count($prdCds) - 1) {
                $listPrdCds .= ",";
            }
        }
        return $listPrdCds;
    }
}
