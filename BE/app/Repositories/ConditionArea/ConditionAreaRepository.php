<?php

namespace App\Repositories\ConditionArea;

use App\Repositories\BaseRepository;
use App\Repositories\ConditionArea\ConditionAreaRepositoryInterface;
use App\Traits\StringConvertTrait;

class ConditionAreaRepository extends BaseRepository implements ConditionAreaRepositoryInterface
{
    use StringConvertTrait;

    public function allSelectFacilityGroup($user_cd)
    {
        $sql = "SELECT select_facility_group_id as select_group_id, select_facility_group_name as select_group_name FROM t_select_facility_group WHERE user_cd = :user_cd";
        return $this->_find($sql, ['user_cd' => $user_cd]);
    }

    public function allSelectPersonGroup($user_cd)
    {
        $sql = "SELECT select_person_group_id as select_group_id, select_person_group_name as select_group_name FROM t_select_person_group WHERE user_cd = :user_cd";
        return $this->_find($sql, ['user_cd' => $user_cd]);
    }

    public function allTargetProduct()
    {
        $sql = "SELECT target_product_cd, target_product_name FROM m_target_product";
        return $this->_all($sql);
    }

    public function allFacilityCategory()
    {
        $sql = "SELECT facility_category_type, facility_category_name FROM m_facility_category";
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
        $sql = "SELECT T1.facility_cd, T1.facility_short_name, T1.facility_type, T1.facility_name_kana,T1.facility_name
                FROM m_facility T1 
                    LEFT JOIN m_target_facility T4 ON T1.facility_cd = T4.facility_cd
                    LEFT JOIN m_target_product T5 ON T4.target_product_cd = T5.target_product_cd 
                    LEFT JOIN m_facility_category T6 ON T1.facility_category_type=T6.facility_category_type
                    LEFT JOIN m_facility_user T7 ON T1.facility_cd = T7.facility_cd
                " . $join . $where . " GROUP BY T1.facility_cd ORDER BY T1.facility_cd,T1.facility_short_name_kana";
        return $this->_paginate($sql, $query, [
            "limit" => $data->limit,
            "offset" => $data->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function filterRelason($facility)
    {
        $sql = "SELECT T5.facility_short_name,T1.relation_facility_cd as facility_cd,T1.relation_type,T3.definition_label as relation_name,T4.facility_category_type, T4.facility_category_name
                FROM m_relation_facility T1 
                    JOIN m_facility T2 on T1.facility_cd=T2.facility_cd 
                    JOIN m_facility T5 on T1.relation_facility_cd=T5.facility_cd 
                    LEFT JOIN (select * from m_variable_definition where definition_name = :definition_name) T3 on T1.relation_type=T3.definition_value 
                    LEFT JOIN m_facility_category T4 on T2.facility_category_type= T4.facility_category_type
                where T1.facility_cd = :facility_cd";
        return $this->_find($sql, ['facility_cd' => $facility, 'definition_name' => '関連施設区分']);
    }
}
