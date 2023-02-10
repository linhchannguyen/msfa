<?php

namespace App\Repositories\InFacilityTargetPersonManagement;

use App\Repositories\BaseRepositoryInterface;

interface InFacilityTargetPersonManagementRepositoryInterface extends BaseRepositoryInterface
{
    public function parameterAddStock();
    public function getFacilityInfo($facility_cd);
    public function getTargetProduct($date_system);
    public function getSegmentType($date_system);
    public function getDepartment($facility_cd);
    public function getSubTarget($facility_cd);
    public function getPerson($facility_cd, $department_cd, $person_name, $target_product_cd, $segment_type, $order_segment, $order_target_product, $limit, $offset);
    public function getSegment($facility_cd, $person_cd, $segment_type, $date_system);
    public function getTargetProductList($facility_cd, $person_cd, $target_product_cd);

    public function deleteSegment($facility_cd, $person_cd);
    public function insertSegment($facility_cd, $person_cd, $segment_type);

    public function deleteTargetProduct($facility_cd, $person_cd);
    public function insertTargetProduct($facility_cd, $person_cd, $target_product_cd);


    // export
    public function getPersonExport($facility_cd, $department_cd, $person_name, $target_product_cd, $segment_type, $order_segment, $order_target_product);
}
