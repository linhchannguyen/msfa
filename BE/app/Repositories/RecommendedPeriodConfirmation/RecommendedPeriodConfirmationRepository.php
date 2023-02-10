<?php

namespace App\Repositories\RecommendedPeriodConfirmation;

use App\Repositories\BaseRepository;
use App\Traits\StringConvertTrait;

class RecommendedPeriodConfirmationRepository extends BaseRepository implements RecommendedPeriodConfirmationRepositoryInterface
{
    use StringConvertTrait;
    public function getDataUser($user_cd, $user_name, $org_cd, $date, $limit, $offset)
    {
        $query = [];
        $sql = "
            SELECT 
            m_user.user_cd,
            m_user.mail_address,
            m_user.user_name,
            (SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 
                             'definition_value', m_variable_definition.definition_value,
                             'definition_label' , m_variable_definition.definition_label,
                             'user_post_type' , m_user_org.user_post_type,
                             'org_label' , m_org.org_label,
                             'main_flag' , case  when m_user_org.main_flag = 1 then '主' else '副' end,
                             'main_flag_as' , m_user_org.main_flag
                         ) 
                     ), 
                 ']' 
             ) FROM m_user_org
               INNER JOIN m_org
                    ON m_org.org_cd = m_user_org.org_cd
               INNER JOIN m_variable_definition
                    ON (m_variable_definition.definition_value = m_user_org.user_post_type AND m_variable_definition.definition_name = 'ユーザ役職区分')
               WHERE m_user_org.user_cd = m_user.user_cd
               AND m_user_org.org_cd IN (SELECT muo_sub.org_cd FROM m_user_org muo_sub WHERE muo_sub.user_cd = muo.user_cd) ";
            $sql .= ") as advance_reservation
            FROM m_user 
            INNER JOIN m_user_org as muo
                ON m_user.user_cd = muo.user_cd
            INNER JOIN m_org
                ON m_org.org_cd = muo.org_cd
            INNER JOIN m_variable_definition
                ON (m_variable_definition.definition_value = muo.user_post_type AND m_variable_definition.definition_name = 'ユーザ役職区分')
            WHERE 1 = 1 ";
            
            if(!empty($user_cd)){
                $sql .= " AND m_user.user_cd = :user_cd";
                $query['user_cd'] = $user_cd;
            }

            if(!empty($user_name)){
                $sql .= " AND m_user.user_name LIKE :user_name";
                $query['user_name'] =  "%".trim($this->convert_kana($user_name))."%";
            }

            if(!empty($org_cd)){
                $sql .= " AND muo.org_cd IN ". $this->_buildCommaString($org_cd,true);
            }

            $sql .= " GROUP BY m_user.user_cd
            ORDER BY m_org.sort_order ASC , muo.user_post_type DESC , m_user.user_cd ASC;";

        return $this->_paginate($sql, $query, [
            "limit" => $limit,
            "offset" => $offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getUserInformation($user_cd_list, $user_name, $org_cd, $date)
    {
        $sql = "
            SELECT 
                i_user.user_cd,
                CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 
                            'user_name', i_user.user_name,
                            'mail_address' , i_user.mail_address,
                            'start_date' , i_user.start_date,
                            'end_date' , i_user.end_date
                        ) 
                    ), 
               ']' ) as user_information
            FROM i_user 
            WHERE user_cd IN ". $this->_buildCommaString($user_cd_list,true);

            if(!empty($date)){
                $sql.= " AND i_user.start_date <= :start_date AND i_user.end_date >= :end_date ";
                $query['start_date'] = $query['end_date'] = $date;
            }
        $sql .= " GROUP BY  i_user.user_cd ORDER BY i_user.user_cd ASC , i_user.start_date ASC;";

        return $this->_find($sql,$query);
    }

    public function getOrganizationInformation($user_cd_list,$org_cd, $date)
    {
        $query = [];
        $sql = "
            SELECT 
                iuo.user_cd,
                DATE_FORMAT(iuo.start_date,'%Y/%m/%d') as start_date,
                DATE_FORMAT(iuo.end_date,'%Y/%m/%d') as end_date,
                (SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 
                                'org_cd', i_user_org.org_cd,
                                'definition_label' , m_variable_definition.definition_label,
                                'org_label' , i_org.org_label,
                                'main_flag' , case  when  i_user_org.main_flag = 1 then '主' else '副' end,
                                'main_flag_as' , i_user_org.main_flag = 1,
                                'user_post_type' , i_user_org.user_post_type
                            ) 
                        ), 
                    ']' 
                ) FROM i_user_org
                  INNER JOIN m_variable_definition
                     ON (m_variable_definition.definition_value = i_user_org.user_post_type AND m_variable_definition.definition_name = 'ユーザ役職区分')
                  INNER JOIN i_org
                     ON (i_org.org_cd = i_user_org.org_cd AND i_org.start_date <= i_user_org.start_date  AND i_org.end_date >= i_user_org.start_date)
                  WHERE i_user_org.user_cd = iuo.user_cd
                  AND i_user_org.start_date = iuo.start_date
                  AND i_user_org.end_date = iuo.end_date
                ) as organization
            FROM i_user_org AS iuo
            WHERE iuo.user_cd IN ". $this->_buildCommaString($user_cd_list,true);

            if(!empty($org_cd)){
                $sql .= " AND iuo.org_cd IN ". $this->_buildCommaString($org_cd,true);
            }

            if(!empty($date)){
                $sql.= " AND iuo.start_date <= :start_date AND iuo.end_date >= :end_date ";
                $query['start_date'] = $query['end_date'] = $date;
            }

            $sql .= " GROUP BY iuo.user_cd,iuo.start_date,iuo.end_date
            ORDER BY iuo.user_cd ASC,iuo.start_date ASC ;";

        return $this->_find($sql,$query);
    }

    public function getRoleInformation($user_cd_list,$date)
    {
        $query = [];
        $sql = "
            SELECT 
                iur.user_cd,
                DATE_FORMAT(iur.start_date,'%Y/%m/%d') as start_date,
                DATE_FORMAT(iur.end_date,'%Y/%m/%d') as end_date,
                (SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 
                                    'role', i_user_role.role,
                                    'role_name' , c_role.role_name
                                ) 
                            ), 
                        ']' 
                    ) FROM i_user_role
                      INNER JOIN c_role
                         ON c_role.role = i_user_role.role
                      WHERE i_user_role.user_cd = iur.user_cd
                      AND i_user_role.start_date = iur.start_date
                      AND i_user_role.end_date = iur.end_date
                    ) as role
            FROM i_user_role as iur
            WHERE iur.user_cd IN ". $this->_buildCommaString($user_cd_list,true);
        
        if(!empty($date)){
            $sql.= " AND iur.start_date <= :start_date AND iur.end_date >= :end_date ";
            $query['start_date'] = $query['end_date'] = $date;
        }

        $sql .= " GROUP BY iur.user_cd,iur.start_date,iur.end_date
            ORDER BY iur.user_cd ASC,iur.start_date ASC ;";

        return $this->_find($sql,$query);
    }

    public function getApproval($user_cd_list,$date,$approval_type)
    {
        $query = [];
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
                                'org_label' , i_org.org_label,
                                'org_cd' , i_user_org.org_cd
                            ) 
                        ), 
                    ']' 
                ) FROM h_approval_user
                  INNER JOIN m_user
                        ON m_user.user_cd = h_approval_user.approval_user_cd
                  INNER JOIN i_user_org
                        ON (i_user_org.user_cd = h_approval_user.approval_user_cd AND i_user_org.main_flag = 1 AND i_user_org.start_date <= :sub_start_date AND  i_user_org.end_date >= :sub_end_date)
                  INNER JOIN i_org
                        ON i_org.org_cd = i_user_org.org_cd
                  INNER JOIN m_variable_definition
                        ON (m_variable_definition.definition_name = :definition_name_temp AND m_variable_definition.definition_value = i_user_org.user_post_type)
                  WHERE h_approval_user.request_user_cd =  hau.request_user_cd
                  AND h_approval_user.start_date <= hau.start_date
                  AND h_approval_user.end_date >= hau.end_date
                  AND h_approval_user.approval_work_type = hau.approval_work_type
                  ORDER BY h_approval_user.approval_layer_num ASC
                ) as approval_layer_num
            FROM h_approval_user hau
            INNER JOIN m_variable_definition
                 ON ( hau.approval_work_type = m_variable_definition.definition_value AND m_variable_definition.definition_name = :definition_name AND m_variable_definition.definition_label = :definition_label)
            WHERE hau.request_user_cd IN " . $this->_buildCommaString($user_cd_list, true);

            $query['sub_start_date'] = $query['sub_end_date'] = $date;
            if(!empty($date)){
                $sql.= " AND hau.start_date <= :start_date AND hau.end_date >= :end_date ";
                $query['start_date'] = $query['end_date'] = $date;
            }

            $sql .= " GROUP BY hau.request_user_cd,hau.start_date,hau.end_date
            ORDER BY hau.request_user_cd ASC;";

        $query['definition_name'] = '承認業務区分';
        $query['definition_label'] = $approval_type;
        $query['definition_name_temp'] = 'ユーザ役職区分';

        return $this->_find($sql,$query);
    }
}
