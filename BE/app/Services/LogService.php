<?php

namespace App\Services;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Traits\AuthTrait;
use App\Enums\LogBatchJob;
use App\Services\SystemParameterService;
use Illuminate\Support\Carbon;

class LogService
{
    use AuthTrait;
    protected $serviceParameter;

    public function __construct(SystemParameterService $serviceParameter)
    {
        $this->serviceParameter = $serviceParameter;
    }
    public function baseLog ($jobId, $keyBatchJob) {
        return "$keyBatchJob, " . $jobId . ", ";
    }
    public function startJob ($jobId, $keyBatchJob) {
        Log::build([
            'driver' => config('logging.default'),
            'path' => getUrlPathExportFileLog('job', $jobId),
            'level' => 'debug',
            'days' => config('logging.number_daily_days')
        ])->info($this->baseLog($jobId, $keyBatchJob) . "START");
    }
    public function endJob ($jobId, $keyBatchJob) {
        Log::build([
            'driver' => config('logging.default'),
            'path' => getUrlPathExportFileLog('job', $jobId),
            'level' => 'debug',
            'days' => config('logging.number_daily_days')
        ])->info($this->baseLog($jobId, $keyBatchJob) . "END");
    }
    public function logInput ($jobId, $keyBatchJob, $params) {
        $paramString = '';
        foreach ($params as $key => $param) {
            $paramString .= "$key : $param ,";
        }
        Log::build([
            'driver' => config('logging.default'),
            'path' => getUrlPathExportFileLog('job', $jobId),
            'level' => 'debug',
            'days' => config('logging.number_daily_days')
        ])->info($this->baseLog($jobId, $keyBatchJob) .'parameters {
            ' . $paramString . '
        }');
    }
    public function logInvalidInput ($jobId, $keyBatchJob) {
        Log::build([
            'driver' => config('logging.default'),
            'path' => getUrlPathExportFileLog('job', $jobId),
            'level' => 'debug',
            'days' => config('logging.number_daily_days')
        ])->info($this->baseLog($jobId, $keyBatchJob) . __('validation.date_format', ['attribute' => 'date', 'format' => 'Y-m-d']));
    }
    public function logInfoContent ($jobId, $keyBatchJob, $contentLog) {
        Log::build([
            'driver' => config('logging.default'),
            'path' => getUrlPathExportFileLog('job', $jobId),
            'level' => 'debug',
            'days' => config('logging.number_daily_days')
        ])->info($this->baseLog($jobId, $keyBatchJob) . $contentLog);
    }
    public function logTargetDay ($jobId, $keyBatchJob, $date) {
        Log::build([
            'driver' => config('logging.default'),
            'path' => getUrlPathExportFileLog('job', $jobId),
            'level' => 'debug',
            'days' => config('logging.number_daily_days')
        ])->info($this->baseLog($jobId, $keyBatchJob) . 'Target Date : ' . $date);
    }
    public function keyBatchJob () {
        return bin2hex(random_bytes(8));
    }
}
