<?php

namespace App\Repositories\TimelineSearch;

use App\Repositories\BaseRepositoryInterface;

interface TimelineSearchRepositoryInterface extends BaseRepositoryInterface
{
    //get Channel
    public function getChannel();
    //get List Data TimeLine
    public function getListDataTimeline($requet);
    public function getDetailByCallID($call_id);
}
