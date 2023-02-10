<?php

namespace App\Repositories\FacilitySearch;

use App\Repositories\BaseRepository;
use App\Repositories\FacilitySearch\FacilitySearchRepositoryInterface;
use App\Traits\StringConvertTrait;
use App\Traits\DateTimeTrait;

class FacilitySearchRepository extends BaseRepository implements FacilitySearchRepositoryInterface
{
    use StringConvertTrait, DateTimeTrait;
    protected $useAutoMeta = true;
    private $date;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
    }
    public function allData($request)
    {
        $where = "";
        $query['main_flag'] = 1;
        $query['user_login'] = $request->user_login;
        // filter facility_short_name
        if ($request->facility_short_name) {
            $where .= " AND T1.facility_short_name like :facility_short_name";
            $query['facility_short_name'] = "%" . trim($this->convert_kana($request->facility_short_name)) . "%";
        }
        // filter facility_cd
        if ($request->facility_cd) {
            $where .= " AND T1.facility_cd = :facility_cd";
            $query['facility_cd'] = $request->facility_cd;
        }
        // filter phone
        if ($request->phone) {
            $where .= " AND REPLACE(T1.phone, '-', '') = :phone";
            $query['phone'] = str_replace('-', '', $request->phone);
        }
        // filter prefecture_cd
        if ($request->prefecture_cd) {
            $where .= " AND T1.prefecture_cd = :prefecture_cd";
            $query['prefecture_cd'] = $request->prefecture_cd;
        }
        // filter municipality_cd
        if ($request->municipality_cd) {
            $where .= " AND T1.municipality_cd = :municipality_cd";
            $query['municipality_cd'] = $request->municipality_cd;
        }
        // filter ultmarc_delete_flag
        if (!$request->ultmarc_delete_flag) {
            $where .= " AND T1.ultmarc_delete_flag = :ultmarc_delete_flag";
            $query['ultmarc_delete_flag'] = 0;
        }
        // filter facility_consideration_options "あり"
        if ($request->facility_consideration_options == "あり") {
            $where .= " AND T6.last_update_datetime is not null";
        }
        // filter facility_consideration_options "通知あり未確認"
        if ($request->facility_consideration_options == "通知あり未確認") {
            $where .= " AND T7.confirmed_flag = :confirmed_flag";
            $query['confirmed_flag'] = 0;
        }
        // filter user_cd
        $user_cd = [];
        if ($request->user_cd) {
            $user_cd = explode(',', $request->user_cd);
        }
        if (count($user_cd) > 0) {
            $where .= " AND T2.user_cd IN " . $this->_buildCommaString($user_cd, true);
        }
        $sql = "SELECT T1.facility_short_name, 
                        T1.facility_cd,
                        T1.address,
                        T1.phone,
                        T3.user_cd,
                        T3.user_name,
                        T5.org_cd,
                        T5.org_label,
                        (
                            SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                                'user_cd', t_facility_consideration_confirm.user_cd,
                                'confirmed_flag', t_facility_consideration_confirm.confirmed_flag,
                                'last_update_datetime', t_facility_consideration.last_update_datetime
                            )),']')
                            FROM t_facility_consideration
                            LEFT JOIN t_facility_consideration_confirm ON t_facility_consideration_confirm.facility_consideration_id = t_facility_consideration.facility_consideration_id
                            WHERE T1.facility_cd = t_facility_consideration.facility_cd
                        ) AS facility_consideration_confirm,
                        T1.ultmarc_delete_flag
                FROM m_facility T1 
                    LEFT JOIN m_facility_user T2 ON T1.facility_cd = T2.facility_cd
                    LEFT JOIN m_user T3 ON T2.user_cd = T3.user_cd
                    LEFT JOIN m_user_org T4 ON T4.user_cd = T3.user_cd
                    LEFT JOIN m_org T5 ON T4.org_cd = T5.org_cd
                    LEFT JOIN t_facility_consideration T6 ON T1.facility_cd = T6.facility_cd
                    LEFT JOIN t_facility_consideration_confirm T7 ON (T7.facility_consideration_id = T6.facility_consideration_id AND T7.user_cd = :user_login)
                    WHERE T4.main_flag = :main_flag " . $where . " GROUP BY T1.facility_cd ORDER BY T1.facility_cd ";
        return $this->_paginate($sql, $query, [
            "limit" => $request->limit,
            "offset" => $request->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }


    public function detailFacility($facility_cd)
    {
        $sql = "SELECT T1.facility_cd,
                        T1.facility_name,
                        T1.address,
                        T1.phone,
                        T2.prefecture_cd,
                        T2.prefecture_name,
                        T3.municipality_cd,
                        T3.municipality_name,
                        T4.management_body_cd,
                        T4.management_body_name,
                        T5.infirmary_type,
                        T5.infirmary_type_name,
                        T1.total_bed_count,
                        T1.general_bed_count,
                        T1.mental_bed_count,
                        T1.infection_bed_count,
                        T1.other_bed_count,
                        T1.tuberculous_bed_count,
                        T6.consultation_hours_note,
                        T7.facility_category_name
                FROM m_facility T1
                    JOIN m_prefecture T2 ON T1.prefecture_cd = T2.prefecture_cd
                    JOIN m_municipality T3 ON T1.municipality_cd = T3.municipality_cd AND T1.prefecture_cd = T3.prefecture_cd
                    JOIN m_management_body T4 ON T1.management_body_cd = T4.management_body_cd
                    JOIN m_infirmary_type T5 ON T1.infirmary_type = T5.infirmary_type
                    LEFT JOIN t_facility_consultation_hours T6 ON T1.facility_cd = T6.facility_cd
                    JOIN m_facility_category T7 ON T7.facility_category_type = T1.facility_category_type
                WHERE T1.facility_cd = :facility_cd";
        return $this->_first($sql, ['facility_cd' => $facility_cd]);
    }

    public function informationFacility($facility_cd)
    {
        $sql = "SELECT T1.facility_short_name,
                        T1.facility_short_name_kana,
                        T1.facility_cd,
                        T1.break_flag,
                        T1.ultmarc_delete_flag,
                        T2.delete_reason_type_label
                FROM m_facility T1
                    LEFT JOIN m_facility_delete_reason_type T2 ON T1.delete_reason_type = T2.delete_reason_type
                WHERE T1.facility_cd = :facility_cd";
        return $this->_first($sql, ['facility_cd' => $facility_cd]);
    }

    public function staffHistoryFacility($facility_cd)
    {
        $sql = "SELECT T1.main_flg, 
                        T3.start_date, 
                        T3.end_date, 
                        T1.user_cd, 
                        T2.profile_image_file_id,
                        T3.user_name,
                        T4.file_url
                FROM h_facility_user T1 
                    JOIN i_user T3 ON T1.user_cd = T3.user_cd
                    LEFT JOIN t_user_profile T2 ON T3.user_cd = T2.user_cd
                    LEFT JOIN t_file T4 ON T4.file_id = T2.profile_image_file_id 
                WHERE T1.facility_cd = :facility_cd AND  T3.start_date <= T1.end_date";
        return $this->_find($sql, ['facility_cd' => $facility_cd]);
    }

    public function medicalSubjectsFacility($facility_cd)
    {
        $sql = "SELECT T2.medical_subjects_cd,T2.medical_subjects_name
                FROM m_facility_subjects T1 
                    JOIN m_medical_subjects T2 ON T1.medical_subjects_cd = T2.medical_subjects_cd
                WHERE T1.facility_cd = :facility_cd";
        return $this->_find($sql, ['facility_cd' => $facility_cd]);
    }

    public function relatedFacility($facility_cd)
    {
        $sql = "SELECT T2.relation_facility_cd,T1.facility_short_name, T2.relation_type, T3.definition_label
                FROM m_facility T1 
                    JOIN m_relation_facility T2 ON T1.facility_cd = T2.relation_facility_cd 
                    LEFT JOIN (
                        SELECT definition_value,definition_label 
                        FROM m_variable_definition 
                        WHERE definition_name = :definition_name) T3 ON T2.relation_type = T3.definition_value
                WHERE T2.facility_cd = :facility_cd";
        return $this->_find($sql, ['facility_cd' => $facility_cd, 'definition_name' => '関連施設区分']);
    }

    public function relationFacility($facility_cd)
    {
        $sql = "SELECT T2.facility_cd,T1.facility_short_name, T2.relation_type, T3.definition_label
                FROM m_relation_facility T2
                    JOIN m_facility T1 ON T1.facility_cd = T2.facility_cd 
                    LEFT JOIN (
                        SELECT definition_value,definition_label 
                        FROM m_variable_definition 
                        WHERE definition_name = :definition_name) T3 ON T2.relation_type = T3.definition_value
                WHERE T2.relation_facility_cd = :facility_cd";
        return $this->_find($sql, ['facility_cd' => $facility_cd, 'definition_name' => '関連施設区分']);
    }

    public function consultationTimeFacility($facility_cd)
    {
        $sql = "SELECT consultation_hours_note FROM t_facility_consultation_hours WHERE facility_cd = :facility_cd";
        return $this->_first($sql, ['facility_cd' => $facility_cd]);
    }

    public function saveConsultationTimeFacility($data)
    {
        $find = "SELECT facility_cd FROM t_facility_consultation_hours WHERE facility_cd = :facility_cd";
        $result = $this->_first($find, ['facility_cd' => $data->facility_cd]);
        $parram = array(
            'facility_cd' => $data->facility_cd,
            'consultation_hours_note' => $data->consultation_hours_note
        );
        if($result->facility_cd ?? ''){
            $sql = "UPDATE t_facility_consultation_hours 
                        SET consultation_hours_note = :consultation_hours_note
                    WHERE
                        facility_cd = :facility_cd;";
            return $this->_update($sql, $parram);
        }else {
            $sql = "INSERT INTO t_facility_consultation_hours (
                        facility_cd,
                        consultation_hours_note) 
                    VALUES(
                        :facility_cd,
                        :consultation_hours_note
                    );";
            return $this->_create($sql, $parram);
        }
    }

    public function getWorkingIndividual($request)
    {
        $where = '';
        $query['facility_cd'] = $request->facility_cd;
        if ($request->department_cd) {
            $where .= " AND T3.department_cd = :department_cd";
            $query['department_cd'] = $request->department_cd;
        }
        if ($request->person_name) {
            $where .= " AND T2.person_name like :person_name";
            $query['person_name'] = "%" . trim($this->convert_kana($request->person_name)) . "%";
        }
        // filter user_cd
        $segment_type = [];
        if ($request->segment_type) {
            $segment_type = explode(',', $request->segment_type);
        }
        if (count($segment_type) > 0) {
            $where .= " AND (T5.segment_type IN " . $this->_buildCommaString($segment_type, true);
            if ($request->part_type) {
                $where .= " OR T7.part_type = 10";
            }
            $where .= ")";
        }else{
            if ($request->part_type) {
                $where .= " AND T7.part_type = 10";
            }
        }
        $sql = "SELECT T1.facility_cd,
                    T2.person_cd,
                    T2.person_name,
                    T3.department_cd,
                    T3.department_name,
                    T1.hospital_position_cd,
                    T1.academic_position_cd,
                    T4.position_name as hospital_position_name,
                    T6.position_name as academic_position_name,
                    T8.user_cd,
                    CASE WHEN T7.part_type = 10 THEN 1 ELSE 0 END part_type
                FROM m_facility_person T1 
                    JOIN m_person T2 ON T1.person_cd = T2.person_cd
                    LEFT JOIN m_department T3 ON T1.department_cd = T3.department_cd
                    LEFT JOIN m_facility_user T8 on T1.facility_cd = T8.facility_cd
                    LEFT JOIN m_position T4 ON T1.hospital_position_cd = T4.position_cd
                    LEFT JOIN m_position T6 ON T1.academic_position_cd = T6.position_cd
                    LEFT JOIN t_facility_person_segment T5 ON T1.facility_cd = T5.facility_cd AND T1.person_cd = T5.person_cd
                    LEFT JOIN t_facility_key_member T7 ON T1.facility_cd = T7.facility_cd AND T2.person_cd = T7.person_cd
                WHERE T1.facility_cd = :facility_cd" . $where . " 
                GROUP BY T2.person_cd,T1.facility_cd
                ORDER BY T1.department_cd,T1.display_position_cd,T2.person_name_kana
                ";
        return $this->_paginate($sql, $query, [
            "limit" => $request->limit,
            "offset" => $request->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getSegmentPerson($person_cd, $facility_cd)
    {
        $query['start_date'] = $this->date;
        $query['end_date'] = $this->date;
        $query['person_cd'] = $person_cd;
        $query['facility_cd'] = $facility_cd;
        $sql = "SELECT A.segment_type, A.segment_name, CASE WHEN B.segment_type IS NOT NULL THEN 1 ELSE 0 END checked, COALESCE(0)  AS delete_flag
                FROM (SELECT * FROM m_facility_person_segment_type WHERE start_date <= :start_date and end_date >= :end_date) A
                LEFT JOIN (SELECT * FROM t_facility_person_segment WHERE person_cd = :person_cd and facility_cd = :facility_cd) B ON A.segment_type = B.segment_type ";
        return $this->_find($sql, $query);
    }

    public function getDepartment()
    {
        $sql = "SELECT T2.department_cd,T2.department_name
                FROM m_facility_department T1 
                    JOIN m_department T2 ON T1.department_cd = T2.department_cd GROUP BY T2.department_cd";
        return $this->_find($sql, []);
    }

    public function getSegmentType()
    {
        $query['start_date'] = $this->date;
        $query['end_date'] = $this->date;
        $sql = "SELECT segment_type, segment_name,uneditable_flag FROM m_facility_person_segment_type WHERE start_date <= :start_date and end_date >= :end_date";
        return $this->_find($sql, $query);
    }

    public function saveSegmentType($facility_cd, $person_cd, $segment_type)
    {
        $sql = "INSERT INTO t_facility_person_segment(facility_cd,person_cd,segment_type) VALUES (:facility_cd,:person_cd,:segment_type);";
        $parram = [
            "facility_cd" => $facility_cd,
            "person_cd" => $person_cd,
            "segment_type" => $segment_type,
        ];
        return $this->_create($sql, $parram);
    }

    public function deleteSegmentType($facility_cd, $person_cd, $segment_type)
    {
        $sql = "DELETE FROM t_facility_person_segment WHERE facility_cd = :facility_cd and person_cd = :person_cd and segment_type = :segment_type";
        $parram = [
            "facility_cd" => $facility_cd,
            "person_cd" => $person_cd,
            "segment_type" => $segment_type,
        ];
        return $this->_destroy($sql, $parram);
    }

    public function saveFacilityKeyMember($facility_cd, $person_cd, $part_type)
    {
        $sql = "INSERT INTO t_facility_key_member(facility_cd,person_cd,part_type,last_updated_at) 
                VALUES (:facility_cd,:person_cd,:part_type,:last_updated_at) ON DUPLICATE KEY UPDATE part_type = VALUES(part_type), last_updated_at = VALUES(last_updated_at);";
        $parram = [
            "facility_cd" => $facility_cd,
            "person_cd" => $person_cd,
            "part_type" => $part_type ?? null,
            "last_updated_at" => date("Y-m-d", strtotime($this->date))
        ];
        return $this->_create($sql, $parram);
    }
}
