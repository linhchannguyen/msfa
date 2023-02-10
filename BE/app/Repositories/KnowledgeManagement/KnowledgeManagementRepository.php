<?php

namespace App\Repositories\KnowledgeManagement;

use App\Repositories\BaseRepository;
use App\Traits\StringConvertTrait;

class KnowledgeManagementRepository extends BaseRepository implements KnowledgeManagementRepositoryInterface
{
    use StringConvertTrait;
    protected $useAutoMeta = true;

    public function getKnowledgeStatus()
    {
        $sql = "SELECT definition_value, definition_label FROM m_variable_definition WHERE definition_name = :definition_name AND definition_value != :knowledge_status;";
        return $this->_find($sql, [
            'definition_name' => KNOWLEDGE_STATUS,
            'knowledge_status' => KNOWLEDGE_STATUS_TYPE_NOW_OPEN
        ]);
    }

    public function getPointTargetType()
    {
        $sql = "SELECT definition_value, definition_label
        FROM m_variable_definition 
        WHERE definition_name = :definition_name
        AND definition_label = :definition_label;";
        return $this->_first($sql, [
            'definition_name' => UTILIZATION_POINT_TARTGET_CLASSIFICATION_TEXT,
            'definition_label' => KNOWLEDGE
        ]);
    }

    public function getActivePointNice()
    {
        $sql = "SELECT active_point FROM m_fixed_active_point WHERE fixed_active_point_cd = :fixed_active_point_cd";
        return $this->_first($sql, [
            'fixed_active_point_cd' => KNOWLEDGE_LIKE_REGISTRATION
        ]);
    }

    public function getActivePointComment()
    {
        $sql = "SELECT active_point FROM m_fixed_active_point WHERE fixed_active_point_cd = :fixed_active_point_cd";
        return $this->_first($sql, [
            'fixed_active_point_cd' => KNOWLEDGE_LIKE_COMMENT_REGISTRATION
        ]);
    }

    public function getKnowledgeCategory()
    {
        $sql = "SELECT knowledge_category_name, knowledge_category_cd FROM m_knowledge_category WHERE delete_flag = 0;";
        return $this->_all($sql);
    }

    public function getFacilityTypeGroup()
    {
        $sql = "SELECT facility_type_group_cd, facility_type_group_name FROM m_facility_type_group;";
        return $this->_all($sql);
    }

    public function getMedicalSubjectsGroup()
    {
        $sql = "SELECT medical_subjects_group_cd, medical_subjects_group_name FROM m_medical_subjects_group;";
        return $this->_all($sql);
    }

    public function getTopLike()
    {
        $sql = "SELECT
            t_knowledge.create_user_cd,
            m_org.org_cd,
            m_org.org_label,
            m_user.user_name,
            ( SELECT COUNT(*) FROM t_knowledge_nice 
                WHERE t_knowledge_nice.knowledge_id = t_knowledge.knowledge_id
            ) as knowledge_like
            FROM t_knowledge
                LEFT JOIN m_user_org ON m_user_org.user_cd = t_knowledge.create_user_cd AND m_user_org.main_flag = 1
                LEFT JOIN m_org ON m_org.org_cd = m_user_org.org_cd
                JOIN m_user ON m_user.user_cd = m_user_org.user_cd
            GROUP BY t_knowledge.create_user_cd
            ORDER BY knowledge_like DESC LIMIT 5";
        return $this->_find($sql, []);
    }

    public function search($conditions)
    {
        $condition = [];
        $sql = "SELECT
            t_knowledge.knowledge_id,
            t_knowledge.anonymous_flag,
            t_knowledge.knowledge_category_cd,
            m_knowledge_category.knowledge_category_name,
            t_knowledge.create_datetime,
            (SELECT parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_knowledge_1 AND parameter_key = :create_datetime_new) AS create_datetime_new,
            t_knowledge.last_update_datetime,
            (SELECT parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_knowledge_2 AND parameter_key = :last_updated_datetime) AS last_update_datetime_updated,
            (
                CASE
                    WHEN CAST( t_knowledge.create_datetime AS DATE ) >= CAST( ( now( ) - INTERVAL ( SELECT parameter_value FROM c_system_parameter WHERE parameter_key = '新着期間日数' AND parameter_name = 'ナレッジ' ) DAY ) AS DATE ) THEN
                    1 ELSE 0 
                END 
            ) AS new_label,
                (
                CASE
                    WHEN t_knowledge.last_update_datetime != t_knowledge.first_release_datetime AND CAST( t_knowledge.last_update_datetime AS DATE ) >= CAST( ( now( ) - INTERVAL ( SELECT parameter_value FROM c_system_parameter WHERE parameter_key = '更新有り期間日数' AND parameter_name = 'ナレッジ' ) DAY ) AS DATE ) THEN
                    1 ELSE 0 
                END 
            ) AS update_label,
            t_knowledge.title,
            t_knowledge.contents,
            t_knowledge.product_name,
            t_knowledge.medical_subjects_group_name,
            t_knowledge.facility_cd,
            t_knowledge.facility_type_group_name,
            t_knowledge.facility_short_name,
            t_knowledge.person_cd,
            t_knowledge.person_name,
            t_knowledge.department_name,
            t_knowledge.display_position_name,
            m_prefecture.prefecture_name,
            m_municipality.municipality_name,
            (
                SELECT
                    CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 'org_label', m_org.org_label, 'user_cd', m_user.user_cd, 'user_name', m_user.user_name )), ']' ) 
                FROM m_user
                    LEFT JOIN m_user_org ON m_user_org.user_cd = m_user.user_cd AND m_user_org.main_flag = 1
                    LEFT JOIN m_org ON m_org.org_cd = m_user_org.org_cd
                WHERE m_user_org.user_cd = t_knowledge.create_user_cd 
            ) AS create_user_cd,
            (
                SELECT
                    CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 'org_label', m_org.org_label, 'user_cd', m_user.user_cd, 'user_name', m_user.user_name )), ']' ) 
                FROM t_knowledge_collaborator
                    JOIN m_user ON m_user.user_cd = t_knowledge_collaborator.user_cd
                    LEFT JOIN m_user_org ON m_user_org.user_cd = t_knowledge_collaborator.user_cd AND m_user_org.main_flag = 1
                    LEFT JOIN m_org ON m_org.org_cd = m_user_org.org_cd
                WHERE t_knowledge_collaborator.knowledge_id = t_knowledge.knowledge_id
            ) AS create_user_cd_together,
            ( SELECT COUNT(*) FROM t_knowledge_nice WHERE t_knowledge_nice.knowledge_id = t_knowledge.knowledge_id ) AS knowledge_like,
            ( 
                SELECT 
                    CONCAT( '[',GROUP_CONCAT(JSON_OBJECT('approval_user_cd', h_approval_user.approval_user_cd )), ']' ) 
                FROM h_approval_user 
                WHERE h_approval_user.request_user_cd = t_knowledge.create_user_cd
                    AND CAST(h_approval_user.start_date AS DATE)  <= :date_system AND :date_system_temp <= CAST(h_approval_user.end_date AS DATE)
                    AND h_approval_user.approval_work_type = :approval_work_type
                    AND h_approval_user.approval_layer_num = :approval_layer_num
            )  as approval_user_cd
        FROM
            t_knowledge
            JOIN m_knowledge_category ON m_knowledge_category.knowledge_category_cd = t_knowledge.knowledge_category_cd
            JOIN m_facility ON m_facility.facility_cd = t_knowledge.facility_cd
            JOIN m_prefecture ON m_prefecture.prefecture_cd = m_facility.prefecture_cd
            JOIN m_municipality ON m_municipality.municipality_cd = m_facility.municipality_cd AND m_municipality.prefecture_cd = m_facility.prefecture_cd
            LEFT JOIN t_knowledge_collaborator ON t_knowledge_collaborator.knowledge_id = t_knowledge.knowledge_id";
        $sql .= " WHERE t_knowledge.release_flag = 1 AND t_knowledge.knowledge_status_type = :knowledge_status_type";
        $condition['date_system'] = $conditions->current;
        $condition['date_system_temp'] = $conditions->current;
        $condition['knowledge_status_type'] = KNOWLEDGE_STATUS_TYPE_NOW_OPEN;
        $condition['approval_layer_num'] = $conditions->approval_layer_num;
        $condition['approval_work_type'] = $conditions->approval_work_type;
        $condition['parameter_knowledge_1'] = KNOWLEDGE;
        $condition['parameter_knowledge_2'] = KNOWLEDGE;
        $condition['create_datetime_new'] = NEW_ARRIAL_PERIOD_DAYS;
        $condition['last_updated_datetime'] = NUMBER_OF_DAYS_WITH_RENEWAL;
        if ($conditions->title != "") {
            $sql .= " AND t_knowledge.title LIKE :title";
            $condition['title'] = "%".trim($this->convert_kana($conditions->title))."%";
        }
        if ($conditions->knowledge_category_cd != "") {
            $sql .= " AND t_knowledge.knowledge_category_cd = :knowledge_category_cd";
            $condition['knowledge_category_cd'] = $conditions->knowledge_category_cd;
        }
        if (!empty(trim($conditions->knowledge_id))) {
            $knowledge_ids = explode(",", trim($conditions->knowledge_id));
            $sql .= " AND t_knowledge.knowledge_id IN " . $this->_buildCommaString($knowledge_ids, true);
        }
        if (!empty($conditions->user_cd)) {
            $sql .= " AND (t_knowledge_collaborator.user_cd IN " . $this->_buildCommaString($conditions->user_cd, true) . " OR t_knowledge.create_user_cd IN " . $this->_buildCommaString($conditions->user_cd, true) . ")";
        }
        if ($conditions->release_datetime_from != "") {
            $sql .= " AND CAST(t_knowledge.release_datetime AS DATE) >= :release_datetime_from";
            $condition['release_datetime_from'] = $conditions->release_datetime_from;
        }
        if ($conditions->release_datetime_to != "") {
            $sql .= " AND CAST(t_knowledge.release_datetime AS DATE) <= :release_datetime_to";
            $condition['release_datetime_to'] = $conditions->release_datetime_to;
        }
        if (!empty($conditions->product_cd)) {
            $sql .= " AND t_knowledge.product_cd IN " . $this->_buildCommaString($conditions->product_cd, true);
        }
        if ($conditions->facility_cd != "") {
            $sql .= " AND t_knowledge.facility_cd = :facility_cd";
            $condition['facility_cd'] = $conditions->facility_cd;
        }
        if ($conditions->person_cd != "") {
            $sql .= " AND t_knowledge.person_cd = :person_cd";
            $condition['person_cd'] = $conditions->person_cd;
        }
        if ($conditions->facility_type_group_cd != "") {
            $sql .= " AND t_knowledge.facility_type_group_cd = :facility_type_group_cd";
            $condition['facility_type_group_cd'] = $conditions->facility_type_group_cd;
        }
        if ($conditions->medical_subjects_group_cd != "") {
            $sql .= " AND t_knowledge.medical_subjects_group_cd = :medical_subjects_group_cd";
            $condition['medical_subjects_group_cd'] = $conditions->medical_subjects_group_cd;
        }
        if ($conditions->prefecture_cd != "") {
            $sql .= " AND m_facility.prefecture_cd = :prefecture_cd";
            $condition['prefecture_cd'] = $conditions->prefecture_cd;
        }
        if ($conditions->municipality_cd != "") {
            $sql .= " AND m_facility.municipality_cd = :municipality_cd";
            $condition['municipality_cd'] = $conditions->municipality_cd;
        }
        if ($conditions->person_cd != "" || $conditions->facility_cd != "" || !empty($conditions->user_cd)) {
            $sql .= " AND t_knowledge.anonymous_flag = 0";
        }
        $sql .= " GROUP BY t_knowledge.knowledge_id";
        if ($conditions->sort_by == 1) {
            $sql .= " ORDER BY t_knowledge.release_datetime DESC";
        } else {
            $sql .= " ORDER BY knowledge_like DESC";
        }
        return $this->_paginate($sql, $condition, [
            "limit" => $conditions->limit,
            "offset" => $conditions->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getRequestUser($conditions)
    {
        $sql = "SELECT 
            h_approval_user.request_user_cd
        FROM h_approval_user 
        WHERE h_approval_user.approval_user_cd = :approval_user_cd
            AND CAST(h_approval_user.start_date AS DATE)  <= :date_system AND :date_system_temp <= CAST(h_approval_user.end_date AS DATE)
            AND h_approval_user.approval_work_type = :approval_work_type";
        $condition['approval_user_cd'] = $conditions->create_user_cd;
        $condition['date_system'] = $conditions->current;
        $condition['date_system_temp'] = $conditions->current;
        $condition['approval_work_type'] = $conditions->approval_work_type;
        return $this->_find($sql, $condition);
    }

    public function knowledgeDetail($conditions)
    {
        $sql = "SELECT
        t_knowledge.knowledge_id,
        t_knowledge.title,
        m_knowledge_category.knowledge_category_cd,
        m_knowledge_category.knowledge_category_name,
        t_knowledge.release_datetime,
        t_knowledge.anonymous_flag,
        t_knowledge.product_name,
        t_knowledge.facility_cd,
        t_knowledge.facility_short_name,
        t_knowledge.department_name,
        t_knowledge.person_cd,
        t_knowledge.person_name,
        t_knowledge.display_position_name,
        t_knowledge.facility_type_group_name,
        t_knowledge.medical_subjects_group_name,
        m_prefecture.prefecture_name,
        m_municipality.municipality_name,
        t_knowledge.contents,
        (
            SELECT
                CONCAT( '[', GROUP_CONCAT( JSON_OBJECT(
                    'org_label', m_org.org_label,
                    'user_cd', m_user.user_cd,
                    'user_name', m_user.user_name,
                    'avatar_image_data', t_file.file_url,
                    'avatar_image_type', t_file.mime_type
                )), ']' ) 
            FROM m_user
                LEFT JOIN m_user_org ON m_user_org.user_cd = m_user.user_cd AND m_user_org.main_flag = 1
                LEFT JOIN m_org ON m_org.org_cd = m_user_org.org_cd
                LEFT JOIN t_user_profile
                    ON m_user.user_cd = t_user_profile.user_cd
                LEFT JOIN t_file
                    ON t_file.file_id = t_user_profile.profile_image_file_id
            WHERE m_user.user_cd = t_knowledge.create_user_cd 
        ) AS create_user_cd,
        (
            SELECT
                CONCAT( '[', GROUP_CONCAT( JSON_OBJECT(
                    'org_label', m_org.org_label,
                    'user_cd', m_user.user_cd,
                    'user_name', m_user.user_name,
                    'avatar_image_data', t_file.file_url,
                    'avatar_image_type', t_file.mime_type
                )), ']' ) 
            FROM t_knowledge_collaborator
            JOIN m_user ON m_user.user_cd = t_knowledge_collaborator.user_cd
            LEFT JOIN m_user_org ON m_user_org.user_cd = t_knowledge_collaborator.user_cd AND m_user_org.main_flag = 1
            LEFT JOIN m_org ON m_org.org_cd = m_user_org.org_cd
            LEFT JOIN t_user_profile
                ON m_user.user_cd = t_user_profile.user_cd
            LEFT JOIN t_file
                ON t_file.file_id = t_user_profile.profile_image_file_id
            WHERE t_knowledge_collaborator.knowledge_id = t_knowledge.knowledge_id
        ) AS create_user_cd_together,
        ( 
            SELECT 
                CONCAT( '[',GROUP_CONCAT(JSON_OBJECT('approval_user_cd', h_approval_user.approval_user_cd )), ']' ) 
            FROM h_approval_user
            WHERE h_approval_user.request_user_cd = t_knowledge.create_user_cd
                AND CAST(h_approval_user.start_date AS DATE)  <= :date_system AND :date_system_temp <= CAST(h_approval_user.end_date AS DATE)
                AND h_approval_user.approval_work_type = :approval_work_type
                AND h_approval_user.approval_layer_num = :approval_layer_num
        )  as approval_user_cd,
        (
            CASE WHEN (
                SELECT COUNT(*) 
                FROM t_knowledge_nice
                WHERE t_knowledge_nice.nice_user_cd = :nice_user_cd
                AND t_knowledge_nice.knowledge_id = :knowledge_nice_id
            ) > 0
            THEN 1
            ELSE 0
            END
        ) AS knowledge_niked,
        (
                CASE
                    WHEN CAST( t_knowledge.create_datetime AS DATE ) >= CAST( ( now( ) - INTERVAL ( SELECT parameter_value FROM c_system_parameter WHERE parameter_key = '新着期間日数' AND parameter_name = 'ナレッジ' ) DAY ) AS DATE ) THEN
                    1 ELSE 0 
                END 
            ) AS new_label,
                (
                CASE
                    WHEN t_knowledge.last_update_datetime != t_knowledge.first_release_datetime AND CAST( t_knowledge.last_update_datetime AS DATE ) >= CAST( ( now( ) - INTERVAL ( SELECT parameter_value FROM c_system_parameter WHERE parameter_key = '更新有り期間日数' AND parameter_name = 'ナレッジ' ) DAY ) AS DATE ) THEN
                    1 ELSE 0 
                END 
            ) AS update_label
        FROM t_knowledge
        JOIN m_knowledge_category ON m_knowledge_category.knowledge_category_cd = t_knowledge.knowledge_category_cd
        JOIN m_facility ON m_facility.facility_cd = t_knowledge.facility_cd
        JOIN m_prefecture ON m_prefecture.prefecture_cd = m_facility.prefecture_cd
        JOIN m_municipality ON m_municipality.municipality_cd = m_facility.municipality_cd AND m_municipality.prefecture_cd = m_facility.prefecture_cd
        LEFT JOIN t_knowledge_collaborator ON t_knowledge_collaborator.knowledge_id = t_knowledge.knowledge_id
        WHERE t_knowledge.knowledge_id = :knowledge_id";
        return $this->_first($sql, [
            'knowledge_id' => $conditions->knowledge_id,
            'knowledge_nice_id' => $conditions->knowledge_id,
            'date_system' => $conditions->current,
            'date_system_temp' => $conditions->current,
            'approval_layer_num' => $conditions->approval_layer_num,
            'approval_work_type' => $conditions->approval_work_type,
            'nice_user_cd' => $conditions->user_login
        ]);
    }

    public function getKnowledgeTimeline($knowledge_id)
    {
        $sql = "SELECT timeline_id FROM t_knowledge_timeline WHERE knowledge_id = :knowledge_id";
        return $this->_find($sql, ['knowledge_id' => $knowledge_id]);
    }

    public function getEvaluationComment($conditions)
    {
        $sql = "SELECT
        t_knowledge_nice.nice_id,
        t_knowledge_nice.knowledge_id,
        t_knowledge_nice.comment,
        t_knowledge_nice.nice_user_cd,
        m_user.user_name,
        t_file.file_url as avatar_image_data,
        t_file.mime_type as avatar_image_type,
        m_org.org_label
        FROM t_knowledge_nice
        JOIN m_user ON m_user.user_cd = t_knowledge_nice.nice_user_cd
        LEFT JOIN m_user_org ON m_user_org.user_cd = m_user.user_cd AND m_user_org.main_flag = 1
        LEFT JOIN m_org ON m_org.org_cd = m_user_org.org_cd
        LEFT JOIN t_user_profile
            ON m_user.user_cd = t_user_profile.user_cd
        LEFT JOIN t_file
            ON t_file.file_id = t_user_profile.profile_image_file_id
        WHERE knowledge_id = :knowledge_id
        AND delete_flag = 0
        ORDER BY t_knowledge_nice.created_at DESC";
        return $this->_paginate($sql, ['knowledge_id' => $conditions->knowledge_id], [
            "limit" => $conditions->limit,
            "offset" => $conditions->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function getUserApprovalKnowledgeAdmin($conditions)
    {
        $sql = "SELECT
        h_approval_user.approval_user_cd
        FROM h_approval_user
        WHERE CAST(h_approval_user.start_date AS DATE)  <= :date_system AND :date_system_temp <= CAST(h_approval_user.end_date AS DATE)
            AND h_approval_user.approval_work_type = :approval_work_type
            AND h_approval_user.approval_layer_num = :approval_layer_num
            AND h_approval_user.request_user_cd = :user_login";
        return $this->_find($sql, $conditions);
    }

    public function getTotalNice($knowledge_id)
    {
        $sql = "SELECT COUNT(*) as nice FROM t_knowledge_nice WHERE knowledge_id = :knowledge_id";
        return $this->_first($sql, ['knowledge_id' => $knowledge_id]);
    }

    public function insertKnowledgeNice($datas)
    {
        $sql = "INSERT INTO t_knowledge_nice 
        (
            knowledge_id,
            nice_user_cd,
            last_update_datetime
        )
        VALUES (
            :knowledge_id,
            :nice_user_cd,
            :last_update_datetime
        );";
        return $this->_create($sql, $datas);
    }

    public function updateKnowledgeNice($datas)
    {
        $sql = "UPDATE t_knowledge_nice SET last_update_datetime = :last_update_datetime, comment = :comment WHERE nice_id = :nice_id;";
        return $this->_update($sql, $datas);
    }

    public function deleteKnowledgeNice($nice_id)
    {
        $sql = "UPDATE t_knowledge_nice SET delete_flag = :delete_flag WHERE nice_id = :nice_id;";
        return $this->_update($sql, ['nice_id' => $nice_id, 'delete_flag' => 1]);
    }

    public function getKnowledgeNice($knowledge_id, $nice_user_cd)
    {
        $sql = "SELECT
        nice_id,
        knowledge_id,
        comment,
        nice_user_cd,
        last_update_datetime,
        delete_flag
        FROM t_knowledge_nice
        WHERE knowledge_id = :knowledge_id
        AND nice_user_cd = :nice_user_cd";
        return $this->_first($sql, [
            'knowledge_id' => $knowledge_id,
            'nice_user_cd' => $nice_user_cd
        ]);
    }

    public function getKnowledge($conditions)
    {
        $sql = "SELECT
        t_knowledge.knowledge_id,
        t_knowledge.title,
        ( 
            SELECT 
                CONCAT( '[',GROUP_CONCAT(JSON_OBJECT(
                    'user_cd', m_user_role.user_cd,
                    'user_name', m_user.user_name,
                    'mail_address', m_user.mail_address
                )), ']' ) 
            FROM m_user
            JOIN m_user_role ON m_user_role.user_cd = m_user.user_cd
            WHERE role = :knowledge_admin
        )  as knowledge_admin,
        ( 
            SELECT 
                CONCAT( '[',GROUP_CONCAT(JSON_OBJECT(
                    'approval_user_cd', h_approval_user.approval_user_cd,
                    'user_name', m_user.user_name,
                    'mail_address', m_user.mail_address
                )), ']' ) 
            FROM h_approval_user
            JOIN m_user ON m_user.user_cd = h_approval_user.approval_user_cd
            WHERE h_approval_user.request_user_cd = t_knowledge.create_user_cd
                AND CAST(h_approval_user.start_date AS DATE)  <= :date_system AND :date_system_temp <= CAST(h_approval_user.end_date AS DATE)
                AND h_approval_user.approval_work_type = :approval_work_type
                AND h_approval_user.approval_layer_num = :approval_layer_num
        )  as approval_user_cd
        FROM t_knowledge
        WHERE t_knowledge.knowledge_id = :knowledge_id";
        return $this->_find($sql, $conditions);
    }

    public function getKnowledgeNiceById($nice_id)
    {
        $sql = "SELECT
        t_knowledge_nice.nice_id,
        t_knowledge_nice.knowledge_id,
        t_knowledge.title,
        t_knowledge_nice.comment,
        t_knowledge_nice.nice_user_cd,
        t_knowledge_nice.last_update_datetime,
        t_knowledge_nice.delete_flag
        FROM t_knowledge_nice
        JOIN t_knowledge ON t_knowledge.knowledge_id = t_knowledge_nice.knowledge_id
        WHERE nice_id = :nice_id AND t_knowledge_nice.delete_flag = 0";
        return $this->_first($sql, [
            'nice_id' => $nice_id
        ]);
    }

    public function insertActivePoint($datas)
    {
        $sql = "INSERT INTO t_active_point (
            point_target_type,
            point_target_id,
            active_point,
            message,
            receive_user_cd
        ) VALUES (
            :point_target_type,
            :point_target_id,
            :active_point,
            :message,
            :receive_user_cd
        );";
        return $this->_create($sql, $datas);
    }

    public function getActivePoint($point_target_id, $point_target_type, $message)
    {
        $sql = "SELECT point_id FROM t_active_point
        WHERE point_target_id = :point_target_id
        AND point_target_type = :point_target_type
        AND message = :message";
        return $this->_first($sql, [
            'point_target_id' => $point_target_id,
            'point_target_type' => $point_target_type,
            'message' => $message,
        ]);
    }

    public function lastKnowledgeNice()
    {
        return $this->_lastInserted('t_knowledge_nice', 'nice_id');
    }

    public function insertKnowledgeRequest($datas)
    {
        $sql = "INSERT INTO t_knowledge_request (
            knowledge_id,
            demand_note,
            create_datetime
        ) VALUES (
            :knowledge_id,
            :demand_note,
            :create_datetime
        );";
        return $this->_create($sql, $datas);
    }

    public function searchPre($conditions)
    {
        $condition = [];
        $sql = "SELECT
            t_knowledge.anonymous_flag,
            t_knowledge.release_datetime,
            t_knowledge.knowledge_id,
            t_knowledge.knowledge_status_type,
            m_variable_definition.definition_label,
            t_knowledge.knowledge_category_cd,
            m_knowledge_category.knowledge_category_name,
            t_knowledge.title,
            t_knowledge.contents,
            t_knowledge.product_name,
            t_knowledge.medical_subjects_group_name,
            t_knowledge.facility_cd,
            t_knowledge.facility_type_group_name,
            t_knowledge.facility_short_name,
            t_knowledge.person_cd,
            t_knowledge.person_name,
            t_knowledge.department_name,
            t_knowledge.display_position_name,
            m_prefecture.prefecture_name,
            m_municipality.municipality_name,
            (
                SELECT
                    CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 'org_label', m_org.org_label, 'user_cd', m_user.user_cd, 'user_name', m_user.user_name )), ']' ) 
                FROM m_user
                    LEFT JOIN m_user_org ON m_user_org.user_cd = m_user.user_cd AND m_user_org.main_flag = 1
                    LEFT JOIN m_org ON m_org.org_cd = m_user_org.org_cd
                WHERE m_user.user_cd = t_knowledge.create_user_cd 
            ) AS create_user_cd,
            (
            SELECT
                CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 'org_label', m_org.org_label, 'user_cd', m_user.user_cd, 'user_name', m_user.user_name )), ']' ) 
            FROM t_knowledge_collaborator
                JOIN m_user ON m_user.user_cd = t_knowledge_collaborator.user_cd
                LEFT JOIN m_user_org ON m_user_org.user_cd = m_user.user_cd AND m_user_org.main_flag = 1
                LEFT JOIN m_org ON m_org.org_cd = m_user_org.org_cd
            WHERE t_knowledge_collaborator.knowledge_id = t_knowledge.knowledge_id
            ) AS create_user_cd_together,
            ( 
                SELECT 
                    CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 'org_label', m_org.org_label, 'user_cd', m_user.user_cd, 'user_name', m_user.user_name )), ']' ) 
                FROM h_approval_user
                    JOIN m_user ON m_user.user_cd = h_approval_user.approval_user_cd
                    LEFT JOIN m_user_org ON m_user_org.user_cd = m_user.user_cd AND m_user_org.main_flag = 1
                    LEFT JOIN m_org ON m_org.org_cd = m_user_org.org_cd
                WHERE h_approval_user.request_user_cd = t_knowledge.create_user_cd
                    AND CAST(h_approval_user.start_date AS DATE)  <= :date_system AND :date_system_temp <= CAST(h_approval_user.end_date AS DATE)
                    AND h_approval_user.approval_work_type = :approval_work_type
                    AND h_approval_user.approval_layer_num < :approval_layer_num
                    AND t_knowledge.knowledge_status_type = :sub_knowledge_status_type
            )  as approval_user_cd
            FROM t_knowledge
            JOIN m_knowledge_category ON m_knowledge_category.knowledge_category_cd = t_knowledge.knowledge_category_cd
            JOIN m_facility ON m_facility.facility_cd = t_knowledge.facility_cd
            JOIN m_prefecture ON m_prefecture.prefecture_cd = m_facility.prefecture_cd
            JOIN m_municipality ON m_municipality.municipality_cd = m_facility.municipality_cd AND m_municipality.prefecture_cd = m_facility.prefecture_cd
            JOIN m_variable_definition ON m_variable_definition.definition_value = t_knowledge.knowledge_status_type AND m_variable_definition.definition_name = :definition_name";
        //Q&A 168
        if (!empty($conditions->request_users)) {
            $sql .= " LEFT JOIN t_knowledge_collaborator ON t_knowledge_collaborator.knowledge_id = t_knowledge.knowledge_id 
                    WHERE (t_knowledge.create_user_cd = :create_user_cd
                    OR t_knowledge_collaborator.user_cd IN " . $this->_buildCommaString($conditions->request_users, true) . "
                    OR t_knowledge.create_user_cd IN " . $this->_buildCommaString($conditions->request_users, true) . ")";
            $condition['create_user_cd'] = $conditions->create_user_cd;
        } else {
            if (!empty($conditions->user_cd)) {
                $sql .= " WHERE t_knowledge.create_user_cd IN " . $this->_buildCommaString($conditions->user_cd, true);
            }else{
                $sql .= " WHERE t_knowledge.create_user_cd = :create_user_cd";
                $condition['create_user_cd'] = $conditions->user_login;
            }
        }
        $condition['definition_name'] = KNOWLEDGE_STATUS;
        $condition['date_system'] = $conditions->current;
        $condition['date_system_temp'] = $conditions->current;
        $condition['approval_work_type'] = $conditions->approval_work_type;
        $condition['approval_layer_num'] = $conditions->approval_layer_num;
        $condition['sub_knowledge_status_type'] = KNOWLEDGE_STATUS_TYPE_WAITING_FOR_RELEASE;
        if (!empty($conditions->knowledge_status_type)) {
            $sql .= " AND t_knowledge.knowledge_status_type IN " . $this->_buildCommaString($conditions->knowledge_status_type, true);
        }else{
            $sql .= " AND t_knowledge.knowledge_status_type != :knowledge_status_type";
            $condition['knowledge_status_type'] = KNOWLEDGE_STATUS_TYPE_NOW_OPEN;
        }
        if ($conditions->title != "") {
            $sql .= " AND t_knowledge.title LIKE :title";
            $condition['title'] = "%".trim($this->convert_kana($conditions->title))."%";
        }
        if ($conditions->knowledge_category_cd != "") {
            $sql .= " AND t_knowledge.knowledge_category_cd = :knowledge_category_cd";
            $condition['knowledge_category_cd'] = $conditions->knowledge_category_cd;
        }
        if (!empty($conditions->knowledge_id)) {
            $knowledge_ids = explode(",", trim($conditions->knowledge_id));
            $sql .= " AND t_knowledge.knowledge_id IN " . $this->_buildCommaString($knowledge_ids, true);
        }
        if ($conditions->release_datetime_from != "") {
            $sql .= " AND CAST(t_knowledge.release_datetime AS DATE) >= :release_datetime_from";
            $condition['release_datetime_from'] = $conditions->release_datetime_from;
        }
        if ($conditions->release_datetime_to != "") {
            $sql .= " AND CAST(t_knowledge.release_datetime AS DATE) <= :release_datetime_to";
            $condition['release_datetime_to'] = $conditions->release_datetime_to;
        }
        if (!empty($conditions->product_cd)) {
            $sql .= " AND t_knowledge.product_cd IN " . $this->_buildCommaString($conditions->product_cd, true);
        }
        if ($conditions->facility_cd != "") {
            $sql .= " AND t_knowledge.facility_cd = :facility_cd";
            $condition['facility_cd'] = $conditions->facility_cd;
        }
        if ($conditions->person_cd != "") {
            $sql .= " AND t_knowledge.person_cd = :person_cd";
            $condition['person_cd'] = $conditions->person_cd;
        }
        if ($conditions->facility_type_group_cd != "") {
            $sql .= " AND t_knowledge.facility_type_group_cd = :facility_type_group_cd";
            $condition['facility_type_group_cd'] = $conditions->facility_type_group_cd;
        }
        if ($conditions->medical_subjects_group_cd != "") {
            $sql .= " AND t_knowledge.medical_subjects_group_cd = :medical_subjects_group_cd";
            $condition['medical_subjects_group_cd'] = $conditions->medical_subjects_group_cd;
        }
        if ($conditions->prefecture_cd != "") {
            $sql .= " AND m_facility.prefecture_cd = :prefecture_cd";
            $condition['prefecture_cd'] = $conditions->prefecture_cd;
        }
        if ($conditions->municipality_cd != "") {
            $sql .= " AND m_facility.municipality_cd = :municipality_cd";
            $condition['municipality_cd'] = $conditions->municipality_cd;
        }
        $sql .= " GROUP BY t_knowledge.knowledge_id ORDER BY t_knowledge.create_datetime DESC";
        return $this->_paginate($sql, $condition, [
            "limit" => $conditions->limit,
            "offset" => $conditions->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }
}