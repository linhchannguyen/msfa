<?php

namespace App\Repositories\CustomMaterialManagement;

use App\Repositories\BaseRepository;
use App\Traits\StringConvertTrait;

class CustomMaterialManagementRepository extends BaseRepository implements CustomMaterialManagementRepositoryInterface
{
    use StringConvertTrait;
    protected $useAutoMeta = true;

    public function getMedicalSubjectsGroup()
    {
        $sql = "SELECT medical_subjects_group_cd, medical_subjects_group_name FROM m_medical_subjects_group;";
        return $this->_all($sql);
    }

    public function getVariableDefinition()
    {
        $sql = "SELECT definition_value, definition_label FROM m_variable_definition WHERE definition_name = :presence_or_absence_options;";
        $condition['presence_or_absence_options'] = PRESENCE_OR_ABSENCE_OPTIONS;
        return $this->_find($sql, $condition);
    }

    public function searchCustomMaterial($conditions)
    {
        $condition = [];
        $sql = "SELECT
            t_document.document_id,
            t_document.document_type,
            t_document.document_name,
            t_document.start_date,
            t_document.end_date,
            t_document.document_contents,
            t_document.disease,
            t_document.medical_subjects_group_cd,
            m_medical_subjects_group.medical_subjects_group_name,
            t_document.safety_information_flag,
            t_document.off_label_information_flag,
            t_document.create_user_cd,
            t_document.last_update_datetime,
            t_document.file_type,
            (SELECT GROUP_CONCAT(m_product.product_name SEPARATOR ', ') FROM t_document_product JOIN m_product ON m_product.product_cd = t_document_product.product_cd WHERE t_document_product.document_id = t_document.document_id) AS product_list,
            (SELECT m_variable_definition.definition_label 
                FROM m_variable_definition 
                WHERE m_variable_definition.definition_name LIKE :presence_or_absence_options1
                AND m_variable_definition.definition_value = t_document.safety_information_flag
            ) as definition_label_a8,
            (SELECT m_variable_definition.definition_label 
                FROM m_variable_definition 
                WHERE m_variable_definition.definition_name LIKE :presence_or_absence_options2
                AND m_variable_definition.definition_value = t_document.off_label_information_flag
            ) as definition_label_a9,
            (CASE WHEN (SELECT count(*) FROM t_document_usage_situation WHERE t_document_usage_situation.document_id = t_document.document_id) > 0 THEN 1 ELSE 0 END) AS edit_mode
        FROM t_document
        JOIN m_variable_definition ON m_variable_definition.definition_value = t_document.document_type
            AND m_variable_definition.definition_name = :material_classification
            AND m_variable_definition.definition_label = :custom_material
        LEFT JOIN t_document_product ON t_document_product.document_id = t_document.document_id
        LEFT JOIN m_medical_subjects_group ON m_medical_subjects_group.medical_subjects_group_cd = t_document.medical_subjects_group_cd";
        if ($conditions->disuse_flag === "0" || $conditions->disuse_flag === "1") {
            $sql .= " JOIN t_customize_document_detail ON t_customize_document_detail.document_id = t_document.document_id";
        }
        $sql .= " WHERE t_document.create_user_cd = :user_cd";
        $condition['user_cd'] = $conditions->user_cd;
        $condition['presence_or_absence_options1'] = '%' . trim($this->convert_kana(PRESENCE_OR_ABSENCE_OPTIONS)) . '%';
        $condition['presence_or_absence_options2'] = '%' . trim($this->convert_kana(PRESENCE_OR_ABSENCE_OPTIONS)) . '%';
        $condition['custom_material'] = CUSTOM_MATERIAL;
        $condition['material_classification'] = MATERIAL_CLASSIFICATION;
        if ($conditions->disuse_flag === "0" || $conditions->disuse_flag === "1") {
            $sql .= " AND t_customize_document_detail.disuse_flag = :disuse_flag";
            $condition['disuse_flag'] = $conditions->disuse_flag;
        }
        if ($conditions->document_name != "") {
            $sql .= " AND t_document.document_name LIKE :document_name";
            $condition['document_name'] = '%' . trim($this->convert_kana($conditions->document_name)) . '%';
        }
        if (!empty($conditions->product_cd)) {
            $sql .= " AND t_document_product.product_cd IN " . $this->_buildCommaString($conditions->product_cd, true);
        }
        if (!empty($conditions->disease)) {
            $sql .= " AND t_document.disease LIKE :disease";
            $condition['disease'] = '%' . trim($this->convert_kana($conditions->disease)) . '%';
        }
        if (!empty($conditions->medical_subjects_group_cd)) {
            $sql .= " AND t_document.medical_subjects_group_cd = :medical_subjects_group_cd";
            $condition['medical_subjects_group_cd'] = $conditions->medical_subjects_group_cd;
        }
        if (!empty($conditions->applicable_date)) {
            $sql .= " AND CAST(t_document.start_date AS DATE) <= :applicable_date_start AND CAST(t_document.end_date AS DATE) >= :applicable_date_end";
            $condition['applicable_date_start'] = $condition['applicable_date_end'] = $conditions->applicable_date;
        }
        if ($conditions->safety_information_flag === "0" || $conditions->safety_information_flag === "1") {
            $sql .= " AND t_document.safety_information_flag = :safety_information_flag";
            $condition['safety_information_flag'] = $conditions->safety_information_flag;
        }
        if ($conditions->off_label_information_flag === "0" || $conditions->off_label_information_flag === "1") {
            $sql .= " AND t_document.off_label_information_flag = :off_label_information_flag";
            $condition['off_label_information_flag'] = $conditions->off_label_information_flag;
        }
        $sql .= " GROUP BY t_document.document_id ORDER BY last_update_datetime DESC, document_id DESC";
        return $this->_paginate($sql, $condition, [
            "limit" => $conditions->limit,
            "offset" => $conditions->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }
}
