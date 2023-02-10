<?php

namespace App\Repositories\TimelineGeneration;

use App\Repositories\BaseRepositoryInterface;

interface TimelineGenerationRepositoryInterface extends BaseRepositoryInterface
{
    //get c_system_parameter parameter_key = 'システム運用日付' and parameter_name = '全般'
    public function getSystemParameterDate();

    //get c_system_parameter parameter_name = '面談' AND parameter_key = '入力期限'
    public function getSystemParameterDetail();
    //delete t_timeline channel_type  = '10'
    public function deleteTimelineDetail($input_date, $parameter_value, $jobId, $keyBatchJob);
    //get timeline detail
    public function getTimelineDetail($date);
    //insert timeline detail
    public function insertTimelineDetail($data, $jobId, $keyBatchJob);
    //update timeline detail
    public function updateTimelineDetail($data, $jobId, $keyBatchJob);

    //get c_system_parameter where parameter_key = '入力期限' and parameter_name = '説明会'
    public function getSystemParameterBriefing();
    //delete t_timeline channel_type  = '20'
    public function deleteTimelineBriefing($input_date, $parameter_value, $jobId, $keyBatchJob);
    //get timeline briefing
    public function getTimelineBriefing($date);
    //insertl timeline briefing
    public function insertTimelineBriefing($data, $jobId, $keyBatchJob);
    //update timeline detail
    public function updateTimelineBriefing($data, $jobId, $keyBatchJob);

    //get c_system_parameter where parameter_key = '入力期限' and parameter_name = '講演会'
    public function getSystemParameterConvention();
    //delete t_timeline channel_type  = '20'
    public function deleteTimelineConvention($input_date, $parameter_value, $jobId, $keyBatchJob);
    //get timeline briefing
    public function getTimelineConvention($date);
    //insertl timeline briefing
    public function insertTimelineConvention($data, $jobId, $keyBatchJob);
    //update timeline detail
    public function updateTimelineConvention($data, $jobId, $keyBatchJob);
}
