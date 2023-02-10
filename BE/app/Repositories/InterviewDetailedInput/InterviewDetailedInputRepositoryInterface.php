<?php

namespace App\Repositories\InterviewDetailedInput;

use App\Repositories\BaseRepositoryInterface;

interface InterviewDetailedInputRepositoryInterface extends BaseRepositoryInterface
{
    public function getScreenData();
    public function getInterviewDateTime($call_id, $schedule_id);
    public function getOffLabel($call_id, $schedule_id);
    public function detailArea($call_id, $schedule_id);
    public function getDetailArea($detail_id);
    public function updateSchedule($schedule_id, $display_option_type);
    public function deleteDetail($detail_id, $facility_cd, $person_cd, $product_cd, $start_date);
    public function deleteDetailDocument($detail_id);
    public function insertDetailDocument($detail_id, $document_id);

    public function updateActFlag($call_id);
    public function getCall($call_id);
    public function getVisit($schedule_id, $visit_id);


    public function getSchedule($schedule_id);
    public function getInfoPerson($person_cd);
    public function updateCall($call_id, $off_label_flag, $off_label_concent_flag, $off_label_call_time, $related_product, $question, $answer, $re_question, $literature, $act_flag, $person_info, $person_name);

    public function checkDetail($detail_id, $call_id);
    public function updateDetail($detail_id,$call_id,$detail_order,$content_cd,$content_name_tran,$product_cd,$product_name,$note,$remarks,$phase_cd,$phase,$reaction_cd,$reaction,$prescription_count);
    public function insertDetail($call_id,$detail_order,$content_cd,$content_name_tran,$product_cd,$product_name,$note,$remarks,$phase_cd,$phase,$reaction_cd,$reaction,$prescription_count);

    public function getMasterVariable();
    public function getDocumentUsageSituation($document_id,$document_usage_id);
    public function deleteDocumentUsageSituation($document_id,$call_id);
    public function insertDocumentUsageSituation($document_id,$definition_value,$call_id,$usage_org_label,$usage_user_cd,$usage_user_name,$start_time);

    public function checkReaction($facility_cd, $person_cd, $product_cd, $reaction_cd);
    public function insertReaction($facility_cd, $person_cd, $start_date, $product_cd, $reaction_cd);
    public function updateReaction($facility_cd, $person_cd, $start_date, $product_cd, $reaction_cd);

    public function checkPhase($facility_cd, $person_cd, $product_cd);
    public function insertPhase($facility_cd, $person_cd, $start_date, $phase_cd, $product_cd);
    public function updatePhase($facility_cd, $person_cd, $start_date, $phase_cd, $product_cd);

    public function checkPrescription($facility_cd, $person_cd, $product_cd);
    public function insertPrescription($facility_cd, $person_cd, $start_date, $prescription_count, $product_cd);
    public function updatePrescription($facility_cd, $person_cd, $start_date, $prescription_count, $product_cd);

}
