<?php

namespace App\Repositories\BriefingSearch;

use App\Repositories\BaseRepository;
use App\Repositories\BriefingSearch\BriefingSearchRepositoryInterface;
use App\Traits\DateTimeTrait;

class BriefingSearchRepository extends BaseRepository implements BriefingSearchRepositoryInterface
{
    use DateTimeTrait;
    protected $useAutoMeta = true;
    private $date, $briefing_session, $information_session, $bento_disposal_method, $display_option_name;
    private $schedule_type, $briefing_type, $result_only_flag;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d');
        $this->briefing_session = "説明会区分";
        $this->information_session = "説明会ステータス";
        $this->bento_disposal_method = "弁当処分方法";
        $this->approval_business = "承認業務区分";
        $this->user_title_classification = "ユーザ役職区分";
        $this->display_option_name = "説明会";
        $this->schedule_type = "スケジュール区分";
        $this->briefing_type = "説明会";
        // result_only_flag = 0 default spec B01-S02
        $this->result_only_flag = 0;
        $this->approval_hierarchy = '承認階層';
    }

    public function getBriefingSession()
    {
        $query['definition_name'] = $this->briefing_session;
        $sql = "SELECT definition_value as briefing_type_value,definition_label as briefing_type_label FROM m_variable_definition WHERE definition_name = :definition_name ORDER BY sort_order";
        return $this->_find($sql, $query);
    }

    public function getInformationSession()
    {
        $query['definition_name'] = $this->information_session;
        $sql = "SELECT definition_value as status_type,definition_label as status_type_label FROM m_variable_definition WHERE definition_name = :definition_name";
        return $this->_find($sql, $query);
    }

    public function getVariableDefinitionApprovalWorkType($variable_name)
    {
        $query['definition_name'] = $this->approval_business;
        $query['definition_label'] = $variable_name;
        $sql = "SELECT definition_value FROM m_variable_definition WHERE definition_name = :definition_name AND definition_label = :definition_label";
        return $this->_first($sql, $query);
    }

    public function getListDataBriefing($request)
    {
        $query['briefing_session'] = $this->briefing_session;
        $query['information_session'] = $this->information_session;
        $where = "";
        if ($request->status_type) {
            $where .= " AND T1.status_type = :status_type";
            $query['status_type'] = $request->status_type;
        }
        if ($request->briefing_type) {
            $where .= " AND T1.briefing_type = :briefing_type";
            $query['briefing_type'] = $request->briefing_type;
        }
        // filter start_date
        if ($request->start_date) {
            $where .= " AND T2.start_date >= :start_date";
            $query['start_date'] = $request->start_date;
        }
        // filter end_date
        if ($request->end_date) {
            $where .= " AND T2.start_date <= :end_date";
            $query['end_date'] = $request->end_date;
        }
        if ($request->remand_flag) {
            $where .= " AND T1.remand_flag = :remand_flag AND T1.implement_user_cd = :implement_user_cd";
            $query['remand_flag'] = $request->remand_flag;
            $query['implement_user_cd'] = $request->user_login;
        }
        if ($request->briefing_id) {
            $where .= " AND T1.briefing_id = :briefing_id";
            $query['briefing_id'] = $request->briefing_id;
        }
        if ($request->approval_flag) {
            $where .= " AND T1.status_type in (20,50)
                        AND T7.request_type IN (20,30)
                        AND T7.status_type = 0
                        AND T8.status_type = 0
                        AND T8.approval_user_cd = :user_login
                        AND T7.active_layer_num = T8.layer_num";
            $query['user_login'] = $request->user_login;
        }
        // filter facility_cd
        $facility_cd = [];
        if ($request->facility_cd) {
            $facility_cd = explode(',', $request->facility_cd);
        }
        if (count($facility_cd) > 0) {
            $where .= " AND T1.facility_cd IN " . $this->_buildCommaString($facility_cd, true);
        }
        // filter org_cd
        $org_cd = [];
        if ($request->org_cd) {
            $org_cd = explode(',', $request->org_cd);
        }
        if (count($org_cd) > 0) {
            $where .= " AND T1.implement_user_org_cd IN " . $this->_buildCommaString($org_cd, true);
        }
        // filter user_cd
        $user_cd = [];
        if ($request->user_cd) {
            $user_cd = explode(',', $request->user_cd);
        }
        if (count($user_cd) > 0) {
            $where .= " AND T1.implement_user_cd IN " . $this->_buildCommaString($user_cd, true);
        }
        // filter product_cd
        $product_cd = [];
        if ($request->product_cd) {
            $product_cd = explode(',', $request->product_cd);
        }
        if (count($product_cd) > 0) {
            $where .= " AND T9.product_cd IN " . $this->_buildCommaString($product_cd, true);
        }
        $sql = "SELECT T1.briefing_id,
                        T2.schedule_id,
                        T2.start_date,
                        T2.start_time,
                        T2.end_time,
                        T1.status_type,
                        T3.definition_label AS status_label,
                        T1.briefing_type,
                        T4.definition_label AS briefing_label,
                        (SELECT SUM(plan_headcount) FROM t_briefing_attendee_count WHERE briefing_id = T1.briefing_id) AS plan_headcount,
                        (SELECT SUM(result_headcount) FROM t_briefing_attendee_count WHERE briefing_id = T1.briefing_id) AS result_headcount,
                        (SELECT SUM(plan_amount) FROM t_briefing_expense_detail WHERE briefing_id = T1.briefing_id) AS plan_amount,
                        (SELECT SUM(result_amount) FROM t_briefing_expense_detail WHERE briefing_id = T1.briefing_id) AS result_amount,
                        T1.remand_flag,
                        T1.briefing_name,
                        T1.implement_user_name,
                        T1.implement_user_org_label,
                        T1.facility_short_name,
                        T1.facility_cd,
                        (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                            'product_cd', product_cd,
                            'product_name', product_name
                            )),']') FROM t_briefing_product 
                            WHERE briefing_id = T1.briefing_id
                        ) AS briefing_product
                FROM t_briefing T1
                    JOIN t_schedule T2 on T1.schedule_id = T2.schedule_id
                    LEFT JOIN t_briefing_product T9 on T1.briefing_id = T9.briefing_id
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = :information_session) T3 ON T1.status_type = T3.definition_value
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = :briefing_session) T4 ON T1.briefing_type = T4.definition_value
                    LEFT JOIN t_approval_request T7 ON T7.request_target_id = T1.briefing_id
                    LEFT JOIN t_approval_request_detail T8 ON T8.request_id = T7.request_id
                WHERE 1 = 1 " . $where . "
                GROUP BY T1.briefing_id
                ORDER BY T2.start_date DESC,T2.start_time DESC,T1.briefing_id DESC;";
        return $this->_paginate($sql, $query, [
            "limit" => $request->limit,
            "offset" => $request->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getBentoDisposalMethod()
    {
        $query['definition_name'] = $this->bento_disposal_method;
        $sql = "SELECT definition_value as bento_disposal_value,definition_label as bento_disposal_label FROM m_variable_definition WHERE definition_name = :definition_name";
        return $this->_find($sql, $query);
    }

    public function getBriefingAttendeeCount($briefing_id)
    {
        $query = [];
        $join = "";
        if (!$briefing_id) {
            $join .= " AND T2.briefing_id IS NULL";
        } else {
            $join .= " AND T2.briefing_id = :briefing_id";
            $query['briefing_id'] = $briefing_id;
        }
        $sql = "SELECT T1.occupation_type,
                    T1.occupation_name,
                    T1.sort_order,
                    T2.briefing_id,
                    T2.plan_headcount,
                    T2.result_headcount
                FROM m_briefing_attendee_occupation T1 
                    LEFT JOIN t_briefing_attendee_count T2 ON T1.occupation_type = T2.occupation_type" . $join . " ORDER BY T1.sort_order";
        return $this->_find($sql, $query);
    }

    public function getBriefingExpenseItem($briefing_id)
    {
        $query['definition_name'] = '経費項目_数量単位';
        $join = "";
        if (!$briefing_id) {
            $join .= " AND T2.briefing_id IS NULL";
        } else {
            $join .= " AND T2.briefing_id = :briefing_id";
            $query['briefing_id'] = $briefing_id;
        }
        $sql = "SELECT T1.expense_item_cd,
                    T1.expense_item_name,
                    T1.sort_order,
                    T1.unit_price_input_flag,
                    T1.quantity_input_flag,
                    T1.quantity_unit_type,
                    T2.plan_unit_price,
                    T2.plan_quantity,
                    T2.plan_amount,
                    T2.result_unit_price,
                    T2.result_quantity,
                    T2.result_amount,
                    T3.definition_label as quantity_unit_label
                FROM m_briefing_expense_item T1 
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = :definition_name) T3 ON T1.quantity_unit_type = T3.definition_value
                    LEFT JOIN t_briefing_expense_detail T2 on T1.expense_item_cd = T2.expense_item_cd" . $join;
        return $this->_find($sql, $query);
    }

    public function getVariableDefinitionBriefing()
    {
        $query['definition_name'] = $this->schedule_type;
        $query['definition_label'] = $this->briefing_type;
        $sql = "SELECT * FROM m_variable_definition WHERE definition_name = :definition_name AND definition_label = :definition_label";
        return $this->_first($sql, $query);
    }

    public function getUserApprovalBriefing($user_login, $briefing_id, $briefing_tpye, $layer_num)
    {
        $query['briefing_id'] = $briefing_id;
        $query['briefing_tpye'] = $briefing_tpye;
        $where = "";
        if ($user_login) {
            $where .= " AND T4.approval_user_cd = :approval_user_cd";
            $query['approval_user_cd'] = $user_login;
        }
        if ($layer_num) {
            $where .= " AND T4.layer_num = :layer_num";
            $query['layer_num'] = $layer_num;
        }
        $sql = "SELECT T4.status_type,T4.comment,T5.user_name,T4.approval_user_cd,T4.layer_num as approval_layer_num
                FROM t_briefing T2
                    JOIN t_approval_request T3 ON T2.briefing_id = T3.request_target_id AND T3.request_type = :briefing_tpye
                    JOIN t_approval_request_detail T4 ON T3.request_id = T4.request_id
                    JOIN m_user T5 ON T4.approval_user_cd = T5.user_cd
                WHERE T2.briefing_id = :briefing_id " . $where . " GROUP BY T4.approval_user_cd,T4.layer_num";
        return $this->_find($sql, $query);
    }

    public function getUserApproval($approval_work_type, $request_user_cd)
    {
        $query['approval_work_type'] = $approval_work_type;
        $query['request_user_cd'] = $request_user_cd;
        $query['start_date'] = $this->date;
        $query['end_date'] = $this->date;
        $sql = "SELECT approval_user_cd,approval_layer_num
                FROM h_approval_user T1 
                WHERE approval_work_type = :approval_work_type 
                    AND request_user_cd = :request_user_cd
                    AND start_date <= :start_date 
                    AND end_date >= :end_date
                GROUP BY approval_user_cd, approval_layer_num";
        return $this->_find($sql, $query);
    }

    public function getDataDetailBriefingByBriefingID($briefing_id, $schedule_id)
    {
        $where = "";
        $query['briefing_id'] = $briefing_id;
        $query['schedule_id'] = $schedule_id;
        $sql = "SELECT T1.*,
                        T2.schedule_id,
                        T2.start_date,
                        T2.start_time,
                        T2.end_time,
                		(SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                                            'product_cd', product_cd,
                                            'product_name', product_name,
                                            'delete_flag', 0
                                            )),']') FROM t_briefing_product 
                                            WHERE briefing_id = T1.briefing_id
                                        ) AS briefing_product,
                		(SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                                            'document_id', B.document_id,
                                            'document_name', B.document_name,
                                            'file_type', B.file_type,
                                            'start_date', B.start_date,
                                            'end_date', B.end_date,
                                            'document_type', B.document_type,
                                            'delete_flag', 0
                                            )),']') FROM t_briefing_document A JOIN t_document B on A.document_id = B.document_id
                                            WHERE A.briefing_id = T1.briefing_id 
                                                -- AND start_date <= :start_date
                                            	-- AND end_date >= :end_date
                                        ) AS briefing_document,
                		(SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                                            'briefing_attendee_id', A.briefing_attendee_id,
                                            'briefing_id', A.briefing_id,
                                            'facility_cd', A.facility_cd,
                                            'facility_short_name', A.facility_short_name,
                                            'department_cd', A.department_cd,
                                            'department_name', A.department_name,
                                            'person_cd', A.person_cd,
                                            'person_name', A.person_name,
                                            'display_position_cd', A.display_position_cd,
                                            'display_position_name', A.display_position_name,
                                            'medical_staff_cd', A.other_medical_staff_type,
                                            'medical_staff_name', B.medical_staff_name,
                                            'other_person_flag', A.other_person_flag,
                                            'delete_flag', 0
                                            )),']') FROM t_briefing_attendee A
                                            		LEFT JOIN m_medical_staff B ON A.other_medical_staff_type = B.medical_staff_cd
                                            WHERE A.briefing_id = T1.briefing_id 
                                        ) AS briefing_attendee
                from t_briefing T1 
                	JOIN t_schedule T2 ON T1.schedule_id = T2.schedule_id
                	LEFT JOIN m_medical_subjects_group T3 ON T1.medical_subjects_group_cd = T3.medical_subjects_group_cd
                WHERE T1.briefing_id = :briefing_id OR T2.schedule_id = :schedule_id GROUP BY T1.briefing_id";
        return $this->_first($sql, $query);
    }

    public function getDisplayOptionTpyeBriefing()
    {
        $query['display_option_name'] = $this->display_option_name;
        $sql = "SELECT * FROM m_display_option WHERE display_option_name = :display_option_name";
        return $this->_first($sql, $query);
    }

    public function saveBriefing($request)
    {
        $sql = "INSERT INTO t_briefing(schedule_id
                            ,status_type
                            ,briefing_type
                            ,briefing_name
                            ,implement_user_cd
                            ,target_org_cd
                            ,facility_cd
                            ,medical_subjects_group_cd
                            ,place
                            ,content
                            ,note
                            ,implement_user_name
                            ,implement_user_post
                            ,implement_user_post_name
                            ,implement_user_org_cd
                            ,implement_user_org_label
                            ,target_org_label
                            ,facility_short_name
                            ,medical_subjects_group_name
                            ,result_only_flag
                            ,reason
                            ,disposal_technique)
                        VALUES(:schedule_id
                            ,:status_type
                            ,:briefing_type
                            ,:briefing_name
                            ,:implement_user_cd
                            ,:target_org_cd
                            ,:facility_cd
                            ,:medical_subjects_group_cd
                            ,:place
                            ,:content
                            ,:note
                            ,:implement_user_name
                            ,:implement_user_post
                            ,:implement_user_post_name
                            ,:implement_user_org_cd
                            ,:implement_user_org_label
                            ,:target_org_label
                            ,:facility_short_name
                            ,:medical_subjects_group_name
                            ,:result_only_flag
                            ,:reason
                            ,:disposal_technique);";
        $parram = [
            "schedule_id" => $request->schedule_id,
            "status_type" => $request->status_type,
            "briefing_type" => $request->briefing_type,
            "briefing_name" => $request->briefing_name,
            "implement_user_cd" => $request->implement_user_cd,
            "target_org_cd" => $request->target_org_cd,
            "facility_cd" => $request->facility_cd,
            "medical_subjects_group_cd" => $request->medical_subjects_group_cd,
            "place" => $request->place,
            "content" => $request->content,
            "note" => $request->note,
            "implement_user_name" => $request->implement_user_name,
            "implement_user_post" => $request->implement_user_post,
            "implement_user_post_name" => $request->implement_user_post_name,
            "implement_user_org_cd" => $request->implement_user_org_cd,
            "implement_user_org_label" => $request->implement_user_org_label,
            "target_org_label" => $request->target_org_label,
            "facility_short_name" => $request->facility_short_name,
            "medical_subjects_group_name" => $request->medical_subjects_group_name,
            "reason" => $request->reason,
            "disposal_technique" => $request->disposal_technique,
            "result_only_flag" => $this->result_only_flag
        ];
        $this->_create($sql, $parram);
        return $this->_lastInserted('t_briefing', 'briefing_id');
    }

    public function updateBriefing($request)
    {
        $sql = "UPDATE t_briefing 
                SET status_type = :status_type
                    ,briefing_type = :briefing_type
                    ,briefing_name = :briefing_name
                    ,implement_user_cd = :implement_user_cd
                    ,target_org_cd = :target_org_cd
                    ,facility_cd = :facility_cd
                    ,medical_subjects_group_cd = :medical_subjects_group_cd
                    ,place = :place
                    ,content = :content
                    ,note = :note
                    ,implement_user_name = :implement_user_name
                    ,implement_user_post = :implement_user_post
                    ,implement_user_post_name = :implement_user_post_name
                    ,implement_user_org_cd = :implement_user_org_cd
                    ,implement_user_org_label = :implement_user_org_label
                    ,target_org_label = :target_org_label
                    ,facility_short_name = :facility_short_name
                    ,medical_subjects_group_name = :medical_subjects_group_name
                    ,result_only_flag = :result_only_flag
                    ,reason = :reason
                    ,disposal_technique = :disposal_technique
                WHERE briefing_id = :briefing_id;";
        $parram = [
            "briefing_id" => $request->briefing_id,
            "status_type" => $request->status_type,
            "briefing_type" => $request->briefing_type,
            "briefing_name" => $request->briefing_name,
            "implement_user_cd" => $request->implement_user_cd,
            "target_org_cd" => $request->target_org_cd,
            "facility_cd" => $request->facility_cd,
            "medical_subjects_group_cd" => $request->medical_subjects_group_cd,
            "place" => $request->place,
            "content" => $request->content,
            "note" => $request->note,
            "implement_user_name" => $request->implement_user_name,
            "implement_user_post" => $request->implement_user_post,
            "implement_user_post_name" => $request->implement_user_post_name,
            "implement_user_org_cd" => $request->implement_user_org_cd,
            "implement_user_org_label" => $request->implement_user_org_label,
            "target_org_label" => $request->target_org_label,
            "facility_short_name" => $request->facility_short_name,
            "medical_subjects_group_name" => $request->medical_subjects_group_name,
            "reason" => $request->reason,
            "disposal_technique" => $request->disposal_technique,
            "result_only_flag" => $this->result_only_flag
        ];
        return $this->_update($sql, $parram);
    }

    public function saveBriefingProduct($briefing_id, $product_cd, $product_name)
    {
        $query['briefing_id'] = $briefing_id;
        $query['product_cd'] = $product_cd;
        $query['product_name'] = $product_name;
        $sql = "INSERT INTO t_briefing_product(briefing_id,product_cd,product_name) VALUES (:briefing_id,:product_cd,:product_name);";
        return $this->_create($sql, $query);
    }

    public function deleteBriefingProduct($briefing_id, $product_cd)
    {
        $query['briefing_id'] = $briefing_id;
        $query['product_cd'] = $product_cd;
        $sql = "DELETE FROM t_briefing_product WHERE briefing_id = :briefing_id AND product_cd = :product_cd;";
        return $this->_destroy($sql, $query);
    }

    public function saveBriefingDocument($briefing_id, $document_id)
    {
        $query['briefing_id'] = $briefing_id;
        $query['document_id'] = $document_id;
        $sql = "INSERT INTO t_briefing_document(briefing_id,document_id) VALUES (:briefing_id,:document_id);";
        return $this->_create($sql, $query);
    }

    public function deleteBriefingDocument($briefing_id, $document_id)
    {
        $query['briefing_id'] = $briefing_id;
        $query['document_id'] = $document_id;
        $sql = "DELETE FROM t_briefing_document WHERE briefing_id = :briefing_id AND document_id = :document_id;";
        return $this->_destroy($sql, $query);
    }

    public function saveBriefingPlanAttendeeCount($briefing_id, $occupation_type, $plan_headcount, $result_headcount)
    {
        $query['briefing_id'] = $briefing_id;
        $query['occupation_type'] = $occupation_type;
        $query['plan_headcount'] = $plan_headcount;
        $query['result_headcount'] = $result_headcount;
        $sql = "INSERT INTO t_briefing_attendee_count(briefing_id,occupation_type,plan_headcount,result_headcount) VALUES (:briefing_id,:occupation_type,:plan_headcount,:result_headcount);";
        return $this->_create($sql, $query);
    }

    public function updateBriefingPlanAttendeeCount($briefing_id, $occupation_type, $plan_headcount, $result_headcount)
    {
        $query['briefing_id'] = $briefing_id;
        $query['occupation_type'] = $occupation_type;
        $query['plan_headcount'] = $plan_headcount;
        $query['result_headcount'] = $result_headcount;
        $sql = "UPDATE t_briefing_attendee_count SET plan_headcount = :plan_headcount, result_headcount = :result_headcount WHERE briefing_id = :briefing_id AND occupation_type = :occupation_type";
        return $this->_update($sql, $query);
    }

    public function updateBriefingResultAttendeeCount($briefing_id, $occupation_type, $result_headcount)
    {
        $query['briefing_id'] = $briefing_id;
        $query['occupation_type'] = $occupation_type;
        $query['result_headcount'] = $result_headcount;
        $sql = "UPDATE t_briefing_attendee_count SET result_headcount = :result_headcount WHERE briefing_id = :briefing_id AND occupation_type = :occupation_type";
        return $this->_update($sql, $query);
    }

    public function updateStatusTypeBriefing($briefing_id, $status_type)
    {
        $query['briefing_id'] = $briefing_id;
        $query['status_type'] = $status_type;
        $sql = "UPDATE t_briefing SET status_type = :status_type WHERE briefing_id = :briefing_id";
        return $this->_update($sql, $query);
    }

    public function saveBriefingAttendee($briefing_id, $data)
    {
        $sql = "INSERT INTO t_briefing_attendee(briefing_id
                            ,person_cd
                            ,facility_cd
                            ,person_name
                            ,person_name_kana
                            ,facility_short_name
                            ,facility_name_kana
                            ,department_cd
                            ,department_name
                            ,display_position_cd
                            ,display_position_name
                            ,other_medical_staff_type
                            ,other_person_flag) 
                VALUES (:briefing_id
                        ,:person_cd
                        ,:facility_cd
                        ,:person_name
                        ,:person_name_kana
                        ,:facility_short_name
                        ,:facility_name_kana
                        ,:department_cd
                        ,:department_name
                        ,:display_position_cd
                        ,:display_position_name
                        ,:other_medical_staff_type
                        ,:other_person_flag);";
        $parram = [
            "briefing_id" => $briefing_id,
            "person_cd" => $data->person_cd ?? null,
            "facility_cd" => $data->facility_cd ?? null,
            "person_name" => $data->person_name ?? null,
            "person_name_kana" => $data->person_name_kana ?? null,
            "facility_short_name" => $data->facility_short_name ?? null,
            "facility_name_kana" => $data->facility_name_kana ?? null,
            "department_cd" => $data->department_cd ?? null,
            "department_name" => $data->department_name ?? null,
            "display_position_cd" => $data->display_position_cd ?? null,
            "display_position_name" => $data->display_position_name ?? null,
            "other_medical_staff_type" => $data->medical_staff_cd ?? null,
            "other_person_flag" => $data->other_person_flag ?? false
        ];
        return $this->_create($sql, $parram);
    }

    public function updateBriefingAttendee($data)
    {
        $sql = "UPDATE t_briefing_attendee
                SET person_cd = :person_cd
                    ,facility_cd = :facility_cd
                    ,person_name = :person_name
                    ,person_name_kana = :person_name_kana
                    ,facility_short_name = :facility_short_name
                    ,facility_name_kana = :facility_name_kana
                    ,department_cd = :department_cd
                    ,department_name = :department_name
                    ,display_position_cd = :display_position_cd
                    ,display_position_name = :display_position_name
                    ,other_medical_staff_type = :other_medical_staff_type
                    ,other_person_flag = :other_person_flag
                WHERE briefing_attendee_id = :briefing_attendee_id;";
        $parram = [
            "briefing_attendee_id" => $data->briefing_attendee_id,
            "person_cd" => $data->person_cd ?? null,
            "facility_cd" => $data->facility_cd ?? null,
            "person_name" => $data->person_name ?? null,
            "person_name_kana" => $data->person_name_kana ?? null,
            "facility_short_name" => $data->facility_short_name ?? null,
            "facility_name_kana" => $data->facility_name_kana ?? null,
            "department_cd" => $data->department_cd ?? null,
            "department_name" => $data->department_name ?? null,
            "display_position_cd" => $data->display_position_cd ?? null,
            "display_position_name" => $data->display_position_name ?? null,
            "other_medical_staff_type" => $data->medical_staff_cd ?? null,
            "other_person_flag" => $data->other_person_flag ?? false
        ];
        return $this->_update($sql, $parram);
    }

    public function deleteBriefingAttendee($briefing_attendee_id)
    {
        $sql = "DELETE FROM t_briefing_attendee WHERE briefing_attendee_id = :briefing_attendee_id;";
        $parram = [
            "briefing_attendee_id" => $briefing_attendee_id
        ];
        return $this->_destroy($sql, $parram);
    }

    public function deleteDetailBriefing($briefing_id)
    {
        $sql_t_schedule = "DELETE T1, T2, T3, T4, T5, T6, T7, T8, T9
            FROM t_schedule T1
            LEFT JOIN t_schedule_common_subject T2 ON T2.schedule_id = T1.schedule_id
            LEFT JOIN t_office_schedule T3 ON T3.schedule_id = T1.schedule_id
            LEFT JOIN t_private T4 ON T4.schedule_id = T1.schedule_id
            LEFT JOIN t_visit T5 ON T5.schedule_id = T1.schedule_id
            LEFT JOIN t_accompanying_user T6 ON T6.visit_id = T5.visit_id
            LEFT JOIN t_call T7 ON T7.visit_id = T5.visit_id
            LEFT JOIN t_detail T8 ON T8.call_id = T7.call_id
            LEFT JOIN t_detail_document T9 ON T9.detail_id = T8.detail_id
            WHERE T1.schedule_id IN (SELECT schedule_id FROM t_briefing WHERE briefing_id = :briefing_id)";
        $sql = "DELETE T1, T2, T3, T4, T5, T6
            FROM t_briefing T1
            LEFT JOIN t_briefing_expense_detail T2 ON T2.briefing_id = T1.briefing_id
            LEFT JOIN t_briefing_attendee_count T3 ON T3.briefing_id = T1.briefing_id
            LEFT JOIN t_briefing_product T4 ON T4.briefing_id = T1.briefing_id
            LEFT JOIN t_briefing_document T5 ON T5.briefing_id = T1.briefing_id
            LEFT JOIN t_briefing_attendee T6 ON T6.briefing_id = T1.briefing_id
            WHERE T1.briefing_id = :briefing_id;";
        $parram = [
            "briefing_id" => $briefing_id
        ];
        $this->_destroy($sql_t_schedule, $parram);
        return $this->_destroy($sql, $parram);
    }

    public function saveBriefingExpenseItem($briefing_id, $data)
    {
        $sql = "INSERT INTO t_briefing_expense_detail(briefing_id
                            ,expense_item_cd
                            ,plan_unit_price
                            ,plan_quantity
                            ,plan_amount
                            ,result_unit_price
                            ,result_quantity
                            ,result_amount) 
                VALUES (:briefing_id
                        ,:expense_item_cd
                        ,:plan_unit_price
                        ,:plan_quantity
                        ,:plan_amount
                        ,:result_unit_price
                        ,:result_quantity
                        ,:result_amount);";
        $parram = [
            "briefing_id" => $briefing_id,
            "expense_item_cd" => $data->expense_item_cd,
            "plan_unit_price" => $data->plan_unit_price,
            "plan_quantity" => $data->plan_quantity,
            "plan_amount" => $data->plan_amount,
            "result_unit_price" => $data->result_unit_price,
            "result_quantity" => $data->result_quantity,
            "result_amount" => $data->result_amount
        ];
        return $this->_create($sql, $parram);
    }

    public function updateBriefingExpenseItem($briefing_id, $data)
    {
        $sql = "UPDATE t_briefing_expense_detail
                SET plan_unit_price = :plan_unit_price,
                    plan_quantity = :plan_quantity,
                    plan_amount = :plan_amount,
                    result_unit_price = :result_unit_price,
                    result_quantity = :result_quantity,
                    result_amount = :result_amount
                WHERE briefing_id = :briefing_id AND expense_item_cd = :expense_item_cd";
        $parram = [
            "briefing_id" => $briefing_id,
            "expense_item_cd" => $data->expense_item_cd,
            "plan_unit_price" => $data->plan_unit_price,
            "plan_quantity" => $data->plan_quantity,
            "plan_amount" => $data->plan_amount,
            "result_unit_price" => $data->result_unit_price,
            "result_quantity" => $data->result_quantity,
            "result_amount" => $data->result_amount
        ];
        return $this->_update($sql, $parram);
    }

    public function updateBriefingResultExpenseItem($briefing_id, $data)
    {
        $sql = "UPDATE t_briefing_expense_detail
                SET result_unit_price = :result_unit_price,
                    result_quantity = :result_quantity,
                    result_amount = :result_amount
                WHERE briefing_id = :briefing_id AND expense_item_cd = :expense_item_cd";
        $parram = [
            "briefing_id" => $briefing_id,
            "expense_item_cd" => $data->expense_item_cd,
            "result_unit_price" => $data->plan_unit_price ?? null,
            "result_quantity" => $data->plan_quantity ?? null,
            "result_amount" => $data->plan_amount ?? null
        ];
        return $this->_update($sql, $parram);
    }

    public function checkUserApprovalFinal($user_login, $briefing_id, $briefing_tpye)
    {
        $sql = "SELECT T4.status_type,T4.comment,T5.user_name,T4.approval_user_cd,T4.layer_num as approval_layer_num
                FROM t_briefing T2
                    JOIN t_approval_request T3 ON T2.briefing_id = T3.request_target_id AND T3.request_type = :briefing_tpye
                    LEFT JOIN t_approval_request_detail T4 ON T3.request_id = T4.request_id
                    LEFT JOIN m_user T5 ON T4.approval_user_cd = T5.user_cd
                WHERE T2.briefing_id = :briefing_id 
                    AND T4.approval_user_cd = :approval_user_cd
                    AND T4.layer_num in (SELECT parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_name AND parameter_key = :parameter_key)
                GROUP BY T4.approval_user_cd,T4.layer_num";
        $query['briefing_id'] = $briefing_id;
        $query['briefing_tpye'] = $briefing_tpye;
        $query['approval_user_cd'] = $user_login;
        $query['parameter_name'] = $this->approval_hierarchy;
        $query['parameter_key'] = $this->briefing_type;
        return $this->_first($sql, $query);
    }

    public function updateApprovalBriefingFinal($briefing_id, $approval_briefing, $request_type)
    {
        $sql = "UPDATE t_approval_request SET status_type = :status_type WHERE request_target_id = :request_target_id AND request_type = :request_type";
        $parram = [
            "status_type" => $approval_briefing,
            "request_target_id" => $briefing_id,
            "request_type" => $request_type
        ];
        return $this->_update($sql, $parram);
    }

    public function getRequestIDApproval($briefing_id, $request_type)
    {
        $sql = "SELECT * FROM t_approval_request WHERE request_target_id = :request_target_id AND request_type = :request_type";
        $parram = [
            "request_target_id" => $briefing_id,
            "request_type" => $request_type
        ];
        return $this->_first($sql, $parram);
    }

    public function updateRemandFlagBriefing($briefing_id, $remand_flag)
    {
        $sql = "UPDATE t_briefing SET remand_flag = :remand_flag WHERE briefing_id = :briefing_id";
        $parram = [
            "remand_flag" => $remand_flag,
            "briefing_id" => $briefing_id
        ];
        return $this->_update($sql, $parram);
    }

    public function updateApprovalBriefingDetail($request_id, $user_login, $approval_briefing, $comment, $layer_num)
    {
        $sql = "UPDATE t_approval_request_detail 
                SET status_type = :status_type,
                    comment = :comment
                WHERE request_id = :request_id 
                    AND approval_user_cd = :approval_user_cd
                    AND layer_num = :layer_num";
        $parram = [
            "status_type" => $approval_briefing,
            "comment" => $comment,
            "request_id" => $request_id,
            "approval_user_cd" => $user_login,
            "layer_num" => $layer_num
        ];
        return $this->_update($sql, $parram);
    }

    public function getLayerNumApprovalFinnalBriefing()
    {
        $sql = "SELECT parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_name AND parameter_key = :parameter_key;";
        $query['parameter_name'] = $this->approval_hierarchy;
        $query['parameter_key'] = $this->briefing_type;
        return $this->_first($sql, $query);
    }

    public function deleteApprovalRequest($request_id)
    {
        $sql_approval_request = "DELETE FROM t_approval_request WHERE request_id = :request_id";
        $sql_approval_request_detail = "DELETE FROM t_approval_request_detail WHERE request_id = :request_id";
        $parram['request_id'] = $request_id;
        $this->_destroy($sql_approval_request_detail, $parram);
        return $this->_destroy($sql_approval_request, $parram);
    }
}
