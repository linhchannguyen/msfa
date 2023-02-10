<?php

namespace App\Services;

use App\Repositories\TimelineSearch\TimelineSearchRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Services\ConventionSearchService;

class TimelineSearchService extends BaseRepository
{
    private $repo, $convention_service;
    public function __construct(
        TimelineSearchRepositoryInterface $repo,
        ConventionSearchService $convention_service

    ) {
        $this->repo = $repo;
        $this->convention_service = $convention_service;
    }

    //List All Facility
    public function getDataIndex()
    {
        $result['channel'] = $this->repo->getChannel();
        return $result;
    }

    private function unique_multidim_array($array, $key)
    {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach ($array as $val) {
            if (!in_array($val->$key, $key_array)) {
                $key_array[$i] = $val->$key;
                $temp_array[]['date'] = $val->$key;
            }
            $i++;
        }
        return $temp_array;
    }

    //get List Data Briefing
    public function getListDataTimeline($request)
    {
        $resultDatas = $this->repo->getListDataTimeline($request);
        $resultDate = $this->unique_multidim_array($resultDatas, 'start_date');
        $countDatas = count($resultDatas);
        if (count($resultDate) > 0) {
            $index = 0;
            foreach ($resultDate as &$item) {
                $timeline_date = [];
                $group_call_id = [];
                for ($i = $index; $i < $countDatas; $i++) {
                    if ($resultDatas[$i]->start_date != $item['date']) {
                        $index = $i;
                        break;
                    }
                    if (date('H:i:s', strtotime($resultDatas[$i]->start_datetime)) == '00:00:00' && date('H:i:s', strtotime($resultDatas[$i]->end_datetime)) == '23:59:59') {
                        $resultDatas[$i]->all_day_flag = 1;
                    } else {
                        $resultDatas[$i]->all_day_flag = 0;
                    }
                    if ($resultDatas[$i]->channel_type == 10) {
                        $status_report = $this->convention_service->checkStatusReport($resultDatas[$i]->schedule_id, $resultDatas[$i]->start_datetime);
                        if (!$status_report) {
                            if(!in_array($resultDatas[$i]->channel_id, $group_call_id)){
                                array_push($group_call_id, $resultDatas[$i]->channel_id);
                                $call = $this->repo->getDetailByCallID($resultDatas[$i]->channel_id);
                                $resultDatas[$i]->detail = $call;
                                array_push($timeline_date, (array)$resultDatas[$i]);
                            }
                        }
                    } elseif ($resultDatas[$i]->channel_type == 20) {
                        if ($resultDatas[$i]->briefing_id) {
                            array_push($timeline_date, (array)$resultDatas[$i]);
                        }
                    } elseif ($resultDatas[$i]->channel_type == 30) {
                        if ($resultDatas[$i]->convention_id) {
                            array_push($timeline_date, (array)$resultDatas[$i]);
                        }
                    } else {
                        array_push($timeline_date, (array)$resultDatas[$i]);
                    }
                }
                array_multisort(
                    array_column($timeline_date, 'all_day_flag'),
                    SORT_DESC,
                    array_column($timeline_date, 'start_datetime'),
                    SORT_DESC,
                    array_column($timeline_date, 'off_label_flag'),
                    SORT_DESC,
                    $timeline_date
                );
                $item['data'] = $timeline_date;
                $item['date'] = date('Y/m/d', strtotime($item['date']));
            }
        }
        return $resultDate;
    }
}
