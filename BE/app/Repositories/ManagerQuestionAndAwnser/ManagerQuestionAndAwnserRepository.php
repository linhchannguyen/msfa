<?php

namespace App\Repositories\ManagerQuestionAndAwnser;

use App\Enums\Document;
use App\Repositories\BaseRepository;
use App\Traits\DateTimeTrait;
use App\Enums\QAEnum;
use stdClass;

class ManagerQuestionAndAwnserRepository extends BaseRepository implements ManagerQuestionAndAwnserInterface
{
    use DateTimeTrait;
    private $date;
    protected $useAutoMeta = true;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
    }
    public function infomationQuestion ($idQuestion, $userCdLogin) {
        $amountDayHotQA = $this->amountDayHotQA();
        $amountDayNewQA = $this->amountDayNewQA();
        $amountDayCompareHotQA = $this->amountDayCompareHotQA();
        $keyType = $this->getKeyTypeUnsuitableReport(QAEnum::QUESTION);
        $role = config('role.QA_MG.CODE');
        $sql = "SELECT T1.create_user_cd, M2.user_name, T1.last_update_datetime, T1.created_at, M3.qa_category_name, M1.org_label,
                T1.title, T1.contents, T1.delete_flag, T1.question_status_type, T7.file_url,
                (SELECT count(*) FROM t_answer T2 WHERE T2.question_id = T1.question_id) AS number_answer_of_question,
                (SELECT count(*) FROM t_unsuitable_report T3 WHERE T3.key_id = T1.question_id AND T3.key_type = 10) AS number_unsuitable_report,
                (SELECT definition_label FROM m_variable_definition M5 WHERE M5.definition_value = T1.question_status_type AND M5.definition_name = 'QAステータス' LIMIT 1) AS question_status_type_label,
                (SELECT GROUP_CONCAT(user_cd) FROM m_user_role WHERE role = :role) AS list_user_cd_manager,
                (SELECT GROUP_CONCAT(create_user_cd) FROM t_unsuitable_report T8 WHERE T8.key_id = T1.question_id AND T8.key_type = 10 AND T8.cancel_flag = 0) AS create_user_cd_unsuitable_report,
                (CASE WHEN
                    (SELECT COUNT(*) FROM t_unsuitable_report T9 WHERE T9.key_id = T1.question_id AND T9.key_type = 10 AND T9.cancel_flag = 1) > 0 THEN 1 ELSE 0 END
                ) AS cancel_flag,
                (CASE WHEN
                    (
                        SELECT COUNT(*) FROM t_answer T4
                        WHERE CAST( T4.created_at AS DATE ) >= CAST( ( now( ) - INTERVAL :amount_day_hot_qa DAY ) AS DATE )
                        AND T4.question_id = T1.question_id
                    ) >= :amount_day_compare_hot THEN 1 ELSE 0 END
                ) AS status_hot,
                (CASE WHEN
                    CAST( T1.created_at AS DATE ) >= CAST( ( now( ) - INTERVAL :amount_day_new_qa DAY ) AS DATE ) THEN 1 ELSE 0 END
                ) AS status_new,
                (SELECT comment FROM t_unsuitable_report T10 WHERE T10.key_id = T1.question_id AND T10.key_type = :key_type AND T10.create_user_cd = :create_user_cd AND T10.cancel_flag = 0 LIMIT 1) AS comment,
                (CASE WHEN
                    (SELECT COUNT(*) FROM t_posting_prohibited WHERE user_cd = :user_cd_prohibited) > 0 THEN 1 ELSE 0 END
                ) AS status_prohibited,
                (CASE WHEN
                    (SELECT COUNT(*) FROM t_posting_prohibited WHERE user_cd = M2.user_cd) > 0 THEN 1 ELSE 0 END
                ) AS status_prohibited_user_this
                FROM t_question T1
                JOIN m_user_org M6 ON M6.user_cd = T1.create_user_cd AND M6.main_flag = 1
                LEFT JOIN m_org M1 ON M1.org_cd = M6.org_cd
                LEFT JOIN m_user M2 ON M2.user_cd = T1.create_user_cd
                LEFT JOIN m_question_category M3 ON M3.qa_category_cd = T1.question_category_cd
                LEFT JOIN m_variable_definition M4 ON M4.definition_value = T1.question_status_type
                LEFT JOIN t_user_profile T6 ON T6.user_cd = M2.user_cd
                LEFT JOIN t_file T7 ON T7.file_id = T6.profile_image_file_id
                WHERE M4.definition_name = :definition_name AND T1.question_id = :question_id";
        $params = [
            'definition_name' => 'QAステータス',
            'question_id' => $idQuestion,
            'amount_day_hot_qa' => $amountDayHotQA,
            'amount_day_compare_hot' => $amountDayCompareHotQA,
            'amount_day_new_qa' => $amountDayNewQA,
            'role' => $role,
            'key_type' => $keyType,
            'create_user_cd' => $userCdLogin,
            'user_cd_prohibited' => $userCdLogin
        ];
        return $this->_first($sql, $params);
    }
    public function infomationAnswer ($idQuestion, $statusAnswer, $userCdLogin, $limit = 10, $offset = 1) {
        // $offset = ($offset - 1)*$limit;
        $keyType = $this->getKeyTypeUnsuitableReport(QAEnum::ANSWER);
        $sql = "SELECT T1.answer_id, T1.create_user_cd, M1.user_name, T1.last_update_datetime, T1.answer_note, T1.best_answer_flag, M3.org_label,
                (SELECT count(*) FROM t_unsuitable_report T2 WHERE T2.key_id = T1.answer_id AND T2.key_type = 20) AS number_unsuitable_report,
                (SELECT GROUP_CONCAT(create_user_cd) FROM t_unsuitable_report T3 WHERE T3.key_id = T1.answer_id AND T3.key_type = 20 AND T3.cancel_flag = 0) AS create_user_cd_unsuitable_report,
                T1.delete_flag, T1.updated_at, T5.file_url,
                (CASE WHEN
                    (SELECT COUNT(*) FROM t_unsuitable_report T6 WHERE T6.key_id = T1.answer_id AND T6.key_type = 20 AND T6.cancel_flag = 1) > 0 THEN 1 ELSE 0 END
                ) AS cancel_flag,
                (SELECT comment FROM t_unsuitable_report T7 WHERE T7.key_id = T1.answer_id AND T7.key_type = :key_type AND T7.create_user_cd = :create_user_cd AND T7.cancel_flag = 0 LIMIT 1) AS comment,
                (CASE WHEN
                    (SELECT COUNT(*) FROM t_posting_prohibited WHERE user_cd = M1.user_cd) > 0 THEN 1 ELSE 0 END
                ) AS status_prohibited_user_this
                FROM t_answer T1
                JOIN m_user M1 ON M1.user_cd = T1.create_user_cd
                JOIN m_user_org M2 ON M2.user_cd = M1.user_cd
                JOIN m_org M3 ON M3.org_cd = M2.org_cd
                LEFT JOIN t_user_profile T4 ON T4.user_cd = M1.user_cd
                LEFT JOIN t_file T5 ON T5.file_id = T4.profile_image_file_id
                WHERE T1.question_id = :question_id AND T1.best_answer_flag = :best_answer_flag
                ";
        // if is not manage then will hide list answer that
        if ( !$this->isUserManager($userCdLogin) ) {
            $sql .= " AND T1.delete_flag = 0";
        }
        $sql .= " GROUP BY T1.answer_id
        ORDER BY T1.created_at DESC ";
        $params = [
            'question_id' => $idQuestion,
            'best_answer_flag' => $statusAnswer,
            'key_type' => $keyType,
            'create_user_cd' => $userCdLogin
        ];
        if ( $statusAnswer == QAEnum::ANSWER_BEST ) {
            return $this->_find($sql, $params);
        }
        return $this->_paginate($sql, $params, [
            "limit" => $limit,
            "offset" => $offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }
    public function changeFlagToBestAnswer ($idAnswer) {
        $sql = "UPDATE t_answer
                SET best_answer_flag = :best_answer_flag
                WHERE answer_id = :answer_id";
        $params = [
            'best_answer_flag' => 1,
            'answer_id' => $idAnswer
        ];
        $this->_update($sql, $params);
    }
    public function givePointForUser ($userCdLogin, $typeAction, $idQuestion, $idAnswer = '') {
        // get point target type by table m_variable_definition
        $pointTargetType = $this->getPointTargetType('活用度ポイント対象区分', 'QA');
        $activePoint = 0;
        $userCdCreateContent = "";
        $infoQuestion = $this->getInfoQuestion ($idQuestion);
        $messagePoint = "";
        $pointTargetId = "";
        if ( $typeAction == QAEnum::TYPE_ACTION_BEST_ANSWER ) {
            $messagePoint = 'あなたがベストアンサーに選ばれました。';
            $userCdCreateContent = $this->getUserCreateContent ($idAnswer, 'answer');
            $pointTargetId = $idAnswer;
            $activePoint = $this->getActivePoint(32);
        }
        if ( $typeAction == QAEnum::TYPE_ACTION_CREATE_ANSWER ) {
            $messagePoint = '質問に回答を登録しました。';
            $userCdCreateContent = $infoQuestion->create_user_cd;
            $pointTargetId = $idQuestion;
            $activePoint = $this->getActivePoint(31);
        }
        $sql = "INSERT INTO t_active_point
                (
                    point_target_type,
                    point_target_id,
                    active_point,
                    message,
                    receive_user_cd
                ) VALUES
                (
                    :point_target_type,
                    :point_target_id,
                    :active_point,
                    :message,
                    :receive_user_cd
                );";
        $params = [
            'point_target_type' => $pointTargetType,
            'point_target_id' => $pointTargetId,
            'active_point' => $activePoint,
            'message' => $messagePoint,
            'receive_user_cd' => $userCdCreateContent
        ];
        return $this->_create($sql, $params);
    }
    public function givePointToUser ($userCdLogin, $typeAction, $targetId) {
        // get point target type by table m_variable_definition
        $pointTargetType = '';
        $activePoint = 0;
        $userCdCreateContent = "";
        $messagePoint = "";
        $pointTargetId = "";
        if ( $typeAction == Document::TYPE_COPY_DOCUMENT_TEXT ) {
            $documentName = $this->getDocumentName ($targetId);
            $messagePoint = "あなたの".$documentName."がコピーされました。";
            $userCdCreateContent = $userCdLogin;
            $pointTargetId = $targetId;
            $activePoint = $this->getActivePoint(11);
            $pointTargetType = $this->getPointTargetType('活用度ポイント対象区分', '資材');
        }
        $sql = "INSERT INTO t_active_point
                (
                    point_target_type,
                    point_target_id,
                    active_point,
                    message,
                    receive_user_cd
                ) VALUES
                (
                    :point_target_type,
                    :point_target_id,
                    :active_point,
                    :message,
                    :receive_user_cd
                );";
        $params = [
            'point_target_type' => $pointTargetType,
            'point_target_id' => $pointTargetId,
            'active_point' => $activePoint,
            'message' => $messagePoint,
            'receive_user_cd' => $userCdCreateContent
        ];
        return $this->_create($sql, $params);
    }
    public function saveInform ($userCdLogin, $typeAction, $idQuestion, $idAnswer = '') {
        $informCategoryCd = $this->getInformCategoryCd($typeAction);
        $infoQuestion = $this->getInfoQuestion ($idQuestion);
        if ( $idAnswer ) {
            $infoAnswer = $this->getInfoAnswer ($idAnswer);
        }
        $titleQuestion = $infoQuestion->title;
        $inform_user_list = [];
        $informUserCd = new stdClass;
        $informContent = "";
        if ( $typeAction == QAEnum::TYPE_ACTION_BEST_ANSWER ) {
            // get user register answer
            $informUserCd->user_cd = $infoAnswer->create_user_cd;
            array_push($inform_user_list, $informUserCd);
            $informContent = "あなたが回答したQA「".$titleQuestion."」のベストアンサーに選ばれました。";
        } else if ( $typeAction == QAEnum::TYPE_ACTION_CREATE_ANSWER ) {
            // get user register question
            $informUserCd->user_cd = $infoQuestion->create_user_cd;
            array_push($inform_user_list, $informUserCd);
            $informContent = "あなたが登録したQA「".$titleQuestion."」に回答が登録されました。";
        } else if ( $typeAction == QAEnum::TYPE_ACTION_UNSUITABLE_REPORT ) {
            // get all manager
            $inform_user_list = $this->getAllUserCdManagerQA();
            $informContent = "QA「".$titleQuestion."」に不適切報告が登録されました。";
        }
        $sql = "INSERT INTO t_inform
                (
                    inform_category_cd,
                    inform_datetime,
                    inform_user_cd,
                    inform_contents
                ) VALUES
                (
                    :inform_category_cd,
                    :inform_datetime,
                    :inform_user_cd,
                    :inform_contents
                );";
        foreach($inform_user_list as $user_cd){
            $not_received_inform_list = $this->installed( $user_cd->user_cd, $informCategoryCd);
            //Check if the user is set to receive notification type
            $check_not_received = 0;
            foreach($not_received_inform_list as $value){
                if($value->checked == 0){
                    $check_not_received++;
                }
            }
            if($check_not_received == 0){
                $params = [
                    'inform_category_cd' => $informCategoryCd,
                    'inform_datetime' => $this->date,
                    'inform_user_cd' => $user_cd->user_cd,
                    'inform_contents' => $informContent
                ];
                $this->_create($sql, $params);
                $idInform = $this->_lastInserted('t_inform', 'inform_id')->inform_id;
                $this->saveInformParameter ($userCdLogin, $idQuestion, $idInform);
            }
        }
    }

    public function installed($user_cd, $inform_category_cd = null)
    {
        $sql = "SELECT
            c.inform_category_cd,
            c.inform_category_name,
            ( CASE WHEN ue.user_cd IS NULL THEN 1 ELSE 0 END ) AS checked
        FROM
            m_inform_category c
            LEFT JOIN t_user_exclusion_inform_category ue ON c.inform_category_cd = ue.inform_category_cd
            AND ue.user_cd = :user_cd
        WHERE
            c.inform_stop_flag = 0";
        if (!empty($inform_category_cd)) {
            $sql .= " AND c.inform_category_cd = :inform_category_cd";
            $condition['inform_category_cd'] = $inform_category_cd;
        }
        $sql .= " ORDER BY c.sort_order ASC";
        $condition['user_cd'] = $user_cd;
        return $this->_find($sql, $condition);
    }

    protected function saveInformParameter ($userCdLogin, $idQuestion, $idInform) {
        $sql = "INSERT INTO t_inform_parameter
                (
                    inform_id,
                    parameter_key,
                    parameter_value
                ) VALUES
                (
                    :inform_id,
                    :parameter_key,
                    :parameter_value
                );";
        $params = [
            'inform_id' => $idInform,
            'parameter_key' => 'question_id',
            'parameter_value' => $idQuestion
        ];
        $this->_create($sql, $params);
    }
    public function createAnswer($idQuestion, $userCdLogin, $answerNote) {
        $sql = "INSERT INTO t_answer
                (
                    question_id,
                    create_user_cd,
                    answer_note,
                    last_update_datetime
                ) VALUES
                (
                    :question_id,
                    :create_user_cd,
                    :answer_note,
                    :last_update_datetime
                );";
        $params = [
            'create_user_cd' => $userCdLogin,
            'question_id' => $idQuestion,
            'answer_note' => $answerNote,
            'last_update_datetime' => $this->currentJapanDateTime('Y-m-d H:i:s')
        ];
        $this->_create($sql, $params);
        return $this->_lastInserted('t_answer', 'answer_id')->answer_id;
    }
    // $typeQA : question or answer
    // $idQA : question_id or answer_id
    public function createUnsuitableReport ($userCdLogin, $comment, $idQA, $typeQA) {
        $keyType = $this->getKeyTypeUnsuitableReport($typeQA);

        $unsuitableReportId = $this->getUnsuitableReportId ($idQA, $keyType, $userCdLogin);
        $action = $unsuitableReportId ? 'update' : 'create';
        $data = [
            'key_type' => $keyType,
            'comment' => $comment,
            'cancel_flag' => 0,
        ];
        if ( $action == 'update' ) {
            // update
            $data['unsuitable_report_id'] = $unsuitableReportId;
            $sql = "UPDATE t_unsuitable_report SET
                    key_type = :key_type,
                    comment = :comment,
                    cancel_flag = :cancel_flag
                    WHERE unsuitable_report_id = :unsuitable_report_id";
            $this->_update($sql, $data);
        } else {
            $data['key_id'] = $idQA;
            $data['create_user_cd'] = $userCdLogin;
            $data['create_datetime'] = $this->currentJapanDateTime('Y-m-d H:i:s');
            // create
            $sql = "INSERT INTO t_unsuitable_report
            (
                key_id,
                key_type,
                create_user_cd,
                create_datetime,
                comment,
                cancel_flag
            ) VALUES
            (
                :key_id,
                :key_type,
                :create_user_cd,
                :create_datetime,
                :comment,
                :cancel_flag
            );";
            $this->_create($sql, $data);
        }
    }
    public function openAcceptAnswer ($idQuestion) {
        $questionStatusType = $this->_first("SELECT definition_value FROM m_variable_definition WHERE definition_name='QAステータス' AND definition_label='回答受付中' LIMIT 1", []);
        $sql = "UPDATE t_question
                SET question_status_type = :question_status_type
                WHERE question_id = :question_id";
        $params = [
            'question_id' => $idQuestion,
            'question_status_type' => $questionStatusType->definition_value ?? 10
        ];
        $this->_update($sql, $params);
    }

    public function closeAcceptAnswer ($idQuestion) {
        $questionStatusType = $this->_first("SELECT definition_value FROM m_variable_definition WHERE definition_name='QAステータス' AND definition_label='回答受付終了' LIMIT 1", []);
        $sql = "UPDATE t_question
                SET question_status_type = :question_status_type
                WHERE question_id = :question_id";
        $params = [
            'question_id' => $idQuestion,
            'question_status_type' => $questionStatusType->definition_value ?? 10
        ];
        $this->_update($sql, $params);
    }

    public function registerPostingProhibited ($userCd, $userCdLogin) {
        $sql = "INSERT INTO t_posting_prohibited
                (
                    user_cd,
                    prohibited_datetime
                ) VALUES
                (
                    :user_cd,
                    :prohibited_datetime
                ) ON DUPLICATE KEY UPDATE user_cd = :user_cd_2;";
        $params = [
            'user_cd' => $userCd,
            'user_cd_2' => $userCd,
            'prohibited_datetime' => $this->currentJapanDateTime('Y-m-d H:i:s')
        ];
        $this->_create($sql, $params);
    }
    // keyId : question_id or answer_id
    public function cancelInform ($userCdLogin, $idQA, $typeQA) {
        $keyType = $this->getKeyTypeUnsuitableReport($typeQA);
        $sql = "UPDATE t_unsuitable_report
                SET cancel_flag = :cancel_flag
                WHERE key_id = :key_id AND key_type = :key_type";
        $params = [
            'cancel_flag' => 1,
            'key_id' => $idQA,
            'key_type' => $keyType
        ];
        if ($userCdLogin) {
            $sql .= " AND create_user_cd = :create_user_cd";
            $params['create_user_cd'] = $userCdLogin;
        }
        $this->_update($sql, $params);
    }
    /**
     * delete cancel report
     */
    public function deleteCancelInform ($idQA, $typeQA) {
        $keyType = $this->getKeyTypeUnsuitableReport($typeQA);
        $sql = "UPDATE t_unsuitable_report
                SET cancel_flag = :cancel_flag
                WHERE key_id = :key_id AND key_type = :key_type";
        $params = [
            'cancel_flag' => 0,
            'key_id' => $idQA,
            'key_type' => $keyType
        ];

        $this->_update($sql, $params);
    }

    public function deleteQA ($idQA, $typeQA, $flagDeletePhysics) {
        if ( $flagDeletePhysics == 1 ) {
            if ( $typeQA == QAEnum::QUESTION ) {
                $sql_delete_qa = 'DELETE t_question, t_question_imformation_org, t_unsuitable_report
                FROM t_question
                LEFT JOIN t_question_imformation_org ON t_question_imformation_org.question_id = t_question.question_id
                LEFT JOIN t_unsuitable_report ON t_unsuitable_report.key_id = t_question.question_id AND t_unsuitable_report.key_type = 10
                WHERE t_question.question_id = :id_qa';
                $sql_delete_answer = 'DELETE t_answer, t_unsuitable_report
                FROM t_answer
                LEFT JOIN t_unsuitable_report ON t_unsuitable_report.key_id = t_answer.answer_id AND t_unsuitable_report.key_type = 20
                WHERE t_answer.question_id = :id_qa';
                $this->_destroy($sql_delete_qa, ['id_qa' => $idQA]);
                $this->_destroy($sql_delete_answer, ['id_qa' => $idQA]);
            }
            if ( $typeQA == QAEnum::ANSWER ) {
                $sql = 'DELETE t_answer, t_unsuitable_report
                FROM t_answer
                LEFT JOIN t_unsuitable_report ON t_unsuitable_report.key_id = t_answer.answer_id AND t_unsuitable_report.key_type = 20
                WHERE t_answer.answer_id = :id_qa';
                $params = [
                    'id_qa' => $idQA
                ];
                $this->_destroy($sql, $params);
            }
        } else {
            if ( $typeQA == QAEnum::QUESTION ) {
                $sql = "UPDATE t_question SET delete_flag = :delete_flag WHERE question_id = :id_qa";
            }
            if ( $typeQA == QAEnum::ANSWER ) {
                $sql = "UPDATE t_answer SET delete_flag = :delete_flag WHERE answer_id = :id_qa";
            }

            $params = [
                'id_qa' => $idQA,
                'delete_flag' => 1
            ];
            $this->_update($sql, $params);
        }
    }
    public function isOverAmountAnswer ($idAnswer) {
        $amountBestAnswerCurrent = (int)$this->amountBestAnswerCurrentByAnswer($idAnswer);
        $amountLimitBestAnswer = (int)$this->getAmountLimitBestAnswer();
        if ($amountBestAnswerCurrent >= $amountLimitBestAnswer) {
            return $amountLimitBestAnswer;
        }
        return 0;
    }
    protected function amountBestAnswerCurrentByAnswer ($idAnswer) {
        $sql = "SELECT COUNT(*) AS amount_answer_current
                FROM t_answer T1
                WHERE T1.best_answer_flag = :best_answer_flag AND T1.question_id = (SELECT question_id FROM t_answer WHERE t_answer.answer_id = :answer_id LIMIT 1)";
        $data = $this->_first($sql, [
            'answer_id' => $idAnswer,
            'best_answer_flag' => 1
        ]);
        return $data->amount_answer_current;
    }
    protected function getAmountLimitBestAnswer () {
        // 既に｛｛上限件数｝｝件のベストアンサーが登録されています。 | ｛｛上限件数｝｝ : parameter_value
        $sql = "SELECT parameter_value
                FROM c_system_parameter
                WHERE parameter_name = :parameter_name AND parameter_key = :parameter_key";
        $params = [
            'parameter_name' => 'QA管理',
            'parameter_key' => 'ベストアンサー上限数'
        ];

        $data = $this->_first($sql, $params);

        return isset($data->parameter_value) ? $data->parameter_value : 0;
    }
    public function isStopCommentByAnswer ($idAnswer) {
        $variableStopAnswer = $this->variableStopAnswer();
        $sql = "SELECT T1.question_status_type
                FROM t_question T1
                JOIN t_answer T2 ON T2.question_id = T1.question_id
                WHERE answer_id = :answer_id AND T1.question_status_type = :variable_stopAnswer";

        $params = [
            'answer_id' => $idAnswer,
            'variable_stopAnswer' => $variableStopAnswer
        ];
        $data = $this->_find($sql, $params);
        return count($data) ? 1 : 0;
    }
    protected function variableStopAnswer () {
        $sql = "SELECT definition_value FROM m_variable_definition WHERE definition_name=:definition_name AND definition_label=:definition_label";
        $data = $this->_first($sql, [
            'definition_name' => 'QAステータス',
            'definition_label' => '回答受付終了' // change text in table m_variable_definition by task BU5_MSFA-1900#comment-121261074
        ]);
        return isset($data->definition_value) ? $data->definition_value : '';
    }
    protected function getPointTargetType ($definitionName, $definitionLabel) {
        $sql = "SELECT definition_value FROM m_variable_definition WHERE definition_name = :definition_name AND definition_label = :definition_label";
        $dataPointTargetType = $this->_first($sql, [
            'definition_name' => $definitionName,
            'definition_label' => $definitionLabel
        ]);
        return isset($dataPointTargetType->definition_value) ? $dataPointTargetType->definition_value : "";
    }
    protected function getActivePoint ($fixedActivePointCd) {
        $sql = "SELECT active_point FROM m_fixed_active_point WHERE fixed_active_point_cd = :fixed_active_point_cd";
        $dataActivePoint = $this->_first($sql, [
            'fixed_active_point_cd' => $fixedActivePointCd
        ]);
        return isset($dataActivePoint->active_point) ? $dataActivePoint->active_point : "";
    }
    protected function getDocumentName ($idDocument) {
        $sql = "SELECT document_name FROM t_document WHERE document_id = :document_id";
        $data = $this->_first($sql, [
            'document_id' => $idDocument
        ]);
        return isset($data->document_name) ? $data->document_name : "";
    }
    protected function getUserCreateContent ($idQA, $type = 'answer') {
        if ( $type == QAEnum::ANSWER ) {
            $sql = "SELECT create_user_cd FROM t_answer WHERE answer_id = :id_qa";
        }
        if ( $type == QAEnum::QUESTION ) {
            $sql = "SELECT create_user_cd FROM t_question WHERE question_id = :id_qa";
        }

        $dataQa = $this->_first($sql, [
            'id_qa' => $idQA
        ]);
        return isset($dataQa->create_user_cd) ? $dataQa->create_user_cd : "";
    }
    protected function getInformCategoryCd ($typeAction) {
        $textAction ="";
        switch ($typeAction) {
            case QAEnum::TYPE_ACTION_BEST_ANSWER:
                $textAction = 'ベストアンサー';
                break;

            case QAEnum::TYPE_ACTION_CREATE_ANSWER:
                $textAction = '回答登録';
                break;
            case QAEnum::TYPE_ACTION_UNSUITABLE_REPORT:
                $textAction = '不適切報告登録';
                break;
        }
        $sql = "SELECT inform_category_cd FROM m_inform_category WHERE inform_category_name = :inform_category_name";
        $data = $this->_first($sql, [
            'inform_category_name' => $textAction
        ]);
        return isset($data->inform_category_cd) ? $data->inform_category_cd : "";
    }
    protected function getInfoQuestion ($idQuestion) {
        $sql = "SELECT * FROM t_question WHERE question_id = :question_id";
        return $this->_first($sql, [
            'question_id' => $idQuestion
        ]);
    }
    public function getInfoAnswer ($idAnswer) {
        $sql = "SELECT * FROM t_answer WHERE answer_id = :answer_id";
        return $this->_first($sql, [
            'answer_id' => $idAnswer
        ]);
    }
    public function isUserQuestion ($idAnswer, $userCdLogin) {
        $sql = "SELECT T1.question_id
                FROM t_question T1
                JOIN t_answer T2 ON T2.question_id = T1.question_id
                WHERE T2.answer_id = :answer_id AND T1.create_user_cd = :create_user_cd";
        $params = [
            'answer_id' => $idAnswer,
            'create_user_cd' => $userCdLogin
        ];
        $data = $this->_first($sql, $params);
        return isset($data->question_id) ? 1 : 0;
    }
    public function isUserOpenStopQuestion ($idQuestion, $userCdLogin) {
        $sql = "SELECT question_id
                FROM t_question
                WHERE question_id = :question_id AND create_user_cd = :create_user_cd";
        $params = [
            'question_id' => $idQuestion,
            'create_user_cd' => $userCdLogin
        ];
        $data = $this->_first($sql, $params);
        return isset($data->question_id) ? 1 : 0;
    }
    public function isUserManager ($userCdLogin) {
        $role = config('role.QA_MG.CODE');
        $sql = "SELECT user_cd FROM m_user_role WHERE role = :role AND user_cd = :user_cd";
        $params = [
            'role' => $role,
            'user_cd' => $userCdLogin
        ];
        $data = $this->_first($sql, $params);
        return isset($data->user_cd) ? 1 : 0;
    }
    public function isUserCreateQA ($idQA, $userCdLogin, $typeQA) {
        if ( $typeQA == QAEnum::QUESTION ) {
            $sql = "SELECT COUNT(*) AS total FROM t_question WHERE create_user_cd = :create_user_cd AND question_id = :id_qa";
        }
        if ( $typeQA == QAEnum::ANSWER ) {
            $sql = "SELECT  COUNT(*) AS total FROM t_answer WHERE create_user_cd = :create_user_cd AND answer_id = :id_qa";
        }

        $params = [
            'create_user_cd' => $userCdLogin,
            'id_qa' => $idQA
        ];
        $total = $this->_first($sql, $params)->total;
        return $total ? true : false;
    }
    public function isUserCreateReport ($idQA, $userCdLogin) {
        $sql = "SELECT COUNT(*) AS total FROM t_unsuitable_report WHERE create_user_cd = :create_user_cd AND key_id = :id_qa";

        $params = [
            'create_user_cd' => $userCdLogin,
            'id_qa' => $idQA
        ];
        $total = $this->_first($sql, $params)->total;
        return $total ? true : false;
    }

    protected function getKeyTypeUnsuitableReport ($typeQA) {
        $labelQA ="";
        switch ($typeQA) {
            case QAEnum::QUESTION:
                $labelQA = '質問';
                break;

            case QAEnum::ANSWER:
                $labelQA = '回答';
                break;
        }
        $sql = "SELECT definition_value FROM m_variable_definition WHERE definition_name = :definition_name AND definition_label = :definition_label";
        $dataPointTargetType = $this->_first($sql, [
            'definition_name' => 'QA不適切報告対象区分',
            'definition_label' => $labelQA
        ]);
        return isset($dataPointTargetType->definition_value) ? $dataPointTargetType->definition_value : "";
    }
    protected function getAllUserCdManagerQA () {
        $role = config('role.QA_MG.CODE');
        $sql = "SELECT user_cd FROM m_user_role WHERE role = :role";
        $params = [
            'role' => $role
        ];
        return $this->_find($sql, $params);
    }
    protected function amountDayHotQA () {
        $sql = "SELECT parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_name AND parameter_key = :parameter_key";
        $data = $this->_first($sql, [
            'parameter_name' => 'QA管理',
            'parameter_key' => 'HOT規定日数'
        ]);
        return isset($data->parameter_value) ? $data->parameter_value : 0;
    }
    protected function amountDayNewQA () {
        $sql = "SELECT parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_name AND parameter_key = :parameter_key";
        $data = $this->_first($sql, [
            'parameter_name' => 'QA管理',
            'parameter_key' => '新着規定日数'
        ]);
        return isset($data->parameter_value) ? $data->parameter_value : 0;
    }
    protected function amountDayCompareHotQA () {
        $sql = "SELECT parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_name AND parameter_key = :parameter_key";
        $data = $this->_first($sql, [
            'parameter_name' => 'QA管理',
            'parameter_key' => 'HOT規定コメント数'
        ]);
        return isset($data->parameter_value) ? $data->parameter_value : 0;
    }
    protected function getUnsuitableReportId ($idQA, $keyType, $userCdLogin) {
        $sql = "SELECT unsuitable_report_id FROM t_unsuitable_report WHERE key_id = :key_id AND key_type = :key_type AND create_user_cd = :create_user_cd";
        $data = $this->_first($sql, [
            'key_id' => $idQA,
            'key_type' => $keyType,
            'create_user_cd' => $userCdLogin
        ]);
        return isset($data->unsuitable_report_id) ? $data->unsuitable_report_id : 0;
    }
    public function unsuitableReports ($idQA, $typeQA) {
        $keyType = $this->getKeyTypeUnsuitableReport($typeQA);
        $sql = "SELECT T1.comment,M3.org_label,M1.user_name
                FROM t_unsuitable_report T1
                JOIN m_user M1 ON M1.user_cd = T1.create_user_cd
                JOIN m_user_org M2 ON M2.user_cd = M1.user_cd
                JOIN m_org M3 ON M3.org_cd = M2.org_cd
                WHERE T1.key_id = :key_id AND T1.key_type = :key_type AND T1.cancel_flag = 0
                GROUP BY T1.unsuitable_report_id";

        $params = [
            'key_id' => $idQA,
            'key_type' => $keyType
        ];
        return $this->_find($sql, $params);
    }
}
