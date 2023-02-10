<?php

namespace App\Repositories\PersonSearch;

use App\Repositories\BaseRepository;
use App\Repositories\PersonSearch\PersonSearchRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Traits\StringConvertTrait;

class PersonSearchRepository extends BaseRepository implements PersonSearchRepositoryInterface
{
    use StringConvertTrait;
    protected $useAutoMeta = true;

    public function allData($request)
    {
        $where = "";
        $query['main_flg'] = 1;
        $query['user_login'] = $request->user_login;
        if ($request->person_name) {
            $where .= " AND T2.person_name like :person_name";
            $query['person_name'] = "%" . trim($this->convert_kana($request->person_name)) . "%";
        }
        if ($request->person_cd) {
            $where .= " AND T2.person_cd = :person_cd";
            $query['person_cd'] = $request->person_cd;
        }
        if ($request->facility_short_name) {
            $where .= " AND T5.facility_short_name like :facility_short_name";
            $query['facility_short_name'] = "%" . trim($this->convert_kana($request->facility_short_name)) . "%";
        }
        if ($request->facility_cd) {
            $where .= " AND T5.facility_cd = :facility_cd";
            $query['facility_cd'] = $request->facility_cd;
        }
        // filter user_cd
        $user_cd = [];
        if ($request->user_cd) {
            $user_cd = explode(',', $request->user_cd);
        }
        if (count($user_cd) > 0) {
            $where .= " AND T9.user_cd IN " . $this->_buildCommaString($user_cd, true);
        }
        // filter person_consideration_options "指定なし"
        // if ($request->person_consideration_options == "指定なし") {
        //     $where .= " AND T3.last_update_datetime is null";
        // }
        // filter person_consideration_options "あり"
        if ($request->person_consideration_options == "あり") {
            $where .= " AND T3.last_update_datetime is not null";
        }
        // filter person_consideration_options "通知あり未確認"
        if ($request->person_consideration_options == "通知あり未確認") {
            $where .= " AND T4.confirmed_flag = :confirmed_flag";
            $query['confirmed_flag'] = 0;
        }
        // filter move_flag
        if ($request->move_flag) {
            $where .= " AND T1.move_flag = :move_flag";
            $query['move_flag'] = $request->move_flag;
        }
        // filter ultmarc_delete_flag
        if (!$request->ultmarc_delete_flag) {
            $where .= " AND T2.ultmarc_delete_flag = :ultmarc_delete_flag";
            $query['ultmarc_delete_flag'] = 0;
        }
        $sql = "SELECT T2.person_cd,
                        T2.person_name,
                        (
                            SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                                'user_cd', t_person_consideration_confirm.user_cd,
                                'confirmed_flag', t_person_consideration_confirm.confirmed_flag,
                                'last_update_datetime', t_person_consideration.last_update_datetime
                            )),']')
                            FROM t_person_consideration
                            LEFT JOIN t_person_consideration_confirm ON t_person_consideration_confirm.person_consideration_id = t_person_consideration.person_consideration_id
                            WHERE t_person_consideration.person_cd = T1.person_cd
                        ) AS person_consideration_confirm,
                        T1.move_flag,
                        T5.facility_cd,
                        T5.facility_short_name,
                        T6.department_cd,
                        T6.department_name,
                        T1.hospital_position_cd,
                        T1.academic_position_cd,
                        T7.position_name as hospital_position_name,
                        T14.position_name as academic_position_name,
                        T9.user_cd,
                        T9.user_name,
                        T11.org_cd,
                        T11.org_label,
                        T12.university_name,
                        T2.graduation_university_cd,
                        T2.ultmarc_delete_flag,
                        CONCAT(T13.definition_label,SUBSTRING(T2.graduation_year,2)) as label_graduation_year
                FROM m_facility_person T1 
                    JOIN m_person T2 ON T1.person_cd = T2.person_cd
                    LEFT JOIN t_person_consideration T3 ON T1.person_cd = T3.person_cd
                    LEFT JOIN (select * from t_person_consideration_confirm where user_cd = :user_login) T4 ON T3.person_consideration_id = T4.person_consideration_id
                    LEFT JOIN m_facility T5 ON T1.facility_cd = T5.facility_cd
                    LEFT JOIN m_department T6 ON T1.department_cd = T6.department_cd
                    LEFT JOIN m_position T7 ON T1.academic_position_cd = T7.position_cd
                    LEFT JOIN m_position T14 ON T1.hospital_position_cd = T14.position_cd
                    LEFT JOIN (SELECT * FROM m_facility_user WHERE main_flg = :main_flg) T8 ON T1.facility_cd = T8.facility_cd
                    LEFT JOIN m_user T9 ON T8.user_cd = T9.user_cd
                    LEFT JOIN m_user_org T10 ON T8.user_cd = T10.user_cd
                    LEFT JOIN m_org T11 ON T10.org_cd = T11.org_cd
                    LEFT JOIN m_university T12 ON T12.university_cd = T2.graduation_university_cd
                    LEFT JOIN (select * from m_variable_definition where definition_name = '元号') T13 on SUBSTRING(T2.graduation_year,1,1) = T13.definition_value 
                WHERE 1 = 1 " . $where . " 
                GROUP BY T1.facility_cd, T2.person_cd 
                ORDER BY T2.person_name_kana,T5.facility_short_name_kana,T1.display_position_cd,T6.department_cd";
        return $this->_paginate($sql, $query, [
            "limit" => $request->limit,
            "offset" => $request->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getPersonInformation($person_cd, $ultmarc_delete_flag)
    {
        $sql = "SELECT T1.ultmarc_delete_flag,
                        T1.person_name,
                        T1.person_name_kana,
                        T1.person_cd,
                        T2.definition_label as label_gender,
                        T3.prefecture_name,
                        T4.university_name,
                        CONCAT('(', T5.definition_label,SUBSTRING(T1.graduation_year,2),'年卒)') as label_graduation_year,
                        CONCAT(T6.definition_label,SUBSTRING(T1.birth_date,2,2),'年',SUBSTRING(T1.birth_date,4,2),'月',SUBSTRING(T1.birth_date,6),'日') as label_birth_date
                FROM m_person T1
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = '性別') T2 ON T1.gender = T2.definition_value
                    LEFT JOIN m_prefecture T3 on T1.home_prefecture_cd = T3.prefecture_cd
                    LEFT JOIN m_university T4 on T1.graduation_university_cd = T4.university_cd
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = '元号') T5 ON SUBSTRING(T1.graduation_year,1,1) = T5.definition_value
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = '元号') T6 ON SUBSTRING(T1.birth_date,1,1) = T6.definition_value
                WHERE T1.person_cd = :person_cd AND T1.ultmarc_delete_flag = (SELECT ultmarc_delete_flag FROM m_person WHERE person_cd = :person_ultmarc)";
        $query['person_cd'] = $person_cd;
        $query['person_ultmarc'] = $person_cd;
        return $this->_first($sql, $query);
    }

    public function getPersonDetail($person_cd)
    {
        $sql = "SELECT T1.work_type,
                        T3.facility_short_name,
                        T3.facility_cd,
                        T4.department_cd,
                        T4.department_name,
                        T2.medical_office_name,
                        T5.definition_label AS label_work_type,
                        T1.practice_year,
                        T7.prefecture_name,
                        T8.delete_reason_type_label,
                        CONCAT('(開業年：',T6.definition_label,SUBSTRING(T1.practice_year,2,3),'）') AS label_graduation_year,
                        CASE WHEN T2.medical_office_name IS NOT NULL THEN CONCAT(T3.facility_short_name,T2.medical_office_name)
                        WHEN T2.medical_office_name IS NULL AND T4.department_name IS NOT NULL THEN CONCAT(T3.facility_short_name,T4.department_name)
                        ELSE CONCAT(T3.facility_short_name)
                        END label_medical_office_name
                FROM m_person T1
                    LEFT JOIN t_person_medical_office T2 ON T1.person_cd = T2.person_cd
                    LEFT JOIN m_facility T3 ON T2.medical_office_facility_cd = T3.facility_cd
                    LEFT JOIN m_department T4 ON T2.medical_office_department_cd = T4.department_cd
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = :work_type) T5 ON T1.work_type = T5.definition_value
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = :practice_year) T6 ON left(T1.practice_year,1) = T6.definition_value
                    LEFT JOIN m_prefecture T7 ON T1.medical_association_cd = T7.prefecture_cd
                    LEFT JOIN m_person_delete_reason_type T8 ON T1.delete_reason_type = T8.delete_reason_type
                WHERE T1.person_cd = :person_cd";
        $query['person_cd'] = $person_cd;
        $query['work_type'] = "開勤区分";
        $query['practice_year'] = "元号";
        return $this->_first($sql, $query);
    }

    public function getMedicalTreatmentSubjects($person_cd)
    {
        $sql = "SELECT T2.medical_subjects_cd,T2.medical_subjects_name
                FROM m_person_subjects T1 
                JOIN m_medical_subjects T2 ON T1.medical_subjects_cd=T2.medical_subjects_cd 
                WHERE T1.person_cd = :person_cd";
        $query['person_cd'] = $person_cd;
        return $this->_find($sql, $query);
    }

    public function getListAcademicSocieties($person_cd)
    {
        $sql = "SELECT T2.society_cd, T2.society_name,CONCAT(T1.entry_year,'年度') AS entry_year  
                FROM m_society_member T1 
                    JOIN m_society T2 ON T1.society_cd=T2.society_cd 
                WHERE T1.person_cd = :person_cd
                ORDER BY T1.entry_year DESC, T1.society_cd ASC";
        $query['person_cd'] = $person_cd;
        return $this->_find($sql, $query);
    }

    public function getListSpecialistQualification($person_cd)
    {
        $sql = "SELECT T2.specialist_cd, T2.specialist_name, T1.specialist_type, T3.definition_label 
                FROM m_society_specialist T1 
                    LEFT JOIN m_specialist T2 ON T1.specialist_cd = T2.specialist_cd 
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = :definition_name) T3 ON T1.specialist_type = T3.definition_value
                WHERE T1.person_cd = :person_cd
                ORDER BY publication_date DESC, T1.specialist_cd ASC;";
        $query['person_cd'] = $person_cd;
        $query['definition_name'] = "専門医区分";
        return $this->_find($sql, $query);
    }

    public function getMedicalOfficeName($person_cd)
    {
        $sql = "SELECT T1.medical_office_facility_cd, T1.medical_office_department_cd, T1.medical_office_name, T2.facility_short_name
                FROM t_person_medical_office T1 
                    JOIN m_facility T2 on T1.medical_office_facility_cd = T2.facility_cd 
                where T1.person_cd = :person_cd";
        $query['person_cd'] = $person_cd;
        return $this->_find($sql, $query);
    }

    public function saveMedicalOffice($request)
    {
        $sql = "INSERT INTO t_person_medical_office(person_cd,medical_office_facility_cd,medical_office_department_cd,medical_office_name) 
                VALUES (:person_cd,:medical_office_facility_cd,:medical_office_department_cd,:medical_office_name) ON DUPLICATE KEY UPDATE
                    medical_office_facility_cd = VALUES(medical_office_facility_cd),
                    medical_office_department_cd = VALUES(medical_office_department_cd),
                    medical_office_name = VALUES(medical_office_name)";
        $query['medical_office_facility_cd'] = $request->medical_office_facility_cd;
        $query['medical_office_department_cd'] = $request->medical_office_department_cd;
        $query['medical_office_name'] = $request->medical_office_name;
        $query['person_cd'] = $request->person_cd;
        return $this->_create($sql, $query);
    }

    public function getWorkingIndividual($request)
    {
        $sql = "SELECT T1.facility_cd,
                        T1.facility_short_name,
                        T3.department_cd,
                        T3.department_name,
                        T2.position_cd,
                        T4.position_name,
                        T2.academic_position_cd,
                        T5.position_name as academic_position_name,
                        T2.start_date,
                        T2.end_date
                FROM m_facility T1 
                    JOIN h_facility_person T2 ON T1.facility_cd = T2.facility_cd
                    LEFT JOIN m_department T3 ON T3.department_cd = T2.department_cd
                    LEFT JOIN m_position T4 ON T4.position_cd = T2.position_cd
                    LEFT JOIN m_position T5 ON T5.position_cd = T2.academic_position_cd
                WHERE T2.person_cd = :person_cd
                ORDER BY T2.end_date DESC, T2.start_date DESC , T1.facility_short_name_kana, T3.department_cd, T2.academic_position_cd";
        return $this->_paginate($sql, ['person_cd' => $request->person_cd], [
            "limit" => $request->limit,
            "offset" => $request->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getDepartmentPersonDetail($facility_cd)
    {
        $sql = "SELECT T2.department_cd,T2.department_name
                FROM m_facility_department T1 
                    JOIN m_department T2 ON T1.department_cd = T2.department_cd 
                WHERE T1.facility_cd = :facility_cd
                GROUP BY T2.department_cd";
        return $this->_find($sql, ['facility_cd' => $facility_cd]);
    }
}
