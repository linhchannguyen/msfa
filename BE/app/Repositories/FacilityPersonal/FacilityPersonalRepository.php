<?php

namespace App\Repositories\FacilityPersonal;

use App\Repositories\BaseRepository;
use App\Repositories\FacilityPersonal\FacilityPersonalRepositoryInterface;
use App\Traits\StringConvertTrait;
use App\Traits\DateTimeTrait;

class FacilityPersonalRepository extends BaseRepository implements FacilityPersonalRepositoryInterface
{
    use StringConvertTrait, DateTimeTrait;

    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
    }

    public function allVariable($data)
    {
        $where = "";
        $query = [];
        if ($data->segment_type) {
            $where .= " AND T1.facility_cd in (SELECT T7.facility_cd FROM t_facility_person_segment T7
                                                JOIN m_facility_person_segment_type T8 ON T7.segment_type = T8.segment_type
                                                WHERE T8.segment_type = :segment_type_facility)
                        AND T1.person_cd in (SELECT T7.person_cd FROM t_facility_person_segment T7
                                JOIN m_facility_person_segment_type T8 ON T7.segment_type = T8.segment_type
                                                WHERE T8.segment_type = :segment_type_person) ";
            $query['segment_type_facility'] = $data->segment_type;
            $query['segment_type_person'] = $data->segment_type;
        }
        if ($data->person_name) {
            $where .= " AND T4.person_name like :person_name";
            $query['person_name'] = "%" . trim($this->convert_kana($data->person_name)) . "%";
        }
        if ($data->department_cd) {
            $where .= " AND T1.department_cd = :department_cd";
            $query['department_cd'] = $data->department_cd;
        }
        // filter facility list
        $facility_cd = [];
        if ($data->facility_cd) {
            $facility_cd = explode(',', $data->facility_cd);
        }
        if (count($facility_cd) > 0) {
            $where .= " AND T1.facility_cd IN " . $this->_buildCommaString($facility_cd, true);
        }
        if($data->select_group_id){
            $select_group_id = explode('_group_id_', $data->select_group_id);
            if ($select_group_id[0] != 'facility') {
                $where .= " AND T1.person_cd IN (SELECT t_select_person_group_detail.person_cd FROM t_select_person_group
                JOIN t_select_person_group_detail ON t_select_person_group_detail.select_person_group_id = t_select_person_group.select_person_group_id
                WHERE t_select_person_group.select_person_group_id = :select_person_group_id)";
                $query['select_person_group_id'] = $select_group_id[1];
            }
        }
        $sql = "SELECT
                    T2.facility_cd,
                    T2.facility_name,
                    T2.facility_short_name,
                    T2.facility_name_kana,
                    T5.department_cd,
                    T5.department_name,
                    T4.person_cd,
                    T4.person_name,
                    T4.person_name_kana,
                    T6.position_cd,
                    T6.position_name,
                    (SELECT GROUP_CONCAT(DISTINCT T8.segment_name) AS segment_name
                        FROM t_facility_person_segment T7
                        JOIN m_facility_person_segment_type T8 ON T7.segment_type = T8.segment_type
                        WHERE T7.facility_cd = T2.facility_cd AND T7.person_cd = T1.person_cd ) AS segment_name,
                    T7.facility_category_type,
                    T7.facility_category_name
                FROM m_facility_person T1
                    LEFT JOIN m_facility T2 ON T1.facility_cd = T2.facility_cd
                    LEFT JOIN m_person T4 ON T1.person_cd = T4.person_cd
                    LEFT JOIN m_department T5 ON T1.department_cd = T5.department_cd
                    LEFT JOIN m_position T6 ON T1.hospital_position_cd = T6.position_cd
                    LEFT JOIN m_facility_category T7 ON T2.facility_category_type = T7.facility_category_type
                WHERE 1 = 1" . $where . " GROUP BY T1.facility_cd, T1.person_cd
                ORDER BY T2.facility_category_type, T2.facility_name_kana, T5.department_cd, T6.position_cd, T4.person_name_kana;";
        return $this->_paginate($sql, $query, [
            "limit" => $data->limit,
            "offset" => $data->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function allSegment()
    {
        $sql = "SELECT segment_type, segment_name FROM m_facility_person_segment_type ORDER BY sort_order";
        return $this->_all($sql);
    }

    public function allMedicalStaff()
    {
        $sql = "SELECT M1.medical_staff_cd, M1.medical_staff_name, T1.facility_cd, T1.facility_short_name
                FROM m_medical_staff M1
                JOIN t_briefing_attendee T1 ON T1.other_medical_staff_type = M1.medical_staff_cd
                GROUP BY M1.medical_staff_cd";
        return $this->_all($sql);
    }

    public function filterListFacility($data)
    {
        $join = "";
        $where = " WHERE 1 = 1 ";
        $query = [];
        if($data->select_group_id){
            $select_group_id = explode('_group_id_', $data->select_group_id);
            if ($select_group_id[0] == 'facility') {
                $where .= " AND T1.facility_cd IN (SELECT t_select_facility_group_detail.facility_cd FROM t_select_facility_group
                JOIN t_select_facility_group_detail ON t_select_facility_group_detail.select_facility_group_id = t_select_facility_group.select_facility_group_id
                WHERE t_select_facility_group.select_facility_group_id = :select_facility_group_id)";
                $query['select_facility_group_id'] = $select_group_id[1];
            } else {
                $where .= " AND T1.facility_cd IN (SELECT t_select_person_group_detail.facility_cd FROM t_select_person_group
                JOIN t_select_person_group_detail ON t_select_person_group_detail.select_person_group_id = t_select_person_group.select_person_group_id
                WHERE t_select_person_group.select_person_group_id = :select_person_group_id)";
                $query['select_person_group_id'] = $select_group_id[1];
            }
        }
        // filter A3.target_product_cd
        if ($data->target_product_cd) {
            $where .= " AND T5.target_product_cd = :target_product_cd";
            $query['target_product_cd'] = $data->target_product_cd;
        }
        // filter A4.prefecture_cd
        if ($data->prefecture_cd) {
            $where .= " AND T1.prefecture_cd = :prefecture_cd";
            $query['prefecture_cd'] = $data->prefecture_cd;
        }
        // filter A4.municipality_cd
        if ($data->municipality_cd) {
            $where .= " AND T1.municipality_cd = :municipality_cd";
            $query['municipality_cd'] = $data->municipality_cd;
        }
        // filter A5.facility_category_type
        if ($data->facility_category_type) {
            $where .= " AND T6.facility_category_type = :facility_category_type";
            $query['facility_category_type'] = $data->facility_category_type;
        }
        // filter A6.facility_category_type
        $facility_cd = [];
        if ($data->facility_cd) {
            $facility_cd = explode(',', $data->facility_cd);
        }
        if (count($facility_cd) > 0) {
            $where .= " AND T1.facility_cd IN " . $this->_buildCommaString($facility_cd, true);
        }
        // filter A7.facility_category_type
        $facility_name = [];
        if ($data->facility_name) {
            $facility_name = explode(',', $data->facility_name);
        }
        if (!empty($facility_name)) {
            for ($i = 0; $i < count($facility_name); $i++) {
                if ($i > 0) {
                    $where .= " OR T1.facility_name LIKE '%" . trim($this->convert_kana($facility_name[$i])) . "%'";
                } else {
                    $where .= " AND T1.facility_name LIKE '%" . trim($this->convert_kana($facility_name[$i])) . "%'";
                }
            }
        }
        // filter A1.user_cd
        if ($data->user_cd) {
            $where .= " AND T7.user_cd = :user_cd";
            $query['user_cd'] = $data->user_cd;
        }
        $sql = "SELECT T7.user_cd, T1.facility_cd, T1.facility_short_name, T1.facility_type , T1.facility_category_type, T6.facility_category_name,";
        if ($data->showRelation) {
            $sql .= "(
                    SELECT
                        CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                        'facility_short_name', T10.facility_short_name,
                        'facility_name', T10.facility_name,
                        'facility_cd', T8.relation_facility_cd,
                        'relation_type', T8.relation_type,
                        'relation_name', T9.definition_label,
                        'facility_category_type', T11.facility_category_type,
                        'facility_category_name', T11.facility_category_name
                        )),']')
                    FROM
                        m_relation_facility T8
                    JOIN (SELECT * FROM m_variable_definition where definition_name = '関連施設区分') T9 on T8.relation_type = T9.definition_value
                    JOIN m_facility T10 ON T10.facility_cd = T8.relation_facility_cd
                    JOIN m_facility_category T11 on T11.facility_category_type= T10.facility_category_type
                    WHERE T8.facility_cd = T1.facility_cd
                ) as relation_facility,";
        }
        $sql .= "
                (
                    SELECT
                        CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                        'facility_cd', m_facility_department.facility_cd,
                        'facility_short_name', T1.facility_short_name,
                        'department_cd', m_department.department_cd,
                        'department_name', m_department.department_name
                        )),']')
                    FROM
                        m_facility_department
                        JOIN m_department ON m_department.department_cd = m_facility_department.department_cd 
                    WHERE
                        m_facility_department.facility_cd = T1.facility_cd
                ) as department_line
                FROM m_facility T1
                    LEFT JOIN m_target_facility_person T4 ON T1.facility_cd = T4.facility_cd
                    LEFT JOIN m_target_product T5 ON T4.target_product_cd = T5.target_product_cd
                    LEFT JOIN m_facility_category T6 ON T1.facility_category_type=T6.facility_category_type
                    LEFT JOIN m_facility_user T7 ON T1.facility_cd = T7.facility_cd
                " . $join . $where . " GROUP BY T1.facility_cd ORDER BY T1.facility_category_type, T1.facility_short_name_kana";
        return $this->_paginate($sql, $query, [
            "limit" => $data->limit,
            "offset" => $data->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }
}
