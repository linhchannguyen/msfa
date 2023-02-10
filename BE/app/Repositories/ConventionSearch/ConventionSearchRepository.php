<?php

namespace App\Repositories\ConventionSearch;

use App\Repositories\BaseRepository;
use App\Repositories\ConventionSearch\ConventionSearchRepositoryInterface;
use App\Traits\StringConvertTrait;
use App\Traits\DateTimeTrait;

class ConventionSearchRepository extends BaseRepository implements ConventionSearchRepositoryInterface
{
    use StringConvertTrait, DateTimeTrait;
    protected $useAutoMeta = true;
    private $date, $document_usage_type, $approval_work_type, $status_type, $remand_flag, $quantity_unit, $layer_parent, $layer_children, $setting_name_user, $variable_status_type, $variable_convention_type, $display_option_name, $file_usage_type;
    private $approval_convention, $remand_convention, $date_input_deadline, $all_select_org;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d');
        $this->date_time = $this->currentJapanDateTime('Y-m-d H:i:s');
        $this->approval_work_type = 2;
        // default value created convention
        $this->status_type = 10;
        $this->remand_flag = 0;
        $this->quantity_unit = '経費項目_数量単位';
        $this->layer_parent = 1;
        $this->layer_children = 2;
        $this->setting_name_user = 'ユーザダミーコード';
        $this->variable_status_type = '講演会ステータス';
        $this->variable_convention_type = '講演会区分';
        $this->display_option_name = "講演会";
        $this->file_usage_type = "ファイル利用種別";
        $this->document_usage_type = "資材使用機能区分";
        $this->approval_convention = "承認階層";
        $this->remand_convention = 2;
        $this->date_input_deadline = '入力期限';
        $this->all_select_org = '対象組織_全選択可能組織コード';
    }

    public function getStatusType()
    {
        $query['definition_name'] = $this->variable_status_type;
        $sql = "SELECT definition_value as status_type,definition_label as status_type_label FROM m_variable_definition WHERE definition_name = :definition_name";
        return $this->_find($sql, $query);
    }

    public function getInputDeadlineConvention($display_option_name)
    {
        $query['parameter_name'] = $display_option_name;
        $query['parameter_key'] = $this->date_input_deadline;
        $sql = "SELECT parameter_name,parameter_key,parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_name AND parameter_key = :parameter_key;";
        return $this->_first($sql, $query);
    }

    public function getAllSelectOrgCode()
    {
        $query['parameter_name'] = $this->display_option_name;
        $query['parameter_key'] = $this->all_select_org;
        $sql = "SELECT parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_name AND parameter_key = :parameter_key;";
        return $this->_first($sql, $query);
    }

    public function getConventionType()
    {
        $query['definition_name'] = $this->variable_convention_type;
        $sql = "SELECT definition_value as convention_type,definition_label as convention_type_label FROM m_variable_definition WHERE definition_name = :definition_name ORDER BY sort_order ";
        return $this->_find($sql, $query);
    }

    public function allData($request)
    {
        $where = "";
        $query = [];
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
        // filter plan_org_cd
        $plan_org_cd = [];
        if ($request->plan_org_cd) {
            $plan_org_cd = explode(',', $request->plan_org_cd);
        }
        if ($request->plan_org_cd) {
            $query['org_cd'] =  $request->plan_org_cd;
            $sql_org_detail = "SELECT * FROM m_org WHERE org_cd = :org_cd";
            $org_detail = $this->_first($sql_org_detail, ['org_cd' => $request->plan_org_cd]);
            $where .= " AND T1.plan_org_cd IN (SELECT org_cd FROM m_org WHERE layer_" . $org_detail->layer_num . " = :org_cd)";
        }
        $org_cd = [];
        // filter org_label
        if ($request->org_cd) {
            $sql_org_detail = "SELECT * FROM m_org WHERE org_cd = :org_cd";
            $org_detail = $this->_first($sql_org_detail, ['org_cd' => $request->org_cd]);
            $sql_list_org_origin_and_children = "SELECT CONCAT(A.list_org,',',A.layer) as list_org from (select GROUP_CONCAT(org_cd) as list_org,CASE WHEN layer_num = 1 THEN layer_1
                                            WHEN layer_num = 2 THEN CONCAT(layer_1)
                                            WHEN layer_num = 3 THEN CONCAT(layer_1,',',layer_2)
                                            WHEN layer_num = 4 THEN CONCAT(layer_1,',',layer_2,',',layer_3)
                                            WHEN layer_num = 5 THEN CONCAT(layer_1,',',layer_2,',',layer_3,',',layer_4)
                                            END layer
                                            FROM m_org WHERE layer_" . $org_detail->layer_num . " = :org_cd) A";
            $list_org_origin_and_children = $this->_first($sql_list_org_origin_and_children, ['org_cd' => $request->org_cd]);
            $org_cd = explode(',', $list_org_origin_and_children->list_org);
            $where .= " AND T3.org_cd IN " . $this->_buildCommaString($org_cd, true);
        }
        // filter product_cd
        $product_cd = [];
        if ($request->product_cd) {
            $product_cd = explode(',', $request->product_cd);
        }
        if (count($product_cd) > 0) {
            $where .= " AND T4.product_cd IN " . $this->_buildCommaString($product_cd, true);
        }
        // filter convention_id
        if ($request->convention_name) {
            $where .= " AND T1.convention_name like :convention_name";
            $query['convention_name'] = "%" . trim($this->convert_kana($request->convention_name)) . "%";
        }
        // filter convention_id
        if ($request->convention_id) {
            $where .= " AND T1.convention_id = :convention_id";
            $query['convention_id'] = $request->convention_id;
        }
        // filter convention_type
        if ($request->convention_type) {
            $where .= " AND T1.convention_type = :convention_type";
            $query['convention_type'] = $request->convention_type;
        }
        // filter status_type
        if ($request->status_type) {
            $where .= " AND T1.status_type = :status_type";
            $query['status_type'] = $request->status_type;
        }
        // filter status_type
        if ($request->remand_flag) {
            $where .= " AND T1.remand_flag = :remand_flag AND T1.plan_user_cd = :plan_user_cd";
            $query['plan_user_cd'] = $request->user_login;
            $query['remand_flag'] = $request->remand_flag;
        }
        // filter approval convention
        if ($request->approved_flag) {
            $where .= " AND T1.status_type in (20,50)
                        AND T7.request_type IN (40,50)
                        AND T7.status_type = 0
                        AND T8.status_type = 0
                        AND T8.approval_user_cd = :user_login
                        AND T7.active_layer_num = T8.layer_num";
            $query['user_login'] = $request->user_login;
        }
        $sql = "SELECT T1.remand_flag,
                        T2.start_date,
                        T2.start_time,
                        T2.end_time,
                        T5.definition_label AS status_type_label,
                        T1.status_type,
                        T1.convention_name,
                        T1.convention_id,
                        T6.definition_label AS convention_type,
                        T1.plan_org_label,
                        (SELECT GROUP_CONCAT(product_name separator ', ')
                        FROM t_convention_product
                        WHERE convention_id = T1.convention_id) AS product_name,
                        (SELECT GROUP_CONCAT(org_label separator ', ')
                        FROM t_convention_target_org
                        WHERE convention_id = T1.convention_id) AS org_label
                FROM t_convention T1 
                    LEFT JOIN t_schedule T2 ON T1.schedule_id = T2.schedule_id
                    LEFT JOIN t_convention_target_org T3 ON T1.convention_id = T3.convention_id
                    LEFT JOIN t_convention_product T4 on T4.convention_id = T1.convention_id
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = :variable_status_type) T5 ON T1.status_type = T5.definition_value
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = :variable_convention_type) T6 ON T1.convention_type = T6.definition_value
                    LEFT JOIN t_approval_request T7 ON T7.request_target_id = T1.convention_id
                    LEFT JOIN t_approval_request_detail T8 ON T8.request_id = T7.request_id
                WHERE 1 = 1 " . $where . " GROUP BY T1.convention_id 
                ORDER BY T2.start_date DESC, T2.start_time DESC ,T1.convention_id DESC";
        $query['variable_status_type'] =   $this->variable_status_type;
        $query['variable_convention_type'] = $this->variable_convention_type;
        return $this->_paginate($sql, $query, [
            "limit" => $request->limit,
            "offset" => $request->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getHoldType()
    {
        $query['definition_name'] = '開催区分';
        $sql = "SELECT definition_value as hold_type_value,definition_label as hold_type_label FROM m_variable_definition WHERE definition_name = :definition_name";
        return $this->_find($sql, $query);
    }

    public function getHoldMethod()
    {
        $query['definition_name'] = '開催方法';
        $sql = "SELECT definition_value as hold_method_value,definition_label as hold_method_label FROM m_variable_definition WHERE definition_name = :definition_name";
        return $this->_find($sql, $query);
    }

    public function getOrganizationLayer()
    {
        $query['definition_name'] = '組織階層名';
        $sql = "SELECT definition_value as organization_layer_value,definition_label as organization_layer_label FROM m_variable_definition WHERE definition_name = :definition_name";
        return $this->_find($sql, $query);
    }

    public function getHoldForm()
    {
        $query['definition_name'] = '開催形態';
        $sql = "SELECT definition_value as hold_form_value,definition_label as hold_form_label FROM m_variable_definition WHERE definition_name = :definition_name";
        return $this->_find($sql, $query);
    }

    public function getCostShareType()
    {
        $query['definition_name'] = '開催費分担';
        $sql = "SELECT definition_value as cost_share_type_value,definition_label as cost_share_type_label FROM m_variable_definition WHERE definition_name = :definition_name";
        return $this->_find($sql, $query);
    }

    public function getExpenseClassification()
    {
        $query['definition_name'] = '経費区分';
        $sql = "SELECT definition_value as expense_classification_value,definition_label as expense_classification_label FROM m_variable_definition WHERE definition_name = :definition_name";
        return $this->_find($sql, $query);
    }

    public function getSeriesRegistration()
    {
        $query['definition_name'] = 'シリーズ登録';
        $sql = "SELECT definition_value as series_registration_value,definition_label as series_registration_label FROM m_variable_definition WHERE definition_name = :definition_name";
        return $this->_find($sql, $query);
    }

    public function getBentoDisposal()
    {
        $query['definition_name'] = '弁当処分方法';
        $sql = "SELECT definition_value as bento_disposal_value,definition_label as bento_disposal_label FROM m_variable_definition WHERE definition_name = :definition_name";
        return $this->_find($sql, $query);
    }

    public function getUserApprovalConventionPlan($convention_id, $request_type, $layer_num)
    {
        $query['convention_id'] = $convention_id;
        $query['request_type'] = $request_type;
        $where = "";
        if ($layer_num) {
            $where .= " AND T4.layer_num = :layer_num";
            $query['layer_num'] = $layer_num;
        }
        $sql = "SELECT CASE WHEN T4.status_type IS NOT NULL THEN T4.status_type ELSE 0 END status_type
                    ,T4.comment,T5.user_name,T4.approval_user_cd,T4.layer_num as approval_layer_num
                FROM t_approval_request T3
                    JOIN t_approval_request_detail T4 ON T3.request_id = T4.request_id 
                    LEFT JOIN m_user T5 ON T4.approval_user_cd = T5.user_cd
                WHERE T3.request_target_id = :convention_id 
                AND T3.request_type = :request_type " . $where . " GROUP BY T4.approval_user_cd,T4.layer_num";
        return $this->_find($sql, $query);
    }

    public function getLayerUserApprovalConvention($user_login, $convention_id, $request_type)
    {
        $query['convention_id'] = $convention_id;
        $query['request_type'] = $request_type;
        $query['user_login'] = $user_login;
        $sql = "SELECT CASE WHEN T4.status_type IS NOT NULL THEN T4.status_type ELSE 0 END status_type,
                        T4.comment,T5.user_name,T4.approval_user_cd,T4.layer_num as approval_layer_num
                FROM t_convention T2 
                    LEFT JOIN t_approval_request T3 ON T2.convention_id = T3.request_target_id AND T3.request_type = :request_type
                    LEFT JOIN t_approval_request_detail T4 ON T3.request_id = T4.request_id 
                    LEFT JOIN m_user T5 ON T4.approval_user_cd = T5.user_cd
                WHERE T2.convention_id = :convention_id AND T4.approval_user_cd = :user_login GROUP BY T4.approval_user_cd,T4.layer_num";
        return $this->_find($sql, $query);
    }

    public function getDetailConventionByConventionID($request)
    {
        $where = "";
        $query = [];
        if ($request->convention_id) {
            $where .= " AND T1.convention_id = :convention_id";
            $query['convention_id'] = $request->convention_id;
        } elseif ($request->schedule_id) {
            $where .= " AND T1.schedule_id = :schedule_id";
            $query['schedule_id'] = $request->schedule_id;
        }
        $sql = "SELECT T1.convention_id,
                        T1.hold_type,
                        T1.expense_num,
                        T1.parent_series_flag,
                        T1.series_convention_id,
                        (SELECT convention_name FROM t_convention WHERE convention_id = T1.series_convention_id) as series_convention_name,
                        T1.convention_name,
                        T1.convention_type,
                        T1.hold_method,
                        T2.start_date,
                        T2.start_time,
                        T2.end_time,
                        T1.place,
                        T1.hold_purpose,
                        T1.remarks,
                        T1.hold_form,
                        T1.cohost_partner_name,
                        T1.cost_share_type,
                        T1.cost_share_content,
                        T1.disposal_technique,
                        T1.reason,
                        T1.note,
                        T1.status_type,
                        T1.schedule_id,
                        T1.plan_user_cd,
                        T1.plan_user_name,
                        T1.plan_org_cd,
                        T1.plan_org_label,
                        T1.remand_flag,
                        (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                            'product_cd', product_cd,
                            'product_name', product_name,
                            'delete_flag', 0
                            )),']') FROM t_convention_product 
                            WHERE convention_id = T1.convention_id
                        ) AS convention_product
                FROM t_convention T1
                    LEFT JOIN t_schedule T2 ON T1.schedule_id = T2.schedule_id
                WHERE 1 = 1" . $where;
        return $where ? $this->_first($sql, $query) : [];
    }

    public function selectionSeriesConventionDetail($convention_id)
    {
        $query['convention_id_product'] = $query['convention_id'] = $convention_id;
        $sql = "SELECT convention_id,
                        COALESCE(20) AS hold_type, 
                        convention_type,
                        hold_method,
                        convention_name,
                        place,
                        hold_purpose,
                        remarks,
                        parent_series_flag,
                        series_convention_id,
                        (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                            'product_cd', product_cd,
                            'product_name', product_name,
                            'delete_flag', 0
                            )),']') FROM t_convention_product 
                            WHERE convention_id = :convention_id_product
                        ) AS convention_product
                FROM t_convention 
                where convention_id = :convention_id";
        return $this->_first($sql, $query);
    }

    public function getDocumentConvention($convention_id)
    {
        $query['convention_id'] = $convention_id;
        $sql = "SELECT T2.start_date, 
                    T2.end_date, 
                    T2.document_name, 
                    T1.document_id,
                    T2.file_type,
                    T2.document_type,
                    COALESCE(0) AS delete_flag
                FROM t_convention_document T1 
                    JOIN t_document T2 ON T1.document_id = T2.document_id
                WHERE T1.convention_id = :convention_id";
        return $this->_find($sql, $query);
    }

    public function getConventionTargetOrg($convention_id)
    {
        $query['convention_id'] = $convention_id;
        $sql = "SELECT T1.org_label,
                    T1.org_cd,
                    T2.layer_num,
                    COALESCE(0) AS delete_flag 
                FROM t_convention_target_org T1 JOIN m_org T2 on T1.org_cd = T2.org_cd
                WHERE convention_id = :convention_id";
        return $this->_find($sql, $query);
    }

    public function getConventionAreaExpense($convention_id)
    {
        $query['layer_parent']  = $this->layer_parent;
        $sql = "SELECT 
                    0 as plan_amount,
                    0 as result_amount,
                    expense_item_cd,
                    expense_item_name 
                FROM m_convention_expense_item
                WHERE layer_num = :layer_parent";
        return $this->_find($sql, $query);
    }

    public function getConventionExpenseDetailByConventionId($convention_id)
    {
        $query['convention_id'] = $convention_id;
        $sql = "SELECT plan_unit_price, plan_quantity, plan_amount, expense_item_cd
                FROM t_convention_expense_detail
                WHERE convention_id = :convention_id";
        return $this->_find($sql, $query);
    }

    public function getConventionAreaExpenseLayer($expense_item_cd, $convention_id)
    {
        $query['expense_item_cd'] = $expense_item_cd;
        $query['convention_id'] = $convention_id;
        $sql = "SELECT T1.*,
                        T2.option_item_name,
                        T2.option_item_content,
                        T2.plan_unit_price,
                        T2.plan_quantity,
                        T2.plan_amount,
                        T2.result_unit_price,
                        T2.result_quantity,
                        T2.result_amount,
                        T1.quantity_unit_type,
                        T3.definition_label as quantity_unit_label
                FROM m_convention_expense_item T1
                    LEFT JOIN t_convention_expense_detail T2 on T1.expense_item_cd = T2.expense_item_cd AND T2.convention_id = :convention_id
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = '経費項目_数量単位') T3 ON T1.quantity_unit_type = T3.definition_value
                WHERE T1.parent_expense_item_id = :expense_item_cd
                GROUP BY T1.expense_item_cd";
        return $this->_find($sql, $query);
    }

    public function getConventionFile($convention_id)
    {
        $query['convention_id'] = $convention_id;
        $sql = "SELECT T2.display_name,T2.file_name, T2.file_id, COALESCE(0) AS delete_flag
                FROM t_convention_file T1 LEFT JOIN t_file T2 ON T1.file_id = T2.file_id 
                WHERE T1.convention_id = :convention_id";
        return $this->_find($sql, $query);
    }

    public function getConventionAttendee($convention_id)
    {
        $query['convention_id'] = $convention_id;
        $sql = "SELECT T1.convention_attendee_id,
                    T1.facility_short_name,
                    T1.department_name,
                    T1.person_cd,
                    T1.person_name,
                    T1.display_position_cd,
                    T1.display_position_name,
                    T1.gratuity,
                    T1.withholding_tax,
                    T1.give_amount,
                    T1.subject,
                    T1.part_type,
                    T1.other_person_flag,
                    T2.definition_label
                FROM t_convention_attendee T1
                    JOIN (SELECT * FROM m_variable_definition where definition_name = '役割区分') T2 on T1.part_type = T2.definition_value
                WHERE T1.convention_id = :convention_id";
        return $this->_find($sql, $query);
    }

    public function getConventionStatusItem()
    {
        $sql = "SELECT status_item_cd, status_item_name FROM m_convention_status_item ORDER BY sort_order";
        return $this->_find($sql, []);
    }

    public function getConventionOccupationType()
    {
        $sql = "SELECT occupation_type, occupation_name,medical_staff_cd FROM m_convention_occupation_type ORDER BY sort_order";
        return $this->_find($sql, []);
    }

    public function updateStatusTypeConvention($convention_id, $status_type)
    {
        $sql = "UPDATE t_convention
                SET status_type = :status_type
                WHERE convention_id = :convention_id";
        $parram = [
            'status_type' => $status_type,
            'convention_id' => $convention_id
        ];
        return $this->_update($sql, $parram);
    }

    public function updateRemandFlagConvention($convention_id, $remand_flag)
    {
        $sql = "UPDATE t_convention
                SET remand_flag = :remand_flag
                WHERE convention_id = :convention_id";
        $parram = [
            'remand_flag' => $remand_flag,
            'convention_id' => $convention_id
        ];
        return $this->_update($sql, $parram);
    }

    public function checkUserApprovalFinal($convention_id, $request_type, $user_login)
    {
        $query['convention_id'] = $convention_id;
        $query['request_type'] = $request_type;
        $query['user_login'] = $user_login;
        $query['parameter_name'] = $this->approval_convention;
        $query['parameter_key'] = $this->display_option_name;
        $sql = "SELECT CASE WHEN T4.status_type IS NOT NULL THEN T4.status_type ELSE 0 END status_type,
                        T4.comment,T5.user_name,T4.approval_user_cd,T4.layer_num as approval_layer_num
                FROM t_convention T2 
                    JOIN t_approval_request T3 ON T2.convention_id = T3.request_target_id 
                    LEFT JOIN t_approval_request_detail T4 ON T3.request_id = T4.request_id 
                    LEFT JOIN m_user T5 ON T4.approval_user_cd = T5.user_cd
                WHERE T2.convention_id = :convention_id
                    AND T4.approval_user_cd = :user_login 
                    AND T3.request_type = :request_type
                    AND T4.layer_num = (SELECT parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_name AND parameter_key = :parameter_key) 
                GROUP BY T4.approval_user_cd,T4.layer_num";
        return $this->_first($sql, $query);
    }

    public function createdConvention($request)
    {
        $sql = "INSERT INTO t_convention(schedule_id
                            ,status_type
                            ,hold_type
                            ,parent_series_flag
                            ,series_convention_id
                            ,convention_type
                            ,hold_method
                            ,convention_name
                            ,place
                            ,hold_purpose
                            ,remarks
                            ,hold_form
                            ,note
                            ,reason
                            ,cohost_partner_name
                            ,cost_share_type
                            ,cost_share_content
                            ,plan_user_cd
                            ,plan_user_name
                            ,plan_org_cd
                            ,plan_org_label
                            ,expense_num
                            ,remand_flag) 
                VALUES (:schedule_id
                            ,:status_type
                            ,:hold_type
                            ,:parent_series_flag
                            ,:series_convention_id
                            ,:convention_type
                            ,:hold_method
                            ,:convention_name
                            ,:place
                            ,:hold_purpose
                            ,:remarks
                            ,:hold_form
                            ,:note
                            ,:reason
                            ,:cohost_partner_name
                            ,:cost_share_type
                            ,:cost_share_content
                            ,:plan_user_cd
                            ,:plan_user_name
                            ,:plan_org_cd
                            ,:plan_org_label
                            ,:expense_num
                            ,:remand_flag);";
        $parram = [
            'schedule_id' => $request->schedule_id,
            'status_type' => $this->status_type,
            'hold_type' => $request->hold_type,
            'parent_series_flag' => $request->parent_series_flag,
            'series_convention_id' => $request->series_convention_id,
            'convention_type' => $request->convention_type,
            'hold_method' => $request->hold_method,
            'convention_name' => $request->convention_name,
            'place' => $request->place,
            'hold_purpose' => $request->hold_purpose,
            'remarks' => $request->remarks,
            'hold_form' => $request->hold_form,
            'cohost_partner_name' => $request->cohost_partner_name,
            'cost_share_type' => $request->cost_share_type,
            'cost_share_content' => $request->cost_share_content,
            'plan_user_cd' => $request->user_login,
            'plan_user_name' => $request->user_name,
            'plan_org_cd' => $request->org_cd,
            'plan_org_label' => $request->org_label,
            'remand_flag' => $this->remand_flag,
            'expense_num' => $request->expense_num,
            'note' => $request->note,
            'reason' => $request->reason,
        ];
        $this->_create($sql, $parram);
        return $this->_lastInserted('t_convention', 'convention_id');
    }

    public function updateConventionPlan($request)
    {
        $sql = "UPDATE t_convention
                SET convention_type = :convention_type
                    ,parent_series_flag = :parent_series_flag
                    ,hold_type = :hold_type
                    ,hold_method = :hold_method
                    ,series_convention_id = :series_convention_id
                    ,convention_name = :convention_name
                    ,place = :place
                    ,hold_purpose = :hold_purpose
                    ,remarks = :remarks
                    ,hold_form = :hold_form
                    ,cohost_partner_name = :cohost_partner_name
                    ,cost_share_type = :cost_share_type
                    ,cost_share_content = :cost_share_content
                    ,note = :note
                    ,reason = :reason
                    ,disposal_technique = :disposal_technique
                    ,expense_num = :expense_num
                    ,status_type = :status_type
                WHERE convention_id = :convention_id";
        $parram = [
            'convention_type' => $request->convention_type,
            'parent_series_flag' => $request->parent_series_flag,
            'hold_method' => $request->hold_method,
            'convention_name' => $request->convention_name,
            'place' => $request->place,
            'hold_purpose' => $request->hold_purpose,
            'remarks' => $request->remarks,
            'hold_form' => $request->hold_form,
            'cohost_partner_name' => $request->cohost_partner_name,
            'cost_share_type' => $request->cost_share_type,
            'cost_share_content' => $request->cost_share_content,
            'convention_id' => $request->convention_id,
            'note' => $request->note,
            'reason' => $request->reason,
            'expense_num' => $request->expense_num,
            'status_type' => $request->status_type,
            'disposal_technique' => $request->disposal_technique,
            'series_convention_id' => $request->series_convention_id,
            'hold_type' => $request->hold_type
        ];
        return $this->_update($sql, $parram);
    }

    public function createdConventionTargetOrg($convention_id, $request)
    {
        $sql = "INSERT INTO t_convention_target_org(convention_id
                            ,org_cd
                            ,org_label) 
                VALUES (:convention_id
                        ,:org_cd
                        ,:org_label);";
        $parram = [
            'convention_id' => $convention_id,
            'org_cd' => $request->org_cd,
            'org_label' => $request->org_label,
        ];
        return $this->_create($sql, $parram);
    }

    public function deleteConventionTargetOrg($convention_id, $org_cd)
    {
        $sql = "DELETE FROM t_convention_target_org
                WHERE convention_id = :convention_id AND org_cd =:org_cd";
        $parram = [
            'convention_id' => $convention_id,
            'org_cd' => $org_cd,
        ];
        return $this->_destroy($sql, $parram);
    }

    public function insertConventionProduct($convention_id, $request)
    {
        $sql = "INSERT INTO t_convention_product(convention_id,product_cd,product_name)
                VALUES(:convention_id,:product_cd,:product_name);";
        $parram = [
            'product_cd' => $request->product_cd,
            'product_name' => $request->product_name,
            'convention_id' => $convention_id,
        ];
        return $this->_create($sql, $parram);
    }

    public function deleteConventionProduct($convention_id, $product_cd)
    {
        $sql = "DELETE FROM t_convention_product WHERE product_cd = :product_cd AND convention_id = :convention_id";
        $parram = [
            'product_cd' => $product_cd,
            'convention_id' => $convention_id,
        ];
        return $this->_destroy($sql, $parram);
    }

    public function createdConventionDocument($convention_id, $request)
    {
        $sql = "INSERT INTO t_convention_document(convention_id,document_id)
                VALUES(:convention_id,:document_id);";
        $parram = [
            'document_id' => $request->document_id,
            'convention_id' => $convention_id,
        ];
        return $this->_create($sql, $parram);
    }

    public function deleteConventionDocument($convention_id, $document_id)
    {
        $sql = "DELETE FROM t_convention_document WHERE document_id = :document_id AND convention_id = :convention_id";
        $parram = [
            'document_id' => $document_id,
            'convention_id' => $convention_id,
        ];
        return $this->_destroy($sql, $parram);
    }

    public function updateConventionAttendee($convention_id, $request)
    {
        $sql = "UPDATE t_convention_attendee SET
                gratuity = :gratuity,
                withholding_tax = :withholding_tax,
                give_amount = :give_amount,
                subject = :subject
                WHERE convention_id = :convention_id AND convention_attendee_id = :convention_attendee_id";
        $parram = [
            'gratuity' => $request->gratuity,
            'withholding_tax' => $request->withholding_tax,
            'give_amount' => $request->give_amount,
            'subject' => $request->subject,
            'convention_id' => $convention_id,
            'convention_attendee_id' => $request->convention_attendee_id
        ];
        return $this->_update($sql, $parram);
    }

    public function deleteScheduleCommonSubject($schedule_id)
    {
        $sql = "DELETE FROM t_schedule_common_subject WHERE schedule_id = :schedule_id";
        $parram = [
            'schedule_id' => $schedule_id,
        ];
        return $this->_destroy($sql, $parram);
    }

    public function saveScheduleCommonSubject($schedule_id, $org_cd)
    {
        $sql = "INSERT INTO t_schedule_common_subject(schedule_id,org_cd,user_cd)
                VALUES (:schedule_id,:org_cd,(SELECT parameter_value FROM c_system_parameter WHERE parameter_key = :setting_name_user));";
        $parram = [
            'schedule_id' => $schedule_id,
            'org_cd' => $org_cd,
            'setting_name_user' => $this->setting_name_user
        ];
        return $this->_create($sql, $parram);
    }

    public function updateConventionPlanMount($convention_id, $expense_item_cd, $plan_amount)
    {
        $sql = "UPDATE t_convention_expense_detail 
                SET plan_amount = :plan_amount
                WHERE convention_id = :convention_id AND expense_item_cd = :expense_item_cd";
        $parram = [
            'plan_amount' => $plan_amount,
            'convention_id' => $convention_id,
            'expense_item_cd' => $expense_item_cd
        ];
        return $this->_update($sql, $parram);
    }

    public function updateConventionPlanMountLayer2($convention_id, $expense_item_cd, $option_item_name, $option_item_content, $plan_unit_price, $plan_quantity, $plan_amount)
    {
        $sql = "UPDATE t_convention_expense_detail 
                SET option_item_name = :option_item_name,
                    option_item_content = :option_item_content,
                    plan_unit_price = :plan_unit_price,
                    plan_quantity = :plan_quantity,
                    plan_amount = :plan_amount
                WHERE convention_id = :convention_id AND expense_item_cd = :expense_item_cd";
        $parram = [
            'convention_id' => $convention_id,
            'expense_item_cd' => $expense_item_cd,
            'option_item_name' => $option_item_name,
            'option_item_content' => $option_item_content,
            'plan_unit_price' => $plan_unit_price,
            'plan_quantity' => $plan_quantity,
            'plan_amount' => $plan_amount
        ];
        return $this->_update($sql, $parram);
    }

    public function updateConventionResultMount($convention_id, $expense_item_cd, $result_amount)
    {
        $sql = "UPDATE t_convention_expense_detail 
                SET result_amount = :result_amount
                WHERE convention_id = :convention_id AND expense_item_cd = :expense_item_cd";
        $parram = [
            'result_amount' => $result_amount,
            'convention_id' => $convention_id,
            'expense_item_cd' => $expense_item_cd
        ];
        return $this->_update($sql, $parram);
    }

    public function updateConventionResultMountLayer2($convention_id, $expense_item_cd, $option_item_name, $option_item_content, $result_unit_price, $result_amount, $result_quantity)
    {
        $sql = "UPDATE t_convention_expense_detail 
                SET option_item_name = :option_item_name,
                    option_item_content = :option_item_content,
                    result_unit_price = :result_unit_price,
                    result_amount = :result_amount,
                    result_quantity = :result_quantity
                WHERE convention_id = :convention_id AND expense_item_cd = :expense_item_cd";
        $parram = [
            'convention_id' => $convention_id,
            'expense_item_cd' => $expense_item_cd,
            'option_item_name' => $option_item_name,
            'option_item_content' => $option_item_content,
            'result_unit_price' => $result_unit_price,
            'result_amount' => $result_amount,
            'result_quantity' => $result_quantity
        ];
        return $this->_update($sql, $parram);
    }

    public function updateConventionResultMountByLastApproval($convention_id, $expense_item_cd, $result_unit_price, $result_amount, $result_quantity)
    {
        $sql = "UPDATE t_convention_expense_detail 
                SET result_unit_price = :result_unit_price,
                    result_amount = :result_amount,
                    result_quantity = :result_quantity
                WHERE convention_id = :convention_id AND expense_item_cd = :expense_item_cd";
        $parram = [
            'convention_id' => $convention_id,
            'expense_item_cd' => $expense_item_cd,
            'result_unit_price' => $result_unit_price,
            'result_amount' => $result_amount,
            'result_quantity' => $result_quantity
        ];
        return $this->_update($sql, $parram);
    }

    public function getUserApproval($approval_work_type, $layer_num, $request_user_cd)
    {
        $where = "";
        if ($layer_num) {
            $where .= " AND approval_layer_num = :approval_layer_num";
            $query['approval_layer_num'] = $layer_num;
        }
        $query['approval_work_type'] = $approval_work_type;
        $query['request_user_cd'] = $request_user_cd;
        $query['start_date'] = $this->date;
        $query['end_date'] = $this->date;
        $sql = "SELECT approval_user_cd,approval_layer_num 
                FROM h_approval_user
                WHERE approval_work_type = :approval_work_type 
                    AND start_date <= :start_date 
                    AND end_date >= :end_date
                    AND request_user_cd = :request_user_cd" . $where;
        return $this->_find($sql, $query);
    }

    public function addApprovalRequest($data_approval_request)
    {
        $sql = "INSERT INTO t_approval_request (
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
        $this->_create($sql, $data_approval_request);
        return $this->_lastInserted("t_approval_request", "request_id");
    }

    public function addApprovalRequestDetail($data_approval_request_detail)
    {
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
        return $this->_create($sql, $data_approval_request_detail);
    }

    public function installed($user_cd, $inform_category_cd = null)
    {
        $sql = "SELECT
            c.inform_category_cd,
            c.inform_category_name,
            ( CASE WHEN ue.user_cd IS NULL THEN 1 ELSE 0 END ) AS checked 
        FROM
            m_inform_category c
            LEFT JOIN t_user_exclusion_inform_category ue ON c.inform_category_cd = ue.inform_category_cd 
            AND ue.user_cd = :user_cd 
        WHERE
            c.inform_stop_flag = 0";
        if (!empty($inform_category_cd)) {
            $sql .= " AND c.inform_category_cd = :inform_category_cd";
            $condition['inform_category_cd'] = $inform_category_cd;
        }
        $sql .= " ORDER BY c.sort_order ASC";
        $condition['user_cd'] = $user_cd;
        return $this->_find($sql, $condition);
    }

    public function addInform($infrom)
    {
        $sql = "INSERT INTO t_inform (inform_category_cd,
                                inform_datetime
                                ,inform_user_cd
                                ,inform_contents)
                VALUES(:inform_category_cd
                        ,:inform_datetime
                        ,:inform_user_cd
                        ,:inform_contents);";
        $this->_create($sql, $infrom);
        $lastInserted = $this->_lastInserted("t_inform", "inform_id");
        return $lastInserted->inform_id;
    }

    public function addInformParameter($inform_parameter)
    {
        $sql = "INSERT INTO t_inform_parameter (
                            inform_id
                            ,parameter_key
                            ,parameter_value)
                VALUES(:inform_id
                    ,:parameter_key
                    ,:parameter_value);";
        return $this->_create($sql, $inform_parameter);
    }

    public function getInformCategory($category_name)
    {
        $sql = "SELECT * FROM m_inform_category WHERE inform_category_name = :category_name";
        return $this->_first($sql, ['category_name' => $category_name]);
    }

    public function getDisplayOptionTpye()
    {
        $sql = "SELECT * FROM m_display_option WHERE display_option_name=:display_option_name";
        return $this->_first($sql, ['display_option_name' => $this->display_option_name]);
    }

    public function addConventionFile($convention_file)
    {
        $sql = "INSERT INTO t_convention_file(convention_id,file_id) VALUES (:convention_id,:file_id);";
        return $this->_create($sql, $convention_file);
    }

    public function deleteFile($file_id)
    {
        $sql = "DELETE FROM t_file WHERE file_id = :file_id";
        return $this->_destroy($sql, ['file_id' => $file_id]);
    }

    public function deleteConventionFile($convention_id, $file_id)
    {
        $sql = "DELETE FROM t_convention_file WHERE file_id = :file_id AND convention_id = :convention_id";
        return $this->_destroy($sql, ['file_id' => $file_id, 'convention_id' => $convention_id]);
    }

    public function getUseType($definition_label)
    {
        $sql = "SELECT * FROM m_variable_definition WHERE definition_name = :definition_name AND definition_label = :definition_label";
        $query['definition_label'] = $definition_label;
        $query['definition_name'] = $this->file_usage_type;
        return $this->_first($sql, $query);
    }

    public function getDocumentUsageType($definition_label_usage_type)
    {
        $sql = "SELECT definition_name,definition_value,definition_label FROM m_variable_definition WHERE definition_name = :definition_name AND definition_label = :definition_label";
        $query['definition_label'] = $definition_label_usage_type;
        $query['definition_name'] = $this->document_usage_type;
        return $this->_first($sql, $query)->definition_value ?? null;
    }

    public function createDocumentUsageSituation($request, $document_usage_id, $document_usage_type, $document_id)
    {
        $sql_document_usage_situation = "SELECT * FROM t_document_usage_situation WHERE document_id = :document_id AND document_usage_id = :document_usage_id AND document_usage_type = :document_usage_type";
        $convention_usage_situation = $this->_first($sql_document_usage_situation, [
            "document_id" => $document_id,
            "document_usage_type" => $document_usage_type,
            "document_usage_id" => $document_usage_id,
        ]);
        $sql = "INSERT INTO t_document_usage_situation(document_id
                    ,document_usage_type
                    ,document_usage_id
                    ,usage_org_label
                    ,usage_user_cd
                    ,usage_user_name
                    ,usage_datetime) 
                VALUES(:document_id
                    ,:document_usage_type
                    ,:document_usage_id
                    ,:usage_org_label
                    ,:usage_user_cd
                    ,:usage_user_name
                    ,:usage_datetime);";
        $parram = [
            "document_id" => $document_id,
            "document_usage_type" => $document_usage_type,
            "document_usage_id" => $document_usage_id,
            "usage_org_label" => $request->org_label,
            "usage_user_cd" => $request->user_login,
            "usage_user_name" => $request->user_name,
            "usage_datetime" => $this->date
        ];
        if (!is_object($convention_usage_situation)) {
            $this->_create($sql, $parram);
        }
        return true;
    }

    public function deleteDocumentUsageSituation($convention_id, $document_usage_type, $document_id)
    {
        $sql = "DELETE T1, T2
            FROM
                t_document_usage_situation T1
            LEFT JOIN t_document_review T2 ON T2.usage_situation_id = T1.usage_situation_id
            WHERE T1.document_id = :document_id 
                AND T1.document_usage_id = :document_usage_id
                AND T1.document_usage_type = :document_usage_type";
        $parram = [
            "document_id" => $document_id,
            "document_usage_type" => $document_usage_type,
            "document_usage_id" => $convention_id
        ];
        return $this->_destroy($sql, $parram);
    }

    public function updateConventionFinal($request_id, $status_type)
    {
        $sql = "UPDATE t_approval_request SET status_type = :status_type WHERE request_id = :request_id";
        $parram = [
            "status_type" => $status_type,
            "request_id" => $request_id
        ];
        return $this->_update($sql, $parram);
    }

    public function updateConvention($request_id, $user_login, $status_type, $comment, $layer_num)
    {
        $where = "";
        if ($layer_num) {
            $where .= " AND layer_num = :layer_num";
        }
        $sql = "UPDATE t_approval_request_detail 
                SET status_type = :status_type, comment = :comment, approval_datetime = :approval_datetime
                WHERE request_id = :request_id AND approval_user_cd = :approval_user_cd" . $where;
        $parram = [
            "status_type" => $status_type,
            "request_id" => $request_id,
            "comment" => $comment,
            "approval_user_cd" => $user_login,
            "layer_num" => $layer_num,
            "approval_datetime" => $this->date_time
        ];
        return $this->_update($sql, $parram);
    }

    public function getApprovalRequest($convention_id, $request_type)
    {
        $sql = "SELECT request_id,request_type,request_target_id,request_user_cd,request_datetime,status_type,active_layer_num
                FROM t_approval_request WHERE request_target_id = :request_target_id AND request_type = :request_type";
        $parram = [
            "request_target_id" => $convention_id,
            "request_type" => $request_type
        ];
        return $this->_first($sql, $parram);
    }

    public function deleteConvention($convention_id)
    {
        $sql_t_schedule = "DELETE FROM t_schedule WHERE schedule_id = (SELECT schedule_id FROM t_convention WHERE convention_id = :convention_id)";
        $sql_convention = "DELETE FROM t_convention WHERE convention_id = :convention_id";
        $parram = [
            "convention_id" => $convention_id,
        ];
        $this->_destroy($sql_t_schedule, $parram);
        return $this->_destroy($sql_convention, $parram);
    }

    public function getConventionOrderType10($convention_id, $occupation_type)
    {
        $sql = "SELECT C.status_item_cd,SUM(COALESCE(B.headcount,0)+ COALESCE(A.headcount,0)) as sum_count_user
                FROM m_convention_status_item C
		            LEFT JOIN t_convention_other_headcount A on A.status_item_cd = C.status_item_cd AND A.convention_id = :convention_id_orther AND A.occupation_type = :occupation_type
                    LEFT JOIN ( SELECT T2.status_item_cd,COUNT(*) as headcount
                        FROM t_convention_attendee T1
                            JOIN t_convention_attendee_status_detail T2 ON T1.convention_attendee_id = T2.convention_attendee_id
                        WHERE T1.other_person_flag = 0 
                            AND T1.convention_id = :convention_id 
                            AND T2.status_item_value LIKE '1%'
                        GROUP BY T2.status_item_cd
                    ) B ON A.status_item_cd = B.status_item_cd
                GROUP BY C.status_item_cd";
        $parram = [
            "convention_id" => $convention_id,
            "convention_id_orther" => $convention_id,
            "occupation_type" => $occupation_type
        ];
        return $this->_find($sql, $parram);
    }

    public function getConventionOrderType20($convention_id, $occupation_type, $medical_staff_cd)
    {
        $sql = "SELECT C.status_item_cd,SUM(COALESCE(B.headcount,0)+ COALESCE(A.headcount,0)) as sum_count_user
                FROM m_convention_status_item C
		            LEFT JOIN t_convention_other_headcount A on A.status_item_cd = C.status_item_cd AND A.convention_id = :convention_id_orther AND A.occupation_type = :occupation_type
                    LEFT JOIN ( SELECT T2.status_item_cd,COUNT(*) as headcount
                FROM t_convention_attendee T1
                    JOIN t_convention_attendee_status_detail T2 on T1.convention_attendee_id = T2.convention_attendee_id
                WHERE T1.other_person_flag = 1 
                    AND T1.convention_id = :convention_id 
                    AND T1.other_medical_staff_type = :medical_staff_cd
                    AND T2.status_item_value like '1%'
                GROUP BY T2.status_item_cd
                ) B on A.status_item_cd = B.status_item_cd
                GROUP BY C.status_item_cd";
        $parram = [
            "convention_id" => $convention_id,
            "convention_id_orther" => $convention_id,
            "occupation_type" => $occupation_type,
            "medical_staff_cd" => $medical_staff_cd
        ];
        return $this->_find($sql, $parram);
    }

    public function getConventionOrderType60($convention_id, $occupation_type)
    {
        $sql = "SELECT C.status_item_cd,COALESCE(A.headcount,0) as sum_count_user
            FROM m_convention_status_item C
		        LEFT JOIN t_convention_other_headcount A on A.status_item_cd = C.status_item_cd 
                    AND A.convention_id = :convention_id 
                    AND A.occupation_type = :occupation_type";
        $parram = [
            "convention_id" => $convention_id,
            "occupation_type" => $occupation_type,
        ];
        return $this->_find($sql, $parram);
    }

    public function getFollowConventionOrderType10($convention_id, $medical_staff_cd, $convention_start_date, $convention_start_date_add_two_day)
    {
        $sql = "SELECT t_schedule.start_date, t_schedule.start_time, t_call.call_id, t_call.visit_id,t_call.person_cd
                FROM t_schedule
                    JOIN t_visit ON t_visit.schedule_id = t_schedule.schedule_id
                  JOIN t_call ON t_call.visit_id = t_visit.visit_id
                WHERE CAST(t_schedule.start_date AS DATE) >= :convention_start_date AND CAST(t_schedule.start_date AS DATE) <= :convention_start_date_add_two_day and t_schedule.schedule_type = 10
                AND t_call.person_cd in (select DISTINCT T1.person_cd
                    from t_convention_attendee T1 
                        join (select * from t_convention_attendee_status_detail 
                        where status_item_value = 1 or status_item_value = 10) T2 on T1.convention_attendee_id = T2.convention_attendee_id
                    where  T1.other_person_flag =0 and T1.other_medical_staff_type= :other_medical_staff_type  and T1.convention_id = :convention_id)
                Group by t_call.person_cd";
        $parram = [
            "convention_id" => $convention_id,
            "convention_start_date" => $convention_start_date,
            "other_medical_staff_type" => $medical_staff_cd,
            "convention_start_date_add_two_day" => $convention_start_date_add_two_day,
        ];
        return $this->_find($sql, $parram);
    }

    public function allConventionExpenseItem()
    {
        $sql = "SELECT * FROM m_convention_expense_item WHERE layer_num = :layer_num";
        return $this->_find($sql, ['layer_num' => 1]);
    }

    public function allConventionExpenseItemChildren($expense_item_cd)
    {
        $sql = "SELECT * FROM m_convention_expense_item WHERE parent_expense_item_id = :parent_expense_item_id";
        return $this->_find($sql, ['parent_expense_item_id' => $expense_item_cd]);
    }

    public function saveConventionPlanExpenseItem($convention_id, $expense_item_cd)
    {
        $sql = "INSERT INTO t_convention_expense_detail(convention_id,expense_item_cd) VALUES (:convention_id,:expense_item_cd);";
        $parram = [
            "convention_id" => $convention_id,
            "expense_item_cd" => $expense_item_cd
        ];
        return $this->_create($sql, $parram);
    }

    public function getDocumentReview($convention_id, $document_usage_type)
    {
        $sql = "SELECT * FROM t_document_usage_situation T1 
                    LEFT JOIN t_document_review T4 ON T1.usage_situation_id = T4.usage_situation_id
                WHERE T1.document_usage_id = :convention_id AND T1.document_usage_type = :document_usage_type AND T4.usage_situation_id IS NULL ";
        $parram = [
            "convention_id" => $convention_id,
            "document_usage_type" => $document_usage_type
        ];
        return $this->_find($sql, $parram);
    }

    public function updateActiveApprovalRequest($request_id, $active_layer_num)
    {
        $sql = "UPDATE t_approval_request SET active_layer_num = :active_layer_num WHERE request_id = :request_id";
        $parram = [
            "request_id" => $request_id,
            "active_layer_num" => $active_layer_num
        ];
        return $this->_update($sql, $parram);
    }

    public function getTargetUserOrg($user_cd, $convention_id)
    {
        $query['user_cd'] = $user_cd;
        $query['convention_id'] = $convention_id;
        $sql = "SELECT T1.*
                FROM m_user T1 
                    INNER JOIN m_user_org T2 ON T1.user_cd = T2.user_cd 
                    INNER JOIN m_org T3 ON T3.org_cd = T2.org_cd
                    INNER JOIN t_convention_target_org T4 on T4.org_cd = T3.org_cd
                WHERE T1.user_cd = :user_cd AND T4.convention_id = :convention_id";
        return $this->_first($sql, $query);
    }

    public function getUserCreateReport($schedule_id)
    {
        $sql = "SELECT T3.report_id,T3.user_cd,T3.user_name
                FROM t_schedule T1
                    JOIN t_report_detail T2 ON T1.schedule_id = T2.report_detail_id AND T1.schedule_type = T2.report_detail_type
                    JOIN t_report T3 ON T2.report_id = T3.report_id
                WHERE T1.schedule_id = :schedule_id";
        return $this->_first($sql, ['schedule_id' => $schedule_id]);
    }

    public function getLayerNumApprovalFinnal()
    {
        $sql = "SELECT parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_name AND parameter_key = :parameter_key";
        $parram['parameter_name'] = $this->approval_convention;
        $parram['parameter_key'] = $this->display_option_name;
        return $this->_first($sql, $parram);
    }

    public function getUserConventionTargetOrg($convention_id)
    {
        $sql = "SELECT T4.user_cd,T4.user_name 
                FROM t_convention_target_org T1
                    JOIN m_org T2 ON T1.org_cd = T2.org_cd
                    JOIN m_user_org T3 ON T2.org_cd = T3.org_cd
                    JOIN m_user T4 ON T3.user_cd = T4.user_cd
                WHERE T1.convention_id = :convention_id
                GROUP BY T4.user_cd";
        $parram['convention_id'] = $convention_id;
        return $this->_find($sql, $parram);
    }

    public function getUserApprovalReport($report_id, $request_type)
    {
        $sql = "SELECT T2.approval_user_cd
                FROM t_approval_request T1 
                    JOIN t_approval_request_detail T2 ON T1.request_id = T2.request_id
                WHERE T1.request_target_id = :request_target_id AND T1.request_type = :request_type AND T2.status_type <> :status_type";
        $parram['request_target_id'] = $report_id;
        $parram['request_type'] = $request_type;
        //status approval report
        $parram['status_type'] = 0;
        return $this->_find($sql, $parram);
    }

    public function updateInvisibleFlagSchedule($schedule_id)
    {
        $sql = "UPDATE t_schedule SET non_display_flag = :non_display_flag WHERE schedule_id = :schedule_id";
        $parram = [
            'schedule_id' => $schedule_id,
            'non_display_flag' => 1
        ];
        return $this->_destroy($sql, $parram);
    }

    public function deleteApprovalRequest($request_id)
    {
        $sql_approve = "DELETE FROM t_approval_request WHERE request_id = :request_id";
        $sql_approve_detail = "DELETE FROM t_approval_request_detail WHERE request_id = :request_id";
        $parram['request_id'] = $request_id;
        $this->_destroy($sql_approve_detail, $parram);
        return $this->_destroy($sql_approve, $parram);
    }

    public function getCopyConvention($convention_id)
    {
        $sql = "SELECT T1.hold_type,
                    T1.parent_series_flag,
                    T1.series_convention_id,
                    T2.convention_name as series_convention_name,
                    T1.convention_type,
                    T1.hold_method,
                    T1.convention_name,
                    T1.place,
                    T1.hold_purpose,
                    T1.remarks,
                    T1.hold_form,
                    T1.cohost_partner_name,
                    T1.cost_share_type,
                    T1.cost_share_content,
                    (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                        'product_cd', product_cd,
                        'product_name', product_name,
                        'delete_flag', 0
                        )),']') FROM t_convention_product 
                        WHERE convention_id = T1.convention_id
                    ) AS convention_product
                FROM t_convention T1
                    LEFT JOIN t_convention T2 on T2.convention_id = T2.series_convention_id
                WHERE T1.convention_id = :convention_id";
        $parram = ['convention_id' => $convention_id];
        return $this->_first($sql, $parram);
    }
}
