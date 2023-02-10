<?php

namespace App\Services;

use App\Repositories\TargetFacilityPersonSetting\TargetFacilityPersonSettingRepositoryInterface;
use App\Traits\ArrayTrait;
use stdClass;

class TargetFacilityPersonSettingService
{
    use ArrayTrait;
    private $repo, $serviceAccessLog;

    public function __construct(TargetFacilityPersonSettingRepositoryInterface $repo, LogService $serviceAccessLog)
    {
        $this->repo = $repo;
        $this->serviceAccessLog = $serviceAccessLog;
    }

    public function getScreenData($date_system)
    {
        $targetProduct = $this->repo->getTargetProduct($date_system);
        $segmentType = $this->repo->getSegmentType($date_system);
        $facilityTypeGroup = $this->repo->getFacilityTypeGroup();
        $parameterAddStock = $this->repo->parameterAddStock();
        return ['targetProduct' => $targetProduct, 'segmentType' => $segmentType, 'facilityTypeGroup' => $facilityTypeGroup, 'parameterAddStock' => $parameterAddStock];
    }

    public function getTargetFacilityPersonSetting($user_cd, $date_system, $facility_type_group_cd, $person_name, $target_product_cd, $segment_type, $sort_order , $limit, $offset)
    {
        $segment_list = [];

        $order_segment = new stdClass;
        $order_target_product = new stdClass;
        if(count($sort_order) > 0){
            foreach($sort_order as $sort_order_item){
                $sort_order_item = (object)$sort_order_item;
                if($sort_order_item->order_type == 'segment'){
                    $order_segment->order_value = $sort_order_item->order_value;
                    $order_segment->order_type = $sort_order_item->order_type;
                }elseif($sort_order_item->order_type == 'target_product'){
                    $order_target_product->order_value = $sort_order_item->order_value;
                    $order_target_product->order_type = $sort_order_item->order_type;
                }
            }
        }

        $facilityPerson = $this->repo->getFacilityPerson($user_cd, $facility_type_group_cd, $person_name, $target_product_cd, $segment_type, $order_segment , $order_target_product , $limit, $offset);
        $facility_list = array_unique(array_column($facilityPerson['records'], 'facility_cd'));
        $person_of_facility = [];
        if (count($facility_list) > 0) {
            foreach ($facility_list as $facility_item) {
                $person_cd = [];
                foreach ($facilityPerson['records'] as $facility_person) {
                    $facility_person = (object)$facility_person;
                    if ($facility_item == $facility_person->facility_cd) {
                        $person_cd[] = $facility_person->person_cd;
                    }
                }
                $person_of_facility[] = [
                    'facility_cd' => $facility_item,
                    'person_cd' => $person_cd
                ];
            }
        }

        if (count((array)$person_of_facility) > 0) {
            foreach ($person_of_facility as $item) {
                $item = (object)$item;
                if (count($item->person_cd) > 0) {
                    $segment_list_temp = $this->repo->getSegment($item->facility_cd, $item->person_cd, $segment_type, $date_system);
                    $target_product_temp = $this->repo->getTargetProductList($item->facility_cd, $item->person_cd, $target_product_cd);
                }
                $segment_list[] = [
                    'facility_cd' => $item->facility_cd,
                    'segment_list' => $segment_list_temp,
                    'target_product_list' => $target_product_temp
                ];
            }
        }

        foreach ($facilityPerson['records'] as $facility_person_list) {
            $facility_person_list = (object)$facility_person_list;
            $facility_person_list->segment_list = [];
            $facility_person_list->target_product_list = [];
            $facility_person_list->sub_target = !empty($facility_person_list->sub_target) ? json_decode($facility_person_list->sub_target) : [];
            if (count((array)$segment_list) > 0) {
                $facility_cd_check = array_search($facility_person_list->facility_cd, array_column($segment_list, 'facility_cd'));
                if ($facility_cd_check !== false) {
                     // segment_list
                    $person_cd_check = array_search($facility_person_list->person_cd, array_column($segment_list[$facility_cd_check]['segment_list'], 'person_cd'));
                    if ($person_cd_check !== false) {
                        $facility_person_list->segment_list = !empty($segment_list[$facility_cd_check]['segment_list'][$person_cd_check]->segment ?? '') ? json_decode($segment_list[$facility_cd_check]['segment_list'][$person_cd_check]->segment ?? '') : [];
                    }
                    // target_product_list
                    $person_cd_check = array_search($facility_person_list->person_cd, array_column($segment_list[$facility_cd_check]['target_product_list'], 'person_cd'));
                    if ($person_cd_check !== false) {
                        $facility_person_list->target_product_list = !empty($segment_list[$facility_cd_check]['target_product_list'][$person_cd_check]->target_product ?? '') ? json_decode($segment_list[$facility_cd_check]['target_product_list'][$person_cd_check]->target_product ?? '') : [];
                    }
                }
            }
        }
        return $facilityPerson;
    }

    public function exportTargetFacilityPersonSetting($user_cd, $date_system, $facility_type_group_cd, $person_name, $target_product_cd, $segment_type , $sort_order)
    {
        $order_segment = new stdClass;
        $order_target_product = new stdClass;
        if(count($sort_order) > 0){
            foreach($sort_order as $sort_order_item){
                $sort_order_item = (object)$sort_order_item;
                if($sort_order_item->order_type == 'segment'){
                    $order_segment->order_value = $sort_order_item->order_value;
                    $order_segment->order_type = $sort_order_item->order_type;
                }elseif($sort_order_item->order_type == 'target_product'){
                    $order_target_product->order_value = $sort_order_item->order_value;
                    $order_target_product->order_type = $sort_order_item->order_type;
                }
            }
        }

        $facilityPerson = $this->repo->exportFacilityPerson($user_cd, $facility_type_group_cd, $person_name, $target_product_cd, $segment_type, $date_system , $order_segment , $order_target_product );
        if (count($facilityPerson) > 0) {
            foreach ($facilityPerson as $dataItem) {
                $dataItem = (object)$dataItem;
                $sub_target = !empty($dataItem->sub_target) ? json_decode($dataItem->sub_target) : [];
                $dataItem->segment_list = !empty($dataItem->segment_list) ? json_decode($dataItem->segment_list) : [];

                $dataItem->target_product_list = !empty($dataItem->target_product_list) ? json_decode($dataItem->target_product_list) : [];
                $target_product_cd_sub = array_column($sub_target,'target_product_cd');
                if(count($dataItem->target_product_list) > 0){
                    foreach($dataItem->target_product_list as $temp){
                        $temp->sub_target = "";
                        $key = array_search($temp->target_product_cd , $target_product_cd_sub);
                        if($key !== false){
                            $temp->sub_target = $sub_target[$key]->sub_target;
                        }
                    }
                }
            }
        }
        return $facilityPerson;
    }

    public function saveSegmentList($facility_cd, $person_cd, $segment_list)
    {
        $this->repo->deleteSegment($facility_cd, $person_cd);
        if (count($segment_list) > 0) {
            foreach ($segment_list as $segment_item) {
                $segment_item = (object)$segment_item;
                $this->repo->insertSegment($facility_cd, $person_cd, $segment_item->segment_type);
            }
        }
        return true;
    }

    public function saveTargetProductList($facility_cd, $person_cd, $target_product_list)
    {
        $this->repo->deleteTargetProduct($facility_cd, $person_cd);
        if (count($target_product_list) > 0) {
            foreach ($target_product_list as $target_product_item) {
                $target_product_item = (object)$target_product_item;
                $this->repo->insertTargetProduct($facility_cd, $person_cd, $target_product_item->target_product_cd);
            }
        }
        return true;
    }
}
