<?php

namespace App\Repositories\BriefingSearch;

use App\Repositories\BaseRepositoryInterface;

interface BriefingSearchRepositoryInterface extends BaseRepositoryInterface
{
    //get Briefing Session
    public function getBriefingSession();
    //get Information Session
    public function getInformationSession();
    //get List Data Briefing
    public function getListDataBriefing($requet);
    //get Variable Definition Approval Work Type
    public function getVariableDefinitionApprovalWorkType($variable_name);
    //get Bento Disposal Method
    public function getBentoDisposalMethod();
    //get Briefing Attendee Count
    public function getBriefingAttendeeCount($briefing_id);
    //get Briefing Expense Item
    public function getBriefingExpenseItem($briefing_id);
    //get Data Detail Briefing By BriefingID
    public function getDataDetailBriefingByBriefingID($briefing_id, $schedule_id);
    //get User Approval Briefing
    public function getUserApprovalBriefing($user_login, $briefing_id, $briefing_tpye, $layer_num);
    //get User Approval Briefing
    public function getUserApproval($approval_work_type, $request_user_cd);
    //update Status Type Briefing
    public function updateStatusTypeBriefing($briefing_id, $status_type);
    //get Display Option Tpye Briefing
    public function getDisplayOptionTpyeBriefing();
    //get Variable Definition Briefing
    public function getVariableDefinitionBriefing();
    //save Briefing
    public function saveBriefing($requet);
    //update Briefing
    public function updateBriefing($requet);
    //save Briefing Product
    public function saveBriefingProduct($briefing_id, $product_cd, $product_name);
    //delete Briefing Product
    public function deleteBriefingProduct($briefing_id, $product_cd);
    //save Briefing Document
    public function saveBriefingDocument($briefing_id, $document_id);
    //delete Briefing Document
    public function deleteBriefingDocument($briefing_id, $document_id);
    //save Briefing Plan Attendee Count
    public function saveBriefingPlanAttendeeCount($briefing_id, $occupation_type, $plan_headcount, $result_headcount);
    //save Briefing Result Attendee Count
    public function updateBriefingResultAttendeeCount($briefing_id, $occupation_type, $result_headcount);
    //update Briefing Plan Attendee Count
    public function updateBriefingPlanAttendeeCount($briefing_id, $occupation_type, $plan_headcount, $result_headcount);
    //save Briefing Attendee
    public function saveBriefingAttendee($briefing_id, $data);
    //update Briefing Attendee
    public function updateBriefingAttendee($data);
    //update Briefing Attendee Result
    public function updateBriefingResultExpenseItem($briefing_id, $data);
    //delete Briefing Attendee
    public function deleteBriefingAttendee($briefing_attendee_id);
    //delete Detail Briefing
    public function deleteDetailBriefing($briefing_id);
    //save Briefing Expense Item
    public function saveBriefingExpenseItem($briefing_id, $data);
    //update Briefing Expense Item
    public function updateBriefingExpenseItem($briefing_id, $data);
    //update Briefing Expense Item
    public function checkUserApprovalFinal($user_login, $briefing_id, $briefing_tpye);
    //update Briefing Expense Item
    public function updateApprovalBriefingFinal($briefing_id, $approval_briefing, $request_type);
    //update Briefing Expense Item
    public function getRequestIDApproval($briefing_id, $request_type);
    //update Briefing Expense Item
    public function updateRemandFlagBriefing($briefing_id, $remand_flag);
    //update Briefing Expense Item
    public function updateApprovalBriefingDetail($request_id, $user_login, $approval_briefing, $comment, $layer_num);
    //update Briefing Expense Item
    public function getLayerNumApprovalFinnalBriefing();
    //update Briefing Expense Item
    public function deleteApprovalRequest($request_id);
}
