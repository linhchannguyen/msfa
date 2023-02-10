<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Traits\ApiResponseTrait;
use App\Enums\ResponseCode;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use App\Traits\AuthTrait;
use App\Traits\Base64StringFileTrait;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait, AuthTrait, Base64StringFileTrait;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
    }

    public function render($request, Throwable $e)
    {
        try {
            $typeErrorCode = $this->FriendlyErrorType($e->getSeverity());
        } catch (\Throwable $th) {
            $typeErrorCode = "";
        }
        $thread = config('global.thread');
        if ($thread == null) {
            $thread = randomStringUnique();
            config(['global.thread' => $thread]);
        }
        // HUYNH - SAVE LOG TO FILE AND SQL - 20220223
        $this->handleException($e, $thread, $typeErrorCode);
        return $this->responseSystemError(__('develop.system_error'));
    }

    private function handleException(Throwable $e, $thread, $typeErrorCode)
    {
        $contentError = "
        エラーレベル : " .  $typeErrorCode . "<br>
        Error Content: " . $e->getMessage() . "<br>
        Error File : " . $e->getFile() . "<br>
        Error Line : " . $e->getLine() . "<br>
        Error Code : " . $e->getCode() . "<br>
        Error Trace : " . $e->getTraceAsString();
        $request = request();
        $request_server = '';
        $dataIgnoreShow = config('environment');
        foreach ($_SERVER as $key => $value) {
            if (!in_array($key, $dataIgnoreShow)) {
                if('REQUEST_URI' == $key || 'QUERY_STRING' == $key){
                    $value = urldecode($value);
                }
                $request_server .= $key . " : " . ((is_array($value) || is_object($value)) ? json_encode($value) : $value) . " <br>";
            }
        }
        $request_server = rtrim($request_server, " <br>");
        $content_param = "{";
        recursiveParam($request->all(), $content_param);
        $content_param = rtrim($content_param, '; ');
        $content_param .= "}";
        $contentErrorMail = "
        Error Content : " . $e->getMessage() . " <br>
        Error File : " . $e->getFile() . " <br>
        Error Line : " . $e->getLine() . " <br>
        Error Code : " . $e->getCode() . " <br>
        Error Trace : " . $e->getTraceAsString() . " <br>
        Request User : " . $this->getCurrentUser() . " <br>
        Request Action : " . $request->path() . " <br>
        Request Method : " . $request->getMethod() . " <br>
        Request Params : " . $content_param . " <br>
        Request Server : <br>" . $request_server . " <br>
        Request Token : " . $request->bearerToken() . " <br>
        Request Token Proxy : " . substr($request->header('originaltoken'), 7) . " <br>";

        $code = ResponseCode::INTERNAL_SERVER_ERROR;

        if ($e instanceof \Illuminate\Database\QueryException || $e instanceof \PDOException) {
            saveInfoLog(
                'PROCESS',
                $code,
                '',
                NULL,
                $contentError,
                NULL,
                FALSE,
                TRUE,
                $contentErrorMail,
                true,
                $typeErrorCode
            );
            return;
        }
        if ($e instanceof AuthorizationException) {
            $code = ResponseCode::FORBIDENT;
        } else
        if ($e instanceof MethodNotAllowedHttpException) {
            $code = ResponseCode::NOT_ALLOWED;
        } else
        if ($e instanceof NotFoundHttpException) {
            $code = ResponseCode::NOT_FOUND;
        } else
        if ($e instanceof ServiceUnavailableHttpException) {
            $code = ResponseCode::SERVICE_UNAVAILABLE;
        } else {
            $code = ResponseCode::INTERNAL_SERVER_ERROR;
        }
        saveInfoLog(
            'PROCESS',
            $code,
            '',
            NULL,
            $contentError,
            NULL,
            FALSE,
            FALSE,
            $contentErrorMail,
            false,
            $typeErrorCode
        );
    }
    private function FriendlyErrorType($type)
    {
        switch($type)
        {
            case 1: // 1 //
                return 'E_ERROR';
            case 2: // 2 //
                return 'E_WARNING';
            case 4: // 4 //
                return 'E_PARSE';
            case 8: // 8 //
                return 'E_NOTICE';
            case 16: // 16 //
                return 'E_CORE_ERROR';
            case 32: // 32 //
                return 'E_CORE_WARNING';
            case 64: // 64 //
                return 'E_COMPILE_ERROR';
            case 128: // 128 //
                return 'E_COMPILE_WARNING';
            case 256: // 256 //
                return 'E_USER_ERROR';
            case 512: // 512 //
                return 'E_USER_WARNING';
            case 1024: // 1024 //
                return 'E_USER_NOTICE';
            case 2048: // 2048 //
                return 'E_STRICT';
            case 4096: // 4096 //
                return 'E_RECOVERABLE_ERROR';
            case 8192: // 8192 //
                return 'E_DEPRECATED';
            case 16384: // 16384 //
                return 'E_USER_DEPRECATED';
        }
        return "";
    }
}
