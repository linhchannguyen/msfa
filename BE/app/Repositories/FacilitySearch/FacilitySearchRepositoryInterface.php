<?php

namespace App\Repositories\FacilitySearch;

use App\Repositories\BaseRepositoryInterface;

interface FacilitySearchRepositoryInterface extends BaseRepositoryInterface
{
    //List All Facility
    public function allData($request);
    //Facility detail
    public function detailFacility($facility_cd);
    //Facility detail
    public function informationFacility($facility_cd);
    //Facility staff history
    public function staffHistoryFacility($facility_cd);
    //Facility detail
    public function medicalSubjectsFacility($facility_cd);
    //Facility detail
    public function relatedFacility($facility_cd);
    //Facility detail
    public function relationFacility($facility_cd);
    //Facility consultation time
    public function consultationTimeFacility($facility_cd);
    //Save Facility consultation time
    public function saveConsultationTimeFacility($data);
    //get Working Individual
    public function getWorkingIndividual($request);
    //get Segment Individual
    public function getSegmentPerson($person_cd, $facility_cd);
    //get Department
    public function getDepartment();
    //get Segment Type
    public function getSegmentType();
    //delete Segment Type Facility
    public function deleteSegmentType($facility_cd, $person_cd, $segment_type);
    //insert Segment Type Facility
    public function saveSegmentType($facility_cd, $person_cd, $segment_type);

    //update or insert Facility Key Member
    public function saveFacilityKeyMember($facility_cd, $person_cd, $part_type);
}
