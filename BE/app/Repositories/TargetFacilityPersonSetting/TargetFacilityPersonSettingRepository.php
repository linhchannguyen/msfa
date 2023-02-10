<?php

namespace App\Repositories\TargetFacilityPersonSetting;

use App\Repositories\BaseRepository;
use App\Repositories\TargetFacilityPersonSetting\TargetFacilityPersonSettingRepositoryInterface;
use App\Traits\StringConvertTrait;

class TargetFacilityPersonSettingRepository extends BaseRepository implements TargetFacilityPersonSettingRepositoryInterface
{
    use StringConvertTrait;
    protected $useAutoMeta = true;

    public function getTargetProduct($date_system)
    {
        $sql = "SELECT target_product_cd,target_product_name,start_date,end_date,selection_start_date,selection_end_date FROM m_target_product WHERE end_date >= :date_system AND delete_flag != 1 ORDER BY sort_order ASC;";
        return $this->_find($sql, ['date_system' => $date_system]);
    }

    public function getSegmentType($date_system)
    {
        $sql = "SELECT segment_type,segment_name,start_date,end_date,uneditable_flag FROM m_facility_person_segment_type WHERE end_date >= :date_system ORDER BY sort_order ASC;";
        return $this->_find($sql, ['date_system' => $date_system]);
    }

    public function getFacilityTypeGroup()
    {
        $sql = "SELECT facility_type_group_cd,facility_type_group_name FROM m_facility_type_group;";
        return $this->_find($sql, []);
    }

    public function parameterAddStock()
    {
        $sql = "SELECT definition_value FROM m_variable_definition WHERE definition_name = :definition_name AND definition_value = :definition_value;";
        return $this->_first($sql, ['definition_name' => DEFINITION_NAME_STOCK_SOURCE_CLASSIFICATION, 'definition_value' => DEFINITION_VALUE_30]);
    }

    public function getFacilityPerson($user_cd, $facility_type_group_cd, $person_name, $target_product_cd, $segment_type, $order_segment, $order_target_product, $limit, $offset)
    {
        $query = [];
        $sql = "
            SELECT 
                m_facility_person.facility_cd,
                m_facility.facility_name,
                m_facility.facility_short_name,
                m_person.person_cd,
                m_person.person_name,
                m_department.department_name,
                m_department.department_cd,
                (SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 
                                'target_product_cd', i_target_facility_selection.target_product_cd  ,
                                'sub_target' , i_target_facility_selection.sub_target) ), ']' ) 
                FROM i_target_facility_selection
                WHERE i_target_facility_selection.facility_cd = m_facility_person.facility_cd
                ) as sub_target, ";

        if (is_object($order_segment) && !empty($order_segment->order_type) && ($order_segment->order_type ?? '') == 'segment') {
            $sql .= "  (SELECT COUNT(t_facility_person_segment.person_cd) FROM t_facility_person_segment 
                        WHERE t_facility_person_segment.facility_cd = m_facility_person.facility_cd 
                        AND t_facility_person_segment.person_cd = m_facility_person.person_cd 
                        AND t_facility_person_segment.segment_type = :segment_type_person) AS order_by_segment_person , ";
            $query['segment_type_person'] = $order_segment->order_value;
        }

        if (is_object($order_target_product) && !empty($order_target_product->order_type) && ($order_target_product->order_type ?? '') == 'target_product') {

            $sql .= " (SELECT COUNT(t_target_facility_person_selection.person_cd) FROM t_target_facility_person_selection WHERE t_target_facility_person_selection.facility_cd = m_facility_person.facility_cd
                                    AND t_target_facility_person_selection.person_cd = m_facility_person.person_cd 
                                    AND t_target_facility_person_selection.target_product_cd = :target_product_cd_order_person) AS order_by_target_product_cd_person , ";
            $query['target_product_cd_order_person'] = $order_target_product->order_value;
        }

        $sql .= " m_position.position_name as display_position_name
            FROM m_facility_person
            INNER JOIN m_facility
                ON m_facility_person.facility_cd = m_facility.facility_cd
            LEFT JOIN m_position
                ON m_position.position_cd = m_facility_person.display_position_cd
            INNER JOIN m_person
                ON m_person.person_cd = m_facility_person.person_cd
            LEFT JOIN t_target_facility_person_selection
                ON (t_target_facility_person_selection.facility_cd = m_facility_person.facility_cd AND t_target_facility_person_selection.person_cd = m_facility_person.person_cd)
            INNER JOIN m_department
                ON m_department.department_cd = m_facility_person.department_cd
            LEFT JOIN t_facility_person_segment
                ON (t_facility_person_segment.facility_cd = m_facility_person.facility_cd AND t_facility_person_segment.person_cd = m_facility_person.person_cd)
                ";
        if (!empty($user_cd)) {
            $sql .= " INNER JOIN m_facility_user
                ON (m_facility_user.facility_cd = m_facility_person.facility_cd AND m_facility_user.user_cd = :user_cd)";
            $query['user_cd'] = $user_cd;
        }

        if (!empty($facility_type_group_cd)) {
            $sql .= " INNER JOIN m_facility_type
                        ON m_facility.facility_type = m_facility_type.facility_type
                      INNER JOIN m_facility_type_group
                        ON (m_facility_type_group.facility_type_group_cd = m_facility_type.facility_type_group AND m_facility_type_group.facility_type_group_cd = :facility_type_group_cd )";
            $query['facility_type_group_cd'] = $facility_type_group_cd;
        }


        $sql .= " WHERE 1 = 1";

        if (!empty($person_name)) {
            $sql .= " AND m_person.person_name LIKE :person_name";
            $query['person_name'] = '%' . trim($this->convert_kana($person_name)) . '%';
        }

        if (!empty($target_product_cd)) {
            $sql .= " AND t_target_facility_person_selection.target_product_cd = :target_product_cd ";
            $query['target_product_cd'] = $target_product_cd;
        }

        if (!empty($segment_type)) {
            $segment_type = explode(',', $segment_type);
            $sql .= " AND t_facility_person_segment.segment_type IN " . $this->_buildCommaString($segment_type, true);
        }

        $sql .= "  GROUP BY m_facility_person.facility_cd , m_facility_person.person_cd ORDER BY ";

        if (is_object($order_segment) && !empty($order_segment->order_type) && ($order_segment->order_type ?? '') == 'segment') {
            $sql .= " order_by_segment_person DESC , ";
        }

        if (is_object($order_target_product) && !empty($order_target_product->order_type) && ($order_target_product->order_type ?? '') == 'target_product') {
            $sql .= " order_by_target_product_cd_person DESC ,";
        }

        $sql .= " m_facility.facility_category_type ASC , m_facility.facility_name_kana ASC , m_facility_person.department_cd ASC , m_facility_person.display_position_cd ASC , m_person.person_name_kana ASC;";

        return $this->_paginate($sql, $query, [
            "limit" => $limit,
            "offset" => $offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function exportFacilityPerson($user_cd, $facility_type_group_cd, $person_name, $target_product_cd, $segment_type, $date_system , $order_segment , $order_target_product )
    {
        $query = [];
        $sql = "
            SELECT 
                m_facility_person.facility_cd,
                m_facility.facility_name,
                m_person.person_cd,
                m_person.person_name,
                m_department.department_name,
                m_department.department_cd,
                i_target_facility_selection.sub_target,
                m_facility_person.display_position_cd,
                (SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 
                                'target_product_cd', i_target_facility_selection.target_product_cd  ,
                                'sub_target' , i_target_facility_selection.sub_target) ), ']' ) 
                FROM i_target_facility_selection
                WHERE i_target_facility_selection.facility_cd = m_facility_person.facility_cd
                ) as sub_target,
                (SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 'segment_type', t_facility_person_segment.segment_type ) ), ']' ) FROM t_facility_person_segment WHERE t_facility_person_segment.facility_cd = m_facility_person.facility_cd AND t_facility_person_segment.person_cd = m_facility_person.person_cd )as segment_list,
                (SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 'target_product_cd', t_target_facility_person_selection.target_product_cd) ), ']' ) FROM  t_target_facility_person_selection WHERE t_target_facility_person_selection.facility_cd = m_facility_person.facility_cd AND t_target_facility_person_selection.person_cd = m_facility_person.person_cd )as target_product_list,";

                if (is_object($order_segment) && !empty($order_segment->order_type) && ($order_segment->order_type ?? '') == 'segment') {
        
                    $sql .= "  (SELECT COUNT(t_facility_person_segment.person_cd) FROM t_facility_person_segment 
                                WHERE t_facility_person_segment.facility_cd = m_facility_person.facility_cd 
                                AND t_facility_person_segment.person_cd = m_facility_person.person_cd 
                                AND t_facility_person_segment.segment_type = :segment_type_person) AS order_by_segment_person , ";
                    $query['segment_type_person'] = $order_segment->order_value;
                }
        
                if (is_object($order_target_product) && !empty($order_target_product->order_type) && ($order_target_product->order_type ?? '') == 'target_product') {

                    $sql .= " (SELECT COUNT(t_target_facility_person_selection.person_cd) FROM t_target_facility_person_selection WHERE t_target_facility_person_selection.facility_cd = m_facility_person.facility_cd
                                            AND t_target_facility_person_selection.person_cd = m_facility_person.person_cd 
                                            AND t_target_facility_person_selection.target_product_cd = :target_product_cd_order_person) AS order_by_target_product_cd_person , ";
                    $query['target_product_cd_order_person'] = $order_target_product->order_value;
                }

            $sql .= "  m_position.position_name as display_position_name
            FROM m_facility_person
            INNER JOIN m_facility
                ON m_facility_person.facility_cd = m_facility.facility_cd
            LEFT JOIN i_target_facility_selection
                ON i_target_facility_selection.facility_cd = m_facility_person.facility_cd
            LEFT JOIN m_position
                ON m_position.position_cd = m_facility_person.display_position_cd
            INNER JOIN m_person
                ON m_person.person_cd = m_facility_person.person_cd
            INNER JOIN m_department
                ON m_department.department_cd = m_facility_person.department_cd
            LEFT JOIN t_target_facility_person_selection
                ON (t_target_facility_person_selection.facility_cd = m_facility_person.facility_cd AND t_target_facility_person_selection.person_cd = m_facility_person.person_cd)
            LEFT JOIN t_facility_person_segment
                ON (t_facility_person_segment.facility_cd = m_facility_person.facility_cd AND t_facility_person_segment.person_cd = m_facility_person.person_cd)
                ";
        if (!empty($user_cd)) {
            $sql .= " INNER JOIN m_facility_user
                ON (m_facility_user.facility_cd = m_facility_person.facility_cd AND m_facility_user.user_cd = :user_cd)";
            $query['user_cd'] = $user_cd;
        }

        if (!empty($facility_type_group_cd)) {
            $sql .= " INNER JOIN m_facility_type
                        ON m_facility.facility_type = m_facility_type.facility_type
                      INNER JOIN m_facility_type_group
                        ON (m_facility_type_group.facility_type_group_cd = m_facility_type.facility_type_group AND m_facility_type_group.facility_type_group_cd = :facility_type_group_cd )";
            $query['facility_type_group_cd'] = $facility_type_group_cd;
        }


        $sql .= " WHERE 1 = 1";

        if (!empty($person_name)) {
            $sql .= " AND m_person.person_name LIKE :person_name";
            $query['person_name'] = '%' . trim($this->convert_kana($person_name)) . '%';
        }

        if (!empty($target_product_cd)) {
            $sql .= " AND t_target_facility_person_selection.target_product_cd = :target_product_cd ";
            $query['target_product_cd'] = $target_product_cd;
        }

        if (!empty($segment_type)) {
            $segment_type = explode(',', $segment_type);
            $sql .= " AND t_facility_person_segment.segment_type IN " . $this->_buildCommaString($segment_type, true);
        }

        $sql .= "  GROUP BY m_facility_person.facility_cd , m_facility_person.person_cd  ORDER BY ";

        if (is_object($order_segment) && !empty($order_segment->order_type) && ($order_segment->order_type ?? '') == 'segment') {
            $sql .= " order_by_segment_person DESC , ";
        }

        if (is_object($order_target_product) && !empty($order_target_product->order_type) && ($order_target_product->order_type ?? '') == 'target_product') {
            $sql .= " order_by_target_product_cd_person DESC , ";
        }

        $sql .= "  m_facility.facility_category_type ASC , m_facility.facility_name_kana ASC , m_facility_person.department_cd ASC , m_facility_person.display_position_cd ASC , m_person.person_name_kana ASC;";
       
        return $this->_find($sql, $query);
    }

    public function getSegment($facility_cd, $person_cd, $segment_type, $date_system)
    {
        $query = [];
        $sql = "SELECT 
                    t_facility_person_segment.person_cd,
                    CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 'segment_type', m_facility_person_segment_type.segment_type , 'segment_name' , m_facility_person_segment_type.segment_name) ), ']' ) as segment
                FROM t_facility_person_segment
                INNER JOIN m_facility_person_segment_type
                    ON (t_facility_person_segment.segment_type = m_facility_person_segment_type.segment_type AND m_facility_person_segment_type.end_date >= :date_system)
                WHERE t_facility_person_segment.facility_cd = :facility_cd
                AND t_facility_person_segment.person_cd IN " . $this->_buildCommaString($person_cd, true);

        $sql .= " GROUP BY t_facility_person_segment.person_cd ORDER BY m_facility_person_segment_type.sort_order ASC ;";

        $query['facility_cd'] = $facility_cd;
        $query['date_system'] = $date_system;
        
        return $this->_find($sql, $query);
    }

    public function getTargetProductList($facility_cd, $person_cd, $target_product_cd)
    {
        $query = [];
        $sql = " SELECT 
                m_facility_person.person_cd,
                CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 'target_product_cd', t_target_facility_person_selection.target_product_cd) ), ']' ) as target_product
            FROM m_facility_person
            INNER JOIN t_target_facility_person_selection
            ON (m_facility_person.facility_cd = t_target_facility_person_selection.facility_cd AND m_facility_person.person_cd = t_target_facility_person_selection.person_cd)
            WHERE m_facility_person.facility_cd = :facility_cd
            AND t_target_facility_person_selection.person_cd IN " . $this->_buildCommaString($person_cd, true);

        $sql .= " GROUP BY m_facility_person.person_cd;";
        $query['facility_cd'] = $facility_cd;

        return $this->_find($sql, $query);
    }

    public function deleteSegment($facility_cd, $person_cd)
    {
        $sql = "DELETE FROM t_facility_person_segment WHERE facility_cd = :facility_cd AND person_cd = :person_cd ;";

        return $this->_destroy($sql, ['facility_cd' => $facility_cd, 'person_cd' => $person_cd]);
    }

    public function insertSegment($facility_cd, $person_cd, $segment_type)
    {
        $sql = "INSERT INTO t_facility_person_segment
                    (facility_cd,person_cd,segment_type)
                VALUES
                    (:facility_cd,:person_cd,:segment_type);";
        return $this->_create($sql, [
            'facility_cd' => $facility_cd,
            'person_cd' => $person_cd,
            'segment_type' => $segment_type
        ]);
    }

    public function deleteTargetProduct($facility_cd, $person_cd)
    {
        $sql = "DELETE FROM t_target_facility_person_selection WHERE facility_cd = :facility_cd AND person_cd = :person_cd ;";

        return $this->_destroy($sql, ['facility_cd' => $facility_cd, 'person_cd' => $person_cd]);
    }

    public function insertTargetProduct($facility_cd, $person_cd, $target_product_cd)
    {
        $sql = "INSERT INTO t_target_facility_person_selection
                    (facility_cd,person_cd,target_product_cd)
                VALUES
                    (:facility_cd,:person_cd,:target_product_cd);";
        return $this->_create($sql, [
            'facility_cd' => $facility_cd,
            'person_cd' => $person_cd,
            'target_product_cd' => $target_product_cd
        ]);
    }
}
