<?php

namespace App\Repositories\Organization;

use App\Repositories\BaseRepository;
use App\Repositories\Organization\OrganizationRepositoryInterface;
use App\Traits\DateTimeTrait;

class OrganizationRepository extends BaseRepository implements OrganizationRepositoryInterface
{
    use DateTimeTrait;
    private $date, $organizational_hierarchy, $general;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
        $this->organizational_hierarchy = "組織階層";
        $this->general = "全般";
    }

    public function allOrganizationLayer1($layer_num)
    {
        $sql = "SELECT org_cd,org_name,layer_num,org_label
                FROM m_org 
                WHERE layer_num = :layer_num
                    AND layer_1 IS NOT NULL 
                    AND COALESCE(layer_2, layer_3, layer_4, layer_5) IS NULL;";
        return $this->_find($sql, ['layer_num' => $layer_num]);
    }
    public function allOrganizationLayer2($layer_num, $layer_1)
    {
        $sql = "SELECT org_cd,org_name,layer_num,org_label
                FROM m_org 
                WHERE layer_num = :layer_num
                    AND layer_1 = :layer_1
                    AND layer_2 IS NOT NULL 
                    AND COALESCE(layer_3, layer_4, layer_5) IS NULL;";
        return $this->_find($sql, [
            'layer_num' => $layer_num,
            'layer_1' => $layer_1
        ]);
    }

    public function allOrganizationLayer3($layer_num, $layer_1, $layer_2)
    {
        $sql = "SELECT org_cd,org_name,layer_num,org_label
                FROM m_org 
                WHERE layer_num = :layer_num
                    AND layer_1 = :layer_1
                    AND layer_2 = :layer_2
                    AND layer_3 IS NOT NULL
                    AND COALESCE(layer_4, layer_5) IS NULL;";
        return $this->_find($sql, [
            'layer_num' => $layer_num,
            'layer_1' => $layer_1,
            'layer_2' => $layer_2
        ]);
    }
    public function allOrganizationLayer4($layer_num, $layer_1, $layer_2, $layer_3)
    {
        $sql = "SELECT org_cd,org_name,layer_num,org_label
                FROM m_org 
                WHERE layer_num = :layer_num
                    AND layer_1 = :layer_1
                    AND layer_2 = :layer_2
                    AND layer_3 = :layer_3
                    AND layer_4 IS NOT NULL 
                    AND layer_5 IS NULL;";
        return $this->_find($sql, [
            'layer_num' => $layer_num,
            'layer_1' => $layer_1,
            'layer_2' => $layer_2,
            'layer_3' => $layer_3
        ]);
    }
    public function allOrganizationLayer5($layer_num, $layer_1, $layer_2, $layer_3, $layer_4)
    {
        $sql = "SELECT org_cd,org_name,layer_num,org_label
                FROM m_org 
                WHERE layer_num = :layer_num
                    AND layer_1 = :layer_1
                    AND layer_2 = :layer_2
                    AND layer_3 = :layer_3
                    AND layer_4 = :layer_4
                    AND layer_5 IS NOT NULL;";
        return $this->_find($sql, [
            'layer_num' => $layer_num,
            'layer_1' => $layer_1,
            'layer_2' => $layer_2,
            'layer_3' => $layer_3,
            'layer_4' => $layer_4
        ]);
    }

    public function allTitleOrg($title)
    {
        $sql = "SELECT definition_value, definition_label
                FROM m_variable_definition 
                WHERE definition_name = :definition_name";
        return $this->_find($sql, ['definition_name' => $title]);
    }

    public function allUser($data)
    {
        $sql_org_detail = "SELECT * FROM m_org WHERE org_cd = :org_cd";
        $org_detail = $this->_first($sql_org_detail, ['org_cd' => $data->org_cd]);
        $where = "";
        $query = [];
        if (isset($org_detail->layer_num)) {
            $where .= " AND T3.layer_" . $org_detail->layer_num . " = :org_cd";
            $query['org_cd'] =  $data->org_cd;
        }
        $sql = "SELECT T3.org_cd, 
                        T3.org_name, 
                        T3.org_label, 
                        T3.layer_num,
                        T1.user_cd,
                        T1.user_name, 
                        T2.main_flag,
                        T2.user_post_type,
                        T4.profile_image_file_id,
                        T5.file_url,
                        T6.definition_label as definition
                FROM m_user T1 
                    INNER JOIN m_user_org T2 ON T1.user_cd = T2.user_cd 
                    INNER JOIN m_org T3 ON T3.org_cd = T2.org_cd
                    LEFT JOIN t_user_profile T4 ON T1.user_cd = T4.user_cd
                    LEFT JOIN t_file T5 ON T5.file_id = T4.profile_image_file_id 
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = 'ユーザ役職区分') T6 on T2.user_post_type = T6.definition_value
                WHERE 1 = 1 " . $where . " GROUP BY T1.user_cd
                ORDER BY T3.sort_order ASC, T2.user_post_type DESC, T1.user_cd ASC;";
        return $this->_find($sql, $query);
    }

    public function getOrg($org_cd)
    {
        $sql = "
            SELECT 
                * 
            FROM 
                m_org 
            WHERE 
                org_cd = :org_cd;
        ";

        return $this->_first($sql, ["org_cd" => $org_cd]);
    }

    public function getMainOrganizationsByUser($user_cd)
    {
        $sql = "SELECT T1.org_cd, T1.org_label, T1.org_name, T1.org_short_name, T2.main_flag
                FROM m_org T1 
                    JOIN m_user_org T2 ON T1.org_cd = T2.org_cd 
                WHERE T2.user_cd = :user_cd AND T2.main_flag";
        return $this->_first($sql, ['user_cd' => $user_cd]);
    }
}
