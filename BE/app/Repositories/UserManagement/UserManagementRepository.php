<?php

namespace App\Repositories\UserManagement;

use App\Repositories\BaseRepository;
use App\Traits\StringConvertTrait;
use Carbon\Carbon as time;

class UserManagementRepository extends BaseRepository implements UserManagementRepositoryInterface
{
    use StringConvertTrait;

    protected $useAutoMeta = true;

    public function getUserList($user_cd, $user_name, $org_cd, $date_system, $limit, $offset)
    {
        $sql = "SELECT 
                ius.user_cd, 
                (SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 
                                'start_date' ,i_user.start_date,
                                'end_date' ,i_user.end_date,
                                'user_name' ,i_user.user_name,
                                'mail_address' ,i_user.mail_address,
                                'account_lock_flag' ,i_user.account_lock_flag,
                                'account_lock_remarks' ,i_user.account_lock_remarks
                            ) 
                        ), 
                    ']' 
                ) FROM i_user
                  WHERE i_user.start_date <= :start_date
                  AND i_user.end_date >= :end_date
                  AND i_user.user_cd = ius.user_cd
                )as current_user_system,
                (SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 
                                'user_cd', i_user.user_cd,
                                'start_date' ,i_user.start_date,
                                'end_date' ,i_user.end_date,
                                'user_name' ,i_user.user_name,
                                'mail_address' ,i_user.mail_address,
                                'account_lock_flag' ,i_user.account_lock_flag,
                                'account_lock_remarks' ,i_user.account_lock_remarks
                            ) 
                        ), 
                    ']' 
                ) FROM i_user
                  WHERE i_user.user_cd = ius.user_cd
                  AND i_user.start_date > :start_date_ad
                  ORDER BY i_user.start_date ASC
                )as advance_reservation
            FROM i_user as ius ";
        if (!empty($org_cd)) {
            $sql .= "LEFT JOIN i_user_org ON ius.user_cd = i_user_org.user_cd
                    LEFT JOIN i_org ON i_org.org_cd = i_user_org.org_cd
                    LEFT JOIN m_variable_definition
                        ON (m_variable_definition.definition_value = i_user_org.user_post_type AND m_variable_definition.definition_name = :definition_name)";
            $query['definition_name'] = 'ユーザ役職区分';
        }
        $sql .= " WHERE 1 = 1 ";

        if (!empty($org_cd)) {
            $sql .= "AND i_user_org.start_date <= :i_start_date
            AND i_user_org.end_date >= :i_end_date
            AND i_org.start_date <= :o_start_date
            AND i_org.end_date >= :o_end_date ";
            $query['i_start_date'] = $query['i_end_date']  = $query['o_start_date'] = $query['o_end_date'] = $date_system;
        }

        $query['start_date'] = $query['end_date'] = $query['start_date_ad'] = $date_system;

        if (!empty($user_cd)) {
            $sql .= " AND ius.user_cd = :user_cd ";
            $query['user_cd']  = $user_cd;
        }

        if (!empty($user_name)) {
            $sql = $sql . " AND ius.user_name LIKE :user_name ";
            $query['user_name'] = "%" . trim($this->convert_kana($user_name)) . "%";
        }

        if (!empty($org_cd)) {
            $org_cd = explode(',', $org_cd);
            $sql .= " AND i_user_org.org_cd IN " . $this->_buildCommaString($org_cd, true) . " ";
        }
        $sql .= " GROUP BY ius.user_cd  ORDER BY ius.user_cd DESC ,ius.start_date ASC ;";
        return $this->_paginate($sql, $query, [
            "limit" => $limit,
            "offset" => $offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getDateUser($user_cd, $data_system)
    {
        $sql = "
            SELECT 
                start_date,end_date
            FROM
                i_user
            WHERE user_cd = :user_cd
            AND ((start_date <= :start_date AND end_date >= :end_date) OR start_date > :ad_start_date)
            GROUP BY start_date
            ORDER BY start_date asc ;";

        return $this->_find($sql, ['user_cd' => $user_cd, 'start_date' => $data_system, 'end_date' => $data_system, 'ad_start_date' => $data_system]);
    }

    public function getDateUserOrg($user_cd, $data_system)
    {
        $sql = "
            SELECT 
            i_user_org.start_date,i_user_org.end_date
            FROM
                i_user_org
            INNER JOIN m_variable_definition
                ON m_variable_definition.definition_value = i_user_org.user_post_type
            WHERE i_user_org.user_cd = :user_cd
            AND ((i_user_org.start_date <= :start_date AND i_user_org.end_date >= :end_date) OR i_user_org.start_date > :ad_start_date)
            AND m_variable_definition.definition_name = 'ユーザ役職区分'
            GROUP BY i_user_org.start_date
            ORDER BY i_user_org.start_date asc ;";

        return $this->_find($sql, ['user_cd' => $user_cd, 'start_date' => $data_system, 'end_date' => $data_system, 'ad_start_date' => $data_system]);
    }

    public function createUser($user_cd, $user_name, $mail_address, $start_date, $end_date, $account_lock_flag, $account_lock_remarks)
    {
        $sql = "
        INSERT INTO
            i_user (user_cd,user_name,mail_address,start_date ,end_date, account_lock_flag, account_lock_remarks)
        VALUES
            (:user_cd,:user_name,:mail_address,:start_date,:end_date,:account_lock_flag,:account_lock_remarks);";

        return $this->_create($sql, [
            'user_cd' => $user_cd,
            'user_name' => $user_name,
            'mail_address' => $mail_address,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'account_lock_flag' => empty($account_lock_flag) ? 0 : $account_lock_flag,
            'account_lock_remarks' => $account_lock_remarks
        ]);
    }

    public function createPassphrase($user_cd, $passphrase)
    {
        $sql = "
        INSERT INTO 
            m_passphrase(user_cd,passphrase,require_change_flag)
        VALUES
            (:user_cd,:passphrase,:require_change_flag);
        ";
        return $this->_create($sql, [
            "user_cd" => $user_cd,
            "passphrase" => $passphrase,
            "require_change_flag" => 1
        ]);
    }

    public function deleteUser($user_cd, $start_date)
    {
        $sql = "
        DELETE FROM 
            i_user
        WHERE user_cd = :user_cd 
        AND start_date = :start_date ;";

        return $this->_destroy($sql, ['user_cd' => $user_cd, 'start_date' => $start_date]);
    }

    public function getListUserOrganization($user_cd, $date_system)
    {
        $sql = "
            SELECT 
                i_user.user_cd,
                DATE_FORMAT(iuo.start_date,'%Y/%m/%d') as start_date,
                DATE_FORMAT(iuo.end_date,'%Y/%m/%d') as end_date,
                (SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 
                                'org_cd', i_user_org.org_cd,
                                'definition_label' , m_variable_definition.definition_label,
                                'org_label' , i_org.org_label,
                                'main_flag' , i_user_org.main_flag,
                                'user_post_type' , i_user_org.user_post_type
                            ) 
                        ), 
                    ']' 
                ) FROM i_user_org
                  INNER JOIN m_variable_definition
                     ON (m_variable_definition.definition_value = i_user_org.user_post_type AND m_variable_definition.definition_name = :definition_name)
                  INNER JOIN i_org
                     ON i_org.org_cd = i_user_org.org_cd
                  WHERE i_user_org.user_cd = iuo.user_cd
                  AND i_user_org.start_date = iuo.start_date
                  AND i_user_org.end_date = iuo.end_date
                  ORDER BY i_user_org.start_date ASC
                ) as organization
            FROM i_user 
            INNER JOIN i_user_org AS iuo
                ON i_user.user_cd = iuo.user_cd
            WHERE iuo.start_date <= :start_date
            AND iuo.end_date >= :end_date";

        if (!empty($user_cd)) {
            $sql .= " AND i_user.user_cd IN " . $this->_buildCommaString($user_cd, true);
        }

        $sql .= " GROUP BY i_user.user_cd ,iuo.start_date,iuo.end_date ORDER BY i_user.user_cd DESC ;";

        $query['start_date'] = $query['end_date']  = $date_system;
        $query['definition_name'] = 'ユーザ役職区分';

        return $this->_find($sql, $query);
    }


    public function getListUserOrganizationReservation($user_cd, $date_system)
    {
        $sql = "
            SELECT
                i_user.user_cd,
                DATE_FORMAT(iuo.start_date,'%Y/%m/%d') as start_date,
                DATE_FORMAT(iuo.end_date,'%Y/%m/%d') as end_date,
                (SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 
                                'org_cd', i_user_org.org_cd,
                                'definition_label' , m_variable_definition.definition_label,
                                'org_label' , i_org.org_label,
                                'org_name' , i_org.org_short_name,
                                'main_flag' , i_user_org.main_flag,
                                'user_post_type' , i_user_org.user_post_type
                            ) 
                        ), 
                    ']' 
                ) FROM i_user_org
                  INNER JOIN m_variable_definition
                     ON (m_variable_definition.definition_value = i_user_org.user_post_type AND m_variable_definition.definition_name = :definition_name)
                  INNER JOIN i_org
                     ON (i_org.org_cd = i_user_org.org_cd AND i_org.start_date <= i_user_org.start_date  AND i_org.end_date >= i_user_org.start_date)
                  WHERE i_user_org.user_cd = iuo.user_cd
                  AND i_user_org.start_date = iuo.start_date
                  AND i_user_org.end_date = iuo.end_date
                  ORDER BY i_user_org.start_date ASC
                ) as organization
            FROM i_user 
            INNER JOIN i_user_org AS iuo
                ON i_user.user_cd = iuo.user_cd
            WHERE iuo.start_date > :start_date
            AND i_user.user_cd IN " . $this->_buildCommaString($user_cd, true) . "  
            GROUP BY i_user.user_cd,iuo.start_date,iuo.end_date
            ORDER BY iuo.start_date ASC ;";

        $query['start_date'] = $date_system;
        $query['definition_name'] = 'ユーザ役職区分';

        return $this->_find($sql, $query);
    }

    public function checkUserExist($user_cd, $start_date)
    {
        $query['user_cd'] = $user_cd;
        $sql = "
            SELECT 
                user_cd,
                user_name,
                start_date,
                end_date,
                mail_address,
                account_lock_flag,
                account_lock_remarks 
            FROM i_user 
            WHERE user_cd = :user_cd ";

        if (!empty($start_date)) {
            $sql .= " AND start_date = :start_date ";
            $query['start_date'] = $start_date;
        }

        $sql .= " LIMIT 1;";

        return $this->_find($sql, $query);
    }

    public function updateUser($user_cd, $start_date_old, $start_date, $end_date, $user_name, $mail_address, $account_lock_flag, $account_lock_remarks)
    {
        $sql = "
            UPDATE 
                i_user
            SET
                start_date = :start_date,
                user_name = :user_name,
                mail_address = :mail_address,
                account_lock_flag = :account_lock_flag,
                account_lock_remarks = :account_lock_remarks,
                end_date = :end_date
            WHERE start_date = :start_date_old
            AND user_cd = :user_cd
        ";

        return $this->_update($sql, [
            'user_cd' => $user_cd,
            'start_date_old' => $start_date_old,
            'start_date' => $start_date,
            'user_name' => $user_name,
            'mail_address' => $mail_address,
            'account_lock_flag' => empty($account_lock_flag) ? 0 : $account_lock_flag,
            'account_lock_remarks' => $account_lock_remarks,
            'end_date' => $end_date
        ]);
    }

    public function updateMasterUser($user_cd, $user_name, $mail_address)
    {
        $find = "SELECT user_cd FROM m_user WHERE user_cd = :user_cd";
        $result = $this->_first($find, [
            'user_cd' => $user_cd,
        ]);
        if ($result->user_cd ?? '') {
            $sql = "UPDATE m_user
                        SET user_name = :user_name, mail_address = :mail_address
                    WHERE user_cd = :user_cd;";
            return $this->_update($sql, [
                'user_cd' => $user_cd,
                'user_name' => $user_name,
                'mail_address' => $mail_address
            ]);
        } else {
            $sql = "INSERT INTO m_user (user_cd, user_name, mail_address) 
                VALUES (
                    :user_cd,
                    :user_name,
                    :mail_address
                );";
            return $this->_create($sql, [
                'user_cd' => $user_cd,
                'user_name' => $user_name,
                'mail_address' => $mail_address
            ]);
        }
    }

    public function deleteMasterUserOrgByUserCd($user_cd)
    {
        $sql = "DELETE FROM m_user_org WHERE user_cd = :user_cd;";
        return $this->_destroy($sql, ['user_cd' => $user_cd]);
    }

    public function updateMasterUserOrg($user_cd, $org_cd, $user_post_type, $main_flag)
    {
        $find = "SELECT user_cd FROM m_user_org WHERE user_cd = :user_cd AND org_cd = :org_cd";
        $result = $this->_first($find, [
            'user_cd' => $user_cd,
            'org_cd' => $org_cd
        ]);
        if ($result->user_cd ?? '') {
            $sql = "UPDATE m_user_org
                        SET user_post_type = :user_post_type, main_flag = :main_flag
                    WHERE user_cd = :user_cd AND org_cd = :org_cd;";
            return $this->_update($sql, [
                'user_cd' => $user_cd,
                'org_cd' => $org_cd,
                'user_post_type' => $user_post_type,
                'main_flag' => $main_flag
            ]);
        } else {
            $sql = "INSERT INTO m_user_org (user_cd, org_cd, user_post_type, main_flag)
                VALUES (
                    :user_cd,
                    :org_cd,
                    :user_post_type,
                    :main_flag
                );";
            return $this->_create($sql, [
                'user_cd' => $user_cd,
                'org_cd' => $org_cd,
                'user_post_type' => $user_post_type,
                'main_flag' => $main_flag
            ]);
        }
    }

    public function updateEndDate($user_cd, $start_date, $end_date)
    {
        $sql = "
            UPDATE 
                i_user
            SET
                end_date = :end_date
            WHERE start_date = :start_date
            AND user_cd = :user_cd ;
        ";
        return $this->_update($sql, [
            "user_cd" => $user_cd,
            "start_date" => $start_date,
            "end_date" => $end_date
        ]);
    }


    public function updateOrgEndDate($user_cd, $start_date, $end_date)
    {
        $sql = "
            UPDATE 
                i_user_org 
            SET
                end_date = :end_date
            WHERE start_date = :start_date
            AND user_cd = :user_cd
        ";

        return $this->_update($sql, [
            "user_cd" => $user_cd,
            "start_date" => $start_date,
            "end_date" => $end_date
        ]);
    }

    public function getUserUpdate($user_cd, $start_date, $type)
    {
        $sql = "
            SELECT 
                user_cd,start_date,end_date
            FROM i_user 
            WHERE user_cd = :user_cd";
        if ($type == 'DESC') {
            $sql .= " AND start_date < :start_date";
        } else {
            $sql .= " AND start_date > :start_date";
        }

        $sql .= " GROUP BY user_cd,start_date,end_date  ORDER BY start_date $type LIMIT 1;";

        return $this->_find($sql, ['user_cd' => $user_cd, 'start_date' => $start_date]);
    }

    public function getUserOrgUpdate($user_cd, $start_date, $type)
    {
        $sql = "
            SELECT 
                user_cd,org_cd,start_date,end_date
            FROM i_user_org 
            WHERE user_cd = :user_cd ";
        if ($type == 'DESC') {
            $sql .= " AND start_date < :start_date";
        } else {
            $sql .= " AND start_date > :start_date";
        }

        $sql .= " GROUP BY user_cd,org_cd,start_date,end_date ORDER BY start_date $type LIMIT 1;";

        return $this->_find($sql, ['user_cd' => $user_cd, 'start_date' => $start_date]);
    }

    public function deleteUserOrganization($user_cd, $start_date, $org_cd)
    {
        $query = [];
        $sql = "DELETE FROM i_user_org WHERE user_cd = :user_cd AND start_date = :start_date ";
        if (!empty($org_cd)) {
            $sql .= " AND org_cd = :org_cd ";
            $query['org_cd'] = $org_cd;
        }
        $sql .= " ;";
        $query['user_cd'] = $user_cd;
        $query['start_date'] = $start_date;

        return $this->_destroy($sql, $query);
    }

    public function insertUserOrganization($user_cd, $start_date, $end_date, $org_cd, $main_flag, $user_post_type)
    {
        $sql = "
            INSERT INTO 
                i_user_org (user_cd,start_date,end_date,org_cd,main_flag,user_post_type)
            VALUES 
                (:user_cd,:start_date,:end_date,:org_cd,:main_flag,:user_post_type);
        ";

        return $this->_create($sql, [
            'user_cd' => $user_cd,
            'start_date' => $start_date,
            'org_cd' => $org_cd,
            'main_flag' => $main_flag,
            'user_post_type' => $user_post_type,
            'end_date' => $end_date
        ]);
    }

    public function getVariableDefinition($definition_name)
    {
        $sql = " SELECT definition_label,definition_value FROM m_variable_definition WHERE definition_name = :definition_name ORDER BY sort_order ASC ; ";
        return $this->_find($sql, ['definition_name' => $definition_name]);
    }

    public function getApprovalLayerNum($parameter_name)
    {
        $sql = " SELECT parameter_key,parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_name; ";
        return $this->_find($sql, ['parameter_name' => $parameter_name]);
    }

    public function getUserListRequest($user_cd, $user_name, $org_cd, $date_system, $limit, $offset)
    {
        // Get list user request
        $sql = "SELECT
                ius.user_cd,
                ius.user_name,
                (SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT('user_name' ,i_user.user_name)),']')
                    FROM i_user
                    WHERE i_user.start_date <= :start_date
                    AND i_user.end_date >= :end_date AND i_user.user_cd = ius.user_cd
                ) as current_user_system,
                (SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT('user_name' ,i_user.user_name)),']')
                    FROM i_user
                    WHERE i_user.user_cd = ius.user_cd
                    AND i_user.start_date > :start_date_ad
                    ORDER BY i_user.start_date ASC
                ) as advance_reservation
            FROM i_user ius ";
        if (!empty($org_cd)) {
            $sql .= "LEFT JOIN i_user_org ON ius.user_cd = i_user_org.user_cd
                        LEFT JOIN i_org ON i_org.org_cd = i_user_org.org_cd
                        LEFT JOIN m_variable_definition
                            ON (m_variable_definition.definition_value = i_user_org.user_post_type AND m_variable_definition.definition_name = :definition_name)";

            $query['definition_name'] = 'ユーザ役職区分';
        }

        $sql .= " WHERE 1 = 1 ";
        $query['start_date'] = $query['end_date'] = $query['start_date_ad'] = $date_system;

        if (!empty($org_cd)) {
            $sql .= "AND i_user_org.start_date <= :i_start_date
                AND i_user_org.end_date >= :i_end_date
                AND i_org.start_date <= :o_start_date
                AND i_org.end_date >= :o_end_date ";

            $query['i_start_date'] = $query['i_end_date']  = $query['o_start_date'] = $query['o_end_date'] = $date_system;
        }

        if (!empty($user_cd)) {
            $sql .= " AND ius.user_cd = :user_cd ";
            $query['user_cd']  = $user_cd;
        }

        if (!empty($user_name)) {
            $sql .= " AND ius.user_name LIKE :user_name ";
            $query['user_name'] = "%" . trim($this->convert_kana($user_name)) . "%";
        }

        if (!empty($org_cd)) {
            $org_cd = explode(',', $org_cd);
            $sql .= " AND i_user_org.org_cd IN " . $this->_buildCommaString($org_cd, true) . " ";
        }

        $sql .= " GROUP BY ius.user_cd ORDER BY ius.user_cd DESC ;";

        return $this->_paginate($sql, $query ?? [], [
            "limit" => $limit,
            "offset" => $offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getOrgUserRequest($user_cd, $org_cd, $date_system)
    {
        $org_cd_temp = [];
        if (!empty($org_cd)) {
            $org_cd_temp = explode(',', $org_cd);
        }
        $sql = "
            SELECT
                i_user.user_cd,
                (
                    SELECT JSON_OBJECT( 
                        'org_label', i_org.org_label,
                        'org_cd' , i_org.org_cd,
                        'definition_label' , m_variable_definition.definition_label
                    ) FROM i_user_org
                      INNER JOIN i_org
                          ON (i_org.org_cd = i_user_org.org_cd AND i_org.start_date <= :c_start_date AND i_org.end_date >= :c_end_date)
                      INNER JOIN m_variable_definition
                          ON (m_variable_definition.definition_value = i_user_org.user_post_type AND m_variable_definition.definition_name = :c_definition_name)
                      WHERE i_user.user_cd = i_user_org.user_cd 
                      AND i_user_org.start_date <= :iuc_start_date 
                      AND i_user_org.end_date >= :iuc_end_date
                      AND i_user_org.main_flag = 1 ";
        if (count($org_cd_temp) > 0) {
            $sql .= " AND i_user_org.org_cd IN " . $this->_buildCommaString($org_cd_temp, true);
        }
        $sql .= " LIMIT 1 ) as org_current,
                (
                    SELECT JSON_OBJECT( 
                        'org_label', i_org.org_label,
                        'org_cd' , i_org.org_cd,
                        'definition_label' , m_variable_definition.definition_label
                    ) FROM i_user_org
                      INNER JOIN i_org
                          ON (i_org.org_cd = i_user_org.org_cd AND i_org.start_date <= :or_start_date AND i_org.end_date >= :or_end_date)
                      INNER JOIN m_variable_definition
                          ON (m_variable_definition.definition_value = i_user_org.user_post_type AND m_variable_definition.definition_name = :or_definition_name)
                      WHERE i_user.user_cd = i_user_org.user_cd 
                      AND i_user_org.start_date > :iuor_start_date 
                      AND i_user_org.main_flag = 1";

        if (count($org_cd_temp) > 0) {
            $sql .= " AND i_user_org.org_cd IN " . $this->_buildCommaString($org_cd_temp, true);
        }

        $sql .= " ORDER BY i_user_org.start_date ASC LIMIT 1

                ) as org_reservation
            FROM i_user
            LEFT JOIN i_user_org
                ON (i_user_org.user_cd = i_user.user_cd)
            WHERE i_user.user_cd IN " . $this->_buildCommaString($user_cd, true);
        if (count($org_cd_temp) > 0) {
            $sql .= " AND i_user_org.org_cd IN " . $this->_buildCommaString($org_cd_temp, true);
        }

        $sql .= " GROUP BY i_user.user_cd ORDER BY i_user.user_cd DESC ;";
        $query['c_start_date'] = $query['c_end_date'] = $query['iuc_start_date'] = $query['iuc_end_date']  = $date_system;
        $query['iuor_start_date']  = $query['or_start_date'] = $query['or_end_date'] = $date_system;
        $query['c_definition_name'] = $query['or_definition_name'] = 'ユーザ役職区分';

        return $this->_find($sql, $query);
    }



    public function getApprovalUserCurrent($user_cd, $date_system, $approval_work_type, $parameter_value)
    {
        $sql = "
            SELECT 
                hau.request_user_cd,
                DATE_FORMAT(hau.start_date,'%Y/%m/%d') as start_date,
                DATE_FORMAT(hau.end_date,'%Y/%m/%d') as end_date,
                (SELECT CONCAT('[', GROUP_CONCAT( JSON_OBJECT( 
                            'approval_layer_num', h_approval_user.approval_layer_num,
                            'approval_user_cd' , h_approval_user.approval_user_cd,
                            'user_name' , m_user.user_name,
                            'definition_label' ,m_variable_definition.definition_label,
                            'org_label' , m_org.org_label,
                            'org_cd' , m_org.org_cd
                        ) 
                    ), 
                ']' 
            ) FROM h_approval_user
              LEFT JOIN m_user
                    ON m_user.user_cd = h_approval_user.approval_user_cd
              LEFT JOIN m_user_org
                    ON (m_user_org.user_cd = h_approval_user.approval_user_cd AND m_user_org.main_flag = 1)
              LEFT JOIN m_org
                    ON m_org.org_cd = m_user_org.org_cd
              LEFT JOIN m_variable_definition
                    ON (m_variable_definition.definition_value = m_user_org.user_post_type AND m_variable_definition.definition_name = :definition_name)
              WHERE h_approval_user.request_user_cd = hau.request_user_cd
              AND h_approval_user.start_date = hau.start_date
              AND h_approval_user.end_date = hau.end_date
              AND h_approval_user.approval_work_type = hau.approval_work_type
              ORDER BY h_approval_user.approval_layer_num ASC
            )as approval_layer_num
            FROM h_approval_user hau
            WHERE hau.request_user_cd IN " . $this->_buildCommaString($user_cd, true) . "
            AND hau.start_date <= :h_start_date 
            AND hau.end_date >= :h_end_date
            AND hau.approval_work_type = :approval_work_type
            AND CAST(hau.approval_layer_num AS UNSIGNED)  <=  CAST(:parameter_value AS UNSIGNED) 
            GROUP BY hau.request_user_cd,hau.start_date,hau.end_date
            ORDER BY hau.request_user_cd DESC;";

        $query['h_start_date'] = $query['h_end_date'] = $date_system;
        $query['definition_name'] = 'ユーザ役職区分';
        $query['approval_work_type'] = $approval_work_type;
        $query['parameter_value'] = $parameter_value;

        return $this->_find($sql, $query ?? []);
    }

    public function getApprovalUserReservation($user_cd, $date_system, $approval_work_type, $parameter_value)
    {
        $sql = "
        SELECT
            hau.request_user_cd,
            DATE_FORMAT(hau.start_date,'%Y/%m/%d') as start_date,
            DATE_FORMAT(hau.end_date,'%Y/%m/%d') as end_date,
            (SELECT CONCAT('[', GROUP_CONCAT( JSON_OBJECT( 
                            'approval_layer_num', h_approval_user.approval_layer_num,
                            'approval_user_cd' , h_approval_user.approval_user_cd,
                            'user_name' , m_user.user_name,
                            'definition_label' ,m_variable_definition.definition_label,
                            'org_label' , m_org.org_label,
                            'org_cd' , m_org.org_cd
                        ) 
                    ), 
                ']' 
            ) FROM h_approval_user
              LEFT JOIN m_user
                    ON m_user.user_cd = h_approval_user.approval_user_cd
              LEFT JOIN m_user_org
                    ON (m_user_org.user_cd = h_approval_user.approval_user_cd AND m_user_org.main_flag = 1)
              LEFT JOIN m_org
                    ON m_org.org_cd = m_user_org.org_cd
              LEFT JOIN m_variable_definition
                    ON (m_variable_definition.definition_value = m_user_org.user_post_type AND m_variable_definition.definition_name = :definition_name)
              WHERE h_approval_user.request_user_cd = hau.request_user_cd
              AND h_approval_user.start_date = hau.start_date
              AND h_approval_user.end_date = hau.end_date
              AND h_approval_user.approval_work_type = hau.approval_work_type
              ORDER BY h_approval_user.approval_layer_num ASC
            ) as approval_layer_num
            FROM h_approval_user AS hau
            WHERE hau.request_user_cd IN " . $this->_buildCommaString($user_cd, true) . " 
            AND hau.start_date > :h_start_date
            AND hau.approval_work_type = :approval_work_type
            AND CAST(hau.approval_layer_num AS UNSIGNED)  <=  CAST(:parameter_value AS UNSIGNED) ";

        $sql .= "GROUP BY hau.request_user_cd,hau.start_date, hau.end_date ORDER BY hau.start_date ASC ;";

        $query['h_start_date'] = $date_system;
        $query['definition_name'] = 'ユーザ役職区分';
        $query['approval_work_type'] = $approval_work_type;
        $query['parameter_value'] = $parameter_value;

        return $this->_find($sql, $query);
    }

    public function deleteApprovalUser($user_cd, $approval_work_type, $start_date, $approval_layer_num, $approval_user_cd)
    {
        $query = [];
        $sql = "DELETE FROM h_approval_user WHERE request_user_cd = :request_user_cd AND approval_work_type = :approval_work_type ";

        if (!empty($start_date)) {
            $sql .= " AND start_date = :start_date  ";
            $query['start_date'] = $start_date;
        }

        if (!empty($approval_layer_num)) {
            $sql .= " AND approval_layer_num = :approval_layer_num ";
            $query['approval_layer_num'] = $approval_layer_num;
        }

        if (!empty($approval_user_cd)) {
            $sql .= " AND approval_user_cd = :approval_user_cd ";
            $query['approval_user_cd'] = $approval_user_cd;
        }

        $sql .= " ;";
        $query['request_user_cd'] = $user_cd;
        $query['approval_work_type'] = $approval_work_type;

        return $this->_destroy($sql, $query);
    }


    public function getApprovalUserUpdate($user_cd, $approval_work_type, $start_date, $type)
    {
        $sql = "
            SELECT 
                request_user_cd,approval_work_type,start_date,end_date
            FROM h_approval_user 
            WHERE request_user_cd = :request_user_cd 
            AND approval_work_type = :approval_work_type ";
        if ($type == 'DESC') {
            $sql .= " AND start_date < :start_date";
        } else {
            $sql .= " AND start_date > :start_date";
        }

        $sql .= " GROUP BY request_user_cd,approval_work_type,start_date ORDER BY start_date $type LIMIT 1;";

        return $this->_find($sql, ['request_user_cd' => $user_cd, 'approval_work_type' => $approval_work_type, 'start_date' => $start_date]);
    }

    public function insertApprovalUser($request_user_cd, $approval_work_type, $start_date, $end_date, $approval_layer_num, $approval_user_cd)
    {
        $sql = "
            INSERT INTO h_approval_user (request_user_cd,approval_work_type,approval_layer_num,approval_user_cd,start_date,end_date)
            VALUES (:request_user_cd,:approval_work_type,:approval_layer_num,:approval_user_cd,:start_date,:end_date);
        ";

        return $this->_create($sql, [
            'request_user_cd' => $request_user_cd,
            'approval_work_type' => $approval_work_type,
            'start_date' => $start_date,
            'approval_layer_num' => $approval_layer_num,
            'approval_user_cd' => $approval_user_cd,
            'end_date' => $end_date
        ]);
    }

    public function updateApprovalUserEndDate($user_cd, $approval_work_type, $start_date, $end_date)
    {
        $sql = "
            UPDATE 
                h_approval_user 
            SET
                end_date = :end_date
            WHERE request_user_cd = :request_user_cd
            AND approval_work_type = :approval_work_type
            AND start_date = :start_date
        ";

        return $this->_update($sql, [
            "request_user_cd" => $user_cd,
            "approval_work_type" => $approval_work_type,
            "start_date" => $start_date,
            "end_date" => $end_date
        ]);
    }

    public function getDateApprovalUser($user_cd, $approval_work_type, $date_system)
    {
        $sql = "
            SELECT 
                h_approval_user.start_date,h_approval_user.end_date
            FROM
                h_approval_user
            WHERE h_approval_user.request_user_cd = :user_cd
            AND h_approval_user.approval_work_type = :approval_work_type
            AND ((h_approval_user.start_date <= :start_date AND h_approval_user.end_date >= :end_date) OR h_approval_user.start_date > :ad_start_date)
            GROUP BY h_approval_user.start_date
            ORDER BY h_approval_user.start_date asc ;";

        return $this->_find($sql, ['user_cd' => $user_cd, 'start_date' => $date_system, 'end_date' => $date_system, 'ad_start_date' => $date_system, 'approval_work_type' => $approval_work_type]);
    }
}
