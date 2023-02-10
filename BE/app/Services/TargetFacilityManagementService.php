<?php

namespace App\Services;

use App\Repositories\TargetFacilityManagement\TargetFacilityManagementRepositoryInterface;

class TargetFacilityManagementService
{
    private $repo;

    public function __construct(TargetFacilityManagementRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getScreenData($date_system)
    {
        return $this->repo->getScreenData($date_system);
    }

    public function targetFacilityManagement($user_cd, $date_system, $order_value, $limit, $offset)
    {
        $datas = $data = [];
        $data_facility = $this->repo->getFacility($user_cd,$order_value, $limit, $offset);
        $facility_cd = array_column($data_facility['records'], 'facility_cd');
        $target_product_data = $this->repo->getScreenData($date_system);
        $target_product_cd = array_column($target_product_data, 'target_product_cd');
        if (count($facility_cd) > 0) {
            $targetProduct = $this->repo->getTargetProduct($facility_cd,$order_value, $target_product_cd);
            foreach ($data_facility['records'] as $item) {
                $item = (object)$item;
                $dataTarget = [];
                if (count(array($targetProduct)) > 0) {
                    foreach ($targetProduct as $target) {
                        $target = (object)$target;
                        if ($item->facility_cd == $target->facility_cd) {
                            $target->person_cd = !empty($target->person_cd) ? json_decode($target->person_cd) : [];
                            $dataTarget[] = [
                                'target_product_cd' => $target->target_product_cd,
                                'sub_target' => $target->sub_target,
                                'number_person_cd' => count($target->person_cd),
                                'person_cd' => $target->person_cd
                            ];
                        }
                    }
                }

                $data[] = [
                    'facility_cd' => $item->facility_cd,
                    'facility_name' => $item->facility_name,
                    'facility_short_name' => $item->facility_short_name,
                    'target_product' => $dataTarget
                ];
            }
        }

        $datas['records'] = $data;
        $datas['pagination'] = $data_facility['pagination'];
        return $datas;
    }
}
