<?php

namespace App\Repositories\InterviewDetails;

use App\Repositories\BaseRepository;

class InterviewDetailsRepository extends BaseRepository implements InterviewDetailsRepositoryInterface
{
    protected $useAutoMeta = true;

    public function getActiveDate($schedule_id)
    {
        $sql = "
            SELECT 
                t_visit.important_flag,
                t_schedule.start_date,
                t_schedule.start_time,
                t_schedule.end_time,
                t_schedule.all_day_flag,
                m_facility.facility_short_name,
                t_schedule.schedule_type,
                t_schedule.schedule_id,
                t_schedule.display_option_type,
                t_visit.visit_id,
                m_variable_definition.definition_label,
                t_visit.facility_cd,
                t_report_detail.report_id
            FROM t_schedule
            INNER JOIN t_visit
                ON t_visit.schedule_id = t_schedule.schedule_id 
            INNER JOIN m_facility
                ON t_visit.facility_cd = m_facility.facility_cd
            INNER JOIN m_variable_definition
                ON (m_variable_definition.definition_value = t_schedule.schedule_type AND m_variable_definition.definition_name = 'スケジュール区分' AND m_variable_definition.definition_label = '面談')
            LEFT JOIN t_report_detail
                ON (t_report_detail.report_detail_type  = t_schedule.schedule_type AND t_report_detail.report_detail_id = t_schedule.schedule_id)
            WHERE t_schedule.schedule_id = :schedule_id ;";

        return $this->_first($sql, ['schedule_id' => $schedule_id]);
    }

    public function getDepartmentInfo($schedule_id)
    {
        $sql = "
            SELECT 
                m_department.department_name,
                t_call.person_name,
                t_call.person_cd,
                m_position.position_name,
                t_call.plan_flag,
                t_call.act_flag,
                t_call.call_id,
                t_visit.facility_cd,
                t_visit.visit_id,
                t_visit.user_cd,
                t_stock.stock_id
            FROM t_visit
            INNER JOIN t_call
                ON t_visit.visit_id = t_call.visit_id
            LEFT JOIN m_facility_person
                ON t_visit.facility_cd = m_facility_person.facility_cd
            LEFT JOIN m_department
                ON m_department.department_cd = m_facility_person.department_cd    
            LEFT JOIN m_person
                ON m_person.person_cd = t_call.person_cd
            LEFT JOIN m_position
                ON m_facility_person.display_position_cd = m_position.position_cd
            LEFT JOIN t_stock
                ON t_stock.person_cd = m_person.person_cd
            WHERE t_visit.schedule_id = :schedule_id
            GROUP BY t_call.department_name,t_call.person_name,t_call.display_position_name,t_call.plan_flag,t_call.act_flag
            ORDER BY t_call.act_flag ASC , m_department.department_cd ASC , m_position.position_cd ASC ,m_person.person_name_kana ASC;
        ";

        return $this->_find($sql, ['schedule_id' => $schedule_id]);
    }

    public function getContent($call_id)
    {
        $sql = "
            SELECT
                t_detail.detail_id,
                t_detail.detail_order,
                m_content.content_name,
                m_product.product_name
            FROM t_detail
            LEFT JOIN m_content
                ON m_content.content_cd = t_detail.content_cd
            LEFT JOIN m_product
                ON m_product.product_cd = t_detail.product_cd
            WHERE t_detail.call_id = :call_id
            GROUP BY t_detail.detail_order,m_content.content_name,m_product.product_name
            ORDER BY t_detail.detail_order ASC;";

        return $this->_find($sql, ['call_id' => $call_id]);
    }

    public function getDetailDocument($detail_id)
    {
        $sql = "
            SELECT 
                t_detail_document.document_id,
                t_document.start_date,
                t_document.end_date,
                t_document.document_name
            FROM t_detail_document
            INNER JOIN t_detail
                ON t_detail_document.detail_id = t_detail.detail_id
            INNER JOIN t_document
                ON t_document.document_id = t_detail_document.document_id 
            WHERE t_detail_document.detail_id = :detail_id
            ORDER BY t_detail_document.document_id ASC;";

        return $this->_find($sql, ['detail_id' => $detail_id]);
    }

    public function getDateTimeSetting($schedule_id)
    {
        $sql = "
            SELECT 
                t_schedule.start_date,
                t_schedule.start_time,
                t_schedule.end_time,
                t_schedule.all_day_flag,
                t_visit.important_flag,
                t_visit.visit_id,
                t_schedule.user_cd as user_cd,
                t_visit.remarks
            FROM t_schedule
            LEFT JOIN t_visit
                ON t_visit.schedule_id = t_schedule.schedule_id
            INNER JOIN m_variable_definition
                ON (m_variable_definition.definition_value = t_schedule.schedule_type AND m_variable_definition.definition_name = 'スケジュール区分' AND m_variable_definition.definition_label = '面談')
            WHERE t_schedule.schedule_id = :schedule_id
            GROUP BY t_schedule.start_date,t_visit.visit_id,t_schedule.schedule_id;";

        return $this->_first($sql, ['schedule_id' => $schedule_id]);
    }


    public function getAccompanyingUserDateTime($visit_id)
    {
        $sql = "
            SELECT
                m_org.org_label,
                m_org.org_cd,
                m_org.org_short_name,
                t_accompanying_user.user_cd,
                t_accompanying_user.accompanying_id,
                t_accompanying_user.user_name
            FROM 
                t_accompanying_user
            INNER JOIN t_visit
                ON t_accompanying_user.visit_id = t_visit.visit_id
            INNER JOIN m_user
                ON m_user.user_cd = t_accompanying_user.user_cd
            INNER JOIN m_org
                ON m_org.org_cd = t_accompanying_user.org_cd
            WHERE t_accompanying_user.visit_id = :visit_id;";

        return $this->_find($sql, ['visit_id' => $visit_id]);
    }

    public function getTitleOffice()
    {
        $sql = "SELECT office_schedule_type,office_schedule_type_name,title_free_flag FROM  m_office_schedule_type ORDER BY sort_order ASC;";
        return $this->_find($sql, []);
    }


    public function updateScheduleTitle($schedule_id, $title, $display_option_type)
    {
        $sql = "UPDATE t_schedule SET title = :title , display_option_type = :display_option_type WHERE schedule_id = :schedule_id;";
        return $this->_update($sql, ['title' => $title, 'display_option_type' => $display_option_type, 'schedule_id' => $schedule_id]);
    }

    public function deleteSchedule($schedule_id)
    {
        $sql = "DELETE t_schedule, t_schedule_common_subject, t_office_schedule, t_private, t_visit, t_accompanying_user, t_call, t_detail, t_detail_document, t_document_usage_situation, t_document_review
                FROM t_schedule 
                LEFT JOIN t_schedule_common_subject
                    ON t_schedule_common_subject.schedule_id = t_schedule.schedule_id
                LEFT JOIN t_office_schedule
                    ON t_office_schedule.schedule_id = t_schedule.schedule_id
                LEFT JOIN t_private
                    ON t_private.schedule_id = t_schedule.schedule_id
                LEFT JOIN t_visit 
                    ON t_visit.schedule_id  = t_schedule.schedule_id
                LEFT JOIN t_accompanying_user
                    ON t_accompanying_user.visit_id = t_visit.visit_id
                LEFT JOIN t_call
                    ON t_call.visit_id = t_visit.visit_id
                LEFT JOIN t_detail
                    ON t_detail.call_id = t_call.call_id
                LEFT JOIN t_detail_document
                    ON t_detail_document.detail_id = t_detail.detail_id
                LEFT JOIN t_document_usage_situation
                    ON t_document_usage_situation.document_usage_id = t_call.call_id AND t_document_usage_situation.document_usage_type = 0
                LEFT JOIN t_document_review
                    ON t_document_review.usage_situation_id = t_document_usage_situation.usage_situation_id
                WHERE t_schedule.schedule_id = :schedule_id;";

        return $this->_destroy($sql, ['schedule_id' => $schedule_id]);
    }

    public function deleteAllInterviewContent($visit_id, $call_id)
    {
        $param = [];
        $sql = "
            DELETE t_call , t_detail , t_detail_document
            FROM t_call 
            LEFT JOIN t_detail
                ON t_detail.call_id = t_call.call_id
            LEFT JOIN t_detail_document
                ON t_detail_document.detail_id = t_detail.detail_id
            WHERE 1 = 1 ";

        if (!empty($visit_id)) {
            $sql .= " AND t_call.visit_id = :visit_id";
            $param['visit_id'] = $visit_id;
        }

        if (!empty($call_id)) {
            $sql .= " AND t_call.call_id = :call_id";
            $param['call_id'] = $call_id;
        }

        $sql .= ";";

        return $this->_destroy($sql, $param);
    }

    public function getTCall($visit_id)
    {
        // t_call
        $sql = "
        SELECT 
            call_id,
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
            related_product,
            question,
            answer,
            re_question,
            literature,
            off_label_call_time
         FROM t_call 
         WHERE visit_id = :visit_id;";
        return $this->_find($sql, ['visit_id' => $visit_id]);
    }

    public function deleteDetailDocument($detail_id)
    {
        // t_detail_document
        $sql = "DELETE FROM t_detail_document WHERE detail_id IN " . $this->_buildCommaString($detail_id, true) . ";";
        $this->_destroy($sql, []);
    }

    public function getVisit($schedule_id, $visit_id)
    {
        $sql = "
            SELECT 
                visit_id,
                schedule_id,
                important_flag,
                facility_cd,
                facility_short_name,
                user_cd,
                user_name,
                org_cd,
                org_short_name,
                remarks
            FROM t_visit 
            WHERE schedule_id = :schedule_id 
            AND visit_id = :visit_id;";
        return $this->_first($sql, ['schedule_id' => $schedule_id, 'visit_id' => $visit_id]);
    }

    // public function getCall($visit_id)
    // {
    //     $sql = "SELECT visit_id,plan_flag,act_flag FROM t_call WHERE visit_id = :visit_id;";
    //     return $this->_find($sql, ['visit_id' => $visit_id]);
    // }

    public function saveSchedule($schedule_id, $facility_short_name, $visit_id, $start_date, $start_time, $end_time, $all_day_flag, $title, $display_option_type)
    {
        $sql = "
            UPDATE t_schedule
            SET 
                title = :title,
                display_option_type = :display_option_type,
                start_date = :start_date,
                start_time = :start_time,
                end_time = :end_time,
                all_day_flag = :all_day_flag
            WHERE schedule_id = :schedule_id;";

        return $this->_update($sql, [
            'title' => $title,
            'display_option_type' => $display_option_type,
            'start_date' => $start_date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'all_day_flag' => $all_day_flag,
            'schedule_id' => $schedule_id
        ]);
    }

    public function saveVisit($visit_id, $schedule_id, $user_name, $org_cd, $org_short_name, $important_flag, $remarks)
    {
        $sql = "
            UPDATE 
                t_visit
            SET user_name = :user_name,
                org_cd = :org_cd,
                org_short_name = :org_short_name,
                important_flag = :important_flag,
                remarks = :remarks
            WHERE visit_id = :visit_id
            AND schedule_id = :schedule_id;";

        return $this->_update($sql, [
            'user_name' => $user_name,
            'org_cd' => $org_cd,
            'org_short_name' => $org_short_name,
            'important_flag' => $important_flag,
            'visit_id' => $visit_id,
            'schedule_id' => $schedule_id,
            'remarks' => $remarks
        ]);
    }

    public function checkAccompanyingUser($visit_id, $user_cd)
    {
        $sql = "SELECT count(*) as sum  FROM t_accompanying_user WHERE visit_id = :visit_id AND user_cd = :user_cd ;";
        $t_accompanying_user = $this->_first($sql, ['visit_id' => $visit_id, 'user_cd' => $user_cd]);
        return $t_accompanying_user->sum ?? 0;
    }

    public function getAccompanyingUser($visit_id)
    {
        $sql = "SELECT accompanying_id,visit_id,user_cd,user_name,org_cd,org_short_name FROM t_accompanying_user WHERE visit_id = :visit_id;";
        return $this->_find($sql, ['visit_id' => $visit_id]);
    }

    public function saveAccompanyingUser($visit_id, $user_cd, $user_name, $org_cd, $org_short_name)
    {
        $sql = "INSERT INTO t_accompanying_user (visit_id,user_cd,user_name,org_cd,org_short_name) VALUES (:visit_id,:user_cd,:user_name,:org_cd,:org_short_name);";

        return $this->_create($sql, [
            'visit_id' => $visit_id,
            'user_cd' => $user_cd,
            'user_name' => $user_name,
            'org_cd' => $org_cd,
            'org_short_name' => $org_short_name
        ]);
    }

    public function deleteAccompanyingUser($visit_id)
    {
        $sql = "DELETE FROM t_accompanying_user WHERE visit_id = :visit_id;";
        return $this->_destroy($sql, ['visit_id' => $visit_id]);
    }

    public function getNumberPerson($schedule_id)
    {
        $sql = "SELECT COUNT(t_call.person_cd) as sum FROM t_visit INNER JOIN t_call ON t_call.visit_id = t_visit.visit_id WHERE t_visit.schedule_id = :schedule_id;";
        $t_visit = $this->_first($sql, ['schedule_id' => $schedule_id]);
        return $t_visit->sum ?? 0;
    }

    public function getTitlePerson($schedule_id)
    {
        $sql = "SELECT t_call.person_name FROM t_visit INNER JOIN t_call ON t_call.visit_id = t_visit.visit_id WHERE t_visit.schedule_id = :schedule_id;";
        $t_visit = $this->_first($sql, ['schedule_id' => $schedule_id]);
        return $t_visit->person_name ?? '';
    }

    public function getInternalSchedule($schedule_id)
    {
        $sql = "
            SELECT 
                t_office_schedule.office_schedule_type,
                t_office_schedule.title,
                DATE_FORMAT(t_schedule.start_date,'%Y/%m/%d') as start_date,
                DATE_FORMAT(t_schedule.start_time,'%Y/%m/%d %H:%i:%s') as start_time,
                DATE_FORMAT(t_schedule.end_time,'%Y/%m/%d %H:%i:%s') as end_time,
                t_schedule.all_day_flag,
                t_office_schedule.remarks,
                t_office_schedule.office_schedule_id,
                t_schedule.user_cd
            FROM t_schedule 
            INNER JOIN t_office_schedule
                ON t_office_schedule.schedule_id = t_schedule.schedule_id
            WHERE t_schedule.schedule_id = :schedule_id";

        return $this->_first($sql, ['schedule_id' => $schedule_id]);
    }

    public function updateInternalSchedule($schedule_id, $title, $office_schedule_type, $start_date, $start_time, $end_time, $all_day_flag)
    {
        $title_new = $title;
        $office = $this->getOfficeScheduleType($office_schedule_type);
        $title_free_flag = $office->title_free_flag ?? "";
        if($office_schedule_type){
            $office_name = $office->office_schedule_type_name ?? '';
            if($title_free_flag){
                $title_new = $office_name . $title;
            }else{
                $title_new = $office_name;
            }
        }
        $sql = "
            UPDATE t_schedule
                SET title = :title,
                    start_date = :start_date,
                    start_time = :start_time,
                    end_time = :end_time,
                    all_day_flag = :all_day_flag
            WHERE schedule_id = :schedule_id;";

        return $this->_update($sql, [
            'schedule_id' => $schedule_id,
            'title' => $title_new,
            'start_date' => $start_date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'all_day_flag' => $all_day_flag
        ]);
    }

    public function getOfficeScheduleType($office_schedule_type)
    {
        $sql = "SELECT office_schedule_type, office_schedule_type_name, title_free_flag FROM m_office_schedule_type WHERE office_schedule_type = :office_schedule_type;";
        return $this->_first($sql, ['office_schedule_type' => $office_schedule_type]);
    }

    public function updateOfficeSchedule($schedule_id, $title, $office_schedule_type, $remarks)
    {
        $sql = "UPDATE t_office_schedule
                    SET title = :title,
                        office_schedule_type = :office_schedule_type,
                        remarks = :remarks
                WHERE schedule_id = :schedule_id;";

        return $this->_update($sql, [
            'schedule_id' => $schedule_id,
            'title' => $title,
            'office_schedule_type' => $office_schedule_type,
            'remarks' => $remarks,
        ]);
    }

    public function deleteInternalSchedule($schedule_id, $office_schedule_id)
    {
        $sql = "DELETE FROM t_schedule WHERE schedule_id = :schedule_id;";
        $schedule = $this->_destroy($sql, ['schedule_id' => $schedule_id]);

        $sql = "DELETE FROM t_office_schedule WHERE schedule_id = :schedule_id;";
        $office_schedule = $this->_destroy($sql, ['schedule_id' => $schedule_id]);
        return $schedule && $office_schedule;
    }

    public function getPrivateSchedule($schedule_id)
    {
        $sql = "
            SELECT 
                t_private.title,
                t_schedule.start_date,
                t_schedule.start_time,
                t_schedule.end_time,
                t_schedule.all_day_flag,
                t_private.remarks,
                t_private.private_id,
                t_schedule.user_cd
            FROM t_private
            INNER JOIN t_schedule
                ON t_schedule.schedule_id = t_private.schedule_id
            WHERE t_private.schedule_id = :schedule_id;";

        return $this->_first($sql, ['schedule_id' => $schedule_id]);
    }

    public function updatePrivateSchedule($schedule_id, $title, $start_date, $start_time, $end_time, $all_day_flag)
    {
        $sql = "
            UPDATE t_schedule
                SET title = :title,
                    start_date = :start_date,
                    start_time = :start_time,
                    end_time = :end_time,
                    all_day_flag = :all_day_flag
            WHERE schedule_id = :schedule_id;";

        return $this->_update($sql, [
            'schedule_id' => $schedule_id,
            'title' => $title,
            'start_date' => $start_date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'all_day_flag' => $all_day_flag
        ]);
    }

    public function updatePrivate($schedule_id, $title, $remarks)
    {
        $sql = "UPDATE t_private SET title = :title , remarks = :remarks WHERE schedule_id = :schedule_id;";
        return $this->_update($sql, ['title' => $title, 'remarks' => $remarks, 'schedule_id' => $schedule_id]);
    }

    public function deletePrivateSchedule($schedule_id)
    {
        $sql = "DELETE FROM t_schedule WHERE schedule_id = :schedule_id;";
        $schedule = $this->_destroy($sql, ['schedule_id' => $schedule_id]);

        $sql = "DELETE FROM t_private WHERE schedule_id = :schedule_id;";
        $private = $this->_destroy($sql, ['schedule_id' => $schedule_id]);

        return $schedule && $private;
    }

    public function checkPersonCd($visit_id, $schedule_id, $person_cd)
    {
        $sql = "SELECT * FROM t_call WHERE t_call.person_cd = :person_cd AND t_call.visit_id = :visit_id;";
        return $this->_first($sql, ['person_cd' => $person_cd, 'visit_id' => $visit_id]);
    }

    public function getPosition($facility_cd, $person_cd)
    {
        $sql = "
            SELECT 
                m_position.position_cd,
                m_position.position_name
            FROM m_position
            INNER JOIN m_facility_person
                ON (m_position.position_cd = m_facility_person.display_position_cd AND m_facility_person.facility_cd = :facility_cd AND m_facility_person.person_cd = :person_cd);";

        return $this->_first($sql, [
            'facility_cd' => $facility_cd,
            'person_cd' => $person_cd
        ]);
    }


    public function insertCall($visit_id, $person_cd, $person_name, $department_cd, $department_name, $plan_flag, $act_flag, $display_position_name)
    {
        $sql = "INSERT INTO t_call 
                    (visit_id,person_cd,person_name,department_cd,department_name,plan_flag,act_flag,display_position_name)
                VALUES
                    (:visit_id,:person_cd,:person_name,:department_cd,:department_name,:plan_flag,:act_flag,:display_position_name); ";

        $this->_create($sql, [
            'visit_id' => $visit_id,
            'person_cd' => $person_cd,
            'person_name' => $person_name,
            'department_cd' => $department_cd ?? null,
            'department_name' => $department_name ?? null,
            'plan_flag' => $plan_flag,
            'act_flag' => $act_flag,
            'display_position_name' => $display_position_name
        ]);

        return $this->_lastInserted('t_call', 'call_id');
    }

    public function updateCall($visit_id, $person_cd, $person_name, $department_cd, $department_name, $plan_flag)
    {
        $sql = "UPDATE t_call 
                        SET person_name = :person_name,
                            department_cd = :department_cd,
                            department_name = :department_name,
                            plan_flag = :plan_flag
                WHERE visit_id = :visit_id
                AND person_cd = :person_cd;";

        return $this->_update($sql, [
            'visit_id' => $visit_id,
            'person_cd' => $person_cd,
            'person_name' => $person_name,
            'department_cd' => $department_cd ?? null,
            'department_name' => $department_name ?? null,
            'plan_flag' => $plan_flag
        ]);
    }

    public function getCallPerson($visit_id, $person_cd)
    {
        $sql = "
        SELECT 
            call_id,
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
            related_product,
            question,
            answer,
            re_question,
            literature,
            off_label_call_time
        FROM t_call 
        WHERE visit_id = :visit_id 
        AND person_cd = :person_cd;";

        return $this->_first($sql, ['visit_id' => $visit_id, 'person_cd' => $person_cd]);
    }

    public function getSchedule($schedule_id)
    {
        $sql = "
        SELECT 
            schedule_id,
            schedule_type,
            start_date,
            start_time,
            end_time,
            title,
            all_day_flag,
            display_option_type,
            user_cd
        FROM t_schedule 
        WHERE schedule_id = :schedule_id;";

        return $this->_first($sql, ['schedule_id' => $schedule_id]);
    }

    public function updateVisitAddStock($user_cd, $user_name, $org_cd, $org_short_name, $visit_id)
    {
        $sql = "UPDATE t_visit
                    SET user_cd = :user_cd,
                        user_name = :user_name,
                        org_cd = :org_cd,
                        org_short_name = :org_short_name
                WHERE visit_id = :visit_id;";

        return $this->_update($sql, [
            'user_cd' => $user_cd ?? '',
            'user_name' => $user_name ?? '',
            'org_cd' => $org_cd ?? '',
            'org_short_name' => $org_short_name ?? '',
            'visit_id' => $visit_id
        ]);
    }


    public function getDetailOrder($call_id)
    {
        $sql = "SELECT MAX(detail_order) as detail_order FROM t_detail WHERE call_id = :call_id;";
        return $this->_first($sql, ['call_id' => $call_id]);
    }

    public function insertDetail($call_id, $detail_order, $content_cd, $content_name, $product_cd, $product_name)
    {
        $sql = "INSERT INTO t_detail
                        (call_id,detail_order,content_cd,content_name_tran,product_cd,product_name)
                VALUES
                        (:call_id,:detail_order,:content_cd,:content_name_tran,:product_cd,:product_name);";

        $this->_create($sql, [
            'call_id' => $call_id,
            'detail_order' => $detail_order,
            'content_cd' => $content_cd,
            'content_name_tran' => $content_name,
            'product_cd' => $product_cd,
            'product_name' => $product_name
        ]);

        return $this->_lastInserted('t_detail', 'detail_id');
    }

    public function insertDetailDocument($detail_id, $document_id)
    {
        $sql = "INSERT INTO t_detail_document (detail_id,document_id) VALUES (:detail_id,:document_id);";
        $this->_create($sql, ['detail_id' => $detail_id, 'document_id' => $document_id]);

        return $this->_lastInserted('t_detail_document', 'detail_id');
    }

    public function updateStatusStock($stock_id)
    {
        $sql = "UPDATE t_stock SET status_type = :status_type WHERE stock_id = :stock_id;";
        return $this->_update($sql, ['stock_id' => $stock_id, 'status_type' => 20]);
    }

    public function createSchedule($t_schedule_temp, $data_user)
    {
        $sql = "INSERT INTO t_schedule
                    (schedule_type,start_date,start_time,end_time,title,all_day_flag,display_option_type,user_cd)
                VALUES
                    (:schedule_type,:start_date,:start_time,:end_time,:title,:all_day_flag,:display_option_type,:user_cd);";

        $this->_create($sql, [
            'schedule_type' => $t_schedule_temp->schedule_type,
            'start_date' => $t_schedule_temp->start_date,
            'start_time' => $t_schedule_temp->start_time,
            'end_time' => $t_schedule_temp->end_time,
            'title' => $t_schedule_temp->title,
            'all_day_flag' => $t_schedule_temp->all_day_flag,
            'display_option_type' => $t_schedule_temp->display_option_type,
            'user_cd' => $data_user->user_cd
        ]);

        return $this->_lastInserted('t_schedule', 'schedule_id');
    }

    public function getVisitCopyPlan($visit_id)
    {
        $sql = "SELECT important_flag,facility_cd,facility_short_name,remarks FROM t_visit WHERE visit_id = :visit_id;";
        return $this->_first($sql, ['visit_id' => $visit_id]);
    }

    public function createVisitCopyPlan($schedule_id, $t_visit, $data_user)
    {
        $sql = "INSERT INTO t_visit
                    (schedule_id,important_flag,facility_cd,facility_short_name,user_cd,user_name,implement_user_post_name,implement_user_post,org_cd,org_short_name,remarks)
                VALUES
                    (:schedule_id,:important_flag,:facility_cd,:facility_short_name,:user_cd,:user_name,:implement_user_post_name,:implement_user_post,:org_cd,:org_short_name,:remarks);";

        $this->_create($sql, [
            'schedule_id' => $schedule_id,
            'important_flag' => $t_visit->important_flag,
            'facility_cd' => $t_visit->facility_cd,
            'facility_short_name' => $t_visit->facility_short_name,
            'user_cd' => $data_user->user_cd,
            'user_name' => $data_user->user_name,
            'implement_user_post_name' => $data_user->implement_user_post_name,
            'implement_user_post' => $data_user->implement_user_post,
            'org_cd' => $data_user->org_cd,
            'org_short_name' => $data_user->org_short_name,
            'remarks' => $t_visit->remarks
        ]);

        return $t_visit = $this->_lastInserted('t_visit', 'visit_id');
    }

    public function getStatusReport($schedule_id)
    {
        $sql = "
            SELECT
            t_schedule.schedule_id,
            t_report_detail.report_id,
            t_report.report_status_type,
            t_report.report_period_start_date,
            t_report.report_period_end_date
            FROM t_schedule
            LEFT JOIN t_report_detail
                ON t_report_detail.report_detail_type  = t_schedule.schedule_type AND t_report_detail.report_detail_id = t_schedule.schedule_id
            LEFT JOIN t_report
                ON t_report.report_id  = t_report_detail.report_id
            WHERE schedule_id = :schedule_id;
        ";
        return $this->_first($sql, ["schedule_id" => $schedule_id]);
    }

    public function checkReportStartDate($start_date, $user_cd_login)
    {
        $sql = "
            SELECT 
                report_id
            FROM t_report
            WHERE user_cd = :user_cd
            AND report_period_start_date <= :report_period_start_date
            AND report_period_end_date >= :report_period_end_date;
        ";
        return $this->_first($sql, [
            "user_cd" => $user_cd_login,
            "report_period_start_date" => $start_date,
            "report_period_end_date" => $start_date
        ]);
    }
}
