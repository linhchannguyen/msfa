<?php

namespace App\Services;

use App\Traits\DateTimeTrait;
use App\Repositories\Inform\InformRepositoryInterface;
use App\Repositories\Organization\OrganizationRepositoryInterface;
use App\Repositories\QAManagement\QAManagementRepositoryInterface;
use stdClass;

class QAManagementService
{
    use DateTimeTrait;
    private $repo, $inform, $organization;

    public function __construct(QAManagementRepositoryInterface $repo, InformRepositoryInterface $inform, OrganizationRepositoryInterface $organization)
    {
        $this->repo = $repo;
        $this->inform = $inform;
        $this->organization = $organization;
    }

    public function getScreenData($user_cd)
    {
        $result['question_status'] = $this->repo->getQuestionStatus();
        $result['question_category'] = $this->repo->getQuestionCategory();
        $result['posting_prohibited'] = $this->repo->checkUserUnsuitableReport($user_cd)->posting_prohibited ?? 0;
        return $result;
    }

    public function checkUserUnsuitableReport($user_cd)
    {
        return $this->repo->checkUserUnsuitableReport($user_cd)->posting_prohibited ?? 0;
    }

    public function createtQA($datas)
    {
        $qa_insert['question_category_cd'] = $datas->question_category_cd;
        $qa_insert['create_user_cd'] = $datas->user_cd;
        $qa_insert['title'] = $datas->title;
        $qa_insert['contents'] = $datas->contents;
        $qa_insert['last_update_datetime'] = $this->currentJapanDateTime();
        //Insert t_question
        $this->repo->insert($qa_insert);
        $last_question_index = $this->repo->lastQuestion();
        $info_org_insert = [];
        $inform_user_cds = [];
        $org_cds = $datas->org_cd;
        $user_list = [];
        foreach($org_cds as $org_cd){
            array_push($info_org_insert, [
                'question_id' => $last_question_index->question_id,
                'org_cd' => $org_cd
            ]);
            $data = new stdClass;
            $data->org_cd = $org_cd;
            $user_list = $this->organization->allUser($data);
            if(!empty($user_list)){
                foreach($user_list as $user_org){
                    if(!in_array($user_org->user_cd, $inform_user_cds)){
                        array_push($inform_user_cds, $user_org->user_cd);
                        $not_received_inform_list = $this->inform->installed($user_org->user_cd, REGISTER_A_QUESTION_INFORM_CATEGORY);
                        //Check if the user is set to receive notification type
                        $check_not_received = 0;
                        foreach($not_received_inform_list as $value){
                            if($value->checked == 0){
                                $check_not_received++;
                            }
                        }
                        if($check_not_received == 0){
                            $inform_contents = __('qa.inform_contents', ['attribute' => $datas->title]);
                            //Insert t_inform, t_inform_parameter
                            if($datas->user_cd != $user_org->user_cd){
                                $inform_id = $this->inform->registerInform($datas->user_cd, $user_org->user_cd, $inform_contents, REGISTER_A_QUESTION_INFORM_CATEGORY);
                                $this->inform->registerInformParameter($datas->user_cd, $inform_id, QUESTION_ID, $last_question_index->question_id);
                            }
                        }
                    }
                }
            }
        }
        //Insert t_question_imformation_org
        if(!empty($info_org_insert)){
            $this->repo->insertQAInfoOrg($info_org_insert);
        }
        //Insert t_active_point
        $active_point_insert['point_target_type'] = UTILIZATION_POINT_TARTGET_CLASSIFICATION;
        $active_point_insert['point_target_id'] = $last_question_index->question_id;
        $active_point_insert['active_point'] = QA_QUESTION_REGISTRATION;
        $active_point_insert['message'] = QUESTION_ACTIVE_POINT_MESSAGE_REGISTER;
        $active_point_insert['receive_user_cd'] = $datas->user_cd;
        $this->repo->insertActivePoint($active_point_insert);
    }

    public function searchQA($conditions)
    {
        $datas = $this->repo->getQA($conditions);
        foreach($datas['records'] as &$data){
            $answer_note = empty($data->answer_note) ? [] : json_decode($data->answer_note, true);
            array_multisort(
                array_column($answer_note, 'last_update_datetime'),
                SORT_DESC,
                $answer_note
            );
            $data->answer_note = [];
            if(!empty($answer_note)){
                array_push($data->answer_note, $answer_note[0]);
            }
        }
        return $datas;
    }
}
