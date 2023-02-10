<?php

namespace App\Services;

use App\Repositories\FacilityPersonal\FacilityPersonalRepositoryInterface;
use App\Repositories\ConditionArea\ConditionAreaRepositoryInterface;

class FacilityPersonalService
{
    private $repo;
    private $condition;

    public function __construct(ConditionAreaRepositoryInterface $condition, FacilityPersonalRepositoryInterface $repo)
    {
        $this->repo = $repo;
        $this->condition = $condition;
    }

    public function getData($data)
    {
        $variable = $this->repo->allVariable($data);
        foreach ($variable['records'] as $item) {
            if ($item->segment_name) {
                $item->segment_name = explode(',', $item->segment_name);
            }
        }
        return $variable;
    }

    public function getListFacility($data)
    {
        $facility = $this->repo->filterListFacility($data);
        $list_facility = "";
        $i = 0;
        foreach ($facility['records'] as $item) {
            if($data->showRelation){
                $item->relation_facility = isset($item->relation_facility) ? json_decode($item->relation_facility, true) : [];
            }
            $item->department_line = json_decode($item->department_line, true);
            if ($i !== 0) {
                $list_facility .= "," . $item->facility_cd;
            } else {
                $list_facility .= $item->facility_cd;
            }
            $i++;
        }
        $facility['list_facility'] = $list_facility;
        return $facility;
    }

    public function allMedicalStaff()
    {
        return $this->repo->allMedicalStaff();
    }
}
