<?php

namespace App\Repositories\OrganizationManagement;

use App\Repositories\BaseRepository;
use App\Repositories\OrganizationManagement\OrganizationManagementRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Enums\LogBatchJob;
use App\Traits\DateTimeTrait;

class OrganizationManagementRepository extends BaseRepository implements OrganizationManagementRepositoryInterface
{
    use DateTimeTrait;
    private $date;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
    }

    public function allOrganizationLayer1($date, $layer_num)
    {
        $sql = "SELECT org_cd,org_name,layer_num,org_label
                FROM i_org 
                WHERE layer_num = :layer_num
                    AND start_date <= :start_date
                    AND end_date >= :end_date
                    AND layer_1 IS NOT NULL 
                    AND COALESCE(layer_2, layer_3, layer_4, layer_5) IS NULL;";
        return $this->_find($sql, [
            'layer_num' => $layer_num,
            'start_date' => $date,
            'end_date' => $date
        ]);
    }

    public function allOrganizationLayer2($date, $layer_num, $layer_1)
    {
        $sql = "SELECT org_cd,org_name,layer_num,org_label
                FROM i_org 
                WHERE layer_num = :layer_num
                    AND start_date <= :start_date
                    AND end_date >= :end_date
                    AND layer_1 = :layer_1
                    AND layer_2 IS NOT NULL 
                    AND COALESCE(layer_3, layer_4, layer_5) IS NULL;";
        return $this->_find($sql, [
            'layer_num' => $layer_num,
            'start_date' => $date,
            'end_date' => $date,
            'layer_1' => $layer_1
        ]);
    }

    public function allOrganizationLayer3($date, $layer_num, $layer_1, $layer_2)
    {
        $sql = "SELECT org_cd,org_name,layer_num,org_label
                FROM i_org 
                WHERE layer_num = :layer_num
                    AND start_date <= :start_date
                    AND end_date >= :end_date
                    AND layer_1 = :layer_1
                    AND layer_2 = :layer_2
                    AND layer_3 IS NOT NULL
                    AND COALESCE(layer_4, layer_5) IS NULL;";
        return $this->_find($sql, [
            'layer_num' => $layer_num,
            'start_date' => $date,
            'end_date' => $date,
            'layer_1' => $layer_1,
            'layer_2' => $layer_2
        ]);
    }

    public function allOrganizationLayer4($date, $layer_num, $layer_1, $layer_2, $layer_3)
    {
        $sql = "SELECT org_cd,org_name,layer_num,org_label
                FROM i_org 
                WHERE layer_num = :layer_num
                    AND start_date <= :start_date
                    AND end_date >= :end_date
                    AND layer_1 = :layer_1
                    AND layer_2 = :layer_2
                    AND layer_3 = :layer_3
                    AND layer_4 IS NOT NULL 
                    AND layer_5 IS NULL;";
        return $this->_find($sql, [
            'layer_num' => $layer_num,
            'start_date' => $date,
            'end_date' => $date,
            'layer_1' => $layer_1,
            'layer_2' => $layer_2,
            'layer_3' => $layer_3
        ]);
    }

    public function allOrganizationLayer5($date, $layer_num, $layer_1, $layer_2, $layer_3, $layer_4)
    {
        $sql = "SELECT org_cd,org_name,layer_num,org_label
                FROM i_org 
                WHERE layer_num = :layer_num
                    AND start_date <= :start_date
                    AND end_date >= :end_date
                    AND layer_1 = :layer_1
                    AND layer_2 = :layer_2
                    AND layer_3 = :layer_3
                    AND layer_4 = :layer_4
                    AND layer_5 IS NOT NULL;";
        return $this->_find($sql, [
            'layer_num' => $layer_num,
            'start_date' => $date,
            'end_date' => $date,
            'layer_1' => $layer_1,
            'layer_2' => $layer_2,
            'layer_3' => $layer_3,
            'layer_4' => $layer_4
        ]);
    }

    public function allUser($date, $org_cd)
    {
        $query['start_date'] = $date;
        $query['end_date'] = $date;
        $query['org_cd'] = $org_cd;
        $sql_org_detail = "SELECT * FROM i_org WHERE org_cd = :org_cd and start_date <= :start_date and end_date >= :end_date";
        $org_detail = $this->_first($sql_org_detail, $query);
        $where = "";
        $parram = [];
        $parram['start_date'] = $date;
        $parram['end_date'] = $date;
        if (isset($org_detail->layer_num)) {
            $where .= " AND T3.layer_" . $org_detail->layer_num . " = :org_cd";
            $parram['org_cd'] =  $org_cd;
        }
        $sql = "SELECT T3.org_cd, T3.org_name, T3.org_label, T3.layer_num, T1.user_cd, T1.user_name, T2.main_flag, T2.user_post_type, T4.definition_label as definition
                FROM i_user T1 
                    JOIN i_user_org T2 ON T1.user_cd = T2.user_cd 
                    JOIN i_org T3 ON T3.org_cd = T2.org_cd  
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = 'ユーザ役職区分') T4 on T2.user_post_type = T4.definition_value
                WHERE T1.start_date <= :start_date
                    AND T1.end_date >= :end_date" . $where . " GROUP BY T1.user_cd";
        return $this->_find($sql, $parram);
    }

    /**
     * update again data table m_user from table i_user
     * 
     * @param date | date system or date setting by enter on terminal
     * @author huynh
     */
    public function updateUserMaster($date, $jobId, $keyBatchJob)
    {
        $sql = "INSERT INTO m_user (user_cd, user_name, mail_address, created_by, proxy_created_by, created_at, updated_by, proxy_updated_by, updated_at) 
        SELECT user_cd, 
                user_name, 
                mail_address, 
                COALESCE('I03-B02') AS created_by,
                COALESCE(NULL) AS proxy_created_by,
                COALESCE('" . $this->date . "') AS created_at,
                COALESCE('I03-B02') AS updated_by,
                COALESCE(NULL) AS proxy_updated_by,
                COALESCE('" . $this->date . "') AS updated_at  
        FROM i_user  
        WHERE '$date' BETWEEN start_date AND end_date";
        return $this->_create($sql, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    /**
     * update again data table m_org from table i_org
     * 
     * @param date | date system or date setting by enter on terminal
     * @author huynh
     */
    public function updateOrganizationMaster($date, $jobId, $keyBatchJob)
    {
        $sql = "INSERT INTO m_org (org_cd, org_name, org_short_name, org_label, layer_num, layer_1, layer_2, layer_3, layer_4, layer_5, created_by, proxy_created_by, created_at, updated_by, proxy_updated_by, updated_at) 
        SELECT org_cd, org_name, org_short_name, org_label, layer_num, layer_1, layer_2, layer_3, layer_4, layer_5,
            COALESCE('I03-B02') AS created_by,
            COALESCE(NULL) AS proxy_created_by,
            COALESCE('" . $this->date . "') AS created_at,
            COALESCE('I03-B02') AS updated_by,
            COALESCE(NULL) AS proxy_updated_by,
            COALESCE('" . $this->date . "') AS updated_at
        FROM i_org
        WHERE '$date' BETWEEN start_date AND end_date";
        return $this->_create($sql, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    /**
     * update again data table m_user_org from table i_user_org
     * 
     * @param date | date system or date setting by enter on terminal
     * @author huynh
     */
    public function updateUserOrganizationMaster($date, $jobId, $keyBatchJob)
    {
        $sql = "INSERT INTO m_user_org (user_cd, org_cd, user_post_type, main_flag, created_by, proxy_created_by, created_at, updated_by, proxy_updated_by, updated_at) 
        SELECT A.user_cd, A.org_cd, A.user_post_type, A.main_flag, 
                COALESCE('I03-B02') AS created_by,
                COALESCE(NULL) AS proxy_created_by,
                COALESCE('" . $this->date . "') AS created_at,
                COALESCE('I03-B02') AS updated_by,
                COALESCE(NULL) AS proxy_updated_by,
                COALESCE('" . $this->date . "') AS updated_at 
        FROM i_user_org A 
        INNER JOIN m_user B ON A.user_cd = B.user_cd 
        INNER JOIN i_org C ON A.org_cd = C.org_cd 
        WHERE '$date' BETWEEN A.start_date AND A.end_date";
        return $this->_create($sql, [], LogBatchJob::ACTION_UPDATE, $jobId, $keyBatchJob);
    }

    /**
     * empty table
     * 
     * @author huynh
     */
    public function emptyTable($table, $jobId, $keyBatchJob)
    {
        $sql1 = "SET FOREIGN_KEY_CHECKS = 0";
        DB::select($sql1);
        $sql2 = "DELETE FROM $table";
        DB::select($sql2);
        logCountRowActionSql($jobId, LogBatchJob::ACTION_DELETE, $keyBatchJob);
        $sql3 = "SET FOREIGN_KEY_CHECKS = 1";
        DB::select($sql3);
    }
}
