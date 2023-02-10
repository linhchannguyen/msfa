<?php

namespace App\Services;

use App\Repositories\MaterialEvaluationInput\MaterialEvaluationInputRepositoryInterface;

class MaterialEvaluationInputService
{
    private $repo;

    public function __construct(MaterialEvaluationInputRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getMaterialEvaluationInput($document_usage_type, $document_usage_id)
    {
        return $this->repo->getMaterialEvaluationInput($document_usage_type, $document_usage_id);
    }

    public function registerItem($data,$date_system,$data_user,$receive_user_cd)
    {
        // get point target type
        $m_variable_definition = $this->repo->getPointTargetType();
        $point_target_type = $m_variable_definition->definition_value ?? 0;
        $m_fixed_active_point = $this->repo->getFixedActivePoint();
        $active_point = $m_fixed_active_point->active_point ?? 0;

        if (count((array)$data) > 0) {
            foreach ($data as $item) {
                $item = (object)$item;
                $data_temp = $this->repo->checkRegister($item->usage_situation_id);
                if(empty($data_temp->usage_situation_id ?? '')){
                    $this->repo->register($item->usage_situation_id,$item->document_id,$item->document_review,$item->source_document_id,$item->review_comment,$date_system,$data_user->org_label,$data_user->user_cd,$data_user->user_name);
                    // insert table t_active_point
                    if(!empty($item->document_review) || !empty($item->review_comment)){
                        $message =  __('materialevaluationinput.message', ['attribute' => $item->document_name]);
                        $this->repo->insertActivePoint($point_target_type,$active_point,$message,$receive_user_cd,$item->usage_situation_id);
                    }
                }else{
                    $this->repo->updateRegisterItem($item->usage_situation_id,$item->document_id,$item->document_review,$item->source_document_id,$item->review_comment,$date_system,$data_user->org_label,$data_user->user_cd,$data_user->user_name);
                }
            }
        }
    }
}
