<?php

namespace App\Repositories\MaterialEvaluationInput;

use App\Repositories\BaseRepositoryInterface;

interface MaterialEvaluationInputRepositoryInterface extends BaseRepositoryInterface
{
    public function getMaterialEvaluationInput($document_usage_type, $document_usage_id);
    public function updateRegisterItem($usage_situation_id,$document_id,$document_review,$review_customize_document_id,$review_comment,$review_datetime,$review_org_label,$review_user_cd,$review_user_name);
    public function checkRegister($usage_situation_id);
    public function register($usage_situation_id,$document_id,$document_review,$review_customize_document_id,$review_comment,$review_datetime,$review_org_label,$review_user_cd,$review_user_name);
    public function getPointTargetType();
    public function getFixedActivePoint();
    public function insertActivePoint($point_target_type,$active_point,$message,$receive_user_cd,$usage_situation_id);
}
