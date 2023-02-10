<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Traits\AuthTrait;
use \Illuminate\Support\Facades\Route;
use App\Enums\StatusLog;
use App\Mail\ServerErrorMail;
use App\Jobs\SendEmail;
use App\Repositories\GetSystemParameter\GetSystemParameterRepository;
use App\Services\GetSystemParameterService;
use Carbon\Carbon;

class AccessLogService
{
    use AuthTrait;
    protected $serviceParameter;

    public function writeLogFile($uri, $typeAccess, $method, $httpReferer, $ip, $userAgent, $params, $timeCurrent, $statusCode, $queryLog, $errors, $logSql, $urlBrowserFe, $buttonActionFE, $errorMail, $errorConnectDB, $typeErrorCode)
    {
        try {
            $errors = empty($errors) ? "" : $errors;
            // get current user
            $proxy = $this->isProxyUser();
            $idUserCurrent = $this->getCurrentUser();
            $idUser = NULL;
            $idUserProxy = NULL;
            if ($proxy) {
                $idUserProxy = $idUserCurrent;
                $idUser = $this->getCurrentProxyUser();
            } else {
                $idUser = $idUserCurrent;
            }
            $request = request();
            $token = $request->bearerToken();
            $tokenProxy = substr($request->header('originaltoken'), 7);
            if (!$tokenProxy) {
                $tokenProxy = '';
            }
            $businessType = strtoupper(explode('.', Route::currentRouteName())[0]);
            $thread = config('global.thread');
            if ($thread == null) {
                $thread = randomStringUnique();
            }
            $dataSave = [
                'process_id' => $thread,
                'api_url' => $uri,
                'business_type' => $businessType,
                'web_access_datetime' => $timeCurrent,
                'db_access_datetime' => $timeCurrent,
                'type_access' => $typeAccess,
                'method' => $method,
                'remote_address' => $ip,
                'http_referer' => $httpReferer,
                'user_cd' => $idUser,
                'user_cd_proxy' => $idUserProxy,
                'user-agent' => $userAgent,
                'parameter' => $params,
                'time' => $timeCurrent,
                'status_code' => $statusCode,
                'errors' => $errors,
                'token' => $token,
                'token_proxy' => $tokenProxy,
                'link_url_fe' => $urlBrowserFe,
                'info_button_fe' => $buttonActionFE
            ];

            $dataSendMail = explode(",", config('variables.email_log_level'));
            $isSendMailInfoInData = in_array(StatusLog::INFO, $dataSendMail);
            $isSendMailWarningInData = in_array(StatusLog::WARNING, $dataSendMail);
            $isSendMailErrorInData = in_array(StatusLog::ERROR, $dataSendMail);

            // Send mail
            $isCanSendMail = config('variables.log_send_mail_flag');
            if ($isCanSendMail && !$logSql) {
                if (($isSendMailInfoInData && $statusCode >= 200 && $statusCode <= 299) ||
                    ($isSendMailWarningInData && $statusCode >= 400 && $statusCode <= 499) ||
                    ($isSendMailErrorInData && $statusCode >= 500)
                ) {
                    $contentSendmail = '';
                    $infoServe = '';
                    $dataIgnoreShow = config('environment');
                    foreach ($_SERVER as $key => $value) {
                        if (!in_array($key, $dataIgnoreShow)) {
                            if('REQUEST_URI' == $key || 'QUERY_STRING' == $key){
                                $value = urldecode($value);
                            }
                            $infoServe .= $key . " : " . ((is_array($value) || is_object($value)) ? json_encode($value) : $value) . " <br>";
                        }
                    }
                    $infoServe = rtrim($infoServe, ' <br>');
                    $content_error = "{";
                    $content_param = "{";
                    if(is_array($errors)){
                        recursiveParam($errors, $content_error);
                    }
                    recursiveParam($params, $content_param);
                    $content_error = rtrim($content_error, '; ');
                    $content_param = rtrim($content_param, '; ');
                    $content_error .= "}";
                    $content_param .= "}";
                    if ($errorMail) {
                        $contentSendmail = $errorMail;
                    } else if ($errors) {
                        if (is_array($errors) || is_object($errors)) {
                            // case validate
                            $errorReturns = [];
                            $errorReturns['Error_Content'] = $content_error;
                            $errorReturns['Request_User'] = $idUser;
                            $errorReturns['Request_User_Proxy'] = $idUserProxy;
                            $errorReturns['Request_Action'] = $uri;
                            $errorReturns['Request_Method'] = $method;
                            $errorReturns['Request_Params'] = $content_param;
                            $errorReturns['Request_Serve'] =  '<br>' . $infoServe;
                            $errorReturns['Request_Token'] = $token;
                            $errorReturns['Request_Token_Proxy'] = $tokenProxy;
                            $errorString = '';
                            foreach ($errorReturns as $key => $value) {
                                $key = str_replace('_', ' ', $key);
                                $errorString .= $key . " : " . $value . " <br>";
                            }
                            $contentSendmail = $errorString;
                        } else {
                            $errors .= "
                                Error Content : " . $errors . " <br>
                                Request User : " . $idUser . " <br>
                                Request User Proxy : " . $idUserProxy . " <br>
                                Request Action : " . $uri . " <br>
                                Request Method : " . $method . " <br>
                                Request Params : " . $content_param . " <br>
                                Request Serve : <br>" . $infoServe . " <br>
                                Request Token : " . $token . " <br>
                                Request Token Proxy : " . $tokenProxy . " <br>";
                            $contentSendmail = $errors;
                        }
                    } else {
                        $errors .= "
                            Request User : " . $idUser . " <br>
                            Request User Proxy : " . $idUserProxy . " <br>
                            Request Action : " . $uri . " <br>
                            Request Method : " . $method . " <br>
                            Request Params : " . $content_param . " <br>
                            Request Serve : <br>" . $infoServe . " <br>
                            Request Token : " . $token . " <br>
                            Request Token Proxy : " . $tokenProxy . " <br>";
                        $contentSendmail = $errors;
                    }
                    if(Route::currentRouteName() != 'OTHER.GET_IMAGE'){
                        $titleSendMail = "【システム名取得不可】" . "アプリケーションエラー（#" . $thread . "）";
                        // DB上、c_system_parameterに定義する「ログエラー通知先」のレコードが一つ。
                        // 　⇒ログ設計書の回答として記載しましたがDB接続エラーの懸念があるのであれば
                        // 　　.envのみの管理とした方がメールアドレスの設定ミスが減るかと思います。
                        $emailTo = explode(',', config('variables.log_default_mail_to'));
                        if (!$errorConnectDB) {
                            $service = new GetSystemParameterService(new GetSystemParameterRepository());
                            $system_name = $service->getSystemParameter();
                            $titleSendMail = "【" . $system_name['system_name']->parameter_value . "】" . "アプリケーションエラー（#" . $thread . "）";
                        }
                        $data = [
                            'process_id' => $thread,
                            'title' => $titleSendMail,
                            'content_title' => "エラー内容",
                            'type_error' => $typeErrorCode,
                            'content' => str_replace('\\', '', $contentSendmail),
                            'env' => config('variables.env_name'),
                            'error_level' => $typeAccess,
                            'day_error' => Carbon::now()->format('Y-m-d H:i:s')
                        ];
                        $mailable = new ServerErrorMail($data);
                        SendEmail::dispatchAfterResponse($emailTo, $mailable);
                    }
                }
            }

            $excuteLogSql = config('variables.excute_log_sql');
            if (!$logSql) {
                if ($statusCode >= 400 && $statusCode <= 499) {
                    Log::build([
                        'driver' => config('logging.default'),
                        'path' => getUrlPathExportFileLog('api'),
                        'level' => 'debug',
                        'days' => config('logging.number_daily_days')
                    ])->warning($dataSave);
                } else if ($statusCode >= 500) {
                    Log::build([
                        'driver' => config('logging.default'),
                        'path' => getUrlPathExportFileLog('api'),
                        'level' => 'debug',
                        'days' => config('logging.number_daily_days')
                    ])->error($dataSave);
                } else {
                    Log::build([
                        'driver' => config('logging.default'),
                        'path' => getUrlPathExportFileLog('api'),
                        'level' => 'debug',
                        'days' => config('logging.number_daily_days')
                    ])->info($dataSave);
                }
            } else if ($excuteLogSql) {
                $dataSave = [
                    'process_id' => $thread,
                    'api_url' => $uri,
                    'business_type' => $businessType,
                    'type_access' => $typeAccess,
                    'query' => $queryLog,
                ];
                if ($statusCode >= 400 && $statusCode <= 499) {
                    Log::build([
                        'driver' => config('logging.default'),
                        'path' => getUrlPathExportFileLog('sql'),
                        'level' => 'debug',
                        'days' => config('logging.number_daily_days')
                    ])->warning($dataSave);
                } else if ($statusCode >= 500) {
                    Log::build([
                        'driver' => config('logging.default'),
                        'path' => getUrlPathExportFileLog('sql'),
                        'level' => 'debug',
                        'days' => config('logging.number_daily_days')
                    ])->error($dataSave);
                } else {
                    Log::build([
                        'driver' => config('logging.default'),
                        'path' => getUrlPathExportFileLog('sql'),
                        'level' => 'debug',
                        'days' => config('logging.number_daily_days')
                    ])->info($dataSave);
                }
            }
        } catch (\Throwable $th) {
            throw $th;
            Log::build([
                'driver' => config('logging.default'),
                'path' => getUrlPathExportFileLog('all'),
                'level' => 'debug',
                'days' => config('logging.number_daily_days')
            ])->error($th);
        }
    }
    public function writeLogDB($levelLog, $uri, $typeAccess, $method, $httpReferer, $ip, $userAgent, $params, $timeCurrent, $statusCode, $queryLog, $errors, $contentAction, $logSql, $urlBrowserFe, $buttonActionFE)
    {
        try {
            $isProxy = $this->isProxyUser();
            $idUser = $this->getCurrentUser() ?? 'system';
            $idUserProxy = $this->getCurrentProxyUser();

            $request = request();
            $token = $request->bearerToken();;
            $tokenProxy = substr($request->header('originaltoken'), 7);
            if (!$tokenProxy) {
                $tokenProxy = '';
            }
            $thread = config('global.thread');
            if (!$thread) {
                $thread = randomStringUnique();
            }
            // business_type,
            $sql = "INSERT INTO z_access
                    (
                        process_id,
                        log_level,
                        api_url,
                        business_type,
                        web_access_datetime,
                        db_access_datetime,
                        method,
                        type_access,
                        remote_address,
                        http_referer,
                        user_cd,
                        user_cd_proxy,
                        user_agent,
                        parameter,
                        status_code,
                        token,
                        token_proxy,
                        link_url_fe,
                        info_button_fe,
                        created_by,
                        proxy_created_by,
                        created_at,
                        updated_by,
                        updated_at
                    ) VALUES (
                        :process_id,
                        :log_level,
                        :api_url,
                        :business_type,
                        :web_access_datetime,
                        :db_access_datetime,
                        :method,
                        :type_access,
                        :remote_address,
                        :http_referer,
                        :user_cd,
                        :user_cd_proxy,
                        :user_agent,
                        :parameter,
                        :status_code,
                        :token,
                        :token_proxy,
                        :link_url_fe,
                        :info_button_fe,
                        :created_by,
                        :proxy_created_by,
                        :created_at,
                        :updated_by,
                        :updated_at
                    )";
            $params = [
                'process_id' => $thread,
                'log_level' => $levelLog,
                'api_url' => $uri,
                'business_type' => strtoupper(explode('.', Route::currentRouteName())[0]),
                'web_access_datetime' => $timeCurrent,
                'db_access_datetime' => $timeCurrent,
                'method' => $method,
                'type_access' => $typeAccess,
                'remote_address' => $ip,
                'http_referer' => $httpReferer,
                'user_cd' => $idUser,
                'user_cd_proxy' => $idUserProxy,
                'user_agent' => $userAgent,
                'parameter' => $params ? json_encode($params) : '',
                'status_code' => $statusCode,
                'token' => $token,
                'token_proxy' => $tokenProxy,
                'link_url_fe' => $urlBrowserFe,
                'info_button_fe' => $buttonActionFE,
                'created_by' => $idUser,
                'proxy_created_by' => $isProxy ? $idUserProxy : $idUser,
                'created_at' => $timeCurrent,
                'updated_by' => $idUser,
                'updated_at' => $timeCurrent
            ];
            DB::select($sql, $params);
        } catch (\Throwable $th) {
            throw $th;
            Log::build([
                'driver' => config('logging.default'),
                'path' => getUrlPathExportFileLog('all'),
                'level' => 'debug',
                'days' => config('logging.number_daily_days')
            ])->error($th);
        }
    }
}
