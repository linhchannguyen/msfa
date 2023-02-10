<?php

namespace App\Repositories\FacilityDetailsTime;

use App\Repositories\BaseRepositoryInterface;

interface FacilityDetailsTimeLineRepositoryInterface extends BaseRepositoryInterface
{
    public function getScreenData();
    public function getFacilityDetailsTimeLine($facility_cd, $person_cd, $product_cd, $start_datetime, $end_datetime, $channel_type, $timeline_id);
    public function getChannel10Detail($facility_cd, $start_datetime, $channel_type, $channel_id, $timeline_id=null);
    public function getChannel20Detail($facility_cd, $start_datetime, $channel_type, $channel_id, $timeline_id=null);
    public function getChannel30Detail($facility_cd, $start_datetime, $channel_type, $channel_id, $timeline_id=null);
    public function getChannelDetail($facility_cd, $start_datetime, $channel_type, $channel_id, $timeline_id=null);
}
