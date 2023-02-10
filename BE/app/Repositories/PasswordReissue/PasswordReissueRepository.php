<?php

namespace App\Repositories\PasswordReissue;

use App\Repositories\BaseRepository;
use App\Repositories\PasswordReissue\PasswordReissueRepositoryInterface;
use App\Traits\StringConvertTrait;

class PasswordReissueRepository extends BaseRepository implements PasswordReissueRepositoryInterface
{
    use StringConvertTrait;
    protected $useAutoMeta = true;
    
    public function getPasswordReissue($password_change,$user_cd,$user_name,$org_cd,$limit,$offset)
    {
        
        $sql = "
        SELECT 
            m_user.user_cd, m_user.user_name,
            m_variable_definition.definition_label,
            m_passphrase.require_change_flag ,
            m_org.org_label
        FROM m_user
        INNER JOIN m_user_org
            ON m_user.user_cd = m_user_org.user_cd
        INNER JOIN m_variable_definition
            ON (m_variable_definition.definition_value = m_user_org.user_post_type AND m_variable_definition.definition_name = :definition_name)
        INNER JOIN m_passphrase
            ON m_user.user_cd = m_passphrase.user_cd
        INNER JOIN m_org
            ON m_user_org.org_cd = m_org.org_cd
        WHERE 1 = 1 ";

        $query['definition_name'] = 'ユーザ役職区分';
        if($password_change == 1){
            $sql .= "AND m_passphrase.require_change_flag = :require_change_flag ";
            $query['require_change_flag'] = $password_change;
        }

        if(!empty($user_cd)){
            $sql .= "AND m_user.user_cd = :user_cd ";
            $query['user_cd'] = $user_cd;
        }

        if(!empty($user_name)){
            $sql .= "AND m_user.user_name LIKE :user_name ";
            $query['user_name'] = "%".trim($this->convert_kana($user_name))."%" ;
        }

        if(!empty($org_cd)){
            $org_cd = explode(',',$org_cd);
            $sql .= "AND m_user_org.org_cd IN ". $this->_buildCommaString($org_cd, true ) . " ";
        }
        
        $sql .= "GROUP BY m_user.user_cd ORDER BY m_org.org_cd ASC, m_user_org.user_post_type DESC , m_user.user_cd ASC;";

        return $this->_paginate($sql, $query, [
            "limit" => $limit,
            "offset" => $offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function updatePasswordReset($user_cd,$pass_word)
    {
        $sql = "UPDATE m_passphrase SET passphrase = :passphrase , require_change_flag = :require_change_flag WHERE user_cd = :user_cd ;";

        return $this->_update($sql,['passphrase' => $pass_word , 'require_change_flag' => 1 , 'user_cd' => $user_cd ]);
    }

    public function getContentEmail($parameter_key)
    {
        $sql = "SELECT parameter_name,parameter_key,parameter_value,remarks FROM c_system_parameter WHERE parameter_key = :parameter_key ;";
        return $this->_find($sql,['parameter_key' => $parameter_key]);
    }

    public function getInfoUser($user_cd)
    {
        $sql = "SELECT user_cd,user_name,mail_address FROM m_user WHERE user_cd = :user_cd;";
        return $this->_find($sql,['user_cd' => $user_cd]);
    }

    public function getInfoUserResult($user_cd)
    {
        $sql = "
        SELECT 
            m_user.user_cd, m_user.user_name,
            m_variable_definition.definition_label,
            m_passphrase.require_change_flag ,
            m_org.org_label
        FROM m_user
        INNER JOIN m_user_org
            ON m_user.user_cd = m_user_org.user_cd
        INNER JOIN m_variable_definition
            ON (m_variable_definition.definition_value = m_user_org.user_post_type AND m_variable_definition.definition_name = :definition_name)
        INNER JOIN m_passphrase
            ON m_user.user_cd = m_passphrase.user_cd
        INNER JOIN m_org
            ON m_user_org.org_cd = m_org.org_cd
        WHERE m_user.user_cd = :user_cd ;";

        $query['definition_name'] = 'ユーザ役職区分';
        $query['user_cd'] = $user_cd;

        return $this->_first($sql,$query);
    }
}
