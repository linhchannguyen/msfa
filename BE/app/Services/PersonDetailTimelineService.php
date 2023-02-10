<?php

namespace App\Services;

use App\Repositories\PersonDetailTimeline\PersonDetailTimelineRepositoryInterface;
use App\Traits\DateTimeTrait;

class PersonDetailTimelineService
{
    use DateTimeTrait;
    private $repo;

    public function __construct(PersonDetailTimelineRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getScreenData()
    {
        return $this->repo->getScreenData();
    }

    private function unique_multidim_array($array, $key)
    {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach ($array as $val) {
            if (!in_array($val->$key, $key_array)) {
                $key_array[$i] = $val->$key;
                $temp_array[]['start_datetime'] = $this->responseDateTimeFormat($val->$key, 'Y/m/d');
            }
            $i++;
        }
        return $temp_array;
    }

    public function searchTimeline($conditions)
    {
        $datas = $this->repo->searchTimeline($conditions);
        $resultDate = $this->unique_multidim_array($datas, 'start_date');
        $countDatas = count($datas);
        $index = 0;
        foreach ($resultDate as &$data) {
            $channel_detail = [];
            $group_call_id = [];
            for ($i = $index; $i < $countDatas; $i++) {
                if ($this->responseDateTimeFormat($datas[$i]->start_date, 'Y/m/d') != $data['start_datetime']) {
                    $index = $i;
                    break;
                }
                if (date('H:i:s', strtotime($datas[$i]->start_datetime)) == '00:00:00' && date('H:i:s', strtotime($datas[$i]->end_datetime)) == '23:59:59') {
                    $datas[$i]->all_day_flag = 1;
                } else {
                    $datas[$i]->all_day_flag = 0;
                }
                $datas[$i]->start_datetime = $this->responseDateTimeFormat($datas[$i]->start_datetime);
                $datas[$i]->end_datetime = $this->responseDateTimeFormat($datas[$i]->end_datetime);
                if ($datas[$i]->channel_type == '10') {
                    if(!in_array($datas[$i]->channel_id, $group_call_id)){
                        array_push($group_call_id, $datas[$i]->channel_id);
                        $call = $this->repo->getDetailByCallID($datas[$i]->channel_id);
                        $datas[$i]->detail = $call;
                        array_push($channel_detail, (array)$datas[$i]);
                    }
                } else {
                    $channel_detail[] = (array)$datas[$i];
                }
            }
            array_multisort(
                array_column($channel_detail, 'all_day_flag'),
                SORT_DESC,
                array_column($channel_detail, 'start_datetime'),
                SORT_DESC,
                array_column($channel_detail, 'off_label_flag'),
                SORT_DESC,
                $channel_detail
            );
            $data['detail'] = $channel_detail;
        }
        return $resultDate;
    }
}
