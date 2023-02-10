<?php

use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\AccessLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Route;

if (!function_exists('slugFileName')) {
    function slugFileName(string $fileName)
    {
        $extension = substr($fileName, strrpos($fileName, '.'));
        $name      = str_replace($extension, '', $fileName);
        $name      = Str::slug($name);
        return $name . strtolower($extension);
    }
}
if (!function_exists('validateDate')) {
    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
}
if (!function_exists('replaceStringToFormatDate')) {
    function replaceStringToFormatDate($stringDate)
    {
        $date = '';
        if ($stringDate) {
            $arrayStringDate = str_split($stringDate);
            foreach ($arrayStringDate as $key => $word) {
                if ($key == 4 || $key == 6) {
                    $date .= '-';
                }
                $date .= $word;
            }
        }
        return $date;
    }
}
/**
 * typeAccess : REQUEST, RESPONSE
 * statusCode : 200, 400, 500, 405 ...
 *
 */
function saveInfoLog($typeAccess, $statusCode, $levelLog, $queryLog = NULL, $errors = NULL, $contentAction = NULL, $logSql = FALSE, $errorSql = FALSE, $errorMail = NULL, $errorConnectDB = false, $typeErrorCode = NULL)
{
    try {
        $request = request();
        $params = $request->all();
        $method = $request->getMethod();
        $uri = $request->path();
        $urlBrowserFe = $request->get('link_url_fe');
        $buttonActionFE = $request->get('info_button_fe');

        $userAgent = $request->server('HTTP_USER_AGENT');
        $timeCurrent = Carbon::now()->format('Y-m-d H:i:s');
        $httpReferer = $request->server('HTTP_REFERER');
        $ip = $request->ip();

        $accesslogService = new AccessLogService();
        // define type access , log level
        if ($statusCode == 400 || $statusCode == 401) {
            $typeAccess = "VALIDATE";
        }
        if ($statusCode >= 200 && $statusCode <= 299) {
            $typeAccess .= ' SUCCESS';
        } else {
            $typeAccess .= ' ERROR';
        }
        if ($statusCode >= 500) {
        }
        // write log file
        $accesslogService->writeLogFile($uri, $typeAccess, $method, $httpReferer, $ip, $userAgent, $params, $timeCurrent, $statusCode, $queryLog, $errors, $logSql, $urlBrowserFe, $buttonActionFE, $errorMail, $errorConnectDB, $typeErrorCode);
        // save into db table log
        if (!$errorSql) {
            $accesslogService->writeLogDB($levelLog, $uri, $typeAccess, $method, $httpReferer, $ip, $userAgent, $params, $timeCurrent, $statusCode, $queryLog, $errors, $contentAction, $logSql, $urlBrowserFe, $buttonActionFE);
        }
    } catch (\Throwable $th) {
        throw $th;
    }
}
/**
 * get date system
 */
function getDateSystem()
{
    $data = DB::select('SELECT parameter_value FROM c_system_parameter WHERE parameter_key = :parameter_key', ['parameter_key' => 'システム運用日付']);
    return count($data) ? $data[0]->parameter_value : null;
}
function checkDateValid($date)
{
    if ($date) {
        $date = replaceStringToFormatDate($date);
        if (!validateDate($date)) {
            return false;
        }
    } else {
        return false;
    }
    return true;
}
/**
 * log count row just insert/update/delete
 * actionType :
 * INSERTED, UPDATED, DELETED
 */
function logCountRowActionSql($actionContent, $actionType, $keyBatchJob)
{
    $sql = "SELECT ROW_COUNT();";
    $data = DB::select($sql);
    if (count($data)) {
        $rowAction = ((array)$data[0])["ROW_COUNT()"];
        Log::info($keyBatchJob . ", " . $actionContent . ', ' . $actionType . " " . $rowAction . " records");
    }
}
function getTimezoneCurrentMachine()
{
    $timezoneDefault = 'Asia/Tokyo';
    try {
        $timeZone = '';
        $user_agent = PHP_OS;
        if ($user_agent == 'Linux') {
            // if system is linux
            $dateinfo = trim(shell_exec("timedatectl | grep -i zone: 2>/dev/null"));
            $dateinfoarray = explode(' ', $dateinfo);
            $timeZone = $dateinfoarray[2];
        } else if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // if system is wwin
            $dateinfo = trim(shell_exec('systeminfo | findstr /L "Zone:"'));
            $positionStart = strpos($dateinfo, '+');
            $positionEnd = strpos($dateinfo, ')');
            $timeZone = substr($dateinfo, $positionStart, $positionEnd - $positionStart);
            $timeZone = getNameTimezoneFromGMT($timeZone);
        } else {
            // if system is mac
            $systemZoneName = readlink('/etc/localtime');
            $dataSystemZoneName = explode('zoneinfo/', $systemZoneName);
            $timeZone = $dataSystemZoneName[1];
        }
        return $timeZone ? $timeZone : $timezoneDefault;
    } catch (\Throwable $th) {
        throw $th;
        return $timezoneDefault;
    }
}
function getNameTimezoneFromGMT($gmt)
{
    $dataTimezone = [
        '+00:00' => 'Africa/Accra',
        '+01:00' => 'Africa/Bangui',
        '+02:00' => 'Africa/Blantyre',
        '+03:00' => 'Africa/Djibouti',
        '+03:30' => 'Asia/Tehran',
        '+04:00' => 'Asia/Baku',
        '+04:30' => 'Asia/Kabul',
        '+05:00' => 'Asia/Atyrau',
        '+05:30' => 'Asia/Calcutta',
        '+05:45' => 'Asia/Kathmandu',
        '+06:00' => 'Antarctica/Vostok',
        '+06:30' => 'Asia/Rangoon',
        '+07:00' => 'Asia/Bangkok',
        '+08:00' => 'Asia/Brunei',
        '+08:45' => 'Australia/Eucla',
        '+09:00' => 'Asia/Dili',
        '+09:30' => 'Australia/Adelaide',
        '+10:00' => 'Asia/Ust-Nera',
        '+11:00' => 'Antarctica/Casey',
        '+12:00' => 'Antarctica/McMurdo',
        '+13:00' => 'Pacific/Enderbury',
        '+14:00' => 'Pacific/Kiritimati',
        '-00:00' => 'Africa/Accra',
        '-01:00' => 'America/Scoresbysund',
        '-02:00' => 'Brazil/DeNoronha',
        '-03:00' => 'Brazil/East',
        '-04:00' => 'Brazil/West',
        '-05:00' => 'Canada/Eastern',
        '-06:00' => 'Canada/Saskatchewan',
        '-07:00' => 'Canada/Yukon',
        '-08:00' => 'America/Ensenada',
        '-09:00' => 'America/Metlakatla',
        '-10:00' => 'America/Adak',
        '-11:00' => 'Pacific/Midway',
        '-12:00' => 'Antarctica/McMurdo',
    ];
    return $dataTimezone[$gmt] ?? 'Asia/Tokyo';
}
function randomStringUnique()
{
    return bin2hex(random_bytes(5));
}
/**
 * define url path export file log
 *
 * typeFile : api | sql | job
 */
function getUrlPathExportFileLog($typeFile, $jobId = null)
{
    $urlPathLogDefine = config('variables.url_folder_export_log');
    $businessType = strtoupper(explode('.', Route::currentRouteName())[0]);
    $urlPathSaveFile = '';
    if ($urlPathLogDefine) {
        $urlPathSaveFile = $urlPathLogDefine;
    } else {
        $urlPathSaveFile = storage_path('logs');
    }
    if ($typeFile == 'api') {
        return $urlPathSaveFile . '/' . $businessType . '/api.log';
    }
    if ($typeFile == 'sql') {
        return $urlPathSaveFile . '/' . $businessType . '/sql.log';
    }
    if ($typeFile == 'job') {
        return $urlPathSaveFile . '/' . substr($jobId, 0, 1) . '/' . $jobId . '.log';
    }
    return $urlPathSaveFile . '/OTHER/api.log';
}
function recursiveParam($array, &$string)
{
    foreach ($array as $key => $value) {
        $string .= $key . ": ";
        if (is_array($value)) {
            $condition_string = "{";
            foreach ($value as $key_ => $value_) {
                if (!is_array($value_)) {
                    $condition_string .= $key_ . " : " . $value_ . ", ";
                }else{
                    recursiveParam($value_, $string);
                }
            }
            $condition_string = rtrim($condition_string, ', ');
            $condition_string .= "}";
            $string .= $condition_string;
        } else {
            $string .= $value . ", ";
        }
        $string = rtrim($string, ', ');
        $string .= "; ";
        $string = str_replace("{}; ", "", $string);
    }
}