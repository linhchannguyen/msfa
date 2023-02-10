<?php

namespace App\Repositories\PermissionSetting;

use App\Repositories\BaseRepository;
use App\Traits\StringConvertTrait;


class PermissionSettingRepository extends BaseRepository implements PermissionSettingRepositoryInterface
{
    use StringConvertTrait;
    protected $useAutoMeta = true;
    
    public function getRole()
    {
        $sql = "SELECT role,role_short_name,role_name,memo FROM c_role WHERE role <> 'RDev' ORDER BY role ASC;";
        return $this->_all($sql);
    }

    public function getDirector()
    {
        $sql = "SELECT definition_value , definition_label FROM m_variable_definition WHERE definition_name = :definition_name ORDER BY sort_order;";
        return $this->_find($sql, ['definition_name' => 'ユーザ役職区分']);
    }

    public function getPermissionList($user_name, $user_cd, $org_cd, $reference_date, $director, $limit, $offset)
    {
        $query = [];
        $sql = "
            SELECT 
                i_user.user_cd,
                i_user.user_name
            FROM i_user
            LEFT JOIN i_user_org
                ON i_user.user_cd = i_user_org.user_cd
            LEFT JOIN i_org
                ON i_user_org.org_cd = i_org.org_cd
            LEFT JOIN m_variable_definition
                ON (m_variable_definition.definition_value = i_user_org.user_post_type AND m_variable_definition.definition_name = 'ユーザ役職区分')
            WHERE 1 = 1 ";

        if (!empty($user_cd)) {
            $sql .=  " AND i_user.user_cd = :user_cd";
            $query['user_cd'] = $user_cd;
        }

        if (!empty($user_name)) {
            $sql .=  " AND i_user.user_name LIKE :user_name";
            $query['user_name'] = '%' . trim($this->convert_kana($user_name)) . '%';
        }

        if (!empty($director)) {
            $sql .=  " AND m_variable_definition.definition_value = :director ";
            $query['director'] = $director;
        }

        if (!empty($org_cd)) {
            $org_cd = explode(',', $org_cd);
            $sql .= " AND i_user_org.org_cd IN " . $this->_buildCommaString($org_cd, true) . " ";
        }

        if (!empty($reference_date)) {
            $sql .= " AND i_user_org.start_date <= :start_date
                         AND i_user_org.end_date >= :end_date
                         AND i_org.start_date <= :i_start_date
                         AND i_org.end_date >= :i_end_date ";

            $query['start_date'] = $query['end_date'] = $query['i_start_date'] = $query['i_end_date'] = $reference_date;
        }

        $sql .= " GROUP BY i_user.user_cd
            ORDER BY i_user.user_cd ASC,i_user_org.org_cd ASC,i_user_org.user_post_type DESC ;";

        return $this->_paginate($sql, $query, [
            "limit" => $limit,
            "offset" => $offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getOrgCdList($user_cd, $org_cd, $reference_date, $director)
    {
        $sql = "
            SELECT 
                i_user_org.user_cd,
                i_user_org.user_cd,
                i_user_org.org_cd,
                i_org.org_label,
                (CASE WHEN i_user_org.main_flag = 1 THEN '主勤務先' WHEN i_user_org.main_flag = 0 THEN '兼務先'  ELSE '' END) as main_flag,
                m_variable_definition.definition_label,
                m_variable_definition.definition_value
            FROM i_user_org
            INNER JOIN i_org
                ON i_user_org.org_cd = i_org.org_cd
            INNER JOIN m_variable_definition
                ON (m_variable_definition.definition_value = i_user_org.user_post_type AND m_variable_definition.definition_name = :definition_name)
            WHERE 1 = 1 ";

        if (!empty($director)) {
            $sql .=  " AND m_variable_definition.definition_value = :director";
            $query['director'] = $director;
        }

        if (!empty($org_cd)) {
            $org_cd = explode(',', $org_cd);
            $sql .= " AND i_user_org.org_cd IN " . $this->_buildCommaString($org_cd, true) . " ";
        }

        if (!empty($reference_date)) {
            $sql .= " AND i_user_org.start_date <= :start_date
                     AND i_user_org.end_date >= :end_date
                     AND i_org.start_date <= :i_start_date
                     AND i_org.end_date >= :i_end_date ";

            $query['start_date'] = $query['end_date'] = $query['i_start_date'] = $query['i_end_date'] = $reference_date;
        }

        $sql .= " AND i_user_org.user_cd IN " . $this->_buildCommaString($user_cd, true) . " 
        ORDER BY i_user_org.user_cd ASC,i_user_org.org_cd ASC,i_user_org.user_post_type DESC ;";

        $query['definition_name'] = 'ユーザ役職区分';
        
        return $this->_find($sql, $query);
    }

    public function getRoleList($user_cd, $reference_date)
    {
        $query = [];
        $sql = "
            SELECT i_user_role.user_cd ,
            CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 
                            'role', i_user_role.role,
                            'start_date' , i_user_role.start_date,
                            'end_date' , i_user_role.end_date,
                            'role_name' , c_role.role_name,
                            'role_short_name' , c_role.role_short_name
                        ) 
                    ), 
                ']' 
            ) as user_role
            FROM i_user_role
            INNER JOIN c_role
                ON c_role.role = i_user_role.role
            WHERE user_cd IN " . $this->_buildCommaString($user_cd, true) . " 
            AND c_role.role <> 'RDev'
        ";

        if (!empty($reference_date)) {
            $sql .= " AND i_user_role.start_date <= :s_reference_date
                      AND i_user_role.end_date >= :e_reference_date";
            $query['s_reference_date'] = $query['e_reference_date'] = $reference_date;
        }
        $sql .= " GROUP BY i_user_role.user_cd ORDER BY i_user_role.user_cd ;";

        return $this->_find($sql, $query);
    }

    public function getRoleListFromCurrent($user_cd, $current_date)
    {
        $query = [];
        $sql = "
            SELECT i_user_role.user_cd ,
            CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 
                            'role', i_user_role.role,
                            'start_date' , i_user_role.start_date,
                            'end_date' , i_user_role.end_date,
                            'role_name' , c_role.role_name,
                            'role_short_name' , c_role.role_short_name
                        ) 
                    ), 
                ']' 
            ) as user_role
            FROM i_user_role
            INNER JOIN c_role
                ON c_role.role = i_user_role.role
            WHERE user_cd IN " . $this->_buildCommaString($user_cd, true);

        if (!empty($current_date)) {
            $sql .= " AND ((i_user_role.start_date <= :current_date_start AND i_user_role.end_date >= :current_date_end) OR i_user_role.start_date >= :current_date_start_1)"; // AND i_user_role.end_date >= :current_date_end";
            $query['current_date_start'] = $query['current_date_start_1'] =  $query['current_date_end'] = $current_date;
        }
        $sql .= " AND c_role.role <> 'RDev' GROUP BY i_user_role.user_cd ORDER BY i_user_role.user_cd";
        return $this->_find($sql, $query);
    }

    public function deletePermission($user_cd, $start_date)
    {
        $sql = " DELETE FROM i_user_role WHERE user_cd = :user_cd AND start_date = :start_date ;";
        return $this->_destroy($sql, ['user_cd' => $user_cd, 'start_date' => $start_date]);
    }

    public function updatePermission($data)
    {
        $insert = "INSERT INTO 
            i_user_role (
                user_cd, role, start_date, end_date
            ) VALUES :values";
        $result = $this->_bulkCreate($insert, $data->datas);
        return $result;
    }

    public function updateMasterUserRole($data)
    {
        $sql = "DELETE FROM m_user_role WHERE user_cd = :user_cd;";
        $this->_destroy($sql, ['user_cd' => $data[0]['user_cd']]);
        $sql = "INSERT INTO m_user_role (user_cd, role)
            VALUES :values;";
        return $this->_bulkCreate($sql, $data);
    }

    public function getCurrenntPermission($user_cd, $start_date, $type)
    {
        $sql = "
            SELECT 
                user_cd,start_date,end_date
            FROM i_user_role 
            WHERE user_cd = :user_cd ";
        if ($type == 'DESC') {
            $sql .= " AND start_date < :start_date";
        } else {
            $sql .= " AND start_date > :start_date";
        }

        $sql .= " GROUP BY user_cd,start_date ORDER BY start_date $type LIMIT 1;";

        return $this->_find($sql, ['user_cd' => $user_cd, 'start_date' => $start_date]);
    }

    public function updatePermissionEndDate($user_cd, $start_date, $end_date)
    {
        $sql = "
            UPDATE 
                i_user_role 
            SET
                end_date = :end_date
            WHERE user_cd = :user_cd
            AND start_date = :start_date ;";

        return $this->_update($sql, [
            "user_cd" => $user_cd,
            "start_date" => $start_date,
            "end_date" => $end_date
        ]);
    }
}
