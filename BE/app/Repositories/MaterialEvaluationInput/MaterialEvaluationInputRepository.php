<?php

namespace App\Repositories\MaterialEvaluationInput;

use App\Repositories\BaseRepository;
use App\Repositories\MaterialEvaluationInput\MaterialEvaluationInputRepositoryInterface;

class MaterialEvaluationInputRepository extends BaseRepository implements MaterialEvaluationInputRepositoryInterface
{
    protected $useAutoMeta = true;
    
    public function getMaterialEvaluationInput($document_usage_type, $document_usage_id)
    {
        $sql = "
            SELECT 
                t_document_usage_situation.document_id,
                t_document.document_name,
                t_document_usage_situation.usage_situation_id,
                t_document_review.document_review,
                t_document_review.review_comment,
                t_customize_document_detail.source_document_id
            FROM t_document_usage_situation
            INNER JOIN t_document
                ON t_document_usage_situation.document_id = t_document.document_id
            LEFT JOIN t_document_review
                ON t_document_review.usage_situation_id = t_document_usage_situation.usage_situation_id
            LEFT JOIN t_customize_document_detail
                ON t_customize_document_detail.document_id = t_document_usage_situation.document_id
            WHERE t_document_usage_situation.document_usage_type = :document_usage_type
            AND t_document_usage_situation.document_usage_id = :document_usage_id
            GROUP BY t_document_usage_situation.document_id
            ORDER BY t_document_usage_situation.document_id DESC;";
        return $this->_find($sql, ['document_usage_type' => $document_usage_type, 'document_usage_id' => $document_usage_id]);
    }

    public function checkRegister($usage_situation_id)
    {
        $sql = "SELECT usage_situation_id FROM t_document_review WHERE usage_situation_id = :usage_situation_id;";
        return $this->_first($sql,[ "usage_situation_id" => $usage_situation_id]);
    }

    public function register($usage_situation_id,$document_id,$document_review,$review_customize_document_id,$review_comment,$review_datetime,$review_org_label,$review_user_cd,$review_user_name)
    {
        $sql = "INSERT INTO t_document_review
            (usage_situation_id,document_id,document_review,review_customize_document_id,review_comment,review_datetime,review_org_label,review_user_cd,review_user_name)
        VALUES
            (:usage_situation_id,:document_id,:document_review,:review_customize_document_id,:review_comment,:review_datetime,:review_org_label,:review_user_cd,:review_user_name);";
        return $this->_create($sql,[
            "usage_situation_id" => $usage_situation_id,
            "document_id" => $document_id,
            "document_review" => $document_review,
            "review_customize_document_id" => $review_customize_document_id,
            "review_comment" => $review_comment,
            "review_datetime" => $review_datetime,
            "review_org_label" => $review_org_label,
            "review_user_cd" => $review_user_cd,
            "review_user_name" => $review_user_name
        ]);
    }

    public function updateRegisterItem($usage_situation_id,$document_id,$document_review,$review_customize_document_id,$review_comment,$review_datetime,$review_org_label,$review_user_cd,$review_user_name)
    {
        $sql = "
                UPDATE t_document_review
                    SET document_id = :document_id,
                        document_review = :document_review,
                        review_customize_document_id = :review_customize_document_id,
                        review_comment = :review_comment,
                        review_datetime = :review_datetime,
                        review_org_label = :review_org_label,
                        review_user_cd = :review_user_cd,
                        review_user_name = :review_user_name
                WHERE usage_situation_id = :usage_situation_id;";

        return $this->_update($sql, [
            'document_id' => $document_id,
            'document_review' => $document_review,
            'review_customize_document_id' => $review_customize_document_id,
            'review_comment' => $review_comment,
            'review_datetime' => $review_datetime,
            'review_org_label' => $review_org_label,
            'review_user_cd' => $review_user_cd,
            'review_user_name' => $review_user_name,
            'usage_situation_id' => $usage_situation_id
        ]);
    }

    public function getPointTargetType()
    {
        $sql = "SELECT definition_name,definition_value,definition_label FROM m_variable_definition WHERE definition_name = :definition_name AND definition_label = :definition_label;";
        return $this->_first($sql,[
            'definition_name' => '活用度ポイント対象区分',
            'definition_label' => '資材'
        ]);
    }

    public function getFixedActivePoint()
    {
        $sql = "SELECT fixed_active_point_cd,active_point FROM m_fixed_active_point WHERE remarks = :remarks;";
        return $this->_first($sql,[
            'remarks' => '資材_レビュー登録'
        ]);
    }

    public function insertActivePoint($point_target_type,$active_point,$message,$receive_user_cd,$usage_situation_id)
    {
        $sql = "
            INSERT INTO t_active_point
                (point_target_type,active_point,message,receive_user_cd,point_target_id)
            VALUES
                (:point_target_type,:active_point,:message,:receive_user_cd,:point_target_id);
        ";
        return $this->_create($sql , [
            "point_target_type" => $point_target_type,
            "active_point" => $active_point,
            "message" => $message,
            "receive_user_cd" => $receive_user_cd,
            "point_target_id" => $usage_situation_id
        ]);
    }
}
