<?php

namespace App\Repositories\TargetFacilityManagement;

use App\Repositories\BaseRepository;
use App\Repositories\TargetFacilityManagement\TargetFacilityManagementRepositoryInterface;

class TargetFacilityManagementRepository extends BaseRepository implements TargetFacilityManagementRepositoryInterface
{
    protected $useAutoMeta = true;

    public function getScreenData($date_system)
    {
        $sql = "SELECT target_product_cd,target_product_name FROM m_target_product WHERE end_date >= :date_system AND delete_flag != 1 ORDER BY sort_order ASC;";
        return $this->_find($sql, ['date_system' => $date_system]);
    }

    public function getFacility($user_cd, $order_value, $limit, $offset)
    {
        $query = [];
        $sql = "SELECT 
            m_facility.facility_cd,
            m_facility.facility_name,
            m_facility.facility_short_name ";
        if (!empty($order_value)) {
            $sql .= " ,(SELECT COUNT(*) FROM t_target_facility_person_selection WHERE t_target_facility_person_selection.facility_cd = m_facility_user.facility_cd 
            AND t_target_facility_person_selection.target_product_cd = :target_product_cd) AS order_by_column ";
            $query['target_product_cd'] = $order_value;
        }

        $sql .= "FROM m_facility_user
            INNER JOIN m_facility ON m_facility.facility_cd = m_facility_user.facility_cd
            LEFT JOIN m_target_facility ON m_target_facility.facility_cd = m_facility.facility_cd
            LEFT JOIN m_target_product ON m_target_product.target_product_cd = m_target_facility.target_product_cd
            WHERE m_facility_user.user_cd = :user_cd
            GROUP BY m_facility_user.facility_cd
            ORDER BY ";

        if (!empty($order_value)) {
            $sql .= " order_by_column DESC , ";
        }

        $sql .= " m_facility.facility_category_type ASC , m_facility.facility_name_kana ASC;";

        $query['user_cd'] = $user_cd;

        return $this->_paginate($sql, $query, [
            "limit" => $limit,
            "offset" => $offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getTargetProduct($facility_cd, $order_value, $target_product_cd)
    {
        $sql = "
            SELECT
            t_target_facility_person_selection.facility_cd,
            t_target_facility_person_selection.target_product_cd , 
            i_target_facility_selection.sub_target,
            CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 
                                            'person_cd', t_target_facility_person_selection.person_cd
                                        ) 
                                    ), 
                                ']' 
                            ) as person_cd 
            FROM t_target_facility_person_selection 
            LEFT JOIN i_target_facility_selection
                ON (i_target_facility_selection.facility_cd = t_target_facility_person_selection.facility_cd AND i_target_facility_selection.target_product_cd = t_target_facility_person_selection.target_product_cd)
            WHERE t_target_facility_person_selection.facility_cd IN " . $this->_buildCommaString($facility_cd, true);
        if (count($target_product_cd) > 0) {
            $sql .= " AND t_target_facility_person_selection.target_product_cd IN " . $this->_buildCommaString($target_product_cd, true);
        }
        $sql .= " GROUP BY t_target_facility_person_selection.facility_cd,t_target_facility_person_selection.target_product_cd;";

        return $this->_find($sql, []);
    }
}
