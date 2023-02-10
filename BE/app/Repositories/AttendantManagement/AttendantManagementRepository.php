<?php

namespace App\Repositories\AttendantManagement;

use App\Repositories\BaseRepository;

class AttendantManagementRepository extends BaseRepository implements AttendantManagementRepositoryInterface
{
    protected $useAutoMeta = true;

    public function getInfoConvention($convention_id)
    {
        $sql = "SELECT
        m_variable_definition.definition_label,
        t_convention.convention_name,
        t_convention.status_type,
        t_convention.plan_user_cd,
        t_convention.plan_org_cd,
        t_schedule.start_date,
        t_schedule.start_time,
        t_schedule.end_time,
        (SELECT GROUP_CONCAT(t_convention_product.product_name SEPARATOR ', ') 
            FROM t_convention_product
            WHERE t_convention_product.convention_id = t_convention.convention_id
        ) AS product_name,
        (
            SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                'product_name', t_convention_product.product_name,
                'product_cd', t_convention_product.product_cd
            )),']')
            FROM t_convention_product
                WHERE t_convention_product.convention_id = t_convention.convention_id
        ) AS product_list,
        (SELECT GROUP_CONCAT(t_convention_target_org.org_cd) 
            FROM t_convention_target_org
            WHERE t_convention_target_org.convention_id = t_convention.convention_id
        ) AS convention_target_org_list
        FROM t_convention
        LEFT JOIN t_schedule ON t_schedule.schedule_id = t_convention.schedule_id
        JOIN m_variable_definition ON m_variable_definition.definition_name = :definition_name AND m_variable_definition.definition_value = t_convention.status_type
        WHERE t_convention.convention_id = :convention_id;";
        return $this->_find($sql, ['convention_id' => $convention_id, 'definition_name' => CONVENTION_STATUS]);
    }

    public function getConventionStatusItem()
    {
        $sql = "SELECT status_item_cd, status_item_name FROM m_convention_status_item order by sort_order";
        return $this->_all($sql);
    }

    public function getParameterAddStock()
    {
        $sql = "SELECT definition_value FROM m_variable_definition WHERE definition_name = :definition_name AND definition_value = :definition_value;";
        return $this->_first($sql, ['definition_name' => DEFINITION_NAME_STOCK_SOURCE_CLASSIFICATION, 'definition_value' => DEFINITION_VALUE_20]);
    }

    public function getLectureFollowUp()
    {
        $sql = "SELECT parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_name AND parameter_key = :parameter_key;";
        return $this->_first($sql, ['parameter_name' => PARAMETER_NAME_CONVENTION, 'parameter_key' => PARAMETER_KEY_CONVENTION]);
    }

    public function getInputDeadline()
    {
        $sql = "SELECT parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_name AND parameter_key = :parameter_key";
        return $this->_first($sql, ['parameter_name' => PARAMETER_NAME_CONVENTION, 'parameter_key' => INPUT_DEADLINE_CONVENTION]);
    }

    public function getOtherAttendee($convention_id)
    {
        $sql = "SELECT
        m_convention_occupation_type.occupation_type,
        m_convention_occupation_type.occupation_name,
        (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
            'status_item_cd', t_convention_other_headcount.status_item_cd,
            'headcount', (CASE 
                            WHEN t_convention_other_headcount.headcount IS NOT NULL
                            THEN t_convention_other_headcount.headcount 
                            ELSE 0 
                        END)
            )),']') FROM t_convention_other_headcount 
            WHERE t_convention_other_headcount.occupation_type = m_convention_occupation_type.occupation_type
            AND t_convention_other_headcount.convention_id = :convention_id 
        ) AS other_headcount
        FROM m_convention_occupation_type";
        return $this->_find($sql, ['convention_id' => $convention_id]);
    }

    public function getVariableDefinitionD14()
    {
        $sql = "SELECT definition_value, definition_label FROM m_variable_definition WHERE definition_name = :definition_name;";
        return $this->_find($sql, ['definition_name' => ROLE_CLASSIFICATION]);
    }

    public function getVariableDefinitionD18()
    {
        $sql = "SELECT definition_value, definition_label FROM m_variable_definition WHERE definition_name = :definition_name;";
        return $this->_find($sql, ['definition_name' => STATUS_CONFIRMATION_CATEGORY_ATTENDANCE_SELECTION_ITEM]);
    }

    public function getMedicalStaff()
    {
        $sql = "SELECT medical_staff_cd, medical_staff_name FROM m_medical_staff;";
        return $this->_all($sql);
    }

    public function addHeadcount($data)
    {
        $sql = "INSERT INTO t_convention_other_headcount ( convention_id, occupation_type, status_item_cd, headcount ) VALUES :values;";
        return $this->_bulkCreate($sql, $data);
    }

    public function deleteHeadcount($convention_id)
    {
        $sql = "DELETE FROM t_convention_other_headcount WHERE convention_id = :convention_id;";
        return $this->_destroy($sql, ['convention_id' => $convention_id]);
    }

    public function addStatusDetail($data)
    {
        $sql = "INSERT INTO t_convention_attendee_status_detail ( convention_attendee_id, status_item_cd, status_item_value, update_date ) 
            VALUES :values;";
        return $this->_bulkCreate($sql, $data);
    }

    public function updateStatusDetail($data)
    {
        $sql = "UPDATE t_convention_attendee_status_detail 
        SET
            status_item_value = :status_item_value,
            update_date = :update_date
        WHERE
            convention_attendee_id = :convention_attendee_id AND
            status_item_cd = :status_item_cd;";
        return $this->_update($sql, $data);
    }

    public function addConventionAttendee($data)
    {
        $sql = "INSERT INTO t_convention_attendee (
            convention_id,
            person_cd,
            facility_cd,
            person_name,
            person_name_kana,
            facility_short_name,
            facility_name_kana,
            department_cd,
            department_name,
            display_position_cd,
            display_position_name,
            other_medical_staff_type,
            other_person_flag,
            part_type
        ) VALUES :values;";
        return $this->_bulkCreate($sql, $data);
    }

    public function deleteConvention($convention_attendee_ids)
    {
        $sql = "DELETE ca, ca_st FROM t_convention_attendee ca
        JOIN t_convention_attendee_status_detail ca_st
        ON ca_st.convention_attendee_id = ca.convention_attendee_id
        WHERE ca.convention_attendee_id IN " . $this->_buildCommaString($convention_attendee_ids, true);
        return $this->_destroy($sql, []);
    }

    public function updateConventionAttendee($data)
    {
        $sql = "UPDATE t_convention_attendee
        SET
            part_type = :part_type
        WHERE convention_attendee_id = :convention_attendee_id;";
        return $this->_update($sql, $data);
    }

    public function lastInsertedConventionAttendee()
    {
        return $this->_lastInserted('t_convention_attendee', 'convention_attendee_id');
    }

    public function searchData($conditions)
    {
        $sql = "SELECT
        t_convention_attendee.convention_attendee_id,
        t_convention_attendee.convention_id,
        t_schedule.start_time as start_date,
        t_convention_attendee.person_cd,
        t_convention_attendee.facility_cd,
        t_convention_attendee.person_name,
        t_convention_attendee.person_name_kana,
        t_convention_attendee.facility_short_name,
        t_convention_attendee.facility_name_kana,
        t_convention_attendee.department_cd,
        t_convention_attendee.department_name,
        t_convention_attendee.display_position_cd,
        t_convention_attendee.display_position_name,
        t_convention_attendee.other_medical_staff_type,
        t_convention_attendee.other_person_flag,
        t_convention_attendee.part_type,
        t_convention_attendee.gratuity,
        t_convention_attendee.withholding_tax,
        t_convention_attendee.give_amount,
        t_convention_attendee.subject,
        (SELECT t_schedule.schedule_id FROM t_convention JOIN t_schedule ON t_schedule.schedule_id = t_convention.schedule_id WHERE t_convention.convention_id = t_convention_attendee.convention_id) AS schedule_id,
        (SELECT t_schedule.schedule_type FROM t_convention JOIN t_schedule ON t_schedule.schedule_id = t_convention.schedule_id WHERE t_convention.convention_id = t_convention_attendee.convention_id) AS schedule_type,
        (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                'convention_attendee_id', t_convention_attendee_status_detail.convention_attendee_id,
                'status_item_cd', t_convention_attendee_status_detail.status_item_cd,
                'status_item_value', t_convention_attendee_status_detail.status_item_value,
                'update_date', t_convention_attendee_status_detail.update_date
                )),']')
                FROM t_convention_attendee_status_detail
                WHERE t_convention_attendee_status_detail.convention_attendee_id = t_convention_attendee.convention_attendee_id
        ) AS convention_attendee_status_detail,
        (
            SELECT
                CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                    'start_date', t_schedule.start_time
                )),']')
            FROM t_schedule
            JOIN t_visit ON t_visit.schedule_id = t_schedule.schedule_id
            JOIN t_call ON t_call.visit_id = t_visit.visit_id
            WHERE CAST(t_schedule.start_date AS DATE) >= :convention_start_date_sub AND CAST(t_schedule.start_date AS DATE) <= :convention_start_date_add_convention_follow_up_sub
            AND t_call.person_cd = t_convention_attendee.person_cd AND t_schedule.schedule_type = :schedule_type
        ) AS follow_date,
        (
            SELECT
                CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                    'start_date', t_schedule.start_time,
                    'person_cd', t_convention_attendee.person_cd,
                    'convention_id', t_convention_sub.convention_id,
                    'status_item_value_200', (
                        SELECT count(*) FROM t_convention_attendee as t_convention_attendee_sub
                        JOIN t_convention_attendee_status_detail as t_convention_attendee_status_detail_sub
                        ON t_convention_attendee_status_detail_sub.convention_attendee_id = t_convention_attendee_sub.convention_attendee_id
                        WHERE t_convention_attendee_sub.convention_id = t_convention_sub.convention_id
                        AND t_convention_attendee_sub.person_cd = t_convention_attendee.person_cd
                        AND t_convention_attendee_sub.other_person_flag = 0
                        AND status_item_cd = :status_item_cd_200
                        AND status_item_value = 1
                    ),
                    'status_item_value_700', (
                        SELECT count(*) FROM t_convention_attendee as t_convention_attendee_sub
                        JOIN t_convention_attendee_status_detail as t_convention_attendee_status_detail_sub
                        ON t_convention_attendee_status_detail_sub.convention_attendee_id = t_convention_attendee_sub.convention_attendee_id
                        WHERE t_convention_attendee_sub.convention_id = t_convention_sub.convention_id
                        AND t_convention_attendee_sub.person_cd = t_convention_attendee.person_cd
                        AND t_convention_attendee_sub.other_person_flag = 0
                        AND status_item_cd = :status_item_cd_700
                        AND status_item_value = 1
                    )
                )),']')
            FROM t_convention as t_convention_sub
            JOIN t_schedule ON t_schedule.schedule_id = t_convention_sub.schedule_id
            WHERE t_convention.series_convention_id = t_convention_sub.series_convention_id
        ) AS series_convention
        FROM m_facility_user
        JOIN t_convention_attendee ON t_convention_attendee.facility_cd = m_facility_user.facility_cd
        JOIN t_convention ON t_convention.convention_id = t_convention_attendee.convention_id
        JOIN t_schedule ON t_schedule.schedule_id = t_convention.schedule_id";
        //出席予定者のみ
        if ($conditions->attendance != 0) {
            $sql .= " JOIN t_convention_attendee_status_detail ON t_convention_attendee_status_detail.convention_attendee_id = t_convention_attendee.convention_attendee_id
                        AND t_convention_attendee_status_detail.status_item_cd = '300' 
                        AND t_convention_attendee_status_detail.status_item_value = '10'";
        }
        $sql .= " WHERE t_convention_attendee.convention_id = :convention_id";
        //未フォローのみ
        if ($conditions->unfollow != 0) {
            $sql .= " AND (SELECT
                CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                    'start_date', t_schedule.start_date
                )),']')
                FROM t_schedule
                JOIN t_visit ON t_visit.schedule_id = t_schedule.schedule_id
                JOIN t_call ON t_call.visit_id = t_visit.visit_id
                WHERE CAST(t_schedule.start_date AS DATE) >= :convention_start_date AND CAST(t_schedule.start_date AS DATE) <= :convention_start_date_add_convention_follow_up
                AND t_call.person_cd = t_convention_attendee.person_cd) IS NULL";
            $condition['convention_start_date'] = $conditions->convention_start_date;
            $condition['convention_start_date_add_convention_follow_up'] = $conditions->convention_start_date_add_convention_follow_up;
        }
        $condition['convention_id'] = $conditions->convention_id;
        if (!empty($conditions->user_cd)) {
            $sql .= " AND m_facility_user.user_cd IN " . $this->_buildCommaString($conditions->user_cd, true);
        }
        $sql .= " GROUP BY t_convention_attendee.convention_attendee_id
            ORDER BY 
                t_convention_attendee.facility_name_kana,
                t_convention_attendee.department_cd,
                t_convention_attendee.display_position_cd,
                t_convention_attendee.person_name_kana";
        $condition['schedule_type'] = SCHEDULE_TYPE_INTERVIEW;
        $condition['convention_start_date_sub'] = $conditions->convention_start_date;
        $condition['convention_start_date_add_convention_follow_up_sub'] = $conditions->convention_start_date_add_convention_follow_up;
        $condition['status_item_cd_200'] = $conditions->status_item_cd_200;
        $condition['status_item_cd_700'] = $conditions->status_item_cd_700;
        return $this->_paginate($sql, $condition, [
            "limit" => $conditions->limit,
            "offset" => $conditions->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getConventionAttende($convention_id, $person_cd)
    {
        $sql = "SELECT convention_attendee_id, convention_id, person_cd FROM t_convention_attendee 
        WHERE convention_id = :convention_id
        AND person_cd = :person_cd";
        return $this->_first($sql, [
            'convention_id' => $convention_id,
            'person_cd' => $person_cd
        ]);
    }

    public function getConventionAttendeeStatusDetail($convention_attendee_id)
    {
        $sql = "SELECT COUNT(*) as record FROM t_convention_attendee_status_detail WHERE convention_attendee_id = :convention_attendee_id";
        return $this->_first($sql, ['convention_attendee_id' => $convention_attendee_id]);
    }

    public function getStartDateConventionSchedule($convention_id)
    {
        $sql = "SELECT t_schedule.start_date
        FROM t_convention
        JOIN t_schedule ON t_schedule.schedule_id = t_convention.schedule_id
        WHERE t_convention.convention_id = :convention_id;";
        return $this->_first($sql, ['convention_id' => $convention_id]);
    }
}
