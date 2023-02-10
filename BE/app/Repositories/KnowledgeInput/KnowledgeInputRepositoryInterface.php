<?php

namespace App\Repositories\KnowledgeInput;

use App\Repositories\BaseRepositoryInterface;

interface KnowledgeInputRepositoryInterface extends BaseRepositoryInterface
{
    // get param
    public function getVariableDefinitionApprovalWorkType($variable_name);
    public function getStatus($definition_name);
    public function getCategory();
    public function getMedicalSubjects();
    public function getActivePoint();

    //get data
    public function getLayerNum();
    public function getStatusKnowledge($knowledge_id);
    public function knowledgeTab1AndTab2($knowledge_id, $user_cd, $approval_layer_num, $request_type);
    public function getLastApproverLayer($knowledge_id, $date_system, $approval_work_type, $approval_layer_num);
    public function getUserApproval($approval_work_type, $request_user_cd);
    public function getOtherLastApproverLayer($knowledge_id, $date_system, $approval_work_type, $approval_layer_num);
    public function knowledgeNotes();
    public function knowledgeTimeLine($knowledge_id);

    public function knowledgeTimeLine10($timeline_id, $channel_type, $knowledge_id);
    public function knowledgeTimeLine20($timeline_id, $channel_type, $knowledge_id);
    public function knowledgeTimeLine30($timeline_id, $channel_type, $knowledge_id);
    public function knowledgeTimeLineNone($timeline_id, $channel_type, $knowledge_id);

    // update and create 
    public function getKnowledge($knowledge_id);
    public function getMasterFacilityPerson($facility_cd, $person_cd);
    public function updateKnowledgeInput($knowledge_id, $request);
    public function createKnowledgeInput($request);
    public function createKnowledgeCollaborator($datas);
    public function deleteKnowledgeCollaborator($knowledge_id);
    public function getFacilityGroup($facility_cd);
    public function checkKnowledgeTimeline($knowledge_id, $timeline_id);
    public function updateKnowledgeTimeline($knowledge_id, $timeline_id, $comment);
    public function insertKnowledgeTimeline($knowledge_id, $channel_type, $timeline_id, $channel_id, $comment);
    public function deleteKnowledgeTimelineDetails($knowledge_id, $timeline_id);

    // remand

    // public
    public function checkActivePoint($knowledge_id);
    public function getVariableDefinition($definition_name, $definition_label);
    public function createActivePoint($knowledge_id, $active_point_cd, $point_target_type, $message, $create_user_cd);
    public function updateKnowledgePublic($knowledge_id, $knowledge_status_type, $first_release_datetime, $release_datetime, $last_update_datetime);
    public function updateApprovalRequestDetail($request_id, $layer_num, $approval_user_cd, $status_type, $approval_datetime);

    // none public
    public function nonePublic($knowledge_id, $knowledge_status_type);

    // submit
    public function updateKnowledgeSubmit($knowledge_id, $knowledge_status_type, $active_point_cd);
    public function createApprovalRequestDetail($request_id, $layer_num, $approval_user_cd, $status_type, $approval_datetime);

    // rejection
    public function noneRejection($knowledge_id, $knowledge_status_type, $submit_comment);
    public function getApprovalLayer($knowledge_id, $layer_num);
    public function getInformCategory($inform_category_name);
    public function createApprovalRequest($request_target_id, $create_user_cd, $request_type, $status_type, $request_datetime);


    // delete
    public function deleteKnowledge($knowledge_id);
    public function getApprovalRequest($knowledge_id, $request_type);
    public function deleteApprovalRequest($knowledge_id, $request_type);
    public function deleteKnowledgeTimeline($knowledge_id);

    public function updateStatusKnowledge($knowledge_id, $knowledge_status_type);
    public function updateFirstReleaseDatetime($knowledge_id, $first_approval_datetime);
    public function updateApprovalRequest($request_id, $status_type, $active_layer_num);
    public function getActivePointTemp($remarks);
    public function getStatusApproval($definition_name);

    public function checkRole($user_cd, $role);
    public function updateCommentApprovalRequestDetail($request_id, $layer_num, $approval_user_cd, $comment);
    public function getDetailByCallID($call_id);
}
