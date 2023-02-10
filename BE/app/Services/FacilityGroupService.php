<?php

namespace App\Services;

use App\Repositories\FacilityGroup\FacilityGroupRepositoryInterface;

class FacilityGroupService
{
    private $repo;

    public function __construct(FacilityGroupRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getData($request)
    {
        $facility_group = $this->repo->allData($request);
        foreach ($facility_group as $item) {
            $item->children = $this->repo->allFacility($item->select_facility_group_id);
        }
        $result['facility_group'] = $facility_group;
        $result['sort_order'] = $facility_group[count($facility_group) - 1]->sort_order ?? "";
        return $result;
    }

    public function deleteFacilityGroup($select_facility_group_id)
    {
        $this->repo->deleteFacility($select_facility_group_id);
        return $this->repo->deleteFacilityGroup($select_facility_group_id);
    }

    public function updateFacilityGroup($data)
    {
        $facility_group_id = "";
        if (empty($data->select_facility_group_id)) {
            $facility_group_id = $this->repo->insertFacilityGroup($data);
        } else {
            $facility_group_id = $this->repo->updateFacilityGroup($data);
        }
        $this->repo->deleteFacility($facility_group_id);
        if (count($data->list_facility) > 0) {
            foreach ($data->list_facility as $item) {
                $this->repo->updateFacility($facility_group_id, $item['facility_cd'], $data->user_cd);
            }
        }
        return $facility_group_id;
    }

    public function changeSelectFacilityGroup($data)
    {
        if (count($data->facility) > 0) {
            foreach ($data->facility as $item) {
                $data = (object)$item;
                $this->repo->updateFacilityGroup($data);
            }
        }
        return true;
    }
}
