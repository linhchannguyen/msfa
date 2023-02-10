<?php

namespace App\Repositories\PersonalDetailsWorkingInformation;

use App\Repositories\BaseRepository;
use App\Traits\DateTimeTrait;

class PersonalDetailsWorkingInformationRepository extends BaseRepository implements PersonalDetailsWorkingInformationRepositoryInterface
{

    public function getPhaseName($date_system)
    {
       $sql = "SELECT phase_cd,phase_name FROM m_phase WHERE :available_start_date BETWEEN available_start_date AND available_end_date ORDER BY phase_cd ASC ;";

       return $this->_find($sql,['available_start_date' => $date_system]);
    }

    public function getWorkingInformation($person_cd,$limit,$offset)
    {
        $sql = "
            SELECT 
                m_facility.facility_short_name,
                (SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 
                                'segment_type', t_facility_person_segment.segment_type,
                                'segment_name' , m_facility_person_segment_type.segment_name
                            ) 
                        ), 
                    ']' 
                ) FROM t_facility_person_segment
                  INNER JOIN m_facility_person_segment_type
                        ON m_facility_person_segment_type.segment_type = t_facility_person_segment.segment_type
                  WHERE t_facility_person_segment.facility_cd = m_facility_person.facility_cd 
                  AND m_facility_person.person_cd = t_facility_person_segment.person_cd
                ) as segment,
                m_user.user_name,
                m_facility_person.facility_cd,
                m_facility_user.user_cd,
                m_user_org.org_cd,
                m_org.org_label,
               (SELECT  CONCAT( '[', GROUP_CONCAT( JSON_OBJECT(
                                      'phase_cd' , t_phase.phase_cd,
                                      'product_cd' , m_product.product_cd,
                                      'product_name' , m_product.product_name
                          ) 
                      ), 
                  ']' 
              ) FROM t_phase
                INNER JOIN m_product
                      ON m_product.product_cd = t_phase.product_cd
                WHERE t_phase.facility_cd = m_facility_person.facility_cd 
                AND t_phase.person_cd = m_facility_person.person_cd
                ORDER BY m_product.product_cd ASC
              ) as item
            FROM m_facility_person
            INNER JOIN m_facility
                 ON m_facility_person.facility_cd = m_facility.facility_cd
            INNER JOIN m_facility_user
                 ON (m_facility_user.facility_cd = m_facility_person.facility_cd AND m_facility_user.main_flg = 1)
            INNER JOIN m_user_org
                 ON (m_user_org.user_cd = m_facility_user.user_cd AND m_user_org.main_flag = 1)
            INNER JOIN m_org
                 ON m_org.org_cd = m_user_org.org_cd
            INNER JOIN m_user
                 ON m_user.user_cd = m_user_org.user_cd
            WHERE m_facility_person.person_cd = :person_cd
            GROUP BY m_facility_person.facility_cd,m_user.user_cd,m_facility_person.facility_cd
            ORDER BY m_facility.facility_short_name_kana ASC;";

            $query['person_cd'] = $person_cd;

            return $this->_paginate($sql, $query, [
               "limit" => $limit,
               "offset" => $offset,
               "key" => "*",
               "key_type" => "normal"
           ]);
    }

    
}