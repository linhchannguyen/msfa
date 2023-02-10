<?php

namespace App\Services;

use App\Repositories\FacilityDetailsNotes\FacilityDetailsNotesRepositoryInterface;
use App\Repositories\Inform\InformRepositoryInterface;
use App\Traits\ArrayTrait;

class FacilityDetailsNotesService
{
    use ArrayTrait;
    private $repo;

    public function __construct(FacilityDetailsNotesRepositoryInterface $repo, InformRepositoryInterface $inform)
    {
        $this->repo = $repo;
        $this->inform = $inform;
    }

    public function getScreenData()
    {
        return $this->repo->getScreenData();
    }

    public function getFacilityDetailsNotes($facility_cd, $content, $start_date, $end_date, $consideration_type)
    {
        $datas =  $this->repo->getFacilityDetailsNotes($facility_cd, $content, $start_date, $end_date, $consideration_type);
        if (count($datas) > 0) {
            foreach ($datas as $data) {
                $data = (object)$data;
                $data->announcement = !empty($data->announcement) ? json_decode($data->announcement) : [];
                $data->m_facility_user = !empty($data->m_facility_user) ? json_decode($data->m_facility_user) : [];
            }
        }
        return $datas;
    }

    public function saveConsiderationConfirm($facility_consideration_id, $confirmed_flag,$user_cd,$confirmed_datetime)
    {
        return $this->repo->saveConsiderationConfirm($facility_consideration_id, $confirmed_flag,$user_cd,$confirmed_datetime);
    }

    public function deleteConsideration($facility_consideration_id)
    {
        return $this->repo->deleteConsideration($facility_consideration_id);
    }

    public function saveNewMode($facility_cd, $consideration_type, $confirmation_request_destination, $consideration_contents,$data_system , $data_user , $facility_short_name)
    {
        $user_cd_login = $data_user->user_cd;
        $user_name_login = $data_user->user_name;
        $org_cd_login = $data_user->org_cd;
        $org_label_login = $data_user->org_label;
        
        $facility_consideration = $this->repo->saveFacilityConsiderationNewMode($facility_cd, $consideration_type, $consideration_contents, $user_cd_login, $user_name_login, $org_cd_login, $org_label_login, $data_system);
        $m_inform_category = $this->repo->getInformCategory();
        $inform_category_cd = $m_inform_category->inform_category_cd ?? '';

        if (count($confirmation_request_destination) > 0) {
            foreach ($confirmation_request_destination as $user_cd) {
                $confirmed_flag = 0;
                $this->repo->facilityConsiderationConfirmNewMode($facility_consideration, $user_cd, $confirmed_flag);
                $inform_contents = __('facilitydetailsnotes.confirmation_request_destination', ['user_name' => $user_name_login, 'facility_name' => $facility_short_name]);
                $this->createInform($facility_cd, $inform_contents, $user_cd, $inform_category_cd);
            }
        }
        return true;
    }

    public function createInform($facility_cd, $inform_contents, $user_cd, $inform_category_cd)
    {
        $not_received_inform_list = $this->inform->installed($user_cd);
        //Check if the user is set to receive notification type
        foreach ($not_received_inform_list as $value) {
            if ($value->inform_category_cd == $inform_category_cd && $value->checked == 0) {
                return true;
            }
        }
        $inform_id = $this->inform->registerInform($user_cd, $user_cd, $inform_contents, $inform_category_cd);
        $this->inform->registerInformParameter($user_cd, $inform_id, 'facility_cd', $facility_cd);
        return  true;
    }

    public function updateMode($facility_cd, $facility_consideration_id, $consideration_type, $confirmation_request_destination, $confirmation_request_delete, $consideration_contents, $data_user_login , $data_system , $facility_short_name)
    {
        $user_cd_login = $data_user_login->user_cd;
        $user_name_login = $data_user_login->user_name;
        $org_cd_login = $data_user_login->org_cd;
        $org_label_login = $data_user_login->org_label;

        $this->repo->updateFacilityConsideration($facility_cd, $facility_consideration_id, $consideration_type, $consideration_contents, $user_cd_login, $user_name_login, $org_cd_login, $org_label_login, $data_system);
        $m_inform_category = $this->repo->getInformCategory();
        $inform_category_cd = $m_inform_category->inform_category_cd ?? '';

        if (count($confirmation_request_delete) > 0) {
            $confirmation_request_delete = array_column($confirmation_request_delete, 'user_cd');
            $this->repo->deleteFacilityConsiderationConfirm($facility_consideration_id, $confirmation_request_delete);
        }

        if (count($confirmation_request_destination) > 0) {
            foreach ($confirmation_request_destination as $item) {
                $item = (object)$item;
                $confirmed_flag = 0;
                // create or update 
                $data = $this->repo->checkFacilityConsiderationConfirm($facility_consideration_id, $item->user_cd);
                if (empty($data->facility_consideration_id ?? '')) {
                    // create 
                    $this->repo->facilityConsiderationConfirmNewMode($facility_consideration_id, $item->user_cd, $confirmed_flag);
                    $inform_contents = __('facilitydetailsnotes.confirmation_request_destination', ['user_name' => $user_name_login, 'facility_name' => $facility_short_name]);
                    $this->createInform($facility_cd, $inform_contents, $item->user_cd, $inform_category_cd);
                }else{
                    $this->repo->facilityConsiderationConfirmUpdateMode($facility_consideration_id, $item->user_cd, $confirmed_flag);
                }
            }
        }
        return true;
    }
}
