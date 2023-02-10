<?php

namespace App\Services;

use App\Repositories\PersonSearch\PersonSearchRepositoryInterface;
use App\Repositories\FacilitySearch\FacilitySearchRepositoryInterface;

class PersonSearchService
{
    private $repo, $facility;

    public function __construct(PersonSearchRepositoryInterface $repo, FacilitySearchRepositoryInterface $facility)
    {
        $this->repo = $repo;
        $this->facility = $facility;
    }

    //Get All Working Individual Person
    public function getDataPerson($request)
    {
        $result = $this->repo->allData($request);
        foreach ($result['records'] as $item) {
            $item->person_consideration_confirm = json_decode($item->person_consideration_confirm, true) ?? [];
            array_multisort(
                array_column($item->person_consideration_confirm, 'last_update_datetime'),
                SORT_DESC,
                $item->person_consideration_confirm
            );
            $item->last_update_datetime = $item->person_consideration_confirm[0]['last_update_datetime'] ?? null;
            $person_consideration_confirm = [];
            foreach($item->person_consideration_confirm as $value){
                if($value['user_cd']){
                    array_push($person_consideration_confirm, $value);
                }
            }
            $item->person_consideration_confirm = $person_consideration_confirm;
        }
        return $result;
    }

    //Get Person Information
    public function getPersonInformation($request)
    {
        return $this->repo->getPersonInformation($request->person_cd, $request->ultmarc_delete_flag);
    }

    //Get Person Detail
    public function getPersonDetail($request)
    {
        $result['detail'] = $this->repo->getPersonDetail($request->person_cd);
        $result['medical_treatment_subjects'] = $this->repo->getMedicalTreatmentSubjects($request->person_cd);
        $result['list_academic_societies'] = $this->repo->getListAcademicSocieties($request->person_cd);
        $result['list_specialist_qualification'] = $this->repo->getListSpecialistQualification($request->person_cd);
        $result['medical_office_name'] = $this->repo->getMedicalOfficeName($request->person_cd);
        return $result;
    }

    //Get Person Detail
    public function getDepartmentPersonDetail($request)
    {
        return $this->repo->getDepartmentPersonDetail($request->facility_cd);
    }

    //save Medical Office
    public function saveMedicalOffice($request)
    {
        return $this->repo->saveMedicalOffice($request);
    }

    //Get All Working Individual Person
    public function getWorkingIndividual($request)
    {
        return $this->repo->getWorkingIndividual($request);
    }
}
