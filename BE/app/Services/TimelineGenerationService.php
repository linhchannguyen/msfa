<?php

namespace App\Services;

use App\Repositories\TimelineGeneration\TimelineGenerationRepositoryInterface;

use Illuminate\Console\Command;
use App\Traits\DateTimeTrait;

class TimelineGenerationService
{
    use DateTimeTrait;
    private $repo, $date;

    public function __construct(TimelineGenerationRepositoryInterface $repo)
    {
        $this->repo = $repo;
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
    }

    public function getSystemParameterDate()
    {
        return $this->repo->getSystemParameterDate();
    }
    // Use only for batch job
    public function updateTimelineGeneration($input_date, $jobId, $keyBatchJob)
    {
        //get c_system_parameter where 異動判定期間
        $system_parameter_detail = $this->repo->getSystemParameterDetail();
        if ($system_parameter_detail->parameter_value) {
            //delete t_timeline channel_type  = '10'
            $this->repo->deleteTimelineDetail($input_date, $system_parameter_detail->parameter_value ?? null, $jobId, $keyBatchJob);
            //get timeline detail
            $date = date('Y-m-d H:i:s', strtotime("-" . ($system_parameter_detail->parameter_value) . " months", strtotime($input_date)));
            $timeline_detail = $this->repo->getTimelineDetail($date);
            if (count($timeline_detail) > 0) {
                foreach ($timeline_detail as $item) {
                    if (!$item->channel_detail_id) {
                        $this->repo->insertTimelineDetail($item, $jobId, $keyBatchJob);
                    } else {
                        $this->repo->updateTimelineDetail($item, $jobId, $keyBatchJob);
                    }
                }
            }
        }
        //get c_system_parameter where parameter_key = '入力期限' and parameter_name = '説明会'
        $system_parameter_briefing = $this->repo->getSystemParameterBriefing();
        if ($system_parameter_briefing->parameter_value) {
            //delete t_timeline channel_type  = '20'
            $this->repo->deleteTimelineBriefing($input_date, $system_parameter_briefing->parameter_value ?? null, $jobId, $keyBatchJob);
            $date = date('Y-m-d H:i:s', strtotime("-" . ($system_parameter_briefing->parameter_value) . " months", strtotime($input_date)));
            $timeline_briefing = $this->repo->getTimelineBriefing($date);
            if (count($timeline_briefing) > 0) {
                foreach ($timeline_briefing as $item_briefing) {
                    if (!$item_briefing->timeline_id) {
                        $this->repo->insertTimelineBriefing($item_briefing, $jobId, $keyBatchJob);
                    } else {
                        $this->repo->updateTimelineBriefing($item_briefing, $jobId, $keyBatchJob);
                    }
                }
            }
        }
        //get c_system_parameter where parameter_key = '入力期限' and parameter_name = '講演会'
        $system_parameter_convention = $this->repo->getSystemParameterConvention();
        if ($system_parameter_convention->parameter_value) {
            //delete t_timeline channel_type  = '30'
            $this->repo->deleteTimelineConvention($input_date, $system_parameter_convention->parameter_value ?? null, $jobId, $keyBatchJob);
            $date = date('Y-m-d H:i:s', strtotime("-" . ($system_parameter_convention->parameter_value) . " months", strtotime($input_date)));
            $timeline_Convention = $this->repo->getTimelineConvention($date);
            if (count($timeline_Convention) > 0) {
                foreach ($timeline_Convention as $item_Convention) {
                    if (!$item_Convention->timeline_id) {
                        $this->repo->insertTimelineConvention($item_Convention, $jobId, $keyBatchJob);
                    } else {
                        $this->repo->updateTimelineConvention($item_Convention, $jobId, $keyBatchJob);
                    }
                }
            }
        }
        return Command::SUCCESS;
    }
}
