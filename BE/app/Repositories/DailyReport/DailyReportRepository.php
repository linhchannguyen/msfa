<?php

namespace App\Repositories\DailyReport;

use App\Repositories\BaseRepository;
use App\Repositories\DailyReport\DailyReportRepositoryInterface;
use App\Traits\DateTimeTrait;

class DailyReportRepository extends BaseRepository implements DailyReportRepositoryInterface
{
    use DateTimeTrait;
    protected $useAutoMeta = true;
    private $date, $date_time, $approval_hierarchy, $display_option_name;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d');
        $this->date_time = $this->currentJapanDateTime('Y-m-d H:i:s');
        $this->display_option_name = "報告";
        $this->approval_hierarchy = "承認階層";
    }

    public function getList($organization, $startDate, $endDate)
    {
        $sql = "SELECT T1.report_id,
                    T1.report_period_start_date,
                    T1.report_period_end_date,
                    T1.report_status_type,
                    T1.user_cd,
                    T2.report_id as vacation_flg
                FROM t_report T1
                    LEFT JOIN t_report_vacation T2 ON T1.report_id=T2.report_id
                WHERE
                    T1.report_period_start_date >= :startDate AND
                    T1.report_period_start_date <= :endDate;";
        return $this->_find($sql, [
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    public function getListDataOfWeek($organization, $startDate, $endDate)
    {
        $sql = "SELECT T1.report_id,
                    T1.report_period_start_date,
                    T1.report_period_end_date,
                    T1.report_status_type,
                    T1.user_cd,
                    T2.report_id as vacation_flg
                FROM t_report T1
                    LEFT JOIN t_report_vacation T2 ON T1.report_id=T2.report_id
                WHERE
                    T1.org_cd = :org_cd AND
                    T1.report_period_start_date = :startDate AND
                    T1.report_period_end_date = :endDate;";
        return $this->_find($sql, [
            'org_cd' => $organization,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    public function listStatusType()
    {
        $sql = "SELECT definition_value, definition_label FROM m_variable_definition WHERE definition_name = :definition_name";
        return $this->_find($sql, ['definition_name' => '報告ステータス']);
    }

    public function getReportDetail($report_id, $request_type, $user_login)
    {
        $sql = "SELECT T1.*,T3.comment
                FROM t_report T1
                LEFT JOIN t_approval_request T2 ON T1.report_id = T2.request_target_id AND T2.request_type = :request_type
                LEFT JOIN t_approval_request_detail T3 ON T3.request_id = T2.request_id
                WHERE report_id = :report_id
                ORDER BY T3.updated_at DESC";
        return $this->_first($sql, ['report_id' => $report_id, 'request_type' => $request_type]);
    }

    public function getVacationReport($report_id)
    {
        $sql = "SELECT * FROM t_report_vacation WHERE report_id = :report_id";
        return $this->_first($sql, ['report_id' => $report_id]);
    }

    public function getUserApproval($user_login, $approval_work_type, $report_id, $user_report, $request_type, $layer_num)
    {
        $where = "";
        $query['approval_work_type'] = $approval_work_type;
        $query['request_type'] = $request_type;
        $query['request_target_id'] = $report_id;
        $query['start_date'] = $this->date;
        $query['end_date'] = $this->date;
        if ($user_login) {
            $where .= " AND T1.approval_user_cd = :approval_user_cd";
            $query['approval_user_cd'] = $user_login;
        }
        if ($user_report) {
            $where .= " AND T1.request_user_cd  = :request_user_cd";
            $query['request_user_cd'] = $user_report;
        }
        if ($layer_num) {
            $where .= " AND T1.approval_layer_num = :layer_num";
            $query['layer_num'] = $layer_num;
        }
        $sql = "SELECT T1.approval_user_cd, T5.user_name, T1.approval_layer_num
                        ,CASE T4.status_type
                        WHEN '1'    THEN '承認済'
                        WHEN '2'    THEN '差戻し'
                        ELSE             '承認待ち'
                        END status_type ,T4.comment
                FROM h_approval_user T1
                	JOIN t_report T2 on T1.request_user_cd = T2.user_cd
                	LEFT JOIN t_approval_request T3 ON T2.report_id = T3.request_target_id AND T3.request_type = :request_type
                  LEFT JOIN t_approval_request_detail T4 ON T3.request_id = T4.request_id AND T1.approval_user_cd = T4.approval_user_cd AND T1.approval_layer_num = T4.layer_num
                  LEFT JOIN m_user T5 ON T1.approval_user_cd = T5.user_cd
                WHERE T1.approval_work_type = :approval_work_type
                    AND T2.report_id = :request_target_id
                    AND T1.start_date <= :start_date
                    AND T1.end_date >= :end_date " . $where . " GROUP BY T1.approval_user_cd,T1.approval_layer_num";
        return $this->_find($sql, $query);
    }

    public function getUserApprovalReport($user_login, $approval_work_type, $report_id, $user_report, $request_type, $layer_num)
    {
        $where = "";
        $query['request_type'] = $request_type;
        $query['request_target_id'] = $report_id;
        if ($layer_num) {
            $where .= " AND T4.layer_num = :layer_num";
            $query['layer_num'] = $layer_num;
        }
        $sql = "SELECT T4.approval_user_cd, T5.user_name, T4.layer_num as approval_layer_num
                        ,CASE T4.status_type
                        WHEN '1'    THEN '承認済'
                        WHEN '2'    THEN '差戻し'
                        ELSE             '承認待ち'
                        END status_type ,T4.comment
                FROM t_report T2
                JOIN t_approval_request T3 ON T2.report_id = T3.request_target_id AND T3.request_type = :request_type
                LEFT JOIN t_approval_request_detail T4 ON T3.request_id = T4.request_id
                LEFT JOIN m_user T5 ON T4.approval_user_cd = T5.user_cd
                WHERE T2.report_id = :request_target_id  " . $where . " GROUP BY T4.approval_user_cd,T4.layer_num";
        return $this->_find($sql, $query);
    }

    public function getDataDetail($date, $report_id, $user_login)
    {
        $join = "";
        $query['start_date'] = $date;
        $query['user_login'] = $user_login;

        if ($report_id) {
            $join .= " AND T2.report_id = :report_id";
            $query['report_id'] = $report_id;
        }
        $sql = "SELECT T1.*,
                        T5.channel_type,
                        T7.org_label,
                        T2.report_detail_note
                FROM t_schedule T1
                    LEFT JOIN t_report_detail T2 ON T1.schedule_id = T2.report_detail_id AND T1.schedule_type = T2.report_detail_type " . $join . "
                    LEFT JOIN t_convention T6 ON T1.schedule_id = T6.schedule_id
                    LEFT JOIN t_convention_target_org T7 ON T6.convention_id = T7.convention_id
                    LEFT JOIN t_knowledge_timeline T5 ON T5.channel_type = T1.schedule_type AND T5.action_id = T1.schedule_id
                WHERE T1.start_date = :start_date AND T1.user_cd = :user_login
                GROUP BY T1.schedule_id
                ORDER BY T1.start_date DESC, T1.start_time DESC, T1.schedule_type ASC";
        return $this->_find($sql, $query);
    }

    public function getScheduleTypeCall($schedule_id)
    {
        $sql = "SELECT
                    T2.facility_short_name,
                    T2.org_short_name,
                    T3.department_name,
                    T3.person_cd,
                    T3.person_name,
                    T3.display_position_name,
                    T3.off_label_flag,
                    T2.user_name,
                    T2.user_cd,
                    T3.call_id
                FROM t_visit T2
                    JOIN t_call T3 ON T2.visit_id = T3.visit_id
                WHERE T2.schedule_id = :schedule_id";
        return $this->_find($sql, ['schedule_id' => $schedule_id]);
    }

    public function getDetailByCallID($call_id)
    {
        $sql = "SELECT detail_order,
                       content_name_tran,
                       product_name,
                       phase,
                       reaction,
                       note,
                       remarks
                FROM t_detail
                WHERE call_id = :call_id";
        return $this->_find($sql, ['call_id' => $call_id]);
    }

    public function getScheduleTypeConvention($schedule_id)
    {
        $sql = "SELECT T1.start_time,
                        T1.end_time,
                        T1.schedule_type,
                        T2.convention_name,
                        T2.convention_type,
                        T5.definition_label,
                        (SELECT GROUP_CONCAT(product_name SEPARATOR ', ') FROM t_convention_product
                            WHERE convention_id = T2.convention_id
                        ) AS product_name,
												(SELECT GROUP_CONCAT(org_label SEPARATOR ', ') FROM t_convention_target_org
                            WHERE convention_id = T2.convention_id
                        ) AS org_label,
                        T2.note,
                        T6.channel_type
                FROM t_schedule T1
                    JOIN t_convention T2 ON T1.schedule_id = T2.schedule_id
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = '講演会区分') T5 ON T2.convention_type = T5.definition_value
                    LEFT JOIN t_convention_product T3 ON T3.convention_id = T2.convention_id
                    LEFT JOIN t_convention_target_org T4 ON T2.convention_id = T4.convention_id
                    LEFT JOIN t_knowledge_timeline T6 ON T6.channel_type = T1.schedule_type and T6.action_id = T1.schedule_id
                WHERE T1.schedule_id = :schedule_id AND T2.status_type in (SELECT definition_value
                                                FROM m_variable_definition
                                                WHERE definition_name = :variable_status_type_convention AND definition_label in ('結果入力中','結果承認待ち','結果承認済'))
                GROUP BY T1.schedule_id";
        return $this->_find($sql, ['schedule_id' => $schedule_id, 'variable_status_type_convention' => '講演会ステータス']);
    }

    public function getScheduleTypeBriefing($schedule_id)
    {
        $sql = "SELECT T1.start_time,
                        T1.end_time,
                        T1.schedule_type,
                        T2.briefing_type,
                        T4.definition_label,
                        (SELECT GROUP_CONCAT(product_name SEPARATOR ', ') FROM t_briefing_product
                            WHERE briefing_id = T2.briefing_id
                        ) AS product_name,
                        T2.facility_short_name,
                        T2.briefing_id,
                        T2.implement_user_org_label,
                        T2.implement_user_name,
                        T2.implement_user_cd,
                        T5.channel_type,
                        T2.briefing_name,
                        T2.note
                FROM t_schedule T1
                    JOIN t_briefing T2 ON T1.schedule_id = T2.schedule_id
                    LEFT JOIN t_briefing_product T3 ON T2.briefing_id = T3.briefing_id
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = '説明会区分') T4 ON T2.briefing_type = T4.definition_value
                    LEFT JOIN t_knowledge_timeline T5 ON T5.channel_type = T1.schedule_type and T5.action_id = T1.schedule_id
                WHERE T1.schedule_id = :schedule_id AND T2.status_type in (SELECT definition_value
                                                FROM m_variable_definition
                                                WHERE definition_name = :variable_status_type_briefing AND definition_label in ('結果入力中','結果承認待ち','結果承認済'))
                GROUP BY T1.schedule_id;";
        return $this->_find($sql, ['schedule_id' => $schedule_id, 'variable_status_type_briefing' => '説明会ステータス']);
    }

    public function getScheduleTypeOfficeSchedule($schedule_id)
    {
        $sql = "SELECT T1.start_time,
                        T1.end_time,
                        T1.schedule_type,
                        T2.office_schedule_type,
                        T3.office_schedule_type_name,
                        T2.title,
                        T2.remarks,
                        T5.channel_type
                FROM t_schedule T1
                    LEFT JOIN t_office_schedule T2 ON T1.schedule_id = T2.schedule_id
                    LEFT JOIN m_office_schedule_type T3 ON T2.office_schedule_type = T3.office_schedule_type
                    LEFT JOIN t_knowledge_timeline T5 ON T5.channel_type = T1.schedule_type and T5.action_id = T1.schedule_id
                WHERE T1.schedule_id = :schedule_id";
        return $this->_find($sql, ['schedule_id' => $schedule_id]);
    }

    public function registerVacationReport($data)
    {
        $sql = "INSERT INTO t_report_vacation (
                        report_id,
                        report_date,
                        user_cd)
                VALUES (
                    :report_id,
                    :report_date,
                    :user_cd);";
        $parram = [
            'report_id' => $data->report_id,
            'report_date' => $this->date,
            'user_cd' => $data->user_login
        ];
        return $this->_create($sql, $parram);
    }

    public function deleteVacationReport($data)
    {
        $sql = "DELETE FROM t_report_vacation WHERE report_id = :report_id";
        $parram = [
            'report_id' => $data->report_id
        ];
        return $this->_destroy($sql, $parram);
    }

    public function saveReport($data)
    {
        $parram = [
            'report_period_start_date' => $data->report_period_start_date,
            'report_period_end_date' => $data->report_period_end_date,
            'report_status_type' => $data->report_status_type,
            'submission_remarks' => $data->submission_remarks,
            'user_cd' => $data->user_cd,
            'user_name' => $data->user_name,
            'org_cd' => $data->org_cd,
            'org_label' => $data->org_label
        ];
        $sql = "INSERT INTO t_report(
                        report_period_start_date,
                        report_period_end_date,
                        report_status_type,
                        submission_remarks,
                        user_cd,
                        user_name,
                        org_cd,
                        org_label)
                VALUES (
                        :report_period_start_date,
                        :report_period_end_date,
                        :report_status_type,
                        :submission_remarks,
                        :user_cd,
                        :user_name,
                        :org_cd,
                        :org_label);";
        $create = $this->_create($sql, $parram);
        $report = $this->_lastInserted("t_report", "report_id");
        return $report;
    }

    public function updateReportDetail($data)
    {
        $parram = [
            'report_id' => $data->report_id,
            'report_status_type' => $data->report_status_type,
            'submission_remarks' => $data->submission_remarks
        ];
        $sql = "UPDATE t_report SET submission_remarks = :submission_remarks, report_status_type = :report_status_type WHERE report_id = :report_id;";
        return $this->_update($sql, $parram);
    }

    public function saveDetailReport($report_id, $schedule_id, $schedule_type, $report_detail_note)
    {
        $sql = "INSERT INTO t_report_detail(
                        report_id,
                        report_detail_type,
                        report_detail_id,
                        report_detail_note)
                VALUES (
                        :report_id,
                        :report_detail_type,
                        :report_detail_id,
                        :report_detail_note) ON DUPLICATE KEY UPDATE report_detail_note = VALUES(report_detail_note);";
        $parram = [
            'report_id' => $report_id,
            'report_detail_type' => $schedule_type,
            'report_detail_id' => $schedule_id,
            'report_detail_note' => $report_detail_note
        ];
        return $this->_create($sql, $parram);
    }

    public function submitReport($data)
    {
        $sql_approval_request_detai = "SELECT * FROM t_approval_request WHERE request_target_id = :request_target_id AND request_type = :request_type";
        $approval_request_detai = $this->_first($sql_approval_request_detai, ['request_type' => $data->request_type, 'request_target_id' => $data->report_id]);
        $sql_insert = "INSERT INTO t_approval_request (
                        request_type,
                        request_target_id,
                        request_user_cd,
                        request_datetime,
                        status_type,
                        active_layer_num)
                VALUES (
                        :request_type,
                        :request_target_id,
                        :request_user_cd,
                        :request_datetime,
                        :status_type,
                        :active_layer_num);";
        $parram = [
            'request_type' => $data->request_type,
            'request_target_id' => $data->report_id,
            'request_user_cd' => $data->approval_user_cd,
            'request_datetime' => $this->date_time,
            'status_type' => $data->status_type,
            'active_layer_num' => 1
        ];
        if (!isset($approval_request_detai->request_id)) {
            $this->_create($sql_insert, $parram);
            $lastInserted = $this->_lastInserted("t_approval_request", "request_id");
        } else {
            $sql_update = "UPDATE t_approval_request SET status_type = :status_type WHERE request_target_id = :request_target_id AND request_type = :request_type;";
            $parram_update = [
                'request_type' => $data->request_type,
                'request_target_id' => $data->report_id,
                'status_type' => $data->status_type
            ];
            $this->_update($sql_update, $parram_update);
            $lastInserted = $this->_first($sql_approval_request_detai, ['request_type' => $data->request_type, 'request_target_id' => $data->report_id]);
        }
        $lastInserted->report_id = $data->report_id;
        $lastInserted->report_status_type = $data->report_status_type;
        return $lastInserted;
    }

    public function cancelApproval($data)
    {
        $sql = "DELETE T1, T2 FROM t_approval_request T1
            JOIN t_approval_request_detail T2 ON T2.request_id  = T1.request_id
            WHERE T1.request_target_id = :request_target_id AND T1.request_type = :request_type";
        $parram = [
            'request_target_id' => $data->report_id,
            'request_type' => $data->request_type
        ];
        return $this->_destroy($sql, $parram);
    }

    public function cancelReport($data)
    {
        $sql = "UPDATE t_report SET report_status_type = :report_status_type WHERE report_id = :report_id ";
        $parram = [
            'report_id' => $data->report_id,
            'report_status_type' => $data->report_status_type
        ];
        return $this->_update($sql, $parram);
    }

    public function approvalReport($report_id, $status_type, $request_type)
    {
        $sql = "UPDATE t_approval_request SET status_type = :status_type WHERE request_target_id = :request_target_id AND request_type = :request_type";
        return  $this->_update($sql, ['request_target_id' => $report_id, 'status_type' => $status_type, 'request_type' => $request_type]);
    }

    public function approvalRequest($report_id, $request_type, $status_type, $user_login)
    {
        $where = "";
        $query['request_target_id'] = $report_id;
        $query['request_type'] = $request_type;
        $query['status_type'] = $status_type;
        if ($user_login) {
            $where .= " AND request_user_cd = :request_user_cd";
            $query['request_user_cd'] = $user_login;
        }
        $sql = "UPDATE t_approval_request
                    SET status_type = :status_type
                WHERE request_target_id = :request_target_id AND request_type = :request_type" . $where;
        return  $this->_update($sql, $query);
    }

    public function approvalRequestDetail($report_id, $comment, $user_login, $status_type)
    {
        $where = "";
        if ($user_login) {
            $where .= " AND approval_user_cd = :approval_user_cd";
            $query['approval_user_cd'] = $user_login;
        }
        $query['request_target_id'] = $report_id;
        $query['comment'] = $comment;
        $query['approval_datetime'] = $this->date;
        $query['status_type'] = $status_type;
        $sql = "UPDATE t_approval_request_detail
                    SET comment = :comment,
                        approval_datetime = :approval_datetime,
                        status_type = :status_type
                WHERE request_id = (
                    SELECT request_id FROM t_approval_request
                    WHERE request_target_id = :request_target_id ORDER BY updated_at DESC LIMIT 1)" . $where;
        return  $this->_update($sql, $query);
    }

    public function approvalDetailReport($data)
    {
        $sql_approval_request_detai = "SELECT * FROM t_approval_request_detail
                        WHERE request_id = :request_id
                            AND approval_user_cd = :approval_user_cd
                            AND layer_num = :layer_num";
        $approval_request_detai = $this->_first($sql_approval_request_detai, ['request_id' => $data->request_id, 'approval_user_cd' => $data->approval_user_cd, 'layer_num' => $data->layer_num]);
        $sql = "INSERT INTO t_approval_request_detail (
                        request_id,
                        layer_num,
                        approval_user_cd,
                        status_type,
                        approval_datetime,
                        comment)
                VALUES (
                        :request_id,
                        :layer_num,
                        :approval_user_cd,
                        :status_type,
                        :approval_datetime,
                        :comment);";
        $parram = [
            'request_id' => $data->request_id,
            'layer_num' => $data->layer_num,
            'approval_user_cd' => $data->approval_user_cd,
            'status_type' => $data->status_type,
            'comment' => $data->comment,
        ];

        if (!isset($approval_request_detai->request_id)) {
            $parram['approval_datetime'] = null;
            $this->_create($sql, $parram);
        } else {
            $parram['approval_datetime'] = $this->date_time;
            $sql_update = "UPDATE t_approval_request_detail
                            SET status_type = :status_type,
                                comment = :comment,
                                approval_datetime = :approval_datetime
                            WHERE request_id = :request_id AND approval_user_cd = :approval_user_cd AND layer_num = :layer_num;";
            $this->_update($sql_update, $parram);
        }
        return true;
    }

    public function getStatusTypeReport($report_id)
    {
        $sql = "SELECT T1.report_id,T2.status_type, T1.report_status_type
                FROM t_report T1
                LEFT JOIN t_approval_request T2 ON T1.report_id = T2.request_target_id AND request_type = 10
                WHERE T1.report_id = :report_id
                ORDER BY T2.updated_at DESC;";
        return $this->_first($sql, ['report_id' => $report_id]);
    }

    public function modeReport($parameter)
    {
        $sql = "SELECT * FROM c_system_parameter where parameter_name = :parameter_name and parameter_key = :parameter_key";
        return $this->_first($sql, ['parameter_name' => $parameter, 'parameter_key' => '報告入力モード']);
    }

    public function getStatusReport($report_id)
    {
        $sql = "SELECT * FROM t_approval_request where request_target_id = :request_target_id";
        return $this->_find($sql, ['request_target_id' => $report_id]);
    }

    public function checkUserApproval($approval_work_type, $user_login)
    {
        $sql = "SELECT approval_setting_id,request_user_cd,approval_layer_num
        FROM h_approval_user where approval_work_type = :approval_work_type
            AND approval_user_cd = :approval_user_cd
            AND start_date <= :start_date
            AND end_date >= :end_date";
        $parram = [
            'approval_work_type' => $approval_work_type,
            'approval_user_cd' => $user_login,
            'start_date' => $this->date,
            'end_date' => $this->date,
        ];
        return $this->_find($sql, $parram);
    }

    public function getLayerNumApprovalFinnal()
    {
        $sql = "SELECT parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_name AND parameter_key = :parameter_key";
        $parram['parameter_name'] = $this->approval_hierarchy;
        $parram['parameter_key'] = $this->display_option_name;
        return $this->_first($sql, $parram);
    }

    public function checkUserApprovalFinal($approval_work_type, $user_login, $user_create_convention)
    {
        $where = "";
        $parram['approval_user_cd'] = $user_login;
        $parram['approval_work_type'] = $approval_work_type;
        $parram['parameter_name'] = $this->approval_hierarchy;
        $parram['parameter_key'] = $this->display_option_name;
        $parram['start_date'] = $this->date;
        $parram['end_date'] = $this->date;
        if ($user_create_convention) {
            $where .= " AND request_user_cd = :user_create_convention";
            $parram['user_create_convention'] = $user_create_convention;
        }
        $sql = "SELECT approval_setting_id,request_user_cd,approval_work_type,approval_layer_num,approval_user_cd
                FROM h_approval_user
                WHERE approval_work_type = :approval_work_type
                    AND approval_user_cd = :approval_user_cd
                    AND start_date <= :start_date
                    AND end_date >= :end_date
                    AND approval_layer_num = (SELECT parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_name AND parameter_key = :parameter_key)" . $where;
        return $this->_first($sql, $parram);
    }

    public function getRequestIDApproval($data)
    {
        $parram = [
            'request_type' => $data->request_type,
            'request_target_id' => $data->report_id
        ];
        $sql = "SELECT * FROM t_approval_request WHERE request_target_id = :request_target_id AND request_type = :request_type";
        return $this->_first($sql, $parram);
    }

    public function updateActiveLayerNumRequest($active_layer_num, $request_id)
    {
        $parram = [
            'active_layer_num' => $active_layer_num,
            'request_id' => $request_id
        ];
        $sql = "UPDATE t_approval_request SET active_layer_num = :active_layer_num WHERE request_id = :request_id";
        return $this->_update($sql, $parram);
    }

    public function deleteApprovalRequest($request_id)
    {
        $parram = [
            'request_id' => $request_id
        ];
        $sql = "DELETE FROM t_approval_request WHERE request_id = :request_id";
        $sql_approval_detail = "DELETE FROM t_approval_request_detail WHERE request_id = :request_id";
        $this->_destroy($sql_approval_detail, $parram);
        return $this->_destroy($sql, $parram);
    }
}
