<?php

namespace App\Repositories\Home;

use App\Repositories\BaseRepository;
use App\Repositories\Home\HomeRepositoryInterface;
use App\Traits\DateTimeTrait;

class HomeRepository extends BaseRepository implements HomeRepositoryInterface
{
    use DateTimeTrait;
    private $date;
    protected $useAutoMeta = true;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d');
    }

    public function widget()
    {
        $sql = "SELECT 
            widget_id,
            widget_type,
            widget_type_id,
            widget_title,
            sort_order,
            height,
            width
        FROM m_widget ORDER BY sort_order";
        return $this->_all($sql);
    }

    public function getOrganizationLayer()
    {
        $sql = "SELECT definition_value, definition_label FROM m_variable_definition WHERE definition_name = :definition_name";
        return $this->_find($sql, ['definition_name' => ACTUAL_DIGESTION_AGGREGATION_HIERARCHY]);
    }

    public function getActualDigestedItems()
    {
        $sql = "SELECT actual_sales_product_cd, actual_sales_product_name, sort_order FROM m_actual_sales_product ORDER BY sort_order ASC";
        return $this->_all($sql);
    }

    public function all($user_cd)
    {
        $sql = "SELECT
            m.message_id,
            m.message_subject,
            m.release_start_date,
            m.release_end_date,
            m.release_org_cd,
            o.org_name,
            m.sender_name,
            m.message_contents,
            m.important_flag,
            m.last_update_datetime,
            m.create_user_cd,
            ( CASE WHEN r.user_cd IS NULL THEN 0 ELSE 1 END ) AS message_read,
            (SELECT layer_num FROM m_org WHERE org_cd = m.release_org_cd) as layer_num
        FROM t_message m
        LEFT JOIN t_message_read r ON r.message_id = m.message_id AND r.user_cd = :user_cd
        LEFT JOIN m_org o ON m.release_org_cd = o.org_cd
        WHERE CAST(m.release_start_date AS DATE) <= :current_date1 AND CAST(m.release_end_date AS DATE) >= :current_date2
        ORDER BY
            m.last_update_datetime DESC,
            m.release_end_date DESC";
        return $this->_find($sql, [
            'user_cd' => $user_cd,
            'current_date1' => $this->date,
            'current_date2' => $this->date
        ]);
    }

    public function readMessage($data)
    {
        $sql_delete_message_read = "DELETE FROM t_message_read where message_id = :message_id AND user_cd = :user_cd";
        $sql_insert_message_read = "INSERT INTO t_message_read ( message_id, user_cd, read_datetime) VALUES (:message_id, :user_cd1, :read_datetime);";
        $delete = $this->_destroy($sql_delete_message_read, ['message_id' => $data->id, 'user_cd' => $data->user_cd]);
        $create = $this->_create($sql_insert_message_read, [
            'message_id' => $data->id,
            'user_cd1' => $data->user_cd,
            'read_datetime' => $this->date
        ]);
        return $delete && $create;
    }

    public function checkRead($data)
    {
        $sql = "SELECT message_id FROM t_message_read WHERE message_id = :message_id AND user_cd = :user_cd";
        return $this->_find($sql, ['message_id' => $data->id, 'user_cd' => $data->user_cd]);
    }

    public function informList($user_cd)
    {
        $sql = "SELECT
            i.inform_id,
            i.inform_category_cd,
            i.inform_datetime,
            i.inform_user_cd,
            i.inform_contents,
            i.archive_flag,
            i.informed_flag,
            i.read_flag,
            ip.parameter_key,
            ip.parameter_value 
        FROM
            t_inform i
            LEFT JOIN t_inform_parameter ip ON i.inform_id = ip.inform_id
        WHERE
            inform_user_cd = :user_cd
            AND archive_flag = 0 
        ORDER BY
            inform_datetime DESC";
        return $this->_find($sql, ['user_cd' => $user_cd]);
    }

    public function countInform($user_cd)
    {
        $sql = "SELECT
            count(*) as uninformed_quantity
        FROM
            t_inform 
        WHERE
            inform_user_cd = :user_cd
            AND informed_flag = 0";
        return $this->_find($sql, ['user_cd' => $user_cd]);
    }

    public function informed($user_cd)
    {
        $sql = "UPDATE t_inform 
        SET informed_flag = :informed_flag_set 
        WHERE
            informed_flag = 0 
            AND inform_user_cd = :user_cd";
        return $this->_update($sql, ['user_cd' => $user_cd, 'informed_flag_set' => 1]);
    }

    public function getActivityPlan($conditions)
    {
        $sql = "SELECT
            t_schedule.schedule_id,
            t_schedule.schedule_type,
            t_schedule.title,
            t_schedule.start_time,
            t_schedule.end_time,
            (
                SELECT COUNT(*)
                FROM t_visit
                JOIN t_call ON t_call.visit_id = t_visit.visit_id
                WHERE t_visit.schedule_id = t_schedule.schedule_id
            ) AS call_list,
            (
                SELECT COUNT(*)
                FROM t_briefing
                JOIN t_briefing_attendee ON t_briefing_attendee.briefing_id = t_briefing.briefing_id
                WHERE t_briefing.schedule_id = t_schedule.schedule_id
            ) AS briefing_list,
            (
                SELECT COUNT(*)
                FROM t_convention
                JOIN t_convention_attendee ON t_convention_attendee.convention_id = t_convention.convention_id
                WHERE t_convention.schedule_id = t_schedule.schedule_id
            ) AS convention_list,
            (
                SELECT
                    CONCAT( '[', GROUP_CONCAT( JSON_OBJECT ( 
                    'schedule_id', t_private.schedule_id,
                    'title', t_private.title
                    ) ), ']' ) 
                FROM t_private
                WHERE t_private.schedule_id = t_schedule.schedule_id
            ) AS private_list,
            (
                SELECT
                    CONCAT( '[', GROUP_CONCAT( JSON_OBJECT ( 
                    'schedule_id', t_office_schedule.schedule_id,
                    'title', t_office_schedule.title
                    ) ), ']' ) 
                FROM t_office_schedule
                WHERE t_office_schedule.schedule_id = t_schedule.schedule_id
            ) AS office_schedule_list
        FROM
            t_schedule
        WHERE
            t_schedule.non_display_flag = 0
            AND CAST( t_schedule.start_time AS DATE ) >= :current1
            AND CAST( t_schedule.end_time AS DATE ) <= :current2
            AND t_schedule.user_cd = :user_cd
        ORDER BY t_schedule.start_time";
        return $this->_find($sql, [
            'user_cd' => $conditions['user_cd'],
            'current1' => $conditions['current'],
            'current2' => $conditions['current']
        ]);
    }

    public function getVisitActivityPlan($schedule_id)
    {
        $sql = "SELECT t_call.call_id, t_visit.facility_short_name, t_call.person_name, t_call.department_name
            FROM t_visit
            JOIN t_call ON t_call.visit_id = t_visit.visit_id
            WHERE t_visit.schedule_id = :schedule_id
            ORDER BY t_call.call_id
            LIMIT 1";
        return $this->_first($sql, ['schedule_id' => $schedule_id]);
    }

    public function getBriefingActivityPlan($schedule_id)
    {
        $sql = "SELECT t_briefing_attendee.briefing_id, t_briefing.facility_short_name, t_briefing_attendee.person_name, t_briefing_attendee.department_name
            FROM t_briefing
            JOIN t_briefing_attendee ON t_briefing_attendee.briefing_id = t_briefing.briefing_id
            WHERE t_briefing.schedule_id = :schedule_id
            ORDER BY t_briefing_attendee.briefing_id
            LIMIT 1";
        return $this->_first($sql, ['schedule_id' => $schedule_id]);
    }

    public function getConventionActivityPlan($schedule_id)
    {
        $sql = "SELECT t_convention_attendee.convention_id, t_convention_attendee.facility_short_name, t_convention_attendee.person_name, t_convention_attendee.department_name
            FROM t_convention
            JOIN t_convention_attendee ON t_convention_attendee.convention_id = t_convention.convention_id
            WHERE t_convention.schedule_id = :schedule_id
            ORDER BY t_convention_attendee.convention_id
            LIMIT 1";
        return $this->_first($sql, ['schedule_id' => $schedule_id]);
    }

    public function getExternalUrlLink()
    {
        $sql = "SELECT
            external_url_link_id, external_url_link_name, external_url
        FROM
            m_external_url_link 
        ORDER BY sort_order";
        return $this->_all($sql);
    }

    public function getExternalEmbeddedUrl()
    {
        $sql = "SELECT
            external_embedded_url_id, external_url
        FROM
            m_external_embedded_url";
        return $this->_all($sql);
    }

    public function getPeriodConfirmation()
    {
        $sql = "SELECT parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_name";
        return $this->_first($sql, ['parameter_name' => HOME]);
    }

    public function getSetPeriod($conditions)
    {
        $sql = "SELECT calendar_date FROM m_calendar WHERE calendar_date <= :current AND holiday_flag = 0
        ORDER BY calendar_date DESC
        LIMIT :period";
        return $this->_find($sql, $conditions);
    }

    public function getDailyReportUnsubmitted($conditions)
    {
        $sql = "SELECT
            COUNT(*) AS total_record
        FROM t_report
        JOIN m_calendar ON m_calendar.calendar_date = t_report.report_period_start_date
        WHERE report_status_type > :report_status_type
            AND user_cd = :user_cd
            AND holiday_flag = 0
            AND report_period_start_date >= :start_date
            AND report_period_end_date <= :end_date";
        return $this->_find($sql, $conditions);
    }

    public function getDailyReportUnApproved($conditions)
    {
        $sql = "SELECT
            t_report.report_id
        FROM
            t_report
            JOIN t_approval_request ON t_approval_request.request_target_id = t_report.report_id
            LEFT JOIN t_approval_request_detail ON t_approval_request_detail.request_id = t_approval_request.request_id
        WHERE
            t_approval_request.request_type = :request_type
            AND t_approval_request.status_type = :status_type
            AND t_approval_request_detail.status_type = :status_type_detail
            AND t_approval_request_detail.approval_user_cd = :user_cd
            AND t_approval_request.active_layer_num = t_approval_request_detail.layer_num
        GROUP BY t_report.report_id";
        return $this->_find($sql, $conditions);
    }

    public function getDailyReportRemand($conditions)
    {
        $sql = "SELECT
            t_report.report_id
        FROM
            t_report
            JOIN t_approval_request ON t_approval_request.request_target_id = t_report.report_id
        WHERE
            t_approval_request.request_type = :request_type
            AND t_report.report_status_type = :report_status_type
            AND t_approval_request.status_type = :status_type
            AND t_report.user_cd = :user_cd
        GROUP BY t_report.report_id";
        return $this->_find($sql, $conditions);
    }

    public function getBriefingUnApproved($conditions)
    {
        $sql = "SELECT
            t_briefing.briefing_id
        FROM
            t_briefing
            JOIN t_approval_request ON t_approval_request.request_target_id = t_briefing.briefing_id
            LEFT JOIN t_approval_request_detail ON t_approval_request_detail.request_id = t_approval_request.request_id
        WHERE
            t_approval_request.request_type IN " . $this->_buildCommaString($conditions['request_type'], true);
        $sql .= "
            AND t_briefing.status_type in (20,50)
            AND t_approval_request.status_type = :status_type
            AND t_approval_request_detail.status_type = :status_type_detail
            AND t_approval_request_detail.approval_user_cd = :user_cd
            AND t_approval_request.active_layer_num = t_approval_request_detail.layer_num
        GROUP BY t_briefing.briefing_id";
        unset($conditions['request_type']);
        return $this->_find($sql, $conditions);
    }

    public function getBriefingRemand($conditions)
    {
        $sql = "SELECT
            COUNT(*) AS total_record
        FROM
            t_briefing
        WHERE remand_flag = 1
            AND implement_user_cd = :user_cd";
        return $this->_find($sql, $conditions);
    }

    public function getConventionUnApproved($conditions)
    {
        $sql = "SELECT
            t_convention.convention_id
        FROM
            t_convention
            JOIN t_approval_request ON t_approval_request.request_target_id = t_convention.convention_id
            LEFT JOIN t_approval_request_detail ON t_approval_request_detail.request_id = t_approval_request.request_id
        WHERE
            t_approval_request.request_type IN " . $this->_buildCommaString($conditions['request_type'], true);
        $sql .= " 
            AND t_convention.status_type in (20,50)
            AND t_approval_request.status_type = :status_type
            AND t_approval_request_detail.status_type = :status_type_detail
            AND t_approval_request_detail.approval_user_cd = :user_cd
            AND t_approval_request.active_layer_num = t_approval_request_detail.layer_num
        GROUP BY t_convention.convention_id";
        unset($conditions['request_type']);
        return $this->_find($sql, $conditions);
    }

    public function getConventionRemand($conditions)
    {
        $sql = "SELECT
            COUNT(*) AS total_record
        FROM
            t_convention
        WHERE remand_flag = 1
            AND plan_user_cd = :user_cd";
        return $this->_find($sql, $conditions);
    }

    public function getKnowledgeUnApproved($conditions)
    {
        $sql = "SELECT
            t_knowledge.knowledge_id
        FROM
            t_knowledge
            JOIN t_approval_request ON t_approval_request.request_target_id = t_knowledge.knowledge_id
            LEFT JOIN t_approval_request_detail ON t_approval_request_detail.request_id = t_approval_request.request_id
        WHERE
            t_approval_request.request_type = :request_type
            AND t_approval_request.status_type = :status_type
            AND t_approval_request_detail.status_type = :status_type_detail
            AND t_approval_request_detail.approval_user_cd = :user_cd
            AND t_approval_request.active_layer_num = t_approval_request_detail.layer_num
        GROUP BY t_knowledge.knowledge_id";
        $data = $this->_find($sql, $conditions);
        return $this->_pluck($data, 'knowledge_id');
    }

    public function getKnowledgeRemand($conditions)
    {
        $sql = "SELECT t_knowledge.knowledge_id
            FROM t_approval_request 
            INNER JOIN t_approval_request_detail
                 ON t_approval_request_detail.request_id = t_approval_request.request_id 
            INNER JOIN m_variable_definition
                 ON (m_variable_definition.definition_name = :definition_name AND m_variable_definition.definition_label = :definition_label 
                    AND t_approval_request.request_type = m_variable_definition.definition_value
                 )
            JOIN t_knowledge ON t_approval_request.request_target_id = t_knowledge.knowledge_id
            WHERE t_knowledge.knowledge_status_type = :knowledge_status_type AND t_knowledge.create_user_cd = :user_cd
            GROUP BY t_knowledge.knowledge_id";
        $conditions['definition_name'] = '承認申請区分';
        $conditions['definition_label'] = 'ナレッジ';

        $data = $this->_find($sql, $conditions);
        return $this->_pluck($data, 'knowledge_id');
    }

    public function getInappropriateReport()
    {
        $sql = "SELECT t_question.question_id
        FROM t_question
        WHERE t_question.delete_flag = 0
        AND 
        (
            (
                SELECT COUNT( * ) 
                FROM t_unsuitable_report 
                WHERE t_unsuitable_report.key_id = t_question.question_id 
                    AND t_unsuitable_report.key_type = 10 
                    AND ( t_unsuitable_report.cancel_flag = 0 OR t_unsuitable_report.cancel_flag IS NULL ) 
            ) > 0 
            OR
            (
                SELECT COUNT( * ) 
                FROM t_unsuitable_report 
                WHERE t_unsuitable_report.key_id IN (SELECT t_answer.answer_id FROM t_answer WHERE t_answer.question_id = t_question.question_id)
                    AND t_unsuitable_report.key_type = 20 
                    AND ( t_unsuitable_report.cancel_flag = 0 OR t_unsuitable_report.cancel_flag IS NULL ) 
            ) > 0 
        )
        GROUP BY t_question.question_id";
        return $this->_all($sql);
    }

    public function getPersonConsiderationUnConfirm($conditions)
    {
        $sql = "SELECT
            t_person_consideration_confirm.person_consideration_id
        FROM
        t_person_consideration_confirm
        WHERE t_person_consideration_confirm.confirmed_flag = 0
            AND t_person_consideration_confirm.user_cd = :user_cd";
        return $this->_find($sql, $conditions);
    }

    public function getFacilityConsiderationUnConfirm($conditions)
    {
        $sql = "SELECT
            COUNT(*) AS total_record
        FROM
            t_facility_consideration_confirm
        WHERE confirmed_flag = 0
            AND user_cd = :user_cd";
        return $this->_find($sql, $conditions);
    }

    public function actualDigestionRanking($conditions)
    {
        $sql = "SELECT
            m_actual_sales_product.actual_sales_product_name,
            i_layer_actual_sales_ranking.sales_amount 
        FROM
            m_actual_sales_user_layer_map
            JOIN i_layer_actual_sales_ranking ON i_layer_actual_sales_ranking.aggregate_layer_cd = m_actual_sales_user_layer_map.aggregate_layer_cd
            JOIN m_actual_sales_product ON m_actual_sales_product.actual_sales_product_cd = i_layer_actual_sales_ranking.actual_sales_product_cd 
        WHERE
            m_actual_sales_user_layer_map.user_cd = :user_cd
            AND m_actual_sales_user_layer_map.aggregate_layer_num = :aggregate_layer_num
        ORDER BY m_actual_sales_product.sort_order ASC";
        return $this->_find($sql, $conditions);
    }

    public function getTimeSalesResults($conditions)
    {
        $sql = "SELECT
            i_layer_actual_sales_comparison.sales_month
        FROM
            m_actual_sales_user_layer_map
            JOIN i_layer_actual_sales_comparison ON i_layer_actual_sales_comparison.aggregate_layer_cd = m_actual_sales_user_layer_map.aggregate_layer_cd 
        WHERE
            m_actual_sales_user_layer_map.user_cd = :user_cd";
        if (!empty($conditions['actual_sales_product_cd'])) {
            $sql .= " AND i_layer_actual_sales_comparison.actual_sales_product_cd = :actual_sales_product_cd";
        } else {
            unset($conditions['actual_sales_product_cd']);
        }
        if (!empty($conditions['aggregate_layer_num'])) {
            $sql .= " AND m_actual_sales_user_layer_map.aggregate_layer_num = :aggregate_layer_num";
        } else {
            unset($conditions['aggregate_layer_num']);
        }
        $sql .= " GROUP BY i_layer_actual_sales_comparison.sales_month
        ORDER BY sales_month";
        return $this->_find($sql, $conditions);
    }

    public function sameAsBeforeSalesResults($conditions)
    {
        $sql = "SELECT
            i_layer_actual_sales_comparison.sales_month,
            i_layer_actual_sales_comparison.sales_amount,
            i_layer_actual_sales_comparison.previous_year_sales_amount
        FROM
            m_actual_sales_user_layer_map
            JOIN i_layer_actual_sales_comparison ON i_layer_actual_sales_comparison.aggregate_layer_cd = m_actual_sales_user_layer_map.aggregate_layer_cd 
        WHERE m_actual_sales_user_layer_map.user_cd = :user_cd
            AND i_layer_actual_sales_comparison.sales_month = :sales_month";
        if (!empty($conditions['actual_sales_product_cd'])) {
            $sql .= " AND i_layer_actual_sales_comparison.actual_sales_product_cd = :actual_sales_product_cd";
        } else {
            unset($conditions['actual_sales_product_cd']);
        }
        if (!empty($conditions['aggregate_layer_num'])) {
            $sql .= " AND m_actual_sales_user_layer_map.aggregate_layer_num = :aggregate_layer_num";
        } else {
            unset($conditions['aggregate_layer_num']);
        }
        return $this->_find($sql, $conditions);
    }

    public function actualDigestionProcess($conditions)
    {
        $sql = "SELECT
            i_layer_actual_sales_process.sales_amount,
            i_layer_actual_sales_process.goal_amount,
            m_actual_sales_product.actual_sales_product_cd,
            m_actual_sales_product.actual_sales_product_name
        FROM
            m_actual_sales_user_layer_map
            JOIN i_layer_actual_sales_process ON i_layer_actual_sales_process.aggregate_layer_cd = m_actual_sales_user_layer_map.aggregate_layer_cd 
            JOIN m_actual_sales_product ON m_actual_sales_product.actual_sales_product_cd = i_layer_actual_sales_process.actual_sales_product_cd
        WHERE
            m_actual_sales_user_layer_map.user_cd = :user_cd";
        if (!empty($conditions['aggregate_layer_num'])) {
            $sql .= " AND m_actual_sales_user_layer_map.aggregate_layer_num = :aggregate_layer_num";
        } else {
            unset($conditions['aggregate_layer_num']);
        }
        $sql .= " ORDER BY i_layer_actual_sales_process.actual_sales_product_cd DESC";
        return $this->_find($sql, $conditions);
    }
}
