<?php

namespace App\Repositories\TimelineGeneration;

use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use App\Repositories\TimelineGeneration\TimelineGenerationRepositoryInterface;
use App\Enums\LogBatchJob;
use App\Traits\DateTimeTrait;

class TimelineGenerationRepository extends BaseRepository implements TimelineGenerationRepositoryInterface
{
    use DateTimeTrait;
    protected $useAutoMeta = false;
    private $date;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
    }

    public function getSystemParameterDate()
    {
        $sql = "SELECT parameter_name,parameter_key,parameter_value FROM c_system_parameter WHERE parameter_key = :parameter_key and parameter_name = :parameter_name";
        return $this->_first($sql, ['parameter_key' => 'システム運用日付', 'parameter_name' => '全般']);
    }

    public function getSystemParameterDetail()
    {
        $sql = "SELECT parameter_name,parameter_key,parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_name AND parameter_key = :parameter_key";
        return $this->_first($sql, ['parameter_key' => '入力期限', 'parameter_name' => '面談']);
    }

    public function deleteTimelineDetail($date, $parameter_value, $jobId, $keyBatchJob)
    {
        if ($parameter_value) {
            $start_datetime = date('Y-m-d H:i:s', strtotime("-" . ($parameter_value) . " months", strtotime($date)));
        } else {
            $start_datetime = $date;
        }
        $sql = "DELETE FROM t_timeline WHERE timeline_id IN (
                SELECT T1.timeline_id 
                FROM t_timeline T1
                    LEFT JOIN t_call T2 ON T2.call_id = T1.channel_id 
                    LEFT JOIN t_detail T3 ON T3.detail_id = T1.channel_detail_id 
                WHERE T1.channel_type in (SELECT definition_value 
                                            FROM m_variable_definition 
                                            WHERE definition_name = :definition_channel AND definition_label = :definition_detail)
                AND T2.call_id IS NULL  
                AND T3.detail_id IS NULL
                AND T1.start_datetime > :start_datetime);";
        $parram = [
            'start_datetime' => $start_datetime,
            'definition_channel' => 'チャネル',
            'definition_detail' => '面談'
        ];
        return $this->_destroy($sql, $parram, LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    public function getTimelineDetail($date)
    {
        $sql = "SELECT A.*,T4.channel_detail_id,T4.timeline_id
                FROM (SELECT T1.start_time AS start_datetime,
                            T1.end_time AS end_datetime,
                            T1.schedule_type AS channel_type,
                            T3.call_id AS channel_id,
                            T5.detail_id,
                            T5.detail_order,
                            T2.org_short_name AS org_label,
                            T2.user_cd,
                            T2.user_name,
                            T2.implement_user_post_name AS user_post_type,
                            COALESCE(NULL) AS title,
                            T2.facility_cd,
                            T2.facility_short_name,
                            T3.department_name,
                            T3.person_cd,
                            T3.person_name,
                            T3.display_position_name,
                            T3.off_label_flag,
                            T1.created_by,
                            T1.proxy_created_by,
                            T1.created_at,
                            T1.updated_by,
                            T1.proxy_updated_by,
                            T1.updated_at,
                            T5.phase as phase_name,
                            T5.content_name_tran as content_name,
                            T5.reaction as reaction_name,
                            T5.product_name,
                            T5.note
                        FROM t_schedule T1 
                           LEFT JOIN t_visit T2 ON T1.schedule_id = T2.schedule_id
                           LEFT JOIN t_call T3 ON T2.visit_id = T3.visit_id
                           LEFT JOIN t_detail T5 ON T5.call_id = T3.call_id
                        WHERE T1.schedule_type in (SELECT definition_value FROM m_variable_definition WHERE definition_name = :schedule_division AND definition_label = :schedule_division_detail) 
                           AND T1.start_time > :start_time
                           AND T3.act_flag = :act_flag
                           AND T5.detail_id IS NOT NULL) A 
                LEFT JOIN t_timeline T4 ON T4.channel_id = A.channel_id AND T4.channel_type in (
                    SELECT definition_value 
                    FROM m_variable_definition 
                    WHERE definition_name = :definition_channel AND definition_label = :definition_detail) 
                AND T4.channel_detail_id = A.detail_id";
        $parram = [
            'start_time' => $date,
            'schedule_division' => 'スケジュール区分',
            'schedule_division_detail' => '面談',
            'act_flag' => 1,
            'definition_channel' => 'チャネル',
            'definition_detail' => '面談'
        ];
        return $this->_find($sql, $parram);
    }

    public function insertTimelineDetail($data, $jobId, $keyBatchJob)
    {
        $sql = "INSERT INTO t_timeline (
                    start_datetime
                    ,end_datetime
                    ,channel_type
                    ,channel_id
                    ,channel_detail_id
                    ,org_label
                    ,user_cd
                    ,user_name
                    ,user_post_type
                    ,title
                    ,facility_cd
                    ,facility_short_name
                    ,department_name
                    ,person_cd
                    ,person_name
                    ,display_position_name
                    ,product_name
                    ,note
                    ,off_label_flag
                    ,content_name
                    ,phase_name
                    ,reaction_name
                    ,detail_order
                    ,created_by
                    ,proxy_created_by
                    ,created_at
                    ,updated_by
                    ,proxy_updated_by
                    ,updated_at
                ) VALUES (
                    :start_datetime
                    ,:end_datetime
                    ,:channel_type
                    ,:channel_id
                    ,:channel_detail_id
                    ,:org_label
                    ,:user_cd
                    ,:user_name
                    ,:user_post_type
                    ,:title
                    ,:facility_cd
                    ,:facility_short_name
                    ,:department_name
                    ,:person_cd
                    ,:person_name
                    ,:display_position_name
                    ,:product_name
                    ,:note
                    ,:off_label_flag
                    ,:content_name
                    ,:phase_name
                    ,:reaction_name
                    ,:detail_order
                    ,:created_by
                    ,:proxy_created_by
                    ,:created_at
                    ,:updated_by
                    ,:proxy_updated_by
                    ,:updated_at
                )";
        $parram = [
            "start_datetime" => $data->start_datetime,
            "end_datetime" => $data->end_datetime,
            "channel_type" => $data->channel_type,
            "channel_id" => $data->channel_id,
            "channel_detail_id" => $data->detail_id,
            "org_label" => $data->org_label,
            "user_cd" => $data->user_cd,
            "user_name" => $data->user_name,
            "user_post_type" => $data->user_post_type,
            "title" => $data->title,
            "facility_cd" => $data->facility_cd,
            "facility_short_name" => $data->facility_short_name,
            "department_name" => $data->department_name,
            "person_cd" => $data->person_cd,
            "person_name" => $data->person_name,
            "display_position_name" => $data->display_position_name,
            "product_name" => $data->product_name,
            "note" => $data->note,
            "off_label_flag" => $data->off_label_flag,
            "content_name" => $data->content_name,
            "phase_name" => $data->phase_name,
            "reaction_name" => $data->reaction_name,
            "detail_order" => $data->detail_order,
            "created_by" => 'H03-B01',
            "proxy_created_by" => NULL,
            "created_at" => $this->date,
            "updated_by" => 'H03-B01',
            "proxy_updated_by" => NULL,
            "updated_at" => $this->date
        ];
        return $this->_create($sql, $parram, LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    public function updateTimelineDetail($data, $jobId, $keyBatchJob)
    {
        $sql = "UPDATE t_timeline 
                SET
                    start_datetime = :start_datetime,
                    end_datetime = :end_datetime,
                    channel_type = :channel_type,
                    channel_id = :channel_id,
                    channel_detail_id = :channel_detail_id,
                    org_label = :org_label,
                    user_cd = :user_cd,
                    user_name = :user_name,
                    user_post_type = :user_post_type,
                    title = :title,
                    facility_cd = :facility_cd,
                    facility_short_name = :facility_short_name,
                    department_name = :department_name,
                    person_cd = :person_cd,
                    person_name = :person_name,
                    display_position_name = :display_position_name,
                    product_name = :product_name,
                    note = :note,
                    off_label_flag = :off_label_flag,
                    content_name = :content_name,
                    phase_name = :phase_name,
                    reaction_name = :reaction_name,
                    detail_order = :detail_order,
                    updated_by = :updated_by,
                    proxy_updated_by = :proxy_updated_by,
                    updated_at = :updated_at
                WHERE timeline_id = :timeline_id";
        $parram = [
            "timeline_id" => $data->timeline_id,
            "start_datetime" => $data->start_datetime,
            "end_datetime" => $data->end_datetime,
            "channel_type" => $data->channel_type,
            "channel_id" => $data->channel_id,
            "channel_detail_id" => $data->detail_id,
            "org_label" => $data->org_label,
            "user_cd" => $data->user_cd,
            "user_name" => $data->user_name,
            "user_post_type" => $data->user_post_type,
            "title" => $data->title,
            "facility_cd" => $data->facility_cd,
            "facility_short_name" => $data->facility_short_name,
            "department_name" => $data->department_name,
            "person_cd" => $data->person_cd,
            "person_name" => $data->person_name,
            "display_position_name" => $data->display_position_name,
            "product_name" => $data->product_name,
            "note" => $data->note,
            "off_label_flag" => $data->off_label_flag,
            "content_name" => $data->content_name,
            "phase_name" => $data->phase_name,
            "reaction_name" => $data->reaction_name,
            "detail_order" => $data->detail_order,
            "updated_by" => 'H03-B01',
            "proxy_updated_by" => NULL,
            "updated_at" => $this->date
        ];
        return $this->_update($sql, $parram, $jobId, $keyBatchJob);
    }

    public function getSystemParameterBriefing()
    {
        $sql = "SELECT parameter_name,parameter_key,parameter_value FROM c_system_parameter WHERE parameter_key = :parameter_key AND parameter_name = :parameter_name";
        return $this->_first($sql, ['parameter_key' => '入力期限', 'parameter_name' => '説明会']);
    }

    public function deleteTimelineBriefing($date, $parameter_value, $jobId, $keyBatchJob)
    {
        if ($parameter_value) {
            $start_datetime = date('Y-m-d H:i:s', strtotime("-" . ($parameter_value) . " months", strtotime($date)));
        } else {
            $start_datetime = $date;
        }
        $sql = "DELETE FROM t_timeline WHERE timeline_id IN (
                SELECT timeline_id FROM t_timeline T1
                    LEFT JOIN t_briefing T2 ON T2.briefing_id= T1.channel_id 
                WHERE T1.channel_type in (SELECT definition_value 
                                            FROM m_variable_definition 
                                            WHERE definition_name = :definition_channel AND definition_label = :definition_briefing)
                    AND (T2.briefing_id IS NULL OR T1.start_datetime > :start_datetime));";
        $parram = [
            'start_datetime' => $start_datetime,
            'definition_channel' => 'チャネル',
            'definition_briefing' => '説明会'
        ];
        return $this->_destroy($sql, $parram, LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    public function getTimelineBriefing($date)
    {
        $sql = "SELECT 
                        (SELECT timeline_id FROM t_timeline T4
                            WHERE T4.facility_cd = T3.facility_cd 
                            AND T4.person_cd = T3.person_cd 
                            AND T4.channel_type in (SELECT definition_value FROM m_variable_definition WHERE definition_name = :definition_channel AND definition_label = :definition_briefing)
                            AND T4.channel_id = T2.briefing_id
                        ) as timeline_id,
                        T1.start_time AS start_datetime,
                        T1.end_time AS end_datetime,
                        COALESCE(20) AS channel_type,
                        T2.briefing_id as channel_id,
                        COALESCE(NULL) AS channel_detail_id,
                        T2.implement_user_org_label AS org_label,
                        T2.implement_user_cd AS user_cd,
                        T2.implement_user_name AS user_name,
                        T2.implement_user_post_name AS user_post_type,
                        T2.briefing_name AS title,
                        T3.facility_cd,
                        T3.facility_short_name,
                        T3.department_name,
                        T3.person_cd,
                        T3.person_name,
                        T3.display_position_name,
                        (select GROUP_CONCAT(product_name SEPARATOR ', ') from t_briefing_product where briefing_id = T2.briefing_id) as product_name,
                        T2.note,
                        T6.definition_label AS briefing_type_name,
                        T2.facility_short_name AS briefing_facility_short_name,
                        T2.created_by,
                        T2.proxy_created_by,
                        T2.created_at,
                        T2.updated_by,
                        T2.proxy_updated_by,
                        T2.updated_at
                FROM t_schedule T1 
                    JOIN t_briefing T2 ON T1.schedule_id = T2.schedule_id
                    JOIN t_briefing_attendee T3 ON T2.briefing_id = T3.briefing_id
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = :definition_name) T6 ON T6.definition_value = T2.briefing_type 
                WHERE T1.schedule_type in (SELECT definition_value FROM m_variable_definition WHERE definition_name = :schedule_division AND definition_label = :schedule_division_briefing) 
                    AND T1.start_time > :start_time
                    AND T2.status_type in (SELECT definition_value
                                            FROM m_variable_definition 
                                            WHERE definition_name = :variable_status_type_briefing AND definition_label in ('結果入力中','結果承認待ち','結果承認済'))";
        $parram = [
            'start_time' => $date,
            'definition_name' => '説明会区分',
            'schedule_division' => 'スケジュール区分',
            'schedule_division_briefing' => '説明会',
            'definition_channel' => 'チャネル',
            'definition_briefing' => '説明会',
            'variable_status_type_briefing' => '説明会ステータス'
        ];
        return $this->_find($sql, $parram);
    }

    public function insertTimelineBriefing($data, $jobId, $keyBatchJob)
    {
        $sql = "INSERT INTO t_timeline (
            start_datetime
            ,end_datetime
            ,channel_type
            ,channel_id
            ,channel_detail_id
            ,org_label
            ,user_cd
            ,user_name
            ,user_post_type
            ,title
            ,facility_cd
            ,facility_short_name
            ,department_name
            ,person_cd
            ,person_name
            ,display_position_name
            ,product_name
            ,note
            ,briefing_type_name
            ,briefing_facility_short_name
            ,created_by
            ,proxy_created_by
            ,created_at
            ,updated_by
            ,proxy_updated_by
            ,updated_at
        ) VALUES (
            :start_datetime
            ,:end_datetime
            ,:channel_type
            ,:channel_id
            ,:channel_detail_id
            ,:org_label
            ,:user_cd
            ,:user_name
            ,:user_post_type
            ,:title
            ,:facility_cd
            ,:facility_short_name
            ,:department_name
            ,:person_cd
            ,:person_name
            ,:display_position_name
            ,:product_name
            ,:note
            ,:briefing_type_name
            ,:briefing_facility_short_name
            ,:created_by
            ,:proxy_created_by
            ,:created_at
            ,:updated_by
            ,:proxy_updated_by
            ,:updated_at)";

        $parram = [
            "start_datetime" => $data->start_datetime,
            "end_datetime" => $data->end_datetime,
            "channel_type" => $data->channel_type,
            "channel_id" => $data->channel_id,
            "channel_detail_id" => $data->channel_detail_id,
            "org_label" => $data->org_label,
            "user_cd" => $data->user_cd,
            "user_name" => $data->user_name,
            "user_post_type" => $data->user_post_type,
            "title" => $data->title,
            "facility_cd" => $data->facility_cd,
            "facility_short_name" => $data->facility_short_name,
            "department_name" => $data->department_name,
            "person_cd" => $data->person_cd,
            "person_name" => $data->person_name,
            "display_position_name" => $data->display_position_name,
            "product_name" => $data->product_name,
            "note" => $data->note,
            "briefing_type_name" => $data->briefing_type_name,
            "briefing_facility_short_name" => $data->briefing_facility_short_name,
            "created_by" => 'H03-B01',
            "proxy_created_by" => NULL,
            "created_at" => $this->date,
            "updated_by" => 'H03-B01',
            "proxy_updated_by" => NULL,
            "updated_at" => $this->date
        ];
        return $this->_create($sql, $parram, LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    public function updateTimelineBriefing($data, $jobId, $keyBatchJob)
    {
        $sql = "UPDATE t_timeline 
                SET
                    start_datetime = :start_datetime,
                    end_datetime = :end_datetime,
                    channel_type = :channel_type,
                    channel_id = :channel_id,
                    channel_detail_id = :channel_detail_id,
                    org_label = :org_label,
                    user_cd = :user_cd,
                    user_name = :user_name,
                    user_post_type = :user_post_type,
                    title = :title,
                    facility_cd = :facility_cd,
                    facility_short_name = :facility_short_name,
                    department_name = :department_name,
                    person_cd = :person_cd,
                    person_name = :person_name,
                    display_position_name = :display_position_name,
                    product_name = :product_name,
                    note = :note,
                    briefing_type_name = :briefing_type_name,
                    briefing_facility_short_name = :briefing_facility_short_name,
                    updated_by = :updated_by,
                    proxy_updated_by = :proxy_updated_by,
                    updated_at = :updated_at
                WHERE timeline_id = :timeline_id";
        $parram = [
            "timeline_id" => $data->timeline_id,
            "start_datetime" => $data->start_datetime,
            "end_datetime" => $data->end_datetime,
            "channel_type" => $data->channel_type,
            "channel_id" => $data->channel_id,
            "channel_detail_id" => $data->channel_detail_id,
            "org_label" => $data->org_label,
            "user_cd" => $data->user_cd,
            "user_name" => $data->user_name,
            "user_post_type" => $data->user_post_type,
            "title" => $data->title,
            "facility_cd" => $data->facility_cd,
            "facility_short_name" => $data->facility_short_name,
            "department_name" => $data->department_name,
            "person_cd" => $data->person_cd,
            "person_name" => $data->person_name,
            "display_position_name" => $data->display_position_name,
            "product_name" => $data->product_name,
            "note" => $data->note,
            "briefing_type_name" => $data->briefing_type_name,
            "briefing_facility_short_name" => $data->briefing_facility_short_name,
            "updated_by" => 'H03-B01',
            "proxy_updated_by" => NULL,
            "updated_at" => $this->date
        ];
        return $this->_update($sql, $parram, LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    public function getSystemParameterConvention()
    {
        $sql = "SELECT parameter_name,parameter_key,parameter_value FROM c_system_parameter where parameter_key = :parameter_key and parameter_name = :parameter_name";
        return $this->_first($sql, ['parameter_key' => '入力期限', 'parameter_name' => '講演会']);
    }

    public function deleteTimelineConvention($date, $parameter_value, $jobId, $keyBatchJob)
    {
        if ($parameter_value) {
            $start_datetime = date('Y-m-d H:i:s', strtotime("-" . ($parameter_value) . " months", strtotime($date)));
        } else {
            $start_datetime = $date;
        }
        $sql = "DELETE FROM t_timeline WHERE timeline_id IN (
                SELECT timeline_id FROM t_timeline T1
                    LEFT JOIN t_convention T2 ON T2.convention_id = T1.channel_id 
                WHERE T1.channel_type in (SELECT definition_value 
                                            FROM m_variable_definition 
                                            WHERE definition_name = :definition_channel AND definition_label = :definition_convention)
                    AND (T2.convention_id IS NULL OR T1.start_datetime > :start_datetime));";
        $parram = [
            'start_datetime' => $start_datetime,
            'definition_channel' => 'チャネル',
            'definition_convention' => '講演会'
        ];
        return $this->_destroy($sql, $parram, LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    public function getTimelineConvention($date)
    {
        $sql = "SELECT 
                        (SELECT
                            T4.timeline_id
                        FROM
                            t_timeline T4 
                            WHERE T4.channel_type IN ( SELECT definition_value FROM m_variable_definition WHERE definition_name = :definition_channel AND definition_label = :definition_convention) 
                            AND T3.person_cd = T4.person_cd 
                            AND T3.facility_cd = T4.facility_cd 
                            AND T3.convention_id = T4.channel_id 
                        ) AS timeline_id,
                        T1.start_time AS start_datetime,
                        T1.end_time AS end_datetime,
                        T1.schedule_type AS channel_type,
                        T2.convention_id AS channel_id,
                        COALESCE(NULL) AS channel_detail_id,
                        (SELECT GROUP_CONCAT(t_convention_target_org.org_label SEPARATOR ', ') FROM t_convention_target_org WHERE convention_id = T2.convention_id) AS org_label,
                        (SELECT parameter_value FROM c_system_parameter WHERE parameter_key = 'ユーザダミーコード' LIMIT 1) AS user_cd,
                        COALESCE(NULL) AS user_name,
                        COALESCE(NULL) AS user_post_type,
                        T2.convention_name AS title,
                        T3.facility_cd,
                        T3.facility_short_name,
                        T3.department_name,
                        T3.person_cd,
                        T3.person_name,
                        T3.display_position_name,
                        (SELECT GROUP_CONCAT(product_name SEPARATOR ', ') FROM t_convention_product WHERE convention_id = T2.convention_id) AS product_name,
                        T2.note,
                        T6.definition_label AS convention_type_name
                FROM t_schedule T1
                    JOIN t_convention T2 ON T1.schedule_id = T2.schedule_id
                    JOIN t_convention_attendee T3 ON T2.convention_id = T3.convention_id
                    LEFT JOIN m_variable_definition T6 ON T2.convention_type = T6.definition_value AND definition_name = '講演会区分'
                WHERE T1.schedule_type in (SELECT definition_value FROM m_variable_definition WHERE definition_name = :schedule_division AND definition_label = :schedule_division_convention)
                        AND T1.start_time > :start_time
                        AND T2.status_type in (SELECT definition_value
                                                FROM m_variable_definition 
                                                WHERE definition_name = :variable_status_type_convention AND definition_label in ('結果入力中','結果承認待ち','結果承認済'))";
        $parram = [
            'start_time' => $date,
            'schedule_division' => 'スケジュール区分',
            'schedule_division_convention' => '講演会',
            'definition_channel' => 'チャネル',
            'definition_convention' => '講演会',
            'variable_status_type_convention' => '講演会ステータス'
        ];
        return $this->_find($sql, $parram);
    }

    public function insertTimelineConvention($data, $jobId, $keyBatchJob)
    {
        $sql = "INSERT INTO t_timeline (
            start_datetime
            ,end_datetime
            ,channel_type
            ,channel_id
            ,channel_detail_id
            ,org_label
            ,user_cd
            ,user_name
            ,user_post_type
            ,title
            ,facility_cd
            ,facility_short_name
            ,department_name
            ,person_cd
            ,person_name
            ,display_position_name
            ,product_name
            ,note
            ,convention_type_name
            ,created_by
            ,proxy_created_by
            ,created_at
            ,updated_by
            ,proxy_updated_by
            ,updated_at
        ) VALUES (
            :start_datetime
            ,:end_datetime
            ,:channel_type
            ,:channel_id
            ,:channel_detail_id
            ,:org_label
            ,:user_cd
            ,:user_name
            ,:user_post_type
            ,:title
            ,:facility_cd
            ,:facility_short_name
            ,:department_name
            ,:person_cd
            ,:person_name
            ,:display_position_name
            ,:product_name
            ,:note
            ,:convention_type_name
            ,:created_by
            ,:proxy_created_by
            ,:created_at
            ,:updated_by
            ,:proxy_updated_by
            ,:updated_at)";

        $parram = [
            "start_datetime" => $data->start_datetime,
            "end_datetime" => $data->end_datetime,
            "channel_type" => $data->channel_type,
            "channel_id" => $data->channel_id,
            "channel_detail_id" => $data->channel_detail_id,
            "org_label" => $data->org_label,
            "user_cd" => $data->user_cd,
            "user_name" => $data->user_name,
            "user_post_type" => $data->user_post_type,
            "title" => $data->title,
            "facility_cd" => $data->facility_cd,
            "facility_short_name" => $data->facility_short_name,
            "department_name" => $data->department_name,
            "person_cd" => $data->person_cd,
            "person_name" => $data->person_name,
            "display_position_name" => $data->display_position_name,
            "product_name" => $data->product_name,
            "note" => $data->note,
            "convention_type_name" => $data->convention_type_name,
            "created_by" => 'H03-B01',
            "proxy_created_by" => NULL,
            "created_at" => $this->date,
            "updated_by" => 'H03-B01',
            "proxy_updated_by" => NULL,
            "updated_at" => $this->date
        ];
        return $this->_create($sql, $parram, LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    public function updateTimelineConvention($data, $jobId, $keyBatchJob)
    {
        $sql = "UPDATE t_timeline 
                SET
                    start_datetime = :start_datetime,
                    end_datetime = :end_datetime,
                    channel_type = :channel_type,
                    channel_id = :channel_id,
                    channel_detail_id = :channel_detail_id,
                    org_label = :org_label,
                    user_cd = :user_cd,
                    user_name = :user_name,
                    user_post_type = :user_post_type,
                    title = :title,
                    facility_cd = :facility_cd,
                    facility_short_name = :facility_short_name,
                    department_name = :department_name,
                    person_cd = :person_cd,
                    person_name = :person_name,
                    display_position_name = :display_position_name,
                    product_name = :product_name,
                    note = :note,
                    convention_type_name = :convention_type_name,
                    updated_by = :updated_by,
                    proxy_updated_by = :proxy_updated_by,
                    updated_at = :updated_at
                WHERE timeline_id = :timeline_id";
        $parram = [
            "timeline_id" => $data->timeline_id,
            "start_datetime" => $data->start_datetime,
            "end_datetime" => $data->end_datetime,
            "channel_type" => $data->channel_type,
            "channel_id" => $data->channel_id,
            "channel_detail_id" => $data->channel_detail_id,
            "org_label" => $data->org_label,
            "user_cd" => $data->user_cd,
            "user_name" => $data->user_name,
            "user_post_type" => $data->user_post_type,
            "title" => $data->title,
            "facility_cd" => $data->facility_cd,
            "facility_short_name" => $data->facility_short_name,
            "department_name" => $data->department_name,
            "person_cd" => $data->person_cd,
            "person_name" => $data->person_name,
            "display_position_name" => $data->display_position_name,
            "product_name" => $data->product_name,
            "note" => $data->note,
            "convention_type_name" => $data->convention_type_name,
            "updated_by" => 'H03-B01',
            "proxy_updated_by" => NULL,
            "updated_at" => $this->date
        ];
        return $this->_update($sql, $parram, LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
}
