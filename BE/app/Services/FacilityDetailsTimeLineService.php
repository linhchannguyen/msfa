<?php

namespace App\Services;

use App\Repositories\FacilityDetailsTime\FacilityDetailsTimeLineRepositoryInterface;
use App\Traits\ArrayTrait;

class FacilityDetailsTimeLineService
{
    use ArrayTrait;
    private $repo;

    public function __construct(FacilityDetailsTimeLineRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getScreenData()
    {
        return $this->repo->getScreenData();
    }

    public function getFacilityDetailsTimeLine($facility_cd, $person_cd, $product_cd, $start_datetime, $end_datetime, $channel_type, $timeline_id)
    {
        $data_result = [];
        $datas = $this->repo->getFacilityDetailsTimeLine($facility_cd, $person_cd, $product_cd, $start_datetime, $end_datetime, $channel_type, $timeline_id);
        if (count($datas) > 0) {
            foreach ($datas as $data) {
                $data = (object)$data;
                $data->content = !empty($data->content) ? json_decode($data->content) : [];
                $timeline_ids = empty($timeline_id) ? [] : array_column($data->content, 'timeline_id');
                $data->content = $this->my_array_unique($data->content);
                if (count($data->content) > 0) {
                    $channel_detail = [];
                    foreach ($data->content as $test) {
                        $data->contents[] = [
                            "channel_type" => $test->channel_type,
                            "channel_id" => $test->channel_id
                        ];
                    }
                    $data->contents = $this->my_array_unique($data->contents);
                    foreach ($data->contents as $content) {
                        $content = (object)$content;
                        if ($content->channel_type == '10') {
                            $datasDetail = $this->repo->getChannel10Detail($facility_cd, $data->start_datetime, $content->channel_type, $content->channel_id, $timeline_ids);
                            if (is_object($datasDetail)) {
                                $datasDetail->channel_detail = !empty($datasDetail->channel_detail ?? '') ? json_decode($datasDetail->channel_detail) : [];
                                if (date('H:i:s', strtotime($datasDetail->start_datetime)) == '00:00:00' && date('H:i:s', strtotime($datasDetail->end_datetime)) == '23:59:59') {
                                    $datasDetail->all_day_flag = 1;
                                } else {
                                    $datasDetail->all_day_flag = 0;
                                }
                                $channel_detail = array_merge($channel_detail, [$datasDetail]);
                            }
                        } elseif ($content->channel_type == '20') {
                            $datasDetail = $this->repo->getChannel20Detail($facility_cd, $data->start_datetime, $content->channel_type, $content->channel_id, $timeline_ids);
                            $channel_detail = array_merge($channel_detail, $datasDetail);
                        } elseif ($content->channel_type == '30') {
                            $datasDetail = $this->repo->getChannel30Detail($facility_cd, $data->start_datetime, $content->channel_type, $content->channel_id, $timeline_ids);
                            $channel_detail = array_merge($channel_detail, $datasDetail);
                        } else {
                            $datasDetail = $this->repo->getChannelDetail($facility_cd, $data->start_datetime, $content->channel_type, $content->channel_id, $timeline_ids);
                            $channel_detail = array_merge($channel_detail, $datasDetail);
                        }
                        if ($content->channel_type != '10') {
                            foreach ($channel_detail as $value) {
                                if (date('H:i:s', strtotime($value->start_datetime)) == '00:00:00' && date('H:i:s', strtotime($value->end_datetime)) == '23:59:59') {
                                    $value->all_day_flag = 1;
                                } else {
                                    $value->all_day_flag = 0;
                                }
                            }
                        }
                    }
                    if (count($channel_detail) > 0) {
                        array_multisort(
                            array_column($channel_detail, 'all_day_flag'),
                            SORT_DESC,
                            array_column($channel_detail, 'start_datetime'),
                            SORT_DESC,
                            array_column($channel_detail, 'off_label_flag'),
                            SORT_DESC,
                            $channel_detail
                        );
                        $data_result[] = [
                            'start_datetime' => $data->start_datetime,
                            'content' => $channel_detail
                        ];
                    }
                }
            }
        }
        return $data_result;
    }
}
