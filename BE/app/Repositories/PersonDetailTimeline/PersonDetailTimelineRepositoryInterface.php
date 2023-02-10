<?php

namespace App\Repositories\PersonDetailTimeline;

use App\Repositories\BaseRepositoryInterface;

interface PersonDetailTimelineRepositoryInterface extends BaseRepositoryInterface
{
    public function getScreenData();
    public function searchTimeline($conditions);
    public function getDetailByCallID($call_id);
}
