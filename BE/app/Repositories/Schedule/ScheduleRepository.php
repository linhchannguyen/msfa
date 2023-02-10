<?php

namespace App\Repositories\Schedule;

use App\Repositories\BaseRepository;
use App\Repositories\Schedule\ScheduleRepositoryInterface;
use App\Traits\StringConvertTrait;

class ScheduleRepository extends BaseRepository implements ScheduleRepositoryInterface
{
    use StringConvertTrait;
    protected $useAutoMeta = true;

    public function getScheduleListByUserLogin($conditions)
    {
        $sql = "SELECT
            t_schedule.schedule_id,
            t_schedule.schedule_type,
            t_schedule.start_date,
            t_schedule.start_time,
            t_schedule.end_time,
            t_schedule.title,
            t_schedule.all_day_flag,
            t_schedule.display_option_type,
            m_display_option.display_option_name,
            m_display_option.background_color,
            m_display_option.icon,
            m_display_option.frame_border_color,
            t_schedule.user_cd
        FROM
            t_schedule
        JOIN m_display_option ON m_display_option.display_option_type = t_schedule.display_option_type
        LEFT JOIN t_schedule_common_subject ON t_schedule_common_subject.user_cd = t_schedule.user_cd
                OR t_schedule.user_cd IN (SELECT m_user_org.user_cd FROM m_user_org WHERE m_user_org.org_cd = t_schedule_common_subject.org_cd)
        WHERE t_schedule.non_display_flag = 0 AND (t_schedule.start_date BETWEEN :schedule_date_from AND :schedule_date_to)
            AND t_schedule.user_cd = :user_cd
        GROUP BY t_schedule.schedule_id
        ORDER BY t_schedule.start_date ASC";
        return $this->_find($sql, $conditions);
    }

    public function getStatusReport($schedule_id, $start_date = null)
    {
        $sql = "SELECT t_report_detail.report_id
        FROM t_schedule
        JOIN t_report_detail ON t_report_detail.report_detail_type = t_schedule.schedule_type
            AND t_report_detail.report_detail_id = t_schedule.schedule_id
        JOIN t_report ON t_report.report_id = t_report_detail.report_id
        WHERE t_schedule.schedule_id = :schedule_id
            AND t_report.report_period_start_date = :start_date";
        $condition['start_date'] = $start_date;
        $condition['schedule_id'] = $schedule_id;
        return $this->_first($sql, $condition);
    }

    public function getStatusReportListAdd($conditions)
    {
        $sql = "SELECT t_report.report_id
        FROM t_report
        WHERE t_report.report_period_start_date <= :start_date
        AND t_report.report_period_end_date >= :end_date
        AND t_report.user_cd = :user_cd";
        return $this->_find($sql, $conditions);
    }

    public function getStatusReportListEdit($conditions)
    {
        $sql = "SELECT t_report.report_id
        FROM t_report
        WHERE (
            (t_report.report_period_start_date <= :start_date AND t_report.report_period_end_date >= :end_date)
            OR
            (t_report.report_period_start_date <= :schedule_start_date_old AND t_report.report_period_end_date >= :schedule_end_date_old)
        )
        AND t_report.user_cd = :user_cd";
        return $this->_find($sql, $conditions);
    }

    public function getVisitByScheduleId($schedule_id)
    {
        $sql = "SELECT
            important_flag,
            facility_cd,
            facility_short_name,
            user_cd,
            user_name,
            org_cd,
            org_short_name,
            remarks,
            visit_id
        FROM t_visit
        WHERE schedule_id = :schedule_id";
        return $this->_find($sql, ['schedule_id' => $schedule_id]);
    }

    public function getCallByVisitId($visit_id)
    {
        $sql = "SELECT
            person_cd,
            plan_flag,
            act_flag,
            person_name,
            department_cd,
            department_name,
            display_position_name,
            off_label_flag,
            off_label_concent_flag,
            off_label_call_time,
            related_product,
            question,
            answer,
            re_question,
            literature
        FROM t_call 
        WHERE visit_id = :visit_id";
        return $this->_find($sql, ['visit_id' => $visit_id]);
    }

    public function getVisitList($conditions)
    {
        $condition = [];
        $sql = "SELECT 
            m_facility_user.user_cd,
            m_facility_user.facility_cd,
            m_facility_person.person_cd,
            m_facility.facility_short_name,
            m_person.person_name,
            m_department.department_cd,
            m_department.department_name,
            m_position.position_cd,
            m_position.position_name
            FROM m_facility_user
            JOIN m_facility_person ON m_facility_person.facility_cd = m_facility_user.facility_cd
            JOIN m_facility ON m_facility.facility_cd = m_facility_user.facility_cd
            JOIN m_person ON m_person.person_cd = m_facility_person.person_cd
            LEFT JOIN m_department ON m_department.department_cd = m_facility_person.department_cd
            LEFT JOIN m_position ON m_position.position_cd = m_facility_person.display_position_cd";
        if (!empty($conditions['segment_type'])) {
            $sql .= " JOIN t_facility_person_segment ON t_facility_person_segment.person_cd = m_facility_person.person_cd AND t_facility_person_segment.facility_cd = m_facility_person.facility_cd";
        }
        if (!empty($conditions['target_product_cd'])) {
            $sql .= " JOIN m_target_facility_person  ON m_target_facility_person.person_cd = m_facility_person.person_cd AND m_target_facility_person.facility_cd = m_facility_person.facility_cd";
        }
        $sql_condition = "";
        if (!empty($conditions['user_cd'])) {
            $sql_condition .= " AND m_facility_user.user_cd IN " . $this->_buildCommaString($conditions['user_cd'], true);
        }
        if (!empty($conditions['person_name'])) {
            $sql_condition .= " AND m_person.person_name LIKE :person_name";
            $condition['person_name'] = '%' . trim($this->convert_kana($conditions['person_name'])) . '%';
        }
        if (!empty($conditions['facility_name'])) {
            $sql_condition .= " AND m_facility.facility_short_name LIKE :facility_name";
            $condition['facility_name'] = '%' . trim($this->convert_kana($conditions['facility_name'])) . '%';
        }
        if (!empty($conditions['segment_type'])) {
            $sql_condition .= " AND t_facility_person_segment.segment_type = :segment_type";
            $condition['segment_type'] = $conditions['segment_type'];
        }
        if (!empty($conditions['target_product_cd'])) {
            $sql_condition .= " AND m_target_facility_person.target_product_cd = :target_product_cd";
            $condition['target_product_cd'] = $conditions['target_product_cd'];
        }
        if ($sql_condition != "") {
            $sql_condition = " WHERE" . substr($sql_condition, 4);
        }
        $sql .= $sql_condition;
        $sql .= " GROUP BY m_facility_person.facility_cd, m_facility_person.person_cd
            ORDER BY m_facility.facility_name_kana, m_department.department_cd, m_facility_person.hospital_position_cd, m_person.person_name_kana";
        return $this->_paginate($sql, $condition, [
            "limit" => $conditions['limit'],
            "offset" => $conditions['offset'],
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function allFacilityPersonSegmentType()
    {
        $sql = "SELECT segment_type, segment_name FROM m_facility_person_segment_type";
        return $this->_all($sql);
    }

    public function getStockList($conditions)
    {
        $condition = [];
        $sql = "SELECT
            t_stock.stock_id,
            (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                'document_id', t_document.document_id,
                'document_name', t_document.document_name,
                'document_type', t_document.document_type,
                'file_type', t_document.file_type
                )),']') FROM t_stock_document 
                JOIN t_document ON t_document.document_id = t_stock_document.document_id
                WHERE t_stock_document.stock_id = t_stock.stock_id) AS document_list,
            (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                'product_cd', m_product.product_cd,
                'product_name', m_product.product_name
                )),']') FROM t_stock_product 
                JOIN m_product ON m_product.product_cd = t_stock_product.product_cd 
                WHERE t_stock_product.stock_id = t_stock.stock_id) AS product_list,
            t_stock.facility_cd,
            m_facility.facility_category_type,
            m_facility.facility_short_name,
            m_facility_person.display_position_cd,
            m_department.department_cd,
            m_department.department_name,
            t_stock.person_cd,
            m_person.person_name,
            t_stock.content_cd,
            m_content.content_name,
            t_stock.status_type,
            t_stock.stock_type,
            t_stock.action_id,
            t_stock.stock_date,
            m_position.position_name
        FROM
            t_stock
            JOIN m_facility ON m_facility.facility_cd = t_stock.facility_cd
            LEFT JOIN m_facility_person ON m_facility_person.facility_cd = t_stock.facility_cd AND m_facility_person.person_cd = t_stock.person_cd
            LEFT JOIN m_position ON m_facility_person.display_position_cd = m_position.position_cd
            LEFT JOIN m_department ON m_department.department_cd = m_facility_person.department_cd
            LEFT JOIN m_person ON m_person.person_cd = t_stock.person_cd
            LEFT JOIN m_content ON m_content.content_cd = t_stock.content_cd";
        $sql .= " WHERE status_type = :status_type AND t_stock.created_by = :user_cd";
        $condition['status_type'] = $conditions['status_type'];
        $condition['user_cd'] = $conditions['user_cd'];
        $sql .= " GROUP BY t_stock.stock_id ORDER BY t_stock.stock_id DESC";
        return $this->_paginate($sql, $condition, [
            "limit" => $conditions['limit'],
            "offset" => $conditions['offset'],
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function selectFacilityGroup($user_cd)
    {
        $sql = "SELECT select_facility_group_id, user_cd, select_facility_group_name FROM t_select_facility_group WHERE user_cd = :user_cd";
        return $this->_find($sql, ['user_cd' => $user_cd]);
    }

    public function selectPersonGroup($user_cd)
    {
        $sql = "SELECT select_person_group_id, user_cd, select_person_group_name FROM t_select_person_group WHERE user_cd = :user_cd";
        return $this->_find($sql, ['user_cd' => $user_cd]);
    }

    public function selectDisplayOption()
    {
        $sql = "SELECT display_option_type, display_option_name, background_color, icon, frame_border_color FROM m_display_option";
        return $this->_all($sql);
    }

    public function getFacilityGroupList($conditions)
    {
        $condition = [];
        $sql = "SELECT
        t_select_facility_group_detail.facility_cd,
        m_facility.facility_short_name
        FROM t_select_facility_group_detail
        JOIN m_facility ON m_facility.facility_cd = t_select_facility_group_detail.facility_cd";
        if ($conditions['select_facility_group_id'] != "") {
            $sql .= " WHERE t_select_facility_group_detail.select_facility_group_id = :select_facility_group_id";
            $condition['select_facility_group_id'] = $conditions['select_facility_group_id'];
        }
        $sql .= " ORDER BY m_facility.facility_category_type, m_facility.facility_name_kana";
        return $this->_paginate($sql, $condition, [
            "limit" => $conditions['limit'],
            "offset" => $conditions['offset'],
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getPersonGroupList($conditions)
    {
        $condition = [];
        $sql = "SELECT
        t_select_person_group_detail.facility_cd,
        m_facility.facility_short_name,
        m_facility.facility_category_type,
        t_select_person_group_detail.person_cd,
        m_person.person_name,
        m_facility_person.department_cd,
        m_department.department_name,
        m_facility_person.display_position_cd,
        m_position.position_name,
        (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
            'content_cd', m_content.content_cd,
            'content_name', m_content.content_name
            )),']') FROM t_select_person_group_content 
            JOIN m_content ON m_content.content_cd = t_select_person_group_content.content_cd 
            WHERE t_select_person_group_content.select_person_group_id = t_select_person_group_detail.select_person_group_id
                AND t_select_person_group_content.facility_category_type = m_facility.facility_category_type) AS content_list,
        (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
            'product_cd', m_product.product_cd,
            'product_name', m_product.product_name
            )),']') FROM t_select_person_group_product 
            JOIN m_product ON m_product.product_cd = t_select_person_group_product.product_cd
            WHERE t_select_person_group_product.select_person_group_id = t_select_person_group_detail.select_person_group_id
                AND t_select_person_group_product.facility_category_type = m_facility.facility_category_type) AS product_list,
        (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
            'document_id', t_document.document_id,
            'document_name', t_document.document_name,
            'document_type', t_document.document_type,
            'file_type', t_document.file_type
            )),']') FROM t_select_person_group_document
            JOIN t_document ON t_document.document_id = t_select_person_group_document.document_id
            WHERE t_select_person_group_document.select_person_group_id = t_select_person_group_detail.select_person_group_id
                AND t_select_person_group_document.facility_category_type = m_facility.facility_category_type) AS document_list
        FROM t_select_person_group_detail
        JOIN m_facility_person ON m_facility_person.facility_cd = t_select_person_group_detail.facility_cd AND m_facility_person.person_cd = t_select_person_group_detail.person_cd
        JOIN m_facility ON m_facility.facility_cd = m_facility_person.facility_cd
        JOIN m_person ON m_person.person_cd = m_facility_person.person_cd
        LEFT JOIN m_department ON m_department.department_cd = m_facility_person.department_cd
        LEFT JOIN m_position ON m_position.position_cd = m_facility_person.display_position_cd";
        if ($conditions['select_person_group_id'] != "") {
            $sql .= " WHERE t_select_person_group_detail.select_person_group_id = :select_person_group_id";
            $condition['select_person_group_id'] = $conditions['select_person_group_id'];
        }
        $sql .= " GROUP BY t_select_person_group_detail.facility_cd, t_select_person_group_detail.person_cd
        ORDER BY m_facility.facility_category_type, m_facility.facility_name_kana, m_facility_person.department_cd, m_facility_person.display_position_cd, m_person.person_name";
        return $this->_paginate($sql, $condition, [
            "limit" => $conditions['limit'],
            "offset" => $conditions['offset'],
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function addSchedule($data)
    {
        $sql = "INSERT INTO t_schedule (
                schedule_type,
                start_date,
                start_time,
                end_time,
                title,
                all_day_flag,
                display_option_type,
                user_cd
            ) 
            VALUES (
                :schedule_type,
                :start_date,
                :start_time,
                :end_time,
                :title,
                :all_day_flag,
                :display_option_type,
                :user_cd
            );";
        return $this->_create($sql, $data);
    }

    public function addVisit($data)
    {
        $sql = "INSERT INTO t_visit (
                schedule_id,
                important_flag,
                facility_cd,
                facility_short_name,
                user_cd,
                user_name,
                implement_user_post_name,
                implement_user_post,
                org_cd,
                org_short_name,
                remarks
            ) 
            VALUES (
                :schedule_id,
                :important_flag,
                :facility_cd,
                :facility_short_name,
                :user_cd,
                :user_name,
                :implement_user_post_name,
                :implement_user_post,
                :org_cd,
                :org_short_name,
                :remarks
            );";
        return $this->_create($sql, $data);
    }

    public function addCall($data)
    {
        $sql = "INSERT INTO t_call (
                visit_id,
                person_cd,
                plan_flag,
                act_flag,
                person_name,
                department_cd,
                department_name,
                display_position_name,
                off_label_flag,
                off_label_concent_flag,
                off_label_call_time,
                related_product,
                question,
                answer,
                re_question,
                literature
            ) 
            VALUES (
                :visit_id,
                :person_cd,
                :plan_flag,
                :act_flag,
                :person_name,
                :department_cd,
                :department_name,
                :display_position_name,
                :off_label_flag,
                :off_label_concent_flag,
                :off_label_call_time,
                :related_product,
                :question,
                :answer,
                :re_question,
                :literature
            );";
        return $this->_create($sql, $data);
    }

    public function addDetail($data)
    {
        $sql = "INSERT INTO t_detail (
                call_id,
                detail_order,
                content_cd,
                content_name_tran,
                product_cd,
                product_name
            ) 
            VALUES (
                :call_id,
                :detail_order,
                :content_cd,
                :content_name_tran,
                :product_cd,
                :product_name
            );";
        return $this->_create($sql, $data);
    }

    public function addDetailDocument($data)
    {
        $sql = "INSERT INTO t_detail_document (
                detail_id,
                document_id
            ) 
            VALUES :values";
        return $this->_bulkCreate($sql, $data);
    }

    public function lastInsertedSchedule()
    {
        return $this->_lastInserted('t_schedule', 'schedule_id');
    }

    public function lastInsertedVisit()
    {
        return $this->_lastInserted('t_visit', 'visit_id');
    }

    public function lastInsertedCall()
    {
        return $this->_lastInserted('t_call', 'call_id');
    }

    public function lastInsertedDetail()
    {
        return $this->_lastInserted('t_detail', 'detail_id');
    }

    public function updateSchedule($data)
    {
        $sql = "UPDATE t_schedule SET
                start_date = :start_date,
                start_time = :start_time,
                end_time = :end_time,
                all_day_flag = :all_day_flag
            WHERE schedule_id = :schedule_id;";
        return $this->_update($sql, $data);
    }

    public function updateScheduleConvention($data)
    {
        $sql = "UPDATE t_schedule SET
                start_date = :start_date,
                start_time = :start_time,
                end_time = :end_time,
                all_day_flag = :all_day_flag,
                title = :title
            WHERE schedule_id = :schedule_id;";
        return $this->_update($sql, $data);
    }

    public function updateScheduleTitle($data)
    {
        $sql = "UPDATE t_schedule SET
                title = :title
            WHERE schedule_id = :schedule_id;";
        return $this->_update($sql, $data);
    }

    public function addOfficeSchedule($data)
    {
        $sql = "INSERT INTO t_office_schedule (
                schedule_id,
                office_schedule_type,
                title
            ) 
            VALUES (
                :schedule_id,
                :office_schedule_type,
                :title
            );";
        return $this->_create($sql, $data);
    }

    public function addPrivate($data)
    {
        $sql = "INSERT INTO t_private (
                schedule_id,
                title
            ) 
            VALUES (
                :schedule_id,
                :title
            );";
        return $this->_create($sql, $data);
    }

    public function getScheduleById($schedule_id)
    {
        $sql = "SELECT
            t_schedule.start_date
        FROM
            t_schedule
        WHERE t_schedule.schedule_id = :schedule_id";
        return $this->_first($sql, ['schedule_id' => $schedule_id]);
    }

    public function getBriefingStatusFromSchedule($schedule_id)
    {
        $sql = "SELECT
            t_briefing.status_type
        FROM
            t_schedule
        JOIN t_briefing ON t_briefing.schedule_id = t_schedule.schedule_id
        WHERE t_schedule.schedule_id = :schedule_id";
        return $this->_first($sql, [
            'schedule_id' => $schedule_id
        ]);
    }

    public function getConventionStatusFromSchedule($schedule_id)
    {
        $sql = "SELECT
            t_convention.status_type
        FROM
            t_schedule
        JOIN t_convention ON t_convention.schedule_id = t_schedule.schedule_id
        WHERE t_schedule.schedule_id = :schedule_id";
        return $this->_first($sql, [
            'schedule_id' => $schedule_id
        ]);
    }

    public function checkCallUsageInSchedule($schedule_id)
    {
        $sql = "SELECT
            COUNT( * ) AS record 
        FROM
            t_schedule
            JOIN t_visit ON t_visit.schedule_id = t_schedule.schedule_id
            JOIN t_call ON t_call.visit_id = t_visit.visit_id
            JOIN t_knowledge_timeline ON t_knowledge_timeline.action_id = t_call.call_id 
        WHERE
            t_schedule.schedule_id = :action_id
            AND t_knowledge_timeline.channel_type = :channel_type";
        return $this->_first($sql, [
            'action_id' => $schedule_id,
            'channel_type' => SCHEDULE_TYPE_INTERVIEW
        ]);
    }
}
