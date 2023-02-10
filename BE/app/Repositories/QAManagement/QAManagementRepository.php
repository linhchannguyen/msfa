<?php

namespace App\Repositories\QAManagement;

use App\Repositories\BaseRepository;
use App\Traits\StringConvertTrait;

class QAManagementRepository extends BaseRepository implements QAManagementRepositoryInterface
{
    use StringConvertTrait;
    protected $useAutoMeta = true;

    public function checkUserUnsuitableReport($user_cd)
    {
        $sql = "SELECT (CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END) AS posting_prohibited FROM t_posting_prohibited WHERE user_cd = :user_cd";
        return $this->_first($sql, ['user_cd' => $user_cd]);
    }

    public function getQuestionStatus()
    {
        $sql = "SELECT definition_value, definition_label FROM m_variable_definition WHERE definition_name = :definition_name AND definition_value != 99";
        return $this->_find($sql, ['definition_name' => QUESTION_STATUS]);
    }

    public function getQuestionCategory()
    {
        $sql = "SELECT qa_category_cd, qa_category_name FROM m_question_category WHERE delete_flag = 0";
        return $this->_all($sql);
    }

    public function insert($datas)
    {
        $sql = "INSERT INTO t_question (
            question_category_cd,
            create_user_cd,
            title,
            contents,
            last_update_datetime
        ) VALUES (
            :question_category_cd,
            :create_user_cd,
            :title,
            :contents,
            :last_update_datetime
        );";
        return $this->_create($sql, $datas);
    }

    public function lastQuestion()
    {
        return $this->_lastInserted('t_question', 'question_id');
    }

    public function insertQAInfoOrg($datas)
    {
        $sql = "INSERT INTO t_question_imformation_org (question_id, org_cd) VALUES :values;";
        return $this->_bulkCreate($sql, $datas);
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

    public function getQA($conditions)
    {
        $sql = "SELECT
        t_question.question_id,
        t_question.question_category_cd,
        m_question_category.qa_category_name,
        t_question.question_status_type,
        m_variable_definition.definition_label,
        t_question.create_user_cd,
        t_question.title,
        t_question.contents,
        (
            CASE
                WHEN ( SELECT t_answer.last_update_datetime FROM t_answer WHERE t_answer.question_id = t_question.question_id ORDER BY t_answer.last_update_datetime DESC LIMIT 1 ) < t_question.last_update_datetime
                THEN t_question.last_update_datetime
                ELSE 
                (
                    CASE
                    WHEN ( SELECT t_answer.last_update_datetime FROM t_answer WHERE t_answer.question_id = t_question.question_id ORDER BY t_answer.last_update_datetime DESC LIMIT 1 ) IS NULL
                    THEN t_question.last_update_datetime
                    ELSE ( SELECT t_answer.last_update_datetime FROM t_answer WHERE t_answer.question_id = t_question.question_id ORDER BY t_answer.last_update_datetime DESC LIMIT 1 )
                    END
                )
                END
        ) AS last_update_datetime,
        t_question.delete_flag,
        (
            SELECT COUNT( * )
            FROM t_answer
            WHERE CAST( t_answer.created_at AS DATE ) >= CAST( ( now( ) - INTERVAL (SELECT parameter_value FROM c_system_parameter WHERE parameter_key = :_hot_specified) DAY ) AS DATE ) 
                AND t_answer.question_id = t_question.question_id 
        ) AS qa_hot,
        (
            CASE WHEN CAST( t_question.created_at AS DATE ) >= CAST( ( now( ) - INTERVAL (SELECT parameter_value FROM c_system_parameter WHERE parameter_key = :_new_arrival) DAY ) AS DATE ) THEN 1 ELSE 0 END 
        ) AS new_arrival,
        ( 
            SELECT COUNT( * ) FROM t_answer WHERE t_answer.question_id = t_question.question_id 
        ) AS quantity_answer,
        ( 
            SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                'answer_note', t_answer.answer_note,
                'last_update_datetime', t_answer.last_update_datetime
            )),']')
            FROM t_answer
            WHERE t_answer.question_id = t_question.question_id
            AND t_answer.best_answer_flag = 1
        ) AS answer_note ";
        $condition['_hot_specified'] = HOT_SPECIFIED_NUMBER_OF_DAYS;
        $condition['_new_arrival'] = NEW_ARRIVAL_SPECIFIED_NUMBER_OF_DAYS;
        $sql .= " FROM t_question
            JOIN m_question_category ON m_question_category.qa_category_cd = t_question.question_category_cd
            JOIN m_variable_definition ON m_variable_definition.definition_value = t_question.question_status_type AND m_variable_definition.definition_name = :definition_name
            WHERE t_question.delete_flag = 0";
        if ($conditions->unsuitable_report == 1 && in_array(config('role.QA_MG.CODE'), $conditions->roles)) {
            $sql .= " AND 
                    (
                        (
                            SELECT COUNT( * ) 
                            FROM t_unsuitable_report 
                            WHERE t_unsuitable_report.key_id = t_question.question_id 
                                AND t_unsuitable_report.key_type = 10 
                                AND ( t_unsuitable_report.cancel_flag = 0 OR t_unsuitable_report.cancel_flag IS NULL ) 
                        ) > 0 
                        OR
                        (
                            SELECT COUNT( * ) 
                            FROM t_unsuitable_report 
                            WHERE t_unsuitable_report.key_id IN (SELECT t_answer.answer_id FROM t_answer WHERE t_answer.question_id = t_question.question_id)
                                AND t_unsuitable_report.key_type = 20 
                                AND ( t_unsuitable_report.cancel_flag = 0 OR t_unsuitable_report.cancel_flag IS NULL ) 
                        ) > 0 
                    )";
        }
        $condition['definition_name'] = QUESTION_STATUS;
        if ($conditions->question_category_cd != "") {
            $sql .= " AND t_question.question_category_cd = :question_category_cd";
            $condition['question_category_cd'] = $conditions->question_category_cd;
        }
        if (!empty($conditions->question_status_type) && $conditions->question_hot == 0 && $conditions->new_arrival == 0) {
            $sql .= " AND t_question.question_status_type IN " . $this->_buildCommaString($conditions->question_status_type, true);
        } else if (!empty($conditions->question_status_type) && $conditions->question_hot == 0 && $conditions->new_arrival == 1) {
            $sql .= " AND (t_question.question_status_type IN " . $this->_buildCommaString($conditions->question_status_type, true);
            $sql .= " OR (
                CASE WHEN CAST( t_question.created_at AS DATE ) >= CAST( ( now( ) - INTERVAL (SELECT parameter_value FROM c_system_parameter WHERE parameter_key = :new_arrival) DAY ) AS DATE ) THEN 1 ELSE 0 END 
            ) = 1)";
            $condition['new_arrival'] = NEW_ARRIVAL_SPECIFIED_NUMBER_OF_DAYS;
        } else if (!empty($conditions->question_status_type) && $conditions->question_hot == 1 && $conditions->new_arrival == 0) {
            $sql .= " AND (t_question.question_status_type IN " . $this->_buildCommaString($conditions->question_status_type, true);
            $sql .= " OR (
                SELECT COUNT( * )
                FROM t_answer
                WHERE CAST( t_answer.created_at AS DATE ) >= CAST( ( now( ) - INTERVAL (SELECT parameter_value FROM c_system_parameter WHERE parameter_key = :hot_specified) DAY ) AS DATE ) 
                    AND t_answer.question_id = t_question.question_id 
            ) >= 5)";
            $condition['hot_specified'] = HOT_SPECIFIED_NUMBER_OF_DAYS;
        } else if (!empty($conditions->question_status_type) && $conditions->question_hot == 1 && $conditions->new_arrival == 1) {
            $sql .= " AND (t_question.question_status_type IN " . $this->_buildCommaString($conditions->question_status_type, true);
            $sql .= " OR (
                SELECT COUNT( * )
                FROM t_answer
                WHERE CAST( t_answer.created_at AS DATE ) >= CAST( ( now( ) - INTERVAL (SELECT parameter_value FROM c_system_parameter WHERE parameter_key = :hot_specified) DAY ) AS DATE ) 
                    AND t_answer.question_id = t_question.question_id 
            ) >= 5";
            $sql .= " OR (
                CASE WHEN CAST( t_question.created_at AS DATE ) >= CAST( ( now( ) - INTERVAL (SELECT parameter_value FROM c_system_parameter WHERE parameter_key = :new_arrival) DAY ) AS DATE ) THEN 1 ELSE 0 END 
            ) = 1)";
            $condition['hot_specified'] = HOT_SPECIFIED_NUMBER_OF_DAYS;
            $condition['new_arrival'] = NEW_ARRIVAL_SPECIFIED_NUMBER_OF_DAYS;
        } else if (empty($conditions->question_status_type) && $conditions->question_hot == 0 && $conditions->new_arrival == 1) {
            $sql .= " AND (
                CASE WHEN CAST( t_question.created_at AS DATE ) >= CAST( ( now( ) - INTERVAL (SELECT parameter_value FROM c_system_parameter WHERE parameter_key = :new_arrival) DAY ) AS DATE ) THEN 1 ELSE 0 END 
            ) = 1";
            $condition['new_arrival'] = NEW_ARRIVAL_SPECIFIED_NUMBER_OF_DAYS;
        } else if (empty($conditions->question_status_type) && $conditions->question_hot == 1 && $conditions->new_arrival == 0) {
            $sql .= " AND (
                SELECT COUNT( * )
                FROM t_answer
                WHERE CAST( t_answer.created_at AS DATE ) >= CAST( ( now( ) - INTERVAL (SELECT parameter_value FROM c_system_parameter WHERE parameter_key = :hot_specified) DAY ) AS DATE ) 
                    AND t_answer.question_id = t_question.question_id 
            ) >= 5";
            $condition['hot_specified'] = HOT_SPECIFIED_NUMBER_OF_DAYS;
        } else if (empty($conditions->question_status_type) && $conditions->question_hot == 1 && $conditions->new_arrival == 1) {
            $sql .= " AND ((
                SELECT COUNT( * )
                FROM t_answer
                WHERE CAST( t_answer.created_at AS DATE ) >= CAST( ( now( ) - INTERVAL (SELECT parameter_value FROM c_system_parameter WHERE parameter_key = :hot_specified) DAY ) AS DATE ) 
                    AND t_answer.question_id = t_question.question_id 
            ) >= 5";
            $sql .= " OR (
                CASE WHEN CAST( t_question.created_at AS DATE ) >= CAST( ( now( ) - INTERVAL (SELECT parameter_value FROM c_system_parameter WHERE parameter_key = :new_arrival) DAY ) AS DATE ) THEN 1 ELSE 0 END 
            ) = 1)";
            $condition['hot_specified'] = HOT_SPECIFIED_NUMBER_OF_DAYS;
            $condition['new_arrival'] = NEW_ARRIVAL_SPECIFIED_NUMBER_OF_DAYS;
        }
        if ($conditions->contents != "") {
            $sql .= " AND (t_question.contents LIKE :contents OR t_question.title LIKE :title)";
            $condition['contents'] = $condition['title'] = '%' . trim($this->convert_kana($conditions->contents)) . '%';
        }
        $sql .= " GROUP BY t_question.question_id
            ORDER BY t_question.last_update_datetime DESC, t_question.question_id DESC";
        return $this->_paginate($sql, $condition, [
            "limit" => $conditions->limit,
            "offset" => $conditions->offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }
}
