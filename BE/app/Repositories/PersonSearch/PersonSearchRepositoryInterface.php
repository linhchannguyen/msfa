<?php

namespace App\Repositories\PersonSearch;

use App\Repositories\BaseRepositoryInterface;

interface PersonSearchRepositoryInterface extends BaseRepositoryInterface
{
    //Get All Working Individual Person
    public function allData($reuqest);

    //Get Person Information
    public function getPersonInformation($person_cd, $ultmarc_delete_flag);

    //Get Person Detail
    public function getPersonDetail($person_cd);

    //Get Medical Treatment Subjects
    public function getMedicalTreatmentSubjects($person_cd);

    //Get List Academic Societies
    public function getListAcademicSocieties($person_cd);

    //Get List Specialist Qualification
    public function getListSpecialistQualification($person_cd);

    //Get List Specialist Qualification
    public function getMedicalOfficeName($person_cd);

    //save Medical Office
    public function saveMedicalOffice($request);

    //Get All Working Individual Person
    public function getWorkingIndividual($request);

    //get Department Person Detail
    public function getDepartmentPersonDetail($facility_cd);
}
