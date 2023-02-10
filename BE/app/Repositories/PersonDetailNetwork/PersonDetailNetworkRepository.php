<?php

namespace App\Repositories\PersonDetailNetwork;

use App\Repositories\BaseRepository;
use App\Traits\DateTimeTrait;
use App\Traits\StringConvertTrait;

class PersonDetailNetworkRepository extends BaseRepository implements PersonDetailNetworkRepositoryInterface
{
    use StringConvertTrait;
    use DateTimeTrait;
    public function getEra($era)
    {
        $sql = "SELECT definition_value, definition_label FROM m_variable_definition WHERE definition_name = :definition_name";
        return $this->_find($sql, ['definition_name' => $era]);
    }

    public function personalDetail($person_cd)
    {
        $sql = "SELECT
        m_person.person_cd,
        m_person.home_prefecture_cd,
        m_person.graduation_university_cd,
        m_prefecture.prefecture_cd,
        m_prefecture.prefecture_name,
        m_university.university_cd,
        m_university.university_name,
        m_person.graduation_year,
        m_facility.facility_cd,
        m_facility.facility_name,
        t_person_medical_office.medical_office_name,
        t_person_medical_office.medical_office_facility_cd,
        t_person_medical_office.medical_office_department_cd,
        m_department.department_name
        FROM m_person
        LEFT JOIN m_prefecture ON m_prefecture.prefecture_cd = m_person.home_prefecture_cd
        LEFT JOIN m_university ON m_university.university_cd = m_person.graduation_university_cd
        LEFT JOIN t_person_medical_office ON t_person_medical_office.person_cd = m_person.person_cd
        LEFT JOIN m_facility ON m_facility.facility_cd = t_person_medical_office.medical_office_facility_cd
        LEFT JOIN m_department ON m_department.department_cd = t_person_medical_office.medical_office_department_cd
        WHERE m_person.person_cd = :person_cd";
        return $this->_find($sql, ['person_cd' => $person_cd]);
    }

    public function search($conditions,  $limit, $offset)
    {
        $condition = [];
        $sql = "SELECT
            m_facility_person.person_cd,
            m_person.person_name,
            m_person.person_name_kana,
            m_person.home_prefecture_cd,
            m_person.graduation_university_cd,
            m_facility_person.facility_cd,
            m_facility_person.department_cd,
            m_facility_person.display_position_cd,
            m_facility.facility_short_name,
            m_facility.facility_short_name_kana,
            m_facility.facility_name,
            t_person_medical_office.medical_office_facility_cd,
            t_person_medical_office.medical_office_name,
            t_person_medical_office.medical_office_department_cd,
            m_department.department_name,
            m_prefecture.prefecture_name,
            m_university.university_name,
            m_person.graduation_year,
            m_person.ultmarc_delete_flag,
            m_facility_user.user_cd,
            m_user.user_name,
            m_user_org.org_cd,
            m_org.org_label
        FROM
            m_person
            JOIN m_facility_person ON m_person.person_cd = m_facility_person.person_cd
            JOIN m_facility ON m_facility.facility_cd = m_facility_person.facility_cd
            LEFT JOIN t_person_medical_office ON t_person_medical_office.person_cd = m_person.person_cd
            LEFT JOIN m_department ON m_department.department_cd = t_person_medical_office.medical_office_department_cd
            LEFT JOIN m_prefecture ON m_prefecture.prefecture_cd = m_person.home_prefecture_cd
            LEFT JOIN m_university ON m_university.university_cd = m_person.graduation_university_cd
            JOIN m_facility_user ON m_facility_user.facility_cd = m_facility_person.facility_cd
            JOIN m_user ON m_user.user_cd = m_facility_user.user_cd
            LEFT JOIN m_user_org ON m_user_org.user_cd = m_user.user_cd AND m_user_org.main_flag = 1
            LEFT JOIN m_org ON m_org.org_cd = m_user_org.org_cd";
        $sql_condition = "";
        if ($conditions['facility_cd'] != "") {
            $sql_condition .= " OR m_facility_person.facility_cd = :facility_cd";
            $condition['facility_cd'] = $conditions['facility_cd'];
        }
        if ($conditions['graduation_year'] != "") {
            $sql_condition .= " OR m_person.graduation_year = :graduation_year";
            $condition['graduation_year'] = $conditions['graduation_year'];
        }
        if ($conditions['graduation_university_cd'] != "") {
            $sql_condition .= " OR m_person.graduation_university_cd = :graduation_university_cd";
            $condition['graduation_university_cd'] = $conditions['graduation_university_cd'];
        }
        if ($conditions['home_prefecture_cd'] != "") {
            $sql_condition .= " OR m_person.home_prefecture_cd = :home_prefecture_cd";
            $condition['home_prefecture_cd'] = $conditions['home_prefecture_cd'];
        }
        if ($conditions['medical_office_name'] != "") {
            $sql_condition .= " OR t_person_medical_office.medical_office_name LIKE :medical_office_name";
            $condition['medical_office_name'] = '%' . trim($this->convert_kana($conditions['medical_office_name'])) . '%';
        }
        if($conditions['medical_office_department_cd'] != "" && $conditions['medical_office_name'] == ""){
            $sql_condition .= " OR (t_person_medical_office.medical_office_facility_cd = :medical_office_facility_cd AND t_person_medical_office.medical_office_department_cd = :medical_office_department_cd)";
            $condition['medical_office_facility_cd'] = $conditions['medical_office_facility_cd'];
            $condition['medical_office_department_cd'] = $conditions['medical_office_department_cd'];
        }
        if ($sql_condition != "") {
            $sql_condition = " WHERE" . substr($sql_condition, 3);
        }
        $sql .= $sql_condition;
        $sql .= " GROUP BY m_facility_person.person_cd, m_facility_person.facility_cd
        ORDER BY m_facility.facility_short_name_kana ASC, m_facility_person.department_cd ASC, m_facility_person.display_position_cd ASC, m_person.person_name_kana ASC";
        return $this->_paginate($sql, $condition, [
            "limit" => $limit,
            "offset" => $offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getWorkplaceHistoryList($person_cd)
    {
        $sql = "SELECT
            h_facility_person.person_cd,
            h_facility_person.facility_cd,
            m_facility.facility_short_name,
            m_department.department_cd,
            m_department.department_name,
            m_facility_person.hospital_position_cd,
            (SELECT m_position.position_name FROM m_position WHERE m_position.position_cd = m_facility_person.hospital_position_cd) as hospital_position_name,
            m_facility_person.academic_position_cd,
            (SELECT m_position.position_name FROM m_position WHERE m_position.position_cd = m_facility_person.academic_position_cd) as academic_position_name,
            h_facility_person.start_date,
            h_facility_person.end_date
        FROM h_facility_person
        JOIN m_facility ON m_facility.facility_cd = h_facility_person.facility_cd
        JOIN m_facility_person ON m_facility_person.facility_cd = h_facility_person.facility_cd AND m_facility_person.person_cd = h_facility_person.person_cd
        LEFT JOIN m_department ON m_department.department_cd = m_facility_person.department_cd
        WHERE h_facility_person.person_cd = :person_cd";
        return $this->_find($sql, ['person_cd' => $person_cd]);
    }
}
