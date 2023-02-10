<?php

namespace App\Services;

use App\Repositories\InterviewDetailedInput\InterviewDetailedInputRepositoryInterface;
use App\Traits\ArrayTrait;
use App\Services\AuthService;
use App\Traits\StatusReportTrait;
use App\Traits\DateTimeTrait;
use App\Services\SystemParameterService;

class InterviewDetailedInputService
{
    use ArrayTrait, StatusReportTrait, DateTimeTrait;
    private $repo, $systemParameterService;

    public function __construct(InterviewDetailedInputRepositoryInterface $repo, AuthService $auth, SystemParameterService $SystemParameterService)
    {
        $this->repo = $repo;
        $this->auth = $auth;
        $this->systemParameterService = $SystemParameterService;
    }


    public function getScreenData()
    {
        return $this->repo->getScreenData();
    }

    public function getInterviewDetailedInput($call_id, $schedule_id)
    {
        $data = [];
        $interviewDateTime = $this->repo->getInterviewDateTime($call_id, $schedule_id);
        if (!empty($interviewDateTime->visit_id ?? '')) {
            $interviewDateTime->report_status_type = $this->getStatusTypeReport($interviewDateTime->report_id ?? '');
        }

        $data['interviewDateTime'] = $interviewDateTime;
        $offLabel = $this->repo->getOffLabel($call_id, $schedule_id);
        $data['offLabel'] = $offLabel;
        $detailArea = $this->repo->detailArea($call_id, $schedule_id);
        $data['detailArea'] = $detailArea;
        
        if (count($data['detailArea']) > 0) {
            foreach ($data['detailArea'] as $item) {
                $item = (object)$item;
                $item->delete_flag = $item->change_flag = 0;
                $item->materials = !empty($item->materials) ? json_decode($item->materials) : [];
            }
        }
        return $data;
    }

    public function getDetailArea($detail_id)
    {
        $data = $this->repo->getDetailArea($detail_id);
        if (!empty($data->materials ?? '')) {
            $data->materials = !empty($data->materials) ? json_decode($data->materials) : [];
            if (count($data->materials) > 0) {
                foreach ($data->materials as $item) {
                    $item = (object)$item;
                    $item->delete_flag = $item->change_flag = 0;
                }
            }
        }
        return $data;
    }

    public function savePlan($schedule_id, $call_id)
    {
        $this->repo->updateActFlag($call_id);
        $t_call = $this->repo->getCall($call_id);
        $t_visit = $this->repo->getVisit($schedule_id,$t_call->visit_id);

        $display_option_type_temp = $t_call->plan_flag . $t_call->act_flag . $t_visit->important_flag;

        switch ($display_option_type_temp) {
            case '000':
                $display_option_type = 'C00';
                break;
            case '100':
                $display_option_type = 'C01';
                break;
            case '010':
                $display_option_type = 'C02';
                break;
            case '110':
                $display_option_type = 'C03';
                break;
            case '001':
                $display_option_type = 'C10';
                break;
            case '101':
                $display_option_type = 'C11';
                break;
            case '011':
                $display_option_type = 'C12';
                break;
            case '111':
                $display_option_type = 'C13';
                break;
            default:
                $display_option_type = 'C01';
        }

        return $this->repo->updateSchedule($schedule_id, $display_option_type);
    }

    public function saveCall($schedule_id, $call_id, $person_cd, $off_label_flag, $off_label_concent_flag, $off_label_call_time, $related_product, $question, $answer, $re_question, $literature, $person_name)
    {
        $date_system = date_create($this->systemParameterService->getCurrentSystemDateTime())->format('Y-m-d');

        $t_schedule = $this->repo->getSchedule($schedule_id);
        $start_date = $t_schedule->start_date ?? date("Y-m-d");

        if ($start_date > $date_system) {
            $act_flag  = 0;
        } elseif ($start_date <= $date_system) {
            $act_flag  = 1;
        }
        $person_info = $this->repo->getInfoPerson($person_cd);
        return $this->repo->updateCall($call_id, $off_label_flag, $off_label_concent_flag, $off_label_call_time, $related_product, $question, $answer, $re_question, $literature, $act_flag, $person_info, $person_name);
    }

    public function saveDetailArea($facility_cd, $person_cd, $start_date, $schedule_id, $call_id, $detailArea, $user_cd , $deleteArray , $start_time)
    {
        if(is_array($deleteArray) && count($deleteArray) > 0){
            foreach($deleteArray as $delete_item){
                $delete_item = (object)$delete_item;
                $this->repo->deleteDetail($delete_item->detail_id,$facility_cd, $person_cd,$delete_item->product_cd,$start_date);
            }
        }

        //sort start date
        if (is_array($detailArea) && count($detailArea) > 0) {
            foreach ($detailArea as $item) {
                $item = (object)$item;
                $check_detail = $this->repo->checkDetail($item->detail_id, $call_id);
                $detail_id_tem = 0;
                if($check_detail->sum > 0){
                    // update t_detail
                    $this->repo->updateDetail($item->detail_id ?? '', $call_id, $item->detail_order, $item->content_cd, $item->content_name_tran, $item->product_cd, $item->product_name, $item->note, $item->remarks, $item->phase_cd, $item->phase, $item->reaction_cd, $item->reaction, $item->prescription_count);
                    $detail_id_tem = $item->detail_id;
                }else{
                    // insert t_detail
                    $t_detail = $this->repo->insertDetail($call_id, $item->detail_order, $item->content_cd, $item->content_name_tran, $item->product_cd, $item->product_name, $item->note, $item->remarks, $item->phase_cd, $item->phase, $item->reaction_cd, $item->reaction, $item->prescription_count);
                    $detail_id_tem = $t_detail->detail_id;
                }

                $this->updateDetailDocument($detail_id_tem, $item->materials, $call_id, $user_cd , $start_time , $item->delete_materials ?? []);

                // update t_reaction_cd
                $this->updateReaction($facility_cd, $person_cd, $start_date,$item->product_cd,$item->reaction_cd);
               
                // update t_phase
                $this->updatePhase($facility_cd, $person_cd, $start_date,$item->phase_cd,$item->product_cd);
                
                // update t_prescription
                $this->updatePrescription($facility_cd, $person_cd, $start_date,$item->prescription_count,$item->product_cd);

            }
        }
        // return $this->savePlan($schedule_id, $call_id);
    }

    public function updateDetailDocument($detail_id, $materials, $call_id, $user_cd , $start_time , $delete_materials)
    {
        // delete all t_detail_document of detail_id
        $this->repo->deleteDetailDocument($detail_id);
        if(count($delete_materials) > 0){
            $this->repo->deleteDocumentUsageSituation($delete_materials,$call_id);
        }

        if (count($materials) > 0) {
            foreach ($materials as $item) {
                $item = (object)$item;
                // insert t_detail_document
                $this->repo->insertDetailDocument($detail_id, $item->document_id);
                // update t_document_usage_situation
                $data_user = $this->auth->getInfoUser($user_cd);
                $usage_org_label = $data_user->org_label;
                $usage_user_cd = $data_user->user_cd;
                $usage_user_name = $data_user->user_name;

                $m_variable_definition = $this->repo->getMasterVariable();
                $t_document_usage_situation = $this->repo->getDocumentUsageSituation($item->document_id,$call_id);
                if(count($t_document_usage_situation) == 0){
                    $this->repo->insertDocumentUsageSituation($item->document_id,$m_variable_definition->definition_value ?? '',$call_id,$usage_org_label,$usage_user_cd,$usage_user_name,$start_time);
                }
            }
        }
    }

    public function updateReaction ($facility_cd, $person_cd, $start_date,$product_cd,$reaction_cd)
    {
        $t_reaction_cd = $this->repo->checkReaction($facility_cd, $person_cd, $product_cd, $reaction_cd);

        if (empty($t_reaction_cd->facility_cd ?? '')) {
            if(!empty($reaction_cd)){
                $this->repo->insertReaction($facility_cd, $person_cd, $start_date, $product_cd, $reaction_cd);
            }
        } else {
            if (date_create($start_date)->format('Y-m-d') >= date_create($t_reaction_cd->final_content_date)->format('Y-m-d')) {
                $this->repo->updateReaction($facility_cd, $person_cd, $start_date, $product_cd, $reaction_cd);
            }
        }
    }

    public function updatePhase ($facility_cd, $person_cd, $start_date,$phase_cd,$product_cd)
    {
        $t_phase = $this->repo->checkPhase($facility_cd, $person_cd, $product_cd);
        if (empty($t_phase->facility_cd ?? '')) {
            if(!empty($phase_cd)){
                $this->repo->insertPhase($facility_cd, $person_cd, $start_date, $phase_cd, $product_cd);
            }
        } else {
            if (date_create($start_date)->format('Y-m-d') >= date_create($t_phase->final_content_date)->format('Y-m-d')) {
                $this->repo->updatePhase($facility_cd, $person_cd, $start_date, $phase_cd, $product_cd);
            }
        }
    }

    public function updatePrescription($facility_cd, $person_cd, $start_date,$prescription_count,$product_cd)
    {
        $t_prescription = $this->repo->checkPrescription($facility_cd, $person_cd, $product_cd);
        if ( empty($t_prescription->facility_cd ?? '')) {
            if(!empty($prescription_count)){
                $this->repo->insertPrescription($facility_cd, $person_cd, $start_date, $prescription_count, $product_cd);
            }
        } else {
            if (date_create($start_date)->format('Y-m-d') >= date_create($t_prescription->final_content_date)->format('Y-m-d')) {
                $this->repo->updatePrescription($facility_cd, $person_cd, $start_date, $prescription_count, $product_cd);
            }
        }
    }
}
