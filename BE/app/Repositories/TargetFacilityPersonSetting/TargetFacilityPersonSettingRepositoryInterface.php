<?php

namespace App\Repositories\TargetFacilityPersonSetting;

use App\Repositories\BaseRepositoryInterface;

interface TargetFacilityPersonSettingRepositoryInterface extends BaseRepositoryInterface
{
    public function parameterAddStock();
    public function getTargetProduct($date_system);
    public function getSegmentType($date_system);

    public function getFacilityTypeGroup();
    public function getFacilityPerson($user_cd, $facility_type_group_cd, $person_name, $target_product_cd, $segment_type, $order_segment, $order_target_product, $limit, $offset);
    public function getSegment($facility_cd, $person_cd_list, $segment_type, $date_system);
    public function getTargetProductList($facility_cd, $person_cd, $target_product_cd);

    public function deleteSegment($facility_cd, $person_cd);
    public function insertSegment($facility_cd, $person_cd, $segment_type);

    public function deleteTargetProduct($facility_cd, $person_cd);
    public function insertTargetProduct($facility_cd, $person_cd, $target_product_cd);

    // export
    public function exportFacilityPerson($user_cd, $facility_type_group_cd, $person_name, $target_product_cd, $segment_type, $date_system , $order_segment , $order_target_product );
}
