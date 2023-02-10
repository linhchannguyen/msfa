<?php

namespace App\Services;

use App\Repositories\ConditionArea\ConditionAreaRepositoryInterface;
use App\Repositories\FacilityPersonal\FacilityPersonalRepositoryInterface;

class ConditionAreaService
{
    private $repo;
    private $facility;

    public function __construct(ConditionAreaRepositoryInterface $repo, FacilityPersonalRepositoryInterface $facility)
    {
        $this->repo = $repo;
        $this->facility = $facility;
    }

    public function getListItem($conditions)
    {
        $segment = $this->facility->allSegment();
        $select_group = [];
        $select_facility_group = $this->repo->allSelectFacilityGroup($conditions->user_cd);
        foreach ($select_facility_group as $value) {
            $key = 'facility_group_id_' . $value->select_group_id;
            $value->select_group_id = $key;
        }
        $select_person_group = $this->repo->allSelectPersonGroup($conditions->user_cd);
        foreach ($select_person_group as $value) {
            $key = 'person_group_id_' . $value->select_group_id;
            $value->select_group_id = $key;
        }
        $select_group = array_merge($select_facility_group, $select_person_group);
        $result['select_group'] = $select_group;
        $result['target_product'] = $this->repo->allTargetProduct();
        $result['facility_category'] = $this->repo->allFacilityCategory();
        $result['segment'] = $segment;
        return $result;
    }

    public function getListFacility($data)
    {
        $facility = $this->repo->filterListFacility($data);
        foreach ($facility['records'] as $item) {
            $item->children = $this->repo->filterRelason($item->facility_cd);
        }
        return $facility;
    }
}
