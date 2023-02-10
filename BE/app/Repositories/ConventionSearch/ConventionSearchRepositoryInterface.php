<?php

namespace App\Repositories\ConventionSearch;

use App\Repositories\BaseRepositoryInterface;

interface ConventionSearchRepositoryInterface extends BaseRepositoryInterface
{
    //get Status Type
    public function getStatusType();
    //get Status Type
    public function getInputDeadlineConvention($display_option_name);
    //get all select org code
    public function getAllSelectOrgCode();
    //get Convention Type
    public function getConventionType();
    //get all data Convention
    public function allData($request);
    //get Hold Status
    public function getHoldType();
    //get Hold Method
    public function getHoldMethod();
    //get Organization Layer
    public function getOrganizationLayer();
    //get Hold Form
    public function getHoldForm();
    //get Cost Share Type
    public function getCostShareType();
    //get Expense Classification
    public function getExpenseClassification();
    //get Series Registration
    public function getSeriesRegistration();
    //get Bento Disposal
    public function getBentoDisposal();
    //get User Approval Convention
    public function getUserApprovalConventionPlan($convention_id, $request_type, $layer_num);
    //get User Approval Convention
    public function getLayerUserApprovalConvention($user_login, $convention_id, $request_type);
    //get Detail Convention
    public function getDetailConventionByConventionID($convention_id);
    //get Document Convention
    public function getDocumentConvention($convention_id);
    //get Convention Area Expense
    public function getConventionAreaExpense($convention_id);
    //get Convention Expense Detail By Convention Id
    public function getConventionExpenseDetailByConventionId($convention_id);
    //get Convention Area Expense
    public function getConventionAreaExpenseLayer($expense_item_cd, $convention_id);
    //get Convention Target Org
    public function getConventionTargetOrg($convention_id);
    //get Convention File
    public function getConventionFile($convention_id);
    //get Convention Attendee
    public function getConventionAttendee($convention_id);
    //get Convention Status Item
    public function getConventionStatusItem();
    //get Convention Occupation Type
    public function getConventionOccupationType();
    //update Status Type Convention
    public function updateStatusTypeConvention($convention_id, $status_type);
    //update Remand Flag Convention
    public function updateRemandFlagConvention($convention_id, $remand_flag);
    //Check user approval finnal
    public function checkUserApprovalFinal($approval_work_type, $user_login, $user_create_convention);
    //insert Convention Product
    public function insertConventionProduct($convention_id, $request);
    //delete Convention Product
    public function deleteConventionProduct($convention_id, $product_cd);
    //created Convention
    public function createdConvention($request);
    //delete Convention Target Org
    public function deleteConventionTargetOrg($convention_id, $org_cd);
    //update Convention save plan
    public function updateConventionPlan($request);
    //created Convention Target Org
    public function createdConventionTargetOrg($convention_id, $data);
    //created Convention Document
    public function createdConventionDocument($convention_id, $request);
    //delete Convention Document
    public function deleteConventionDocument($convention_id, $document_id);
    //update Convention Attendee
    public function updateConventionAttendee($convention_id, $request);
    //update Convention Plan Mount
    public function updateConventionPlanMount($convention_id, $expense_item_cd, $plan_amount);
    //update Convention Plan Mount
    public function updateConventionPlanMountLayer2($convention_id, $area_expense, $option_item_name, $option_item_content, $plan_unit_price, $plan_quantity, $plan_amount);
    //update Convention Plan Mount
    public function updateConventionResultMount($convention_id, $expense_item_cd, $plan_amount);
    //update Convention Plan Mount
    public function updateConventionResultMountLayer2($convention_id, $expense_item_cd, $option_item_name, $option_item_content, $result_unit_price, $result_amount, $result_quantity);
    //update Convention Result Mount By Last Approval
    public function updateConventionResultMountByLastApproval($convention_id, $expense_item_cd, $result_unit_price, $result_amount, $result_quantity);
    //delete schedule common subject
    public function deleteScheduleCommonSubject($schedule_id);
    //save schedule common subject
    public function saveScheduleCommonSubject($schedule_id, $org_cd);
    //save schedule common subject
    public function getUserApproval($approval_work_type, $layer_num, $request_user_cd);
    //add Approval Request
    public function addApprovalRequest($data_approval_request);
    //add Approval Request Detail
    public function addApprovalRequestDetail($data_approval_request_detail);
    //add Approval Request Detail
    public function selectionSeriesConventionDetail($convention_id);
    //get installed
    public function installed($user_cd, $inform_category_cd = null);
    //add Inform
    public function addInform($infrom);
    //add Inform
    public function addInformParameter($inform_parameter);
    //get Inform Category Name
    public function getInformCategory($category_name);
    //get Display Option Tpye
    public function getDisplayOptionTpye();
    //add convention file
    public function addConventionFile($convention_file);
    //delete File
    public function deleteFile($file_id);
    //delete File
    public function deleteConventionFile($convention_id, $file_id);
    //get Use Type
    public function getUseType($definition_label);
    //get document Use Type
    public function getDocumentUsageType($definition_label_usage_type);
    //create Document Usage Situation
    public function createDocumentUsageSituation($request, $document_usage_id, $document_usage_type, $document_id);
    //delete Document Usage Situation
    public function deleteDocumentUsageSituation($convention_id, $document_usage_type, $document_id);
    //remand Convention Final
    public function updateConventionFinal($request_id, $status_type);
    //remand Convention
    public function updateConvention($request_id, $user_login, $status_type, $comment, $layer_num);
    //get approval request
    public function getApprovalRequest($convention_id, $request_type);
    //delete convention
    public function deleteConvention($convention_id);
    //get Convention Order type 10
    public function getConventionOrderType10($convention_id, $occupation_type);
    //get Convention Order type 10
    public function getConventionOrderType20($convention_id, $occupation_type, $medical_staff_cd);
    //get Convention Order type 60 or 70
    public function getConventionOrderType60($convention_id, $occupation_type);
    //get Follow Convention Order Type 10
    public function getFollowConventionOrderType10($convention_id, $medical_staff_cd, $convention_start_date, $convention_start_date_add_two_day);
    //all Convention Expense Item
    public function allConventionExpenseItem();
    //all Convention Expense Item
    public function saveConventionPlanExpenseItem($convention_id, $expense_item_cd);
    //all Convention Expense Item Children
    public function allConventionExpenseItemChildren($expense_item_cd);
    //get Convention Document Review
    public function getDocumentReview($convention_id, $document_usage_type);
    //get Uer Org
    public function getTargetUserOrg($user_cd, $convention_id);
    //get User Create Report
    public function getUserCreateReport($schedule_id);
    //get User Create Report
    public function getLayerNumApprovalFinnal();
    //get User Create Report
    public function getUserConventionTargetOrg($convention_id);
    //get User Create Report
    public function getUserApprovalReport($report_id, $request_type);
    //get User Create Report
    public function updateInvisibleFlagSchedule($schedule_id);
    //get User Create Report
    public function updateActiveApprovalRequest($request_id, $active_layer_num);
    //get User Create Report
    public function deleteApprovalRequest($request_id);
    //get User Create Report
    public function getCopyConvention($convention_id);
}
