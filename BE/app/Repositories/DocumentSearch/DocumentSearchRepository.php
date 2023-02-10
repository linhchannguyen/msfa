<?php

namespace App\Repositories\DocumentSearch;

use App\Repositories\BaseRepository;
use App\Repositories\DocumentSearch\DocumentSearchRepositoryInterface;
use App\Traits\StringConvertTrait;
use App\Traits\DateTimeTrait;

class DocumentSearchRepository extends BaseRepository implements DocumentSearchRepositoryInterface
{
    use StringConvertTrait, DateTimeTrait;
    protected $useAutoMeta = true;
    private $date, $document_type, $presence_or_absence, $document_classification;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
        $this->document_type = 10;
        $this->presence_or_absence = "有無選択肢";
        $this->document_classification = "資材区分";
    }

    public function getVariable()
    {
        $query['definition_name'] = $this->presence_or_absence;
        $sql = "SELECT definition_value,definition_label FROM m_variable_definition WHERE definition_name = :definition_name";
        return $this->_find($sql, $query);
    }

    public function getDateNewIcon()
    {
        $query['parameter_key'] = PARAMETER_KEYS_LATEST_PERIOD_DAYS;
        $sql = "SELECT parameter_value FROM c_system_parameter WHERE parameter_key = :parameter_key";
        return $this->_first($sql, $query);
    }

    public function getRoleUser($user_login)
    {
        $query['user_cd'] = $user_login;
        $query['role'] = 'R40';
        $sql = "SELECT * FROM m_user_role WHERE user_cd = :user_cd AND role = :role";
        return $this->_first($sql, $query);
    }

    public function getDateUpdateIcon()
    {
        $query['parameter_key'] = PARAMETER_KEYS_NUMBER_OF_DAYS_WITH_RENEWAL;
        $query['parameter_name'] = PARAMETER_NAME_MATERIAL_MANAGEMENT;
        $sql = "SELECT parameter_value FROM c_system_parameter WHERE parameter_key = :parameter_key AND parameter_name = :parameter_name";
        return $this->_first($sql, $query);
    }

    public function allData($request)
    {
        $where = "";
        $query['document_type'] = $this->document_type;
        if (!$request->role_user_r40) {
        $sql_org_user = "SELECT T3.org_cd,T3.layer_num,layer_1 
                    FROM m_user T1 
                        JOIN m_user_org T2 ON T1.user_cd = T2.user_cd 
                        JOIN m_org T3 ON T2.org_cd = T3.org_cd
                    WHERE T1.user_cd = :user_cd
                    GROUP BY layer_1";
        $org_user = $this->_find($sql_org_user, ['user_cd' => $request->user_login]) ?? [];
        $where_org = "";
        if (count($org_user) > 0) {
            for ($i = 0; $i < count($org_user); $i++) {
                if ($i == 0) {
                    $where_org .= ' layer_' . $org_user[$i]->layer_num . ' = ' . $org_user[$i]->org_cd;
                } else {
                    $where_org .= ' OR layer_' . $org_user[$i]->layer_num . ' = ' . $org_user[$i]->org_cd;
                }
                if ($i == (count($org_user) - 1)) {
                    $where_org .= ')';
                }
            }
        }
            $where .= " AND ((CASE WHEN T2.available_org_cd IS NULL THEN (T2.available_org_cd IS NULL) ELSE T2.available_org_cd IN ( SELECT org_cd FROM m_org WHERE (" . $where_org . ") END) OR T1.create_user_cd = :create_user_cd)";
            $query['create_user_cd'] = $request->user_login;
        }
        if ($request->document_name) {
            $where .= " AND T1.document_name like :document_name";
            $query['document_name'] = "%" . trim($this->convert_kana($request->document_name)) . "%";
        }
        if ($request->product_cd) {
            $where .= " AND T4.product_cd = :product_cd";
            $query['product_cd'] = $request->product_cd;
        }
        if ($request->disease) {
            $where .= " AND T1.disease like :disease";
            $query['disease'] = "%" . trim($this->convert_kana($request->disease)) . "%";
        }
        if ($request->medical_subjects_group_cd) {
            $where .= " AND T5.medical_subjects_group_cd = :medical_subjects_group_cd";
            $query['medical_subjects_group_cd'] = $request->medical_subjects_group_cd;
        }
        if ($request->safety_information_flag) {
            $where .= " AND T1.safety_information_flag = :safety_information_flag";
            $query['safety_information_flag'] = $request->safety_information_flag;
        }
        if ($request->off_label_information_flag) {
            $where .= " AND T1.off_label_information_flag = :off_label_information_flag";
            $query['off_label_information_flag'] = $request->off_label_information_flag;
        }
        if (isset($request->date)) {
            $where .= " AND T1.start_date <= :start_date AND T1.end_date >= :end_date";
            $query['start_date'] = $request->date;
            $query['end_date'] = $request->date;
        }
        $sql = "SELECT T1.document_id,
                        T1.last_update_datetime,
                        T1.created_at,
                        T1.updated_at,
                        T1.file_type,
                        T1.document_name,
                        T1.document_type,
                        T1.disease,
                        T2.document_version,
                        T4.product_cd,
                        T4.product_name,
                        T5.medical_subjects_group_cd,
                        T5.medical_subjects_group_name,
                        T1.safety_information_flag,
                        T7.definition_label AS safety_information_label,
                        T1.off_label_information_flag,
                        T8.definition_label AS off_label_information_label,
                        T1.start_date,
                        T1.end_date
                FROM t_document T1 
                    JOIN t_original_document_detail T2 ON T1.document_id = T2.document_id
                    LEFT JOIN t_document_product T3 ON T1.document_id = T3.document_id
                    LEFT JOIN m_product T4 ON T3.product_cd = T4.product_cd
                    LEFT JOIN m_medical_subjects_group T5 ON T1.medical_subjects_group_cd = T5.medical_subjects_group_cd
                    LEFT JOIN (SELECT definition_value,definition_label FROM m_variable_definition WHERE definition_name = :safety_information) T7 ON T1.safety_information_flag = T7.definition_value
                    LEFT JOIN (SELECT definition_value,definition_label FROM m_variable_definition WHERE definition_name = :off_label_information) T8 ON T1.off_label_information_flag = T8.definition_value
                WHERE T1.document_type = :document_type " . $where . "
                GROUP BY T1.document_id
                ORDER BY T1.last_update_datetime DESC ,T1.document_id DESC";
        $query['safety_information'] = $this->presence_or_absence;
        $query['off_label_information'] = $this->presence_or_absence;
        return $this->_paginate($sql, $query, [
            "limit" => $request->limit,
            "offset" => $request->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getDetailDocument($document_id)
    {
        $sql = "SELECT * FROM t_document WHERE document_id = :document_id";
        $query['document_id'] = $document_id;
        return $this->_first($sql, $query);
    }

    public function getDetailDocumentOriginal($document_id)
    {
        $sql = "SELECT T3.document_id, T4.document_name, T4.document_type,T4.file_type,CONCAT(T4.document_name,' ',T3.document_version) AS document_title
                FROM t_original_document_detail T1 
                    JOIN t_document T2 ON T2.document_id = T1.parent_document_id 
                    JOIN t_original_document_detail T3 on T2.document_id = T3.parent_document_id
                    JOIN t_document T4 ON T4.document_id = T3.document_id 
                WHERE T1.document_id = :document_id";
        $query['document_id'] = $document_id;
        return $this->_find($sql, $query);
    }

    public function getScoreDocumentOriginal($document_id)
    {
        $sql = "SELECT count(A.document_review) as sum_review,ROUND(AVG(A.document_review),1) AS avg_review
                FROM (
                SELECT T7.document_review
                FROM t_original_document_detail T1
                    JOIN t_document T2 on T1.parent_document_id = T2.document_id
                    JOIN t_original_document_detail T3 on T3.parent_document_id = T2.document_id
                    JOIN s_original_document_usage_situation_map T12 on T12.original_document_id = T3.document_id
                    JOIN t_document_review T7 ON T7.usage_situation_id = T12.usage_situation_id
                WHERE T1.document_id = :document_id
                GROUP BY T7.usage_situation_id) A";
        $query['document_id'] = $document_id;
        return $this->_first($sql, $query);
    }

    public function getCountSituationDocumentOriginal($document_id)
    {
        $sql = "SELECT T6.*
                FROM t_original_document_detail T1
                    JOIN t_document T2 on T1.parent_document_id = T2.document_id
                    JOIN t_original_document_detail T3 on T3.parent_document_id = T2.document_id
                        JOIN t_document_composition T8 on T3.document_id = T8.original_document_id
                        JOIN t_document_usage_situation T6 ON T6.document_id = T8.document_id
                WHERE T1.document_id = :document_id
                GROUP BY T6.usage_situation_id";
        $query['document_id'] = $document_id;
        return $this->_find($sql, $query);
    }

    public function getDetailDocumentCustom($document_id)
    {
        $sql = "SELECT T1.document_id, 
                    T1.document_name,
                    T1.file_type, 
                    T1.document_type,
                    T1.document_name AS document_title
                FROM t_document T1 JOIN t_customize_document_detail T2 ON T1.document_id = T2.document_id
                WHERE T1.document_id = :document_id";
        $query['document_id'] = $document_id;
        return $this->_first($sql, $query);
    }

    public function getDocumentOriginalProfile($document_id)
    {
        $sql = "SELECT T1.start_date,
                        T1.end_date,
                        T1.document_id,
                        T1.document_type,
                        T1.disease,
                        T2.medical_subjects_group_cd,
                        T2.medical_subjects_group_name,
                        T1.safety_information_flag,
                        T3.definition_label as safety_information_value,
                        T1.off_label_information_flag,
                        T4.definition_label as off_label_information_value,
                        T6.document_category_cd,
                        T6.document_category_name,
                        T5.management_org_name,
                        T7.org_label as available_org_label,
                        T5.available_org_cd,
                        T1.document_contents,
                        T1.document_name,
                        T9.product_cd,
                        T9.product_name,
                        T1.file_type,
                        T1.create_user_cd,
                        T10.user_name AS create_user_name,
                        T13.org_label,
                        CASE WHEN T11.usage_situation_id is null then 1 else 0 end document_edit,
                        (SELECT COUNT(*) FROM t_document_composition WHERE document_id = T1.document_id) AS count_page_document
                FROM t_document T1
                    LEFT JOIN m_medical_subjects_group T2 ON T1.medical_subjects_group_cd = T2.medical_subjects_group_cd
                    JOIN (SELECT * FROM m_variable_definition WHERE definition_name = :safety_information_flag) T3 ON T1.safety_information_flag = T3.definition_value
                    JOIN (SELECT * FROM m_variable_definition WHERE definition_name = :off_label_information_flag) T4 ON T1.off_label_information_flag = T4.definition_value
                    JOIN t_original_document_detail T5 ON T1.document_id = T5.document_id
                    JOIN m_document_category T6 ON T5.document_category_cd = T6.document_category_cd
                    LEFT JOIN m_org T7 ON T5.available_org_cd = T7.org_cd
                    LEFT JOIN t_document_product T8 on T1.document_id = T8.document_id
                    LEFT JOIN (SELECT * FROM m_product WHERE start_date <= :start_date and end_date >= :end_date) T9 on T8.product_cd = T9.product_cd
                    LEFT JOIN t_document_usage_situation T11 on T1.document_id = T11.document_id
                    LEFT JOIN m_user T10 ON T1.create_user_cd = T10.user_cd
                    LEFT JOIN m_user_org T12 ON T10.user_cd = T12.user_cd AND T12.main_flag = 1
                    LEFT JOIN m_org T13 ON T13.org_cd = T12.org_cd
                WHERE T1.document_id = :document_id ";
        $query['document_id'] = $document_id;
        $query['start_date'] = $this->date;
        $query['end_date'] = $this->date;
        $query['safety_information_flag'] = $this->presence_or_absence;
        $query['off_label_information_flag'] = $this->presence_or_absence;
        return $this->_first($sql, $query);
    }

    public function getDocumentCustomProfile($document_id)
    {
        $sql = "SELECT T1.start_date,
		                T1.end_date,
		                T1.document_id,
		                T1.document_type,
		                T1.disease,
		                T2.medical_subjects_group_cd,
		                T2.medical_subjects_group_name,
		                T1.safety_information_flag,
		                T3.definition_label as safety_information_value,
		                T1.off_label_information_flag,
		                T4.definition_label as off_label_information_value,
		                T7.org_label as available_org_label,
		                T5.available_org_cd,
		                T1.document_contents,
		                T1.document_name,
		                T9.product_cd,
		                T9.product_name,
                        T1.file_type,
                        T1.create_user_cd,
                        T10.user_name AS create_user_name,
                        T13.org_label,
                        CASE WHEN T11.usage_situation_id is null then 1 else 0 end document_edit,
                        (SELECT COUNT(*) FROM t_document_composition WHERE document_id = T1.document_id) AS count_page_document
                FROM t_document T1
                    LEFT JOIN m_medical_subjects_group T2 ON T1.medical_subjects_group_cd = T2.medical_subjects_group_cd
				    JOIN (SELECT * FROM m_variable_definition WHERE definition_name = :safety_information_flag) T3 ON T1.safety_information_flag = T3.definition_value
				    JOIN (SELECT * FROM m_variable_definition WHERE definition_name = :off_label_information_flag) T4 ON T1.off_label_information_flag = T4.definition_value
				    JOIN t_customize_document_detail T5 ON T1.document_id = T5.document_id
				    LEFT JOIN m_org T7 ON T5.available_org_cd = T7.org_cd
				    LEFT JOIN t_document_product T8 on T1.document_id = T8.document_id
				    LEFT JOIN (SELECT * FROM m_product WHERE start_date <= :start_date and end_date >= :end_date) T9 on T8.product_cd = T9.product_cd
                    LEFT JOIN t_document_usage_situation T11 on T1.document_id = T11.document_id
                    LEFT JOIN m_user T10 ON T1.create_user_cd = T10.user_cd
                    LEFT JOIN m_user_org T12 ON T10.user_cd = T12.user_cd AND T12.main_flag = 1
                    LEFT JOIN m_org T13 ON T13.org_cd = T12.org_cd
                WHERE T1.document_id = :document_id
                GROUP BY T1.document_id";
        $query['document_id'] = $document_id;
        $query['start_date'] = $this->date;
        $query['end_date'] = $this->date;
        $query['safety_information_flag'] = $this->presence_or_absence;
        $query['off_label_information_flag'] = $this->presence_or_absence;
        return $this->_first($sql, $query);
    }

    public function getDocumentCustom($request)
    {
        $sql = "SELECT CAST(ROUND(AVG(T7.document_review),1) AS float) AS avg_review,
                        COUNT(T7.document_review) AS sum_review,
                        T7.document_review,
                        T7.review_datetime,
                        T7.review_org_label,
                        T7.review_user_name,
                        A.*
                FROM (
                SELECT  T12.document_id,
                        T12.file_type,
                        T12.document_type,
                        T12.document_name,
                        T12.disease,
                        T12.create_user_cd,
                        T4.medical_subjects_group_name,
                        T12.safety_information_flag,
                        T5.definition_label AS safety_information_value,
                        T12.off_label_information_flag,
                        T6.definition_label AS off_label_information_value,
                        T12.document_contents,
                        T9.product_cd,
                        T9.product_name,
                        T11.available_org_cd,
                        T2.last_update_datetime
                from t_document_composition T1 
                    JOIN t_document T2 on T1.document_id = T2.document_id
                    JOIN t_customize_document_detail T11 on T2.document_id = T11.document_id
                    JOIN t_document T12 on T12.document_id = T11.document_id
                    LEFT JOIN m_medical_subjects_group T4 on T12.medical_subjects_group_cd = T4.medical_subjects_group_cd
                    LEFT JOIN t_document_product T8 on T12.document_id = T8.document_id
                    JOIN (SELECT * FROM m_product WHERE start_date <= :start_date and end_date >= :end_date) T9 on T8.product_cd = T9.product_cd
                    JOIN (SELECT * FROM m_variable_definition WHERE definition_name = :safety_information_flag) T5 ON T12.safety_information_flag = T5.definition_value
                    JOIN (SELECT * FROM m_variable_definition WHERE definition_name = :off_label_information_flag) T6 ON T12.off_label_information_flag = T6.definition_value
                WHERE T1.original_document_id = :original_document_id AND T1.document_id <> :document_id
                GROUP BY T12.document_id ) A 
                    -- LEFT JOIN t_document_usage_situation T10 on A.document_id = T10.document_id
                    LEFT JOIN t_document_review T7 on A.document_id = T7.review_customize_document_id or A.document_id = T7.document_id 
                GROUP BY A.document_id
                ORDER BY A.last_update_datetime DESC";
        $query['original_document_id'] = $request->document_id;
        $query['document_id'] = $request->document_id;
        $query['start_date'] = $this->date;
        $query['end_date'] = $this->date;
        $query['safety_information_flag'] = $this->presence_or_absence;
        $query['off_label_information_flag'] = $this->presence_or_absence;
        return $this->_paginate($sql, $query, [
            "limit" => $request->limit,
            "offset" => $request->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getListComposition($document_id)
    {
        $sql = "SELECT T1.original_document_id, T3.document_version, CONCAT(T2.document_name,' (',T3.document_version,') P', GROUP_CONCAT(T1.original_document_page_num separator ', ')) as original_document_page_num
                FROM t_document_composition T1 
                    JOIN t_document T2 on T1.original_document_id = T2.document_id
                    JOIN t_original_document_detail T3 ON T2.document_id = T3.document_id
                WHERE T1.document_id = :document_id
                GROUP BY T1.original_document_id";
        $query['document_id'] = $document_id;
        return $this->_find($sql, $query);
    }

    public function allDocumentCategory()
    {
        $sql = "SELECT document_category_cd, document_category_name FROM m_document_category";
        return $this->_find($sql, []);
    }

    public function getDocumentRegistrationDetail($document_id)
    {
        $sql = "SELECT T1.document_id,
                        T1.document_name,
                        T2.file_id,
                        T5.display_name,
                        T1.document_contents,
                        T2.document_version,
                        T2.parent_document_id,
                        T7.document_name as parent_document_name,
                        T1.start_date,
                        T1.end_date,
                        T2.available_org_cd,
                        CASE WHEN T2.available_org_cd IS NULL THEN 0 ELSE 1 END as with_settings,
                        T6.org_name,
                        T6.org_label,
                        T4.product_cd,
                        T4.product_name,
                        T1.disease,
                        T1.file_type,
                        T1.medical_subjects_group_cd,
                        T2.management_org_name,
                        T2.document_category_cd,
                        T1.safety_information_flag,
                        T1.off_label_information_flag,
                        T5.file_name
                FROM t_document T1
                    LEFT JOIN t_original_document_detail T2 ON T1.document_id = T2.document_id
                    LEFT JOIN t_document T7 ON T7.document_id = T2.parent_document_id
                    LEFT JOIN t_document_product T3 ON T1.document_id = T3.document_id
                    LEFT JOIN m_product T4 ON T3.product_cd = T4.product_cd
                    LEFT JOIN t_file T5 ON T2.file_id = T5.file_id
                    LEFT JOIN m_org T6 ON T2.available_org_cd = T6.org_cd
                WHERE T1.document_id = :document_id";
        return $this->_first($sql, ['document_id' => $document_id]);
    }

    public function updateDocument($request)
    {
        $sql = "UPDATE t_document 
                SET document_name = :document_name,
                        file_type = :file_type,
                        document_contents = :document_contents,
                        start_date = :start_date,
                        end_date = :end_date,
                        disease = :disease,
                        safety_information_flag = :safety_information_flag,
                        off_label_information_flag = :off_label_information_flag,
                        create_user_cd = :create_user_cd,
                        last_update_datetime = :last_update_datetime,
                        medical_subjects_group_cd = :medical_subjects_group_cd
                WHERE document_id = :document_id";
        $parram = [
            'document_id' => $request->document_id,
            'file_type' => $request->file_type,
            'document_name' => $request->document_name,
            'medical_subjects_group_cd' => $request->medical_subjects_group_cd,
            'document_contents' => $request->document_contents,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'disease' => $request->disease,
            'safety_information_flag' => $request->safety_information_flag,
            'off_label_information_flag' => $request->off_label_information_flag,
            'create_user_cd' => $request->user_login,
            'last_update_datetime' => $this->date
        ];
        return $this->_update($sql, $parram);
    }

    public function updateOriginalDocumentDetail($request)
    {
        $sql = "UPDATE t_original_document_detail 
                SET document_version = :document_version,
                        parent_document_id = :parent_document_id,
                        available_org_cd = :available_org_cd,
                        file_id = :file_id,
                        document_category_cd =:document_category_cd,
                        management_org_cd = :management_org_cd,
                        management_org_name = :management_org_name
                where document_id = :document_id";
        $parram = [
            'document_id' => $request->document_id,
            'document_version' => $request->document_version,
            'parent_document_id' => !empty($request->parent_document_id) ? $request->parent_document_id : $request->document_id,
            'available_org_cd' => $request->available_org_cd,
            'document_category_cd' => $request->document_category_cd,
            'management_org_cd' => $request->org_cd,
            'management_org_name' => $request->management_org_name,
            'file_id' => $request->file_id
        ];
        return $this->_update($sql, $parram);
    }

    public function updateDocumentProduct($request)
    {
        $sql = "UPDATE t_document_product 
                SET product_cd = :product_cd
                WHERE document_id = :document_id";
        $parram = [
            'document_id' => $request->document_id,
            'product_cd' => $request->product_cd
        ];
        return $this->_update($sql, $parram);
    }

    public function addDocumentProduct($request)
    {
        $sql = "INSERT INTO t_document_product(document_id,product_cd) VALUES (:document_id,:product_cd);";
        $parram = [
            'document_id' => $request->document_id,
            'product_cd' => $request->product_cd
        ];
        return $this->_create($sql, $parram);
    }

    public function insertDocument($request)
    {
        $sql = "INSERT INTO t_document(document_type
                            ,document_name
                            ,start_date
                            ,end_date
                            ,document_contents
                            ,disease
                            ,medical_subjects_group_cd
                            ,safety_information_flag
                            ,off_label_information_flag
                            ,create_user_cd
                            ,last_update_datetime
                            ,file_type
                        ) VALUES (:document_type
                            ,:document_name
                            ,:start_date
                            ,:end_date
                            ,:document_contents
                            ,:disease
                            ,:medical_subjects_group_cd
                            ,:safety_information_flag
                            ,:off_label_information_flag
                            ,:create_user_cd
                            ,:last_update_datetime
                            ,:file_type);";
        $parram = [
            'document_type' => $this->document_type,
            'document_name' => $request->document_name,
            'document_contents' => $request->document_contents,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'disease' => $request->disease,
            'medical_subjects_group_cd' => $request->medical_subjects_group_cd,
            'safety_information_flag' => $request->safety_information_flag,
            'off_label_information_flag' => $request->off_label_information_flag,
            'create_user_cd' => $request->user_login,
            'last_update_datetime' => $this->date,
            'file_type' => $request->file_type
        ];
        $this->_create($sql, $parram);
        return $this->_lastInserted('t_document', 'document_id');
    }

    public function deleteDocument($document_id)
    {
        $query['document_id'] = $document_id;
        $sql = "DELETE T1, T2, T3, T4
            FROM t_document T1
            LEFT JOIN t_document_usage_situation T2 ON T2.document_id = T1.document_id
            LEFT JOIN t_document_review T3 ON T3.usage_situation_id = T2.usage_situation_id
            LEFT JOIN t_customize_document_detail T4 ON T4.document_id = T1.document_id
            WHERE T1.document_id = :document_id";
        return $this->_destroy($sql, $query);
    }

    public function deleteDocumentDetail($document_id)
    {
        $query['document_id'] = $document_id;
        $sql = "DELETE FROM t_original_document_detail WHERE document_id = :document_id";
        return $this->_destroy($sql, $query);
    }

    public function deleteDocumentProduct($document_id)
    {
        $query['document_id'] = $document_id;
        $sql = "DELETE FROM t_document_product WHERE document_id = :document_id";
        return $this->_destroy($sql, $query);
    }

    public function getScoreDocumentOriginalReviewComment($document_id, $comprehensive_status)
    {
        $where = "";
        if (!$comprehensive_status) {
            $where .= " AND T1.document_id = :document_id";
        } else {
            $where .= " AND T3.document_id = :document_id";
        }
        $query['document_id'] = $document_id;
        $sql = "SELECT ROUND(AVG(A.document_review),1) AS avg_review, COUNT(A.document_review) AS sum_review
                FROM (
                    SELECT T4.document_type,
                        T4.document_id,
                        T4.document_name,
                        T11.document_version, 
                        T7.document_review, 
                        T7.review_datetime, 
                        T7.review_org_label,
                        T7.review_user_name, 
                        T7.review_comment
                    FROM t_original_document_detail T1
                        JOIN t_document T2 on T1.parent_document_id = T2.document_id
                        JOIN t_original_document_detail T3 on T3.parent_document_id = T2.document_id
                        JOIN s_original_document_usage_situation_map T12 on T12.original_document_id = T3.document_id
                        JOIN t_document_usage_situation T6 ON T6.usage_situation_id = T12.usage_situation_id
                        JOIN t_document_review T7 ON T7.usage_situation_id = T12.usage_situation_id
                        JOIN t_document T4 on T4.document_id = T6.document_id
                        LEFT JOIN t_original_document_detail T11 on T11.document_id = T4.document_id
                    WHERE 1 = 1 " . $where . " GROUP BY T7.usage_situation_id) A;";
        return $this->_first($sql, $query);
    }

    public function getDistributionGraphDocumentOriginalReviewComment($document_id, $comprehensive_status)
    {
        $where = "";
        if (!$comprehensive_status) {
            $where .= " AND T1.document_id = :document_id";
        } else {
            $where .= " AND T3.document_id = :document_id";
        }
        $query['document_id'] = $document_id;
        $sql = "SELECT A.document_review,COUNT(A.document_review) as count_review, SUM(A.document_review) AS sum_review
                FROM (
                    SELECT T4.document_type,
                        T4.document_id,
                        T4.document_name,
                        T11.document_version, 
                        T7.document_review, 
                        T7.review_datetime, 
                        T7.review_org_label,
                        T7.review_user_name, 
                        T7.review_comment
                    FROM t_original_document_detail T1
                    JOIN t_document T2 on T1.parent_document_id = T2.document_id
                        JOIN t_original_document_detail T3 on T3.parent_document_id = T2.document_id
                        JOIN s_original_document_usage_situation_map T12 on T12.original_document_id = T3.document_id
                        JOIN t_document_usage_situation T6 ON T6.usage_situation_id = T12.usage_situation_id
                        JOIN t_document_review T7 ON T7.usage_situation_id = T12.usage_situation_id
                        JOIN t_document T4 on T4.document_id = T6.document_id
                        LEFT JOIN t_original_document_detail T11 on T11.document_id = T4.document_id
                    WHERE 1 = 1 " . $where . " GROUP BY T7.usage_situation_id) A GROUP BY A.document_review;";
        return $this->_find($sql, $query);
    }

    public function getListReviewCommnetDocumentOriginalReviewComment($request)
    {
        $where = "";
        if (!$request->comprehensive_status) {
            $where .= " AND T1.document_id = :document_id";
        } else {
            $where .= " AND T3.document_id = :document_id";
        }
        if ($request->document_review) {
            $where .= " AND T7.document_review = :document_review";
            $query['document_review'] = $request->document_review;
        }
        $query['document_id'] = $request->document_id;
        $query['definition_name'] = $this->document_classification;
        $sql = "SELECT T4.document_type,
                        T4.document_id,
                        T4.document_name,
                        T4.file_type,
                        T11.document_version, 
                        T7.document_review, 
                        T7.review_datetime, 
                        T7.review_org_label,
                        T7.review_user_name, 
                        T7.review_comment, 
                        T9.file_url,
                        T10.definition_label as document_label 
                FROM t_original_document_detail T1
                    JOIN t_document T2 on T1.parent_document_id = T2.document_id
                    JOIN t_original_document_detail T3 on T3.parent_document_id = T2.document_id
                    JOIN s_original_document_usage_situation_map T12 on T12.original_document_id = T3.document_id
                    JOIN t_document_usage_situation T6 ON T6.usage_situation_id = T12.usage_situation_id
                    JOIN t_document_review T7 ON T7.usage_situation_id = T12.usage_situation_id
                    JOIN t_document T4 on T4.document_id = T6.document_id
                    LEFT JOIN t_original_document_detail T11 on T11.document_id = T4.document_id
                    LEFT JOIN t_user_profile T8 on T7.review_user_cd = T8.user_cd
                    LEFT JOIN t_file T9 on T8.profile_image_file_id = T9.file_id
                    LEFT JOIN (SELECT * FROM m_variable_definition WHERE definition_name = :definition_name) T10 on T4.document_type = T10.definition_value
                WHERE 1 = 1 " . $where . " GROUP BY T7.usage_situation_id";
        return $this->_paginate($sql, $query, [
            "limit" => $request->limit,
            "offset" => $request->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getScoreDocumentCustomReviewComment($document_id)
    {
        $query['document_id'] = $document_id;
        $query['source_document_id'] = $document_id;
        $query['definition_name'] = $this->document_classification;
        $query['source_definition_name'] = $this->document_classification;
        $sql = "SELECT ROUND(AVG(A.document_review),1) AS avg_review, COUNT(A.document_review) AS sum_review,count(A.usage_situation_id) as count_situation
                FROM (SELECT T1.document_id,
                        T3.definition_label as document_label,
                        T4.document_version,
                        T5.document_review,
                        T5.review_datetime,
                        T5.review_comment,
                        T5.review_org_label,
                        T5.review_user_name,
                        T6.usage_situation_id,
						T1.source_document_id
                FROM t_customize_document_detail T1 
                    JOIN t_document T2 on T2.document_id = T1.document_id
                    JOIN m_variable_definition T3 on T2.document_type = T3.definition_value
                    LEFT JOIN t_original_document_detail T4 on T2.document_id = T4.document_id
                    LEFT JOIN t_document_usage_situation T6 on T2.document_id = T6.document_id
		            LEFT JOIN t_document_review T5 on T6.usage_situation_id = T5.usage_situation_id
                WHERE T1.document_id = :document_id AND T3.definition_name = :definition_name
                UNION
                SELECT T1.document_id,
                        T3.definition_label as document_label,
                        T4.document_version,
                        T5.document_review,
                        T5.review_datetime,
                        T5.review_comment,
                        T5.review_org_label,
                        T5.review_user_name,
                        T6.usage_situation_id,
						T1.source_document_id
                FROM t_customize_document_detail T1 
                    JOIN t_document T2 on T2.document_id = T1.document_id
                    JOIN m_variable_definition T3 on T2.document_type = T3.definition_value
                    LEFT JOIN t_original_document_detail T4 on T2.document_id = T4.document_id
                    LEFT JOIN t_document_usage_situation T6 on T2.document_id = T6.document_id
		            LEFT JOIN t_document_review T5 on T6.usage_situation_id = T5.usage_situation_id
                WHERE T1.source_document_id = :source_document_id AND T3.definition_name = :source_definition_name) A GROUP BY A.source_document_id";
        return $this->_first($sql, $query);
    }

    public function getDistributionGraphDocumentCustomReviewComment($document_id)
    {
        $query['document_id'] = $document_id;
        $query['source_document_id'] = $document_id;
        $query['definition_name'] = $this->document_classification;
        $query['source_definition_name'] = $this->document_classification;
        $sql = "SELECT A.document_review,Count(A.document_review) as count_review, SUM(A.document_review) AS sum_review
                FROM (SELECT T1.document_id,
                        T3.definition_label as document_label,
                        T4.document_version,
                        T5.document_review,
                        T5.review_datetime,
                        T5.review_comment,
                        T5.review_org_label,
                        T5.review_user_name
                FROM t_customize_document_detail T1 
                    JOIN t_document T2 on T2.document_id = T1.document_id
                    JOIN m_variable_definition T3 on T2.document_type = T3.definition_value
                    LEFT JOIN t_original_document_detail T4 on T2.document_id = T4.document_id
                    JOIN t_document_review T5 on T2.document_id = T5.document_id
                WHERE T1.document_id = :document_id AND T3.definition_name = :definition_name
                UNION
                SELECT T1.document_id,
                        T3.definition_label as document_label,
                        T4.document_version,
                        T5.document_review,
                        T5.review_datetime,
                        T5.review_comment,
                        T5.review_org_label,
                        T5.review_user_name
                FROM t_customize_document_detail T1 
                    JOIN t_document T2 on T2.document_id = T1.document_id
                    JOIN m_variable_definition T3 on T2.document_type = T3.definition_value
                    LEFT JOIN t_original_document_detail T4 on T2.document_id = T4.document_id
                    JOIN t_document_review T5 on T2.document_id = T5.document_id
                WHERE T1.source_document_id = :source_document_id AND T3.definition_name = :source_definition_name) A
                GROUP BY A.document_review";
        return $this->_find($sql, $query);
    }

    public function getListReviewCommnetDocumentCustomReviewComment($request)
    {
        $query['document_id'] = $request->document_id;
        $query['source_document_id'] = $request->document_id;
        $query['definition_name'] = $this->document_classification;
        $query['source_definition_name'] = $this->document_classification;
        $where = "";
        if ($request->document_review) {
            $where .= " AND A.document_review = :document_review";
            $query['document_review'] = $request->document_review;
        }
        $sql = "SELECT A.* FROM(
                    SELECT T1.document_id,
                        T3.definition_label as document_label,
                        T4.document_version,
                        T2.document_name,
                        T2.file_type,
                        T2.document_type,
                        T5.document_review,
                        T5.review_datetime,
                        T5.review_comment,
                        T5.review_org_label,
                        T5.review_user_name,
                        T7.file_url
                FROM t_customize_document_detail T1 
                    JOIN t_document T2 on T2.document_id = T1.document_id
                    JOIN m_variable_definition T3 on T2.document_type = T3.definition_value
                    LEFT JOIN t_original_document_detail T4 on T2.document_id = T4.document_id
                    JOIN t_document_review T5 on T2.document_id = T5.document_id
                    LEFT JOIN t_user_profile T6 on T5.review_user_cd = T6.user_cd
                    LEFT JOIN t_file T7 on T6.profile_image_file_id = T7.file_id
                WHERE T1.document_id = :document_id AND T3.definition_name = :definition_name
                UNION
                SELECT T1.document_id,
                        T3.definition_label as document_label,
                        T4.document_version,
                        T2.document_name,
                        T2.file_type,
                        T2.document_type,
                        T5.document_review,
                        T5.review_datetime,
                        T5.review_comment,
                        T5.review_org_label,
                        T5.review_user_name,
                        T7.file_url
                FROM t_customize_document_detail T1 
                    JOIN t_document T2 on T2.document_id = T1.document_id
                    JOIN m_variable_definition T3 on T2.document_type = T3.definition_value
                    LEFT JOIN t_original_document_detail T4 on T2.document_id = T4.document_id
                    LEFT JOIN t_document_review T5 on T2.document_id = T5.document_id
                    LEFT JOIN t_user_profile T6 on T5.review_user_cd = T6.user_cd
                    LEFT JOIN t_file T7 on T6.profile_image_file_id = T7.file_id
                WHERE T1.source_document_id = :source_document_id AND T3.definition_name = :source_definition_name
            ) A WHERE A.document_review IS NOT NULL " . $where;
        return $this->_paginate($sql, $query, [
            "limit" => $request->limit,
            "offset" => $request->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function addOriginalDocumentDetail($request)
    {
        $sql = "INSERT INTO t_original_document_detail(document_id
                            ,document_version
                            ,document_category_cd
                            ,file_id
                            ,management_org_cd
                            ,management_org_name
                            ,available_org_cd
                            ,parent_document_id
                        ) VALUES (:document_id
                            ,:document_version
                            ,:document_category_cd
                            ,:file_id
                            ,:management_org_cd
                            ,:management_org_name
                            ,:available_org_cd
                            ,:parent_document_id);";
        $parram = [
            'document_id' => $request->document_id,
            'document_version' => $request->document_version,
            'document_category_cd' => $request->document_category_cd,
            'file_id' => $request->file_id,
            'management_org_cd' => $request->management_org_cd,
            'management_org_name' => $request->management_org_name,
            'available_org_cd' => $request->available_org_cd,
            'parent_document_id' => !empty($request->parent_document_id) ? $request->parent_document_id : $request->document_id
        ];
        return $this->_create($sql, $parram);
    }

    public function addDocumentFilePage($data)
    {
        $sql = "INSERT INTO t_original_document_file_page(original_document_id
                            ,original_document_page_num
                            ,mime_type
                        ) VALUES (:original_document_id
                            ,:original_document_page_num
                            ,:mime_type);";
        return $this->_create($sql, $data);
    }

    public function addDocumentComposition($data)
    {
        $sql = "INSERT INTO t_document_composition(document_id
                            ,page_num
                            ,original_document_id
                            ,original_document_page_num
                            ,cover_html
                        ) VALUES (:document_id
                            ,:page_num
                            ,:original_document_id
                            ,:original_document_page_num
                            ,:cover_html);";
        return $this->_create($sql, $data);
    }

    public function deleteDocumentFilePage($document_id)
    {
        $sql = "DELETE FROM t_original_document_file_page WHERE original_document_id = :original_document_id";
        return $this->_destroy($sql, ['original_document_id' => $document_id]);
    }

    public function deleteDocumentComposition($document_id)
    {
        $sql = "DELETE FROM t_document_composition WHERE document_id = :document_id";
        return $this->_destroy($sql, ['document_id' => $document_id]);
    }

    public function getDocumentUsageSituation($document_id)
    {
        $sql = "SELECT usage_situation_id FROM t_document_usage_situation WHERE document_id = :document_id";
        return $this->_first($sql, ['document_id' => $document_id]);
    }

    public function getDocumentComposition($document_id)
    {
        $sql = "SELECT document_id FROM t_document_composition WHERE original_document_id = :original_document_id AND document_id <> :document_id GROUP BY original_document_id;";
        return $this->_first($sql, ['document_id' => $document_id, 'original_document_id' => $document_id]);
    }

    public function checkOrgDocumentInOrgUser($available_org_cd, $user_login)
    {
        $sql_org_detail = "SELECT org_cd,org_name,org_short_name,org_label,layer_num FROM m_org where org_cd = :org_cd";
        $org_user = $this->_first($sql_org_detail, ['org_cd' => $available_org_cd]);
        if (!is_object($org_user)) {
            return [];
        }
        $sql_all_org = "SELECT T1.org_cd,T1.org_name,T1.org_short_name,T1.org_label,layer_num
                        FROM m_org T1
                            join m_user_org T2 on T1.org_cd = T2.org_cd
                        WHERE T1.layer_" . $org_user->layer_num . " = :org_cd AND T2.user_cd = :user_cd";
        return $this->_find($sql_all_org, ['org_cd' => $available_org_cd, 'user_cd' => $user_login]);
    }
}
