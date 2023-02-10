<?php

namespace App\Services;

use App\Repositories\FacilitySearch\FacilitySearchRepositoryInterface;
use App\Traits\Base64StringFileTrait;

class FacilitySearchService
{
    use Base64StringFileTrait;
    private $repo;

    public function __construct(FacilitySearchRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    //List All Facility
    public function getData($request)
    {
        $result = $this->repo->allData($request);
        foreach ($result['records'] as $item) {
            $item->facility_consideration_confirm = json_decode($item->facility_consideration_confirm, true) ?? [];
            array_multisort(
                array_column($item->facility_consideration_confirm, 'last_update_datetime'),
                SORT_DESC,
                $item->facility_consideration_confirm
            );
            $item->last_update_datetime = $item->facility_consideration_confirm[0]['last_update_datetime'] ?? null;
            $facility_consideration_confirm = [];
            foreach($item->facility_consideration_confirm as $value){
                if($value['user_cd']){
                    array_push($facility_consideration_confirm, $value);
                }
            }
            $item->facility_consideration_confirm = $facility_consideration_confirm;
        }
        return $result;
    }

    //Facility Information
    public function getFacilityInformation($request)
    {
        return $this->repo->informationFacility($request->facility_cd);
    }

    //Facility detail
    public function getFacilityDetail($request)
    {
        $result['detail'] = $this->repo->detailFacility($request->facility_cd);
        $staff_history = $this->repo->staffHistoryFacility($request->facility_cd);
        foreach ($staff_history as $value) {
            $value->file_url = $this->encodeBase64String($value->file_url);
        }
        $result['staff_history'] = $staff_history;
        $result['medical_subjects'] = $this->repo->medicalSubjectsFacility($request->facility_cd);
        $result['related'] = $this->repo->relatedFacility($request->facility_cd);
        $result['parent'] = $this->repo->relationFacility($request->facility_cd);
        $result['consultation_time'] = $this->repo->consultationTimeFacility($request->facility_cd);
        return $result;
    }

    //Save Consultation Time Facility
    public function saveConsultationTimeFacility($request)
    {
        return $this->repo->saveConsultationTimeFacility($request);
    }

    //List All Facility
    public function getIndexWorkingIndividualFacility()
    {
        $result['department'] = $this->repo->getDepartment();
        $result['segment_list'] = $this->repo->getSegmentType();
        return $result;
    }

    //List All Facility
    public function getWorkingIndividualFacility($request)
    {
        $data = $this->repo->getWorkingIndividual($request);
        // segment_list
        foreach ($data['records'] as $item) {
            $item->segment_list = $this->repo->getSegmentPerson($item->person_cd, $item->facility_cd);
        }
        return $data;
    }

    //List All Facility
    public function saveSegmentTypeFacility($request)
    {
        if (count($request->facility_list) > 0) {
            foreach ($request->facility_list as $item) {
                $facility_list = (object)$item;
                //update or insert Facility Key Member
                if ($facility_list->part_type) {
                    $this->repo->saveFacilityKeyMember($facility_list->facility_cd, $facility_list->person_cd, 10);
                } else {
                    $this->repo->saveFacilityKeyMember($facility_list->facility_cd, $facility_list->person_cd, 0);
                }
                if (count($facility_list->segment_list) > 0) {
                    foreach ($facility_list->segment_list as $element) {
                        $segment = (object)$element;
                        if ($segment->delete_flag === 1) {
                            $this->repo->saveSegmentType($facility_list->facility_cd, $facility_list->person_cd, $segment->segment_type);
                        }
                        if ($segment->delete_flag === -1) {
                            $this->repo->deleteSegmentType($facility_list->facility_cd, $facility_list->person_cd, $segment->segment_type);
                        }
                    }
                }
            }
        }
        return $request;
    }
}
