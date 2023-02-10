<?php

namespace App\Repositories\AccountSetting;

use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use App\Repositories\AccountSetting\AccountSettingRepositoryInterface;

class AccountSettingRepository extends BaseRepository implements AccountSettingRepositoryInterface
{
    protected $useAutoMeta = true;

    public function getUserInfo($userCd)
    {
        $sql = "
            SELECT 
                t1.user_name, 
                t1.mail_address, 
                t2.note_1, 
                t2.note_2, 
                t2.note_3, 
                t2.note_4, 
                t2.note_5,
                t3.file_id, 
                t3.file_name AS avatar_file_name,
                t3.file_url AS avatar_file_url,
                t3.mime_type AS avatar_image_type, 
                CASE WHEN t4.user_cd IS NULL THEN 0 ELSE SUM(t4.active_point) END total_points
            FROM 
                m_user AS t1 
            LEFT JOIN 
                t_user_profile AS t2 
            ON 
                t1.user_cd = t2.user_cd 
            LEFT JOIN 
                t_file AS t3 
            ON 
                t2.profile_image_file_id = t3.file_id 
            LEFT JOIN 
                s_active_point AS t4 
            ON 
                t4.user_cd = t1.user_cd 
            LEFT JOIN (select * from m_variable_definition where definition_name = '活用度ポイント対象区分') t5 on t4.point_target_type= t5.definition_value
            WHERE 
                t1.user_cd = :user_cd
            GROUP BY t1.user_cd;
        ";

        return $this->_first($sql, ['user_cd' => $userCd]);
    }

    public function getAccountPoint($userCd, $pointTargetType, $limit, $offset)
    {
        $condistions = [];
        if (!empty($pointTargetType)) {
            $sub_where = "AND A.point_target_type = :sub_point_target_type";
            $where = "AND T1.point_target_type = :point_target_type";
            $condistions['sub_point_target_type'] = $pointTargetType;
            $condistions['point_target_type'] = $pointTargetType;
        } else {
            $sub_where = '';
            $where = '';
        }

        $sql = "
            SELECT 
                (
                    SELECT 
                        SUM(A.active_point)
                    FROM 
                        s_active_point A
                        JOIN (select * from m_variable_definition where definition_name = '活用度ポイント対象区分') B on A.point_target_type= B.definition_value
                    WHERE 
                        A.user_cd = :user_cd
                        $sub_where
                ) AS total_points,
                T1.point_id,
                T2.definition_label as point_target_type_title,
                T1.point_target_type,
                T1.point_target_id,
                T1.active_point,
                T1.message,
                T1.created_at 
            FROM 
                t_active_point T1
                JOIN (select definition_value,definition_label from m_variable_definition where definition_name = '活用度ポイント対象区分') T2 on T1.point_target_type= T2.definition_value
            WHERE 
                T1.receive_user_cd = :receive_user_cd
                $where 
            ORDER BY 
                T1.created_at DESC;
        ";

        $condistions['user_cd'] = $userCd;
        $condistions['receive_user_cd'] = $userCd;

        return $this->_paginate($sql, $condistions, [
            "limit" => $limit,
            "offset" => $offset,
            "key" => "point_list",
            "key_type" => "json"
        ]);
    }

    public function getPointTargetType()
    {
        $sql = "select definition_value as value, definition_label as description  from m_variable_definition where definition_name = '活用度ポイント対象区分'";
        return $this->_all($sql);
    }
    public function changeAccountInfo($userCd, $data)
    {
        $find = "SELECT user_cd FROM t_user_profile WHERE user_cd = :user_cd";
        $result = $this->_first($find, ['user_cd' => $userCd]);
        if ($result->user_cd ?? '') {
            $sql = "UPDATE t_user_profile
                        SET 
                        note_1 = :note_1,
                        note_2 = :note_2,
                        note_3 = :note_3,
                        note_4 = :note_4,
                        note_5 = :note_5,
                        profile_image_file_id = :profile_image_file_id
                    WHERE user_cd = :user_cd;";
            return $this->_update($sql, [
                "user_cd" => $userCd,
                "profile_image_file_id" => $data['profile_image_file_id'],
                "note_1" => $data['note_1'],
                "note_2" => $data['note_2'],
                "note_3" => $data['note_3'],
                "note_4" => $data['note_4'],
                "note_5" => $data['note_5']
            ]);
        } else {
            $sql = "INSERT INTO t_user_profile (user_cd, note_1, note_2, note_3, note_4, note_5, profile_image_file_id)
                VALUES (:user_cd, :note_1, :note_2, :note_3, :note_4, :note_5, :profile_image_file_id);";

            return $this->_create($sql, [
                "user_cd" => $userCd,
                "profile_image_file_id" => $data['profile_image_file_id'],
                "note_1" => $data['note_1'],
                "note_2" => $data['note_2'],
                "note_3" => $data['note_3'],
                "note_4" => $data['note_4'],
                "note_5" => $data['note_5']
            ]);
        }
    }

    public function updateAvatar($userCd, $avatarId)
    {
        $find = "SELECT user_cd FROM t_user_profile WHERE user_cd = :user_cd";
        $result = $this->_first($find, ['user_cd' => $userCd]);
        if ($result->user_cd ?? '') {
            $sql = "UPDATE t_user_profile
                        SET profile_image_file_id = :profile_image_file_id
                    WHERE user_cd = :user_cd;";
            return $this->_update($sql, [
                "profile_image_file_id" => $avatarId,
                "user_cd" => $userCd,
            ]);
        } else {
            $sql = "INSERT INTO t_user_profile (user_cd, profile_image_file_id)
                VALUES (:user_cd, :profile_image_file_id);";
            return $this->_create($sql, [
                "profile_image_file_id" => $avatarId,
                "user_cd" => $userCd
            ]);
        }
    }

    public function findAccount($userCd)
    {
        $sql = "
            SELECT 
                * 
            FROM 
                t_user_profile 
            WHERE 
                user_cd = :user_cd;
        ";

        return $this->_first($sql, [
            "user_cd" => $userCd
        ]);
    }

    public function findOrg($userCd)
    {
        $sql = "SELECT T3.org_cd,
        T3.org_label,
        T3.layer_1,
        T3.layer_2,
        T3.layer_3,
        T3.layer_4,
        T3.layer_5,
        T5.definition_label as definition
        FROM m_user T1 
        INNER JOIN m_user_org T2 ON T1.user_cd = T2.user_cd 
        INNER JOIN m_org T3 ON T3.org_cd = T2.org_cd
        LEFT JOIN t_user_profile T4 ON T1.user_cd = T4.user_cd 
        LEFT JOIN m_variable_definition T5 ON T5.definition_value = T2.user_post_type 
        WHERE T2.user_cd = :user_cd and T5.definition_name = 'ユーザ役職区分';
        ";
        return $this->_find($sql, ["user_cd" => $userCd]);
    }
}
