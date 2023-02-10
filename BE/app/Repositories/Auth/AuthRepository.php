<?php

namespace App\Repositories\Auth;

use App\Repositories\BaseRepository;
use App\Repositories\Auth\AuthRepositoryInterface;
use Carbon\Carbon as time;

class AuthRepository extends BaseRepository implements AuthRepositoryInterface
{
    public function login($user_cd)
    {
        $sql = "
        SELECT  m_user.user_cd,
                m_user.user_name,
                m_user.mail_address,
                m_passphrase.passphrase as password,
                m_passphrase.require_change_flag,
                t_file.file_url as avatar_image_data,
                t_file.mime_type as avatar_image_type,
                m_user_org.org_cd,
                m_org.org_short_name,
                m_org.org_label,
                m_org.layer_num
        FROM m_user
        LEFT JOIN m_passphrase
            ON m_user.user_cd = m_passphrase.user_cd
        LEFT JOIN t_user_profile
            ON m_user.user_cd = t_user_profile.user_cd
        LEFT JOIN t_file
            ON t_file.file_id = t_user_profile.profile_image_file_id
        LEFT JOIN m_user_org
            ON (m_user.user_cd = m_user_org.user_cd AND m_user_org.main_flag = :main_flag)
        LEFT JOIN m_org
            ON m_org.org_cd = m_user_org.org_cd
        WHERE m_user.user_cd = :user_cd;";

        return $this->_first($sql, ['user_cd' => $user_cd , 'main_flag' => 1]);
    }

    public function changePassword($user_cd,$new_password)
    {
        $sql = "
            UPDATE
                m_passphrase
            SET
                require_change_flag = :require_change_flag
                ,passphrase = :passphrase
            WHERE user_cd = :user_cd ;";

        $this->_update($sql,['passphrase' => $new_password,'user_cd' => $user_cd, 'require_change_flag' => 0]);
    }

    public function getInfoUser($user_cd)
    {
        $sql = "
        SELECT  m_user.user_cd,
                m_user.user_name,
                m_user.mail_address,
                m_passphrase.require_change_flag,
                t_file.file_url as avatar_image_data,
                t_file.mime_type as avatar_image_type,
                m_user_org.org_cd,
                m_org.org_short_name,
                m_org.org_label,
                m_org.layer_num,
                (SELECT definition_label FROM m_variable_definition WHERE definition_name = 'ユーザ役職区分' AND definition_value = m_user_org.user_post_type LIMIT 1 ) as implement_user_post_name,
                m_user_org.user_post_type AS implement_user_post
        FROM m_user
        LEFT JOIN m_passphrase
            ON m_user.user_cd = m_passphrase.user_cd
        LEFT JOIN t_user_profile
            ON m_user.user_cd = t_user_profile.user_cd
        LEFT JOIN t_file
            ON t_file.file_id = t_user_profile.profile_image_file_id
        LEFT JOIN m_user_org
            ON (m_user.user_cd = m_user_org.user_cd AND m_user_org.main_flag = :main_flag)
        LEFT JOIN m_org
            ON m_org.org_cd = m_user_org.org_cd
        WHERE m_user.user_cd = :user_cd;";

        return $this->_first($sql, ['user_cd' => $user_cd , 'main_flag' => 1]);
    }

    public function getInfoUserLogin($user_cd)
    {
        $sql = "
        SELECT  
            m_user.user_cd, m_user_org.org_cd
        FROM m_user
        LEFT JOIN m_user_org
            ON (m_user.user_cd = m_user_org.user_cd AND m_user_org.main_flag = :main_flag)
        WHERE m_user.user_cd = :user_cd;";

        return $this->_find($sql, ['user_cd' => $user_cd , 'main_flag' => 1]);
    }

    public function findOrg($userCd)
    {
        $sql = "SELECT org_cd FROM m_user_org WHERE user_cd = :user_cd";
        return $this->_find($sql, ["user_cd" => $userCd]);
    }
}
