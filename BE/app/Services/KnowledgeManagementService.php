<?php

namespace App\Services;

use App\Jobs\SendEmail;
use App\Mail\RegisterRequest;
use App\Traits\DateTimeTrait;
use App\Services\FacilityDetailsTimeLineService;
use App\Repositories\Inform\InformRepositoryInterface;
use App\Repositories\UserManagement\UserManagementRepositoryInterface;
use App\Repositories\PasswordReissue\PasswordReissueRepositoryInterface;
use App\Repositories\KnowledgeManagement\KnowledgeManagementRepositoryInterface;
use App\Traits\Base64StringFileTrait;

class KnowledgeManagementService
{
    use DateTimeTrait, Base64StringFileTrait;
    private $repo, $user_management, $inform, $password_reissue;

    public function __construct(
        KnowledgeManagementRepositoryInterface $repo,
        UserManagementRepositoryInterface $user_management,
        FacilityDetailsTimeLineService $facility_timeline,
        InformRepositoryInterface $inform,
        PasswordReissueRepositoryInterface $password_reissue
    ) {
        $this->repo = $repo;
        $this->user_management = $user_management;
        $this->facility_timeline = $facility_timeline;
        $this->inform = $inform;
        $this->password_reissue = $password_reissue;
    }

    public function getScreenData()
    {
        $result['top_like'] = $this->repo->getTopLike();
        foreach($result['top_like'] as $value){
            $value->org_label = $value->org_label ?? NO_AFFILIATION;
        }
        $result['knowledge_status'] = $this->repo->getKnowledgeStatus();
        $result['knowledge_category'] = $this->repo->getKnowledgeCategory();
        $result['facility_type_group'] = $this->repo->getFacilityTypeGroup();
        $result['medical_subjects_group'] = $this->repo->getMedicalSubjectsGroup();
        return $result;
    }

    public function search($conditions)
    {
        $approval_titles = $this->user_management->getVariableDefinition(APPROVAL_BUSINESS_CLASSIFICATION);
        $approval_layer_num = $this->user_management->getApprovalLayerNum(APPROVAL_HIERACHY);
        $conditions->approval_layer_num = 0;
        $conditions->approval_work_type = 0;
        foreach ($approval_layer_num as $approval_layer) {
            if ($approval_layer->parameter_key == KNOWLEDGE) {
                $conditions->approval_layer_num = $approval_layer->parameter_value;
                break;
            }
        }
        foreach ($approval_titles as $approval_title) {
            if ($approval_title->definition_label == KNOWLEDGE) {
                $conditions->approval_work_type = $approval_title->definition_value;
                break;
            }
        }
        $conditions->current = $this->currentJapanDateTime("Y-m-d");
        $conditions->user_login = $conditions->create_user_cd;
        $result = $this->repo->search($conditions);
        foreach ($result['records'] as $data) {
            $data->create_datetime = $this->responseDateTimeFormat($data->create_datetime);
            $data->last_update_datetime = $this->responseDateTimeFormat($data->last_update_datetime);
            $data->create_user_cd = empty($data->create_user_cd) ? [] : json_decode($data->create_user_cd, true);
            $data->create_user_cd_together = empty($data->create_user_cd_together) ? [] : json_decode($data->create_user_cd_together, true);
            $data->approval_user_cd = empty($data->approval_user_cd) ? [] : json_decode($data->approval_user_cd, true);
            // ◆ナレッジ管理者・最終承認者の場合
            //     匿名に設定されているナレッジでも匿名ではなくDBに登録されている情報を表示する。
            // ◆上記以外
            //     匿名に設定されているナレッジは作成者情報・施設情報・個人情報を匿名で表示する。
            if (in_array(config('role.KNOWLEDGE_MG.CODE'), $conditions->roles) || in_array($conditions->create_user_cd, $data->approval_user_cd)) {
                // 対象のナレッジが匿名に設定されている場合は「匿名」と表示
                // 共同作成者が登録されている場合は後ろに「,（カンマ）」を付与して共同作成者を追加で表示
                if (!empty($data->create_user_cd)) {
                    if(empty($data->create_user_cd[0]['org_label'])){
                        $data->create_user_cd[0]['org_label'] = NO_AFFILIATION;
                    }
                    $data->create_user_cd = $data->create_user_cd[0]['org_label'] . ' ' . $data->create_user_cd[0]['user_name'];
                } else {
                    $data->create_user_cd = "";
                }
                $user_togethers = "";
                foreach ($data->create_user_cd_together as $user_together) {
                    if(empty($user_together['org_label'])){
                        $user_together['org_label'] = NO_AFFILIATION;
                    }
                    $user_togethers .= $user_together['org_label'] . ' ' . $user_together['user_name'] . ", ";
                }
                $user_togethers = rtrim($user_togethers, ", ");
                $data->create_user_cd_together = $user_togethers;
            } else {
                // 対象のナレッジが匿名に設定されている場合は「匿名」と表示
                // 共同作成者が登録されている場合は後ろに「,（カンマ）」を付与して共同作成者を追加で表示
                if ($data->anonymous_flag == 1) {
                    $data->facility_short_name = ANONYMOUS;
                    $data->person_name = ANONYMOUS;
                    $data->department_name = ANONYMOUS;
                    $data->display_position_name = ANONYMOUS;
                    $data->create_user_cd = ANONYMOUS;
                    $data->create_user_cd_together = ANONYMOUS;
                } else {
                    if (!empty($data->create_user_cd)) {
                        if(empty($data->create_user_cd[0]['org_label'])){
                            $data->create_user_cd[0]['org_label'] = NO_AFFILIATION;
                        }
                        $data->create_user_cd = $data->create_user_cd[0]['org_label'] . ' ' . $data->create_user_cd[0]['user_name'];
                    } else {
                        $data->create_user_cd = "";
                    }
                    $user_togethers = "";
                    foreach ($data->create_user_cd_together as $user_together) {
                        if(empty($user_together['org_label'])){
                            $user_together['org_label'] = NO_AFFILIATION;
                        }
                        $user_togethers .= $user_together['org_label'] . ' ' . $user_together['user_name'] . ", ";
                    }
                    $user_togethers = rtrim($user_togethers, ", ");
                    $data->create_user_cd_together = $user_togethers;
                }
            }
        }
        return $result;
    }

    public function knowledgeDetail($conditions)
    {
        $knowledge_timeline = $this->repo->getKnowledgeTimeline($conditions->knowledge_id);
        $timeline_ids = [];
        $timelines = [];
        if (count($knowledge_timeline) > 0) {
            foreach ($knowledge_timeline as $value) {
                array_push($timeline_ids, $value->timeline_id);
            }
            $timelines = $this->facility_timeline->getFacilityDetailsTimeLine(null, null, null, null, null, null, $timeline_ids);
        }

        $approval_titles = $this->user_management->getVariableDefinition(APPROVAL_BUSINESS_CLASSIFICATION);
        $approval_layer_num = $this->user_management->getApprovalLayerNum(APPROVAL_HIERACHY);
        $conditions->approval_layer_num = 0;
        $conditions->approval_work_type = 0;
        foreach ($approval_layer_num as $approval_layer) {
            if ($approval_layer->parameter_key == KNOWLEDGE) {
                $conditions->approval_layer_num = $approval_layer->parameter_value;
                break;
            }
        }
        foreach ($approval_titles as $approval_title) {
            if ($approval_title->definition_label == KNOWLEDGE) {
                $conditions->approval_work_type = $approval_title->definition_value;
                break;
            }
        }
        $conditions->current = $this->currentJapanDateTime("Y-m-d");
        $conditions->user_login = $conditions->create_user_cd;
        $data = $this->repo->knowledgeDetail($conditions);
        if (empty($data)) {
            return false;
        }
        $data->release_datetime = $this->responseDateTimeFormat($data->release_datetime, "Y/m/d");
        $data->create_user_cd = empty($data->create_user_cd) ? [] : json_decode($data->create_user_cd, true);
        $data->create_user_cd_together = empty($data->create_user_cd_together) ? [] : json_decode($data->create_user_cd_together, true);
        $data->approval_user_cd = empty($data->approval_user_cd) ? [] : json_decode($data->approval_user_cd, true);
        $data->group_user_file = [];
        // ◆ナレッジの匿名設定が有効の場合
        // 　◆ナレッジ管理者・最終承認者の場合
        // 　　登録されているナレッジ元作成者組織名・ユーザ名を表示＋”※匿名に設定されています”を表示
        // 　◆上記以外
        // 　　”匿名”と表示
        // ◆上記以外
        // 　登録されているナレッジ元作成者組織名・ユーザ名を表示
        if ($data->anonymous_flag == 1) {
            if (in_array(config('role.KNOWLEDGE_MG.CODE'), $conditions->roles) || in_array($conditions->create_user_cd, $data->approval_user_cd)) {
                if (!empty($data->create_user_cd)) {
                    array_push($data->group_user_file, [
                        'avatar_image_data' => $this->encodeBase64String($data->create_user_cd[0]['avatar_image_data']),
                        'avatar_image_type' => $data->create_user_cd[0]['avatar_image_type'],
                        'anonymous_flag' => 1
                    ]);
                    if(empty($data->create_user_cd[0]['org_label'])){
                        $data->create_user_cd[0]['org_label'] = NO_AFFILIATION;
                    }
                    $data->create_user_name = $data->create_user_cd[0]['org_label'] . ' ' . $data->create_user_cd[0]['user_name'] . SET_TO_ANONYMOUS;
                    $data->create_user_cd = $data->create_user_cd[0]['user_cd'];
                } else {
                    $data->create_user_cd = "";
                    $data->create_user_name = "";
                }
                $user_togethers = "";
                foreach ($data->create_user_cd_together as $user_together) {
                    array_push($data->group_user_file, [
                        'avatar_image_data' => $this->encodeBase64String($user_together['avatar_image_data']),
                        'avatar_image_type' => $user_together['avatar_image_type'],
                        'anonymous_flag' => 1
                    ]);
                    if(empty($user_together['org_label'])){
                        $user_together['org_label'] = NO_AFFILIATION;
                    }
                    $user_togethers .= $user_together['org_label'] . ' ' . $user_together['user_name'] . SET_TO_ANONYMOUS . ", ";
                }
                $user_togethers = rtrim($user_togethers, ", ");
                // $data->create_user_cd_together = $user_togethers;
                $data->facility_short_name .= SET_TO_ANONYMOUS;
                $data->person_name .= $data->person_name != "" ? SET_TO_ANONYMOUS : "";
                $data->department_name = '';
                $data->display_position_name = '';
            } else {
                $data->facility_short_name = ANONYMOUS;
                $data->person_name = $data->person_name != "" ? ANONYMOUS : "";
                $data->department_name = '';
                $data->display_position_name = '';
                $data->create_user_cd = ANONYMOUS;
                $data->create_user_name = ANONYMOUS;
                $data->create_user_cd_together = ANONYMOUS;
            }
        } else {
            if (!empty($data->create_user_cd)) {
                array_push($data->group_user_file, [
                    'avatar_image_data' => $this->encodeBase64String($data->create_user_cd[0]['avatar_image_data']),
                    'avatar_image_type' => $data->create_user_cd[0]['avatar_image_type'],
                    'anonymous_flag' => 0
                ]);
                if(empty($data->create_user_cd[0]['org_label'])){
                    $data->create_user_cd[0]['org_label'] = NO_AFFILIATION;
                }
                $data->create_user_name = $data->create_user_cd[0]['org_label'] . ' ' . $data->create_user_cd[0]['user_name'];
                $data->create_user_cd = $data->create_user_cd[0]['user_cd'];
            } else {
                $data->create_user_cd = "";
                $data->create_user_name = "";
            }
            $user_togethers = "";
            foreach ($data->create_user_cd_together as $user_together) {
                array_push($data->group_user_file, [
                    'avatar_image_data' => $this->encodeBase64String($user_together['avatar_image_data']),
                    'avatar_image_type' => $user_together['avatar_image_type'],
                    'anonymous_flag' => 0
                ]);
                if(empty($user_together['org_label'])){
                    $user_together['org_label'] = NO_AFFILIATION;
                }
                $user_togethers .= $user_together['org_label'] . ' ' . $user_together['user_name'] . ", ";
            }
            $user_togethers = rtrim($user_togethers, ", ");
            // $data->create_user_cd_together = $user_togethers;
        }
        $data->timelines = $timelines;
        $data->evaluation_comments = $this->repo->getEvaluationComment($conditions);
        foreach($data->evaluation_comments['records'] as $value){
            $value->avatar_image_data = $this->encodeBase64String($value->avatar_image_data);
        }
        //評価コメントを削除するためのボタン
        //◆ナレッジ管理者・最終承認者の場合
        //　表示
        //◆上記以外
        //　非表示
        $approval_titles = $this->user_management->getVariableDefinition(APPROVAL_BUSINESS_CLASSIFICATION);
        $approval_layer_num = $this->user_management->getApprovalLayerNum(APPROVAL_HIERACHY);
        $user_approval_condition['approval_layer_num'] = 0;
        $user_approval_condition['approval_work_type'] = 0;
        foreach ($approval_layer_num as $approval_layer) {
            if ($approval_layer->parameter_key == KNOWLEDGE) {
                $user_approval_condition['approval_layer_num'] = $approval_layer->parameter_value;
                break;
            }
        }
        foreach ($approval_titles as $approval_title) {
            if ($approval_title->definition_label == KNOWLEDGE) {
                $user_approval_condition['approval_work_type'] = $approval_title->definition_value;
                break;
            }
        }
        $user_approval_condition['date_system'] = $this->currentJapanDateTime("Y-m-d");
        $user_approval_condition['date_system_temp'] = $this->currentJapanDateTime("Y-m-d");
        $user_approval_condition['user_login'] = $data->create_user_cd;
        $data->user_approval = $this->repo->getUserApprovalKnowledgeAdmin($user_approval_condition);
        $data->total_evaluation_comment = $data->evaluation_comments['pagination']['total_items'] ?? 0;
        $data->total_knowledge_nice = $this->repo->getTotalNice($conditions->knowledge_id)->nice ?? 0;
        return $data;
    }

    public function createKnowledgeNice($datas)
    {
        $result = false;
        $knowledge_nice_exists = $this->repo->getKnowledgeNice($datas->knowledge_id, $datas->user_login);
        //Liked
        if ($knowledge_nice_exists) {
            return true;
        }
        //Insert t_knowledge_nice
        $knowledge_nice_insert['knowledge_id'] = $datas->knowledge_id;
        $knowledge_nice_insert['nice_user_cd'] = $datas->user_login;
        $knowledge_nice_insert['last_update_datetime'] = $this->currentJapanDateTime("Y-m-d H:i:s");
        $result = $this->repo->insertKnowledgeNice($knowledge_nice_insert);
        if (!$result) {
            return false;
        }
        $knowledge_nice = $this->repo->lastKnowledgeNice();
        $point_target_type = $this->repo->getPointTargetType();
        $active_point_nice = $this->repo->getActivePointNice();
        //Insert t_active_point
        $active_point_insert['point_target_type'] = $point_target_type->definition_value ?? 0;
        $active_point_insert['point_target_id'] = $knowledge_nice->nice_id;
        $active_point_insert['active_point'] = $active_point_nice->active_point ?? 0;
        $active_point_insert['message'] = KNOWLEDGE_NICE_ACTIVE_POINT_MESSAGE_REGISTER;
        $active_point_insert['receive_user_cd'] = $datas->create_user_cd;
        $result = $this->repo->insertActivePoint($active_point_insert);
        if (!$result) {
            return false;
        }
        return $result;
    }

    public function upsertComment($datas)
    {
        $result = false;
        $knowledge_nice_exists = $this->repo->getKnowledgeNice($datas->knowledge_id, $datas->user_login);
        //◆対象行のナレッジいいね登録者自身の場合
        //　表示
        //◆上記以外
        //　非表示
        if (!$knowledge_nice_exists) {
            return false;
        }
        //Update t_knowledge_nice
        $knowledge_nice_update['nice_id'] = $knowledge_nice_exists->nice_id;
        $knowledge_nice_update['last_update_datetime'] = $this->currentJapanDateTime("Y-m-d H:i:s");
        $knowledge_nice_update['comment'] = $datas->comment ?? null;
        $result = $this->repo->updateKnowledgeNice($knowledge_nice_update);
        if (!$result) {
            return false;
        }
        // 以下の条件に当てはまる値を[変数定義マスタ]より取得する
        // 　定義名 = "活用度ポイント機能区分”
        // 　定義値 = ”ナレッジ”
        $point_target_type = $this->repo->getPointTargetType();
        //以下の条件で取得した[活用度固定ポイントマスタ．ポイント]を登録
        //　[活用度固定ポイントマスタ．固定ポイント区分]が”ナレッジいいね”
        $active_point_comment = $this->repo->getActivePointComment();
        $active_point = $this->repo->getActivePoint($knowledge_nice_exists->nice_id, $point_target_type->definition_value ?? 0, KNOWLEDGE_COMMENT_ACTIVE_POINT_MESSAGE_REGISTER);
        if (empty($active_point)) {
            //Insert t_active_point
            $active_point_insert['point_target_type'] = $point_target_type->definition_value ?? 0;
            $active_point_insert['point_target_id'] = $knowledge_nice_exists->nice_id;
            $active_point_insert['active_point'] = $active_point_comment->active_point ?? 0;
            $active_point_insert['message'] = KNOWLEDGE_COMMENT_ACTIVE_POINT_MESSAGE_REGISTER;
            $active_point_insert['receive_user_cd'] = $datas->user_login;
            $result = $this->repo->insertActivePoint($active_point_insert);
            if (!$result) {
                return false;
            }
        }
        return $result;
    }

    public function getKnowledgeNiceById($nice_id)
    {
        return $this->repo->getKnowledgeNiceById($nice_id);
    }

    public function deleteKnowledgeNice($datas)
    {
        $result = false;
        $knowledge_nice = $this->repo->getKnowledgeNiceById($datas->nice_id);
        $result =  $this->repo->deleteKnowledgeNice($datas->nice_id);
        if (!$result) {
            return $result;
        }
        //Check if the user is set to receive notification type
        $not_received_inform_list = $this->inform->installed($datas->user_login);
        foreach ($not_received_inform_list as $value) {
            if ($value->inform_category_cd == PERSON_DETAIL_NOTE_INFORM_CATEGORY && $value->checked == 0) {
                return true;
            }
        }
        //Insert t_inform, t_inform_parameter
        $inform_contents = __('knowledge.inform_contents', ['attribute' => $knowledge_nice->title]);
        $inform_id = $this->inform->registerInform(null, $knowledge_nice->nice_user_cd, $inform_contents, DELETE_RATING_COMMENT_INFORM_CATEGORY);
        $result = $this->inform->registerInformParameter(null, $inform_id, KNOWLEDGE_ID, $knowledge_nice->knowledge_id);
        if (!$result) {
            return false;
        }
        return $result;
    }

    public function registerKnowledgeRequest($datas)
    {
        $result = false;
        $knowledge_request_insert['knowledge_id'] = $datas->knowledge_id;
        $knowledge_request_insert['demand_note'] = $datas->demand_note ?? "";
        $knowledge_request_insert['create_datetime'] = $this->currentJapanDateTime("Y-m-d H:i:s");
        $result =  $this->repo->insertKnowledgeRequest($knowledge_request_insert);
        if(!$result){
            return $result;
        }
        //Check if the user is set to receive notification type
        $not_received_inform_list = $this->inform->installed($datas->user_login);
        foreach ($not_received_inform_list as $value) {
            if ($value->inform_category_cd == PERSON_DETAIL_NOTE_INFORM_CATEGORY && $value->checked == 0) {
                return true;
            }
        }
        // 対象ナレッジの最終承認者とナレッジ管理者のユーザコードをセット
        // 複数のユーザが存在する場合は対象の全ユーザ分の通知を登録する
        $approval_titles = $this->user_management->getVariableDefinition(APPROVAL_BUSINESS_CLASSIFICATION);
        $approval_layer_num = $this->user_management->getApprovalLayerNum(APPROVAL_HIERACHY);
        $conditions['approval_layer_num'] = 0;
        $conditions['approval_work_type'] = 0;
        foreach ($approval_layer_num as $approval_layer) {
            if ($approval_layer->parameter_key == KNOWLEDGE) {
                $conditions['approval_layer_num'] = $approval_layer->parameter_value;
                break;
            }
        }
        foreach ($approval_titles as $approval_title) {
            if ($approval_title->definition_label == KNOWLEDGE) {
                $conditions['approval_work_type'] = $approval_title->definition_value;
                break;
            }
        }
        $conditions['date_system'] = $this->currentJapanDateTime("Y-m-d");
        $conditions['date_system_temp'] = $this->currentJapanDateTime("Y-m-d");
        $conditions['knowledge_id'] = $datas->knowledge_id;
        $conditions['knowledge_admin'] = config('role.KNOWLEDGE_MG.CODE');
        $knowledge = $this->repo->getKnowledge($conditions);
        $knowledge_admin = empty($knowledge[0]->knowledge_admin) ? [] : json_decode($knowledge[0]->knowledge_admin, true);
        $approval_user_cd = empty($knowledge[0]->approval_user_cd) ? [] : json_decode($knowledge[0]->approval_user_cd, true);
        //Insert t_inform, t_inform_parameter
        $inform_contents = __('knowledge.knowledge_request_contents', ['attribute1' => $knowledge[0]->title, 'attribute2' => $datas->demand_note ?? ""]);
        $user_login_info = $this->password_reissue->getInfoUser($datas->user_login);
        $system_parameter = $this->password_reissue->getContentEmail('システム名');
        $system_name = $system_parameter[0]->parameter_value ?? "";
        //Check if the user is set to receive notification type
        $not_received_inform_list = $this->inform->installed($datas->user_login);
        $check_recieve_mail = true;
        foreach ($not_received_inform_list as $value) {
            if ($value->inform_category_cd == PERSON_DETAIL_NOTE_INFORM_CATEGORY && $value->checked == 0) {
                $check_recieve_mail = false;
                break;
            }
        }
        foreach ($knowledge_admin as $knowledge_admin_user_cd) {
            $data = [
                'title' => "【" . $system_name . "】ナレッジ要望・要求事項が登録されました",
                'user_name_receive' => $knowledge_admin_user_cd['user_name'] ?? "",
                'knowledge_id' => $datas->knowledge_id,
                'user_name_login' => $user_login_info[0]->user_name ?? "",
                'demand_note' => $datas->demand_note ?? "",
            ];
            $emailTo = $knowledge_admin_user_cd['mail_address'] ?? '';
            $mailable = new RegisterRequest($data);
            if($check_recieve_mail){
                $inform_id = $this->inform->registerInform(null, $knowledge_admin_user_cd['user_cd'], $inform_contents, KNOWLEDGE_REQUEST_INFORM_CATEGORY);
                $result = $this->inform->registerInformParameter(null, $inform_id, KNOWLEDGE_ID, $datas->knowledge_id);
            }
            if($emailTo){
                SendEmail::dispatchAfterResponse($emailTo, $mailable);
            }
        }
        foreach ($approval_user_cd as $approval_user) {
            $data = [
                'title' => "タイトル：【" . $system_name . "】ナレッジ要望・要求事項が登録されました",
                'user_name_receive' => $approval_user['user_name'] ?? "",
                'knowledge_id' => $datas->knowledge_id,
                'user_name_login' => $user_login_info[0]->user_name ?? "",
                'demand_note' => $datas->demand_note ?? "",
            ];
            $emailTo = $approval_user['mail_address'] ?? '';
            $mailable = new RegisterRequest($data);
            if($check_recieve_mail){
                $inform_id = $this->inform->registerInform(null, $approval_user['approval_user_cd'], $inform_contents, KNOWLEDGE_REQUEST_INFORM_CATEGORY);
                $result = $this->inform->registerInformParameter(null, $inform_id, KNOWLEDGE_ID, $datas->knowledge_id);
            }
            if($emailTo){
                SendEmail::dispatchAfterResponse($emailTo, $mailable);
            }
        }
        return true;
    }

    public function getKnowledge($knowledge_id)
    {
        $approval_titles = $this->user_management->getVariableDefinition(APPROVAL_BUSINESS_CLASSIFICATION);
        $approval_layer_num = $this->user_management->getApprovalLayerNum(APPROVAL_HIERACHY);
        $conditions['approval_layer_num'] = 0;
        $conditions['approval_work_type'] = 0;
        foreach ($approval_layer_num as $approval_layer) {
            if ($approval_layer->parameter_key == KNOWLEDGE) {
                $conditions['approval_layer_num'] = $approval_layer->parameter_value;
                break;
            }
        }
        foreach ($approval_titles as $approval_title) {
            if ($approval_title->definition_label == KNOWLEDGE) {
                $conditions['approval_work_type'] = $approval_title->definition_value;
                break;
            }
        }
        $conditions['date_system'] = $this->currentJapanDateTime("Y-m-d");
        $conditions['date_system_temp'] = $this->currentJapanDateTime("Y-m-d");
        $conditions['knowledge_id'] = $knowledge_id;
        $conditions['knowledge_admin'] = config('role.KNOWLEDGE_MG.CODE');
        return $this->repo->getKnowledge($conditions);
    }

    public function searchPre($conditions)
    {
        $approval_titles = $this->user_management->getVariableDefinition(APPROVAL_BUSINESS_CLASSIFICATION);
        $approval_layer_num = $this->user_management->getApprovalLayerNum(APPROVAL_HIERACHY);
        $conditions->approval_layer_num = 0;
        $conditions->approval_work_type = 0;
        foreach ($approval_layer_num as $approval_layer) {
            if ($approval_layer->parameter_key == KNOWLEDGE) {
                $conditions->approval_layer_num = $approval_layer->parameter_value;
                break;
            }
        }
        foreach ($approval_titles as $approval_title) {
            if ($approval_title->definition_label == KNOWLEDGE) {
                $conditions->approval_work_type = $approval_title->definition_value;
                break;
            }
        }
        $conditions->current = $this->currentJapanDateTime("Y-m-d");
        $request_users = $this->repo->getRequestUser($conditions);
        $conditions->request_users = [];
        if (!empty($conditions->user_cd)) {
            // ●ロールによる制限事項
            // ◆最終確認者の場合
            // ◆最終確認者以外の承認者の場合
            //      ログインユーザ自身が承認者に設定されているナレッジ元のみ表示。
            // ◆上記以外
            //      ログインユーザ自身が作成したナレッジ元のみ表示。
            foreach ($conditions->user_cd as $user_cd) {
                foreach ($request_users as $r_user_cd) {
                    if ($r_user_cd->request_user_cd == $user_cd) {
                        array_push($conditions->request_users, $r_user_cd->request_user_cd);
                    }
                }
            }
        }else{
            foreach ($request_users as $r_user_cd) {
                array_push($conditions->request_users, $r_user_cd->request_user_cd);
            }
        }
        $conditions->user_login = $conditions->create_user_cd;
        $result = $this->repo->searchPre($conditions);
        foreach ($result['records'] as $data) {
            $data->create_user_cd = empty($data->create_user_cd) ? [] : json_decode($data->create_user_cd, true);
            $data->create_user_cd_together = empty($data->create_user_cd_together) ? [] : json_decode($data->create_user_cd_together, true);
            $data->approval_user_cd = empty($data->approval_user_cd) ? [] : json_decode($data->approval_user_cd, true);
            // 対象のナレッジが匿名に設定されている場合は「匿名」と表示
            // 共同作成者が登録されている場合は後ろに「,（カンマ）」を付与して共同作成者を追加で表示
            if ($data->anonymous_flag == 1) {
                $data->facility_short_name = ANONYMOUS;
                $data->person_name = ANONYMOUS;
                $data->department_name = ANONYMOUS;
                $data->display_position_name = ANONYMOUS;
                $data->create_user_cd = ANONYMOUS;
                $approval_users = "";
                foreach ($data->approval_user_cd as $approval_user) {
                    if(empty($approval_user['org_label'])){
                        $approval_user['org_label'] = NO_AFFILIATION;
                    }
                    $approval_users .= $approval_user['org_label'] . ' ' . $approval_user['user_name'] . ", ";
                }
                $approval_users = rtrim($approval_users, ", ");
                $data->approval_user_cd = $approval_users;
                $data->create_user_cd_together = ANONYMOUS;
            } else {
                if (!empty($data->create_user_cd)) {
                    if(empty($data->create_user_cd[0]['org_label'])){
                        $data->create_user_cd[0]['org_label'] = NO_AFFILIATION;
                    }
                    $data->create_user_cd = $data->create_user_cd[0]['org_label'] . ' ' . $data->create_user_cd[0]['user_name'];
                } else {
                    $data->create_user_cd = "";
                }
                $approval_users = "";
                foreach ($data->approval_user_cd as $approval_user) {
                    if(empty($approval_user['org_label'])){
                        $approval_user['org_label'] = NO_AFFILIATION;
                    }
                    $approval_users .= $approval_user['org_label'] . ' ' . $approval_user['user_name'] . ", ";
                }
                $approval_users = rtrim($approval_users, ", ");
                $data->approval_user_cd = $approval_users;
                $user_togethers = "";
                foreach ($data->create_user_cd_together as $user_together) {
                    if(empty($user_together['org_label'])){
                        $user_together['org_label'] = NO_AFFILIATION;
                    }
                    $user_togethers .= $user_together['org_label'] . ' ' . $user_together['user_name'] . ", ";
                }
                $user_togethers = rtrim($user_togethers, ", ");
                $data->create_user_cd_together = $user_togethers;
            }
        }
        return $result;
    }
}
