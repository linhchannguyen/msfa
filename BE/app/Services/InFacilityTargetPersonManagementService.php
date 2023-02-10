<?php

namespace App\Services;

use App\Repositories\InFacilityTargetPersonManagement\InFacilityTargetPersonManagementRepositoryInterface;
use App\Traits\ArrayTrait;
use stdClass;

class InFacilityTargetPersonManagementService
{
    use ArrayTrait;
    private $repo;

    public function __construct(InFacilityTargetPersonManagementRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getScreenData($date_system, $facility_cd)
    {
        $facilityInfo = $this->repo->getFacilityInfo($facility_cd);
        $targetProduct = $this->repo->getTargetProduct($date_system);
        $segmentType = $this->repo->getSegmentType($date_system);
        $department = $this->repo->getDepartment($facility_cd);
        $subTarget = $this->repo->getSubTarget($facility_cd);
        $parameterAddStock = $this->repo->parameterAddStock();

        return ['targetProduct_a3' => $targetProduct, 'segmentType_c7' => $segmentType, 'department_c2' => $department, 'subTarget' => $subTarget, 'facilityInfo' => $facilityInfo, 'parameterAddStock' => $parameterAddStock];
    }

    public function getInFacilityTargetPersonManagement($facility_cd, $date_system, $department_cd, $person_name, $target_product_cd, $segment_type, $sort_order, $limit, $offset)
    {
        $datas = $result = [];
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

        $person_list = $this->repo->getPerson($facility_cd, $department_cd, $person_name, $target_product_cd, $segment_type, $order_segment, $order_target_product, $limit, $offset);
        $person_cd_list = array_column($person_list['records'], 'person_cd');
        if (count($person_cd_list) > 0) {
            $segment_list = $this->repo->getSegment($facility_cd, $person_cd_list, $segment_type, $date_system);
            $target_product_list = $this->repo->getTargetProductList($facility_cd, $person_cd_list, $target_product_cd);
            if (count($person_list['records']) > 0) {
                foreach ($person_list['records'] as $item_list) {
                    $item_list = (object)$item_list;
                    $segment_list_data = [];
                    $target_product_list_data = [];

                    if (count((array)$segment_list) > 0) {
                        $segment_list_key = array_search($item_list->person_cd, array_column($segment_list,'person_cd'));
                        if ($segment_list_key !== false) {
                            $segment_list_data = !empty($segment_list[$segment_list_key]->segment ?? '') ? json_decode($segment_list[$segment_list_key]->segment) : [];
                        }
                    }

                    if (count((array)$target_product_list) > 0) {
                        $target_product_list_key = array_search($item_list->person_cd, array_column($target_product_list,'person_cd'));
                        if ($target_product_list_key !== false) {
                            $target_product_list_data = !empty($target_product_list[$target_product_list_key]->target_product ?? '') ? json_decode($target_product_list[$target_product_list_key]->target_product) : [];
                        }

                    }

                    $result[] = [
                        'facility_cd' => $item_list->facility_cd,
                        'facility_name' => $item_list->facility_name,
                        'facility_short_name' => $item_list->facility_short_name,
                        'person_cd' => $item_list->person_cd,
                        'person_name' => $item_list->person_name,
                        'department_name' => $item_list->department_name,
                        'position_name' => $item_list->position_name,
                        'segment_list' => $segment_list_data,
                        'target_product_list' => $target_product_list_data
                    ];
                }
            }
        }
        $datas['records'] = $result;
        $datas['pagination'] = $person_list['pagination'];
        return $datas;
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

    public function exportInFacilityTargetPersonManagement($facility_cd, $date_system, $department_cd, $person_name, $target_product_cd, $segment_type, $sort_order)
    {
        $datas = $result = [];
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

        $person_list = $this->repo->getPersonExport($facility_cd, $department_cd, $person_name, $target_product_cd, $segment_type, $order_segment, $order_target_product);
        $person_cd_list = array_column($person_list, 'person_cd');
        if (count($person_cd_list) > 0) {
            $segment_list = $this->repo->getSegment($facility_cd, $person_cd_list, $segment_type, $date_system);
            $segment_list_column = array_column($segment_list, 'person_cd');
            $target_product_list = $this->repo->getTargetProductList($facility_cd, $person_cd_list, $target_product_cd);
            $target_product_list_column =  array_column($target_product_list, 'person_cd');
            if (count($person_list) > 0) {
                foreach ($person_list as $item_list) {
                    $item_list = (object)$item_list;
                    $segment_list_data = [];
                    $target_product_list_data = [];
                    $person_cd = $item_list->person_cd;
                    $sub_target = !empty($item_list->sub_target) ? json_decode($item_list->sub_target) : [];

                    if (count((array)$segment_list) > 0) {
                        $segment_list_key = array_search($person_cd, $segment_list_column);
                        if ($segment_list_key !== false) {
                            $segment_list_data = !empty($segment_list[$segment_list_key]->segment ?? '') ? json_decode($segment_list[$segment_list_key]->segment) : [];
                        }
                    }

                    if (count((array)$target_product_list) > 0) {
                        $target_product_list_key = array_search($person_cd, $target_product_list_column);
                        if ($target_product_list_key !== false) {
                            $target_product_list_data = !empty($target_product_list[$target_product_list_key]->target_product ?? '') ? json_decode($target_product_list[$target_product_list_key]->target_product) : [];
                            $target_product_cd_sub = array_column($sub_target,'target_product_cd');
                            if(count($target_product_list_data) > 0){
                                foreach($target_product_list_data as $temp){
                                    $temp->sub_target = "";
                                    $key = array_search($temp->target_product_cd , $target_product_cd_sub);
                                    if($key !== false){
                                        $temp->sub_target = $sub_target[$key]->sub_target;
                                    }
                                }
                            }
                        }
                    }

                    $result[] = [
                        'facility_cd' => $item_list->facility_cd,
                        'facility_name' => $item_list->facility_name,
                        'person_cd' => $item_list->person_cd,
                        'person_name' => $item_list->person_name,
                        'department_name' => $item_list->department_name,
                        'department_cd' => $item_list->department_cd,
                        'display_position_name' => $item_list->display_position_name,
                        'display_position_cd' => $item_list->display_position_cd,
                        'segment_list' => $segment_list_data,
                        'target_product_list' => $target_product_list_data
                    ];
                }
            }
        }
        return $result;
    }
}
