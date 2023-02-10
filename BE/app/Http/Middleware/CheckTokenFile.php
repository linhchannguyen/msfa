<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use App\Traits\ApiResponseTrait;
use App\Traits\Base64StringFileTrait;

class CheckTokenFile extends BaseMiddleware
{
    use ApiResponseTrait, Base64StringFileTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = '';
        // check token on header
        if ($request->header('Authorization')) {
            $tokenCheck = str_replace('Bearer ' , '' ,$request->header('Authorization'));
            try {
                JWTAuth::setToken($tokenCheck);
                $token = JWTAuth::getToken();
                $payload = JWTAuth::decode($token)->toArray();
            } catch (\Exception $e) {
                return $this->responseErrorForbidden(__('auth.token_is_invalid'));
            }
        }
        // check token params
        $filename = $request->route()->parameter('filename');
        // check is login
        $tokenParamCheck = $this->decodeStringBase64Token($filename);
        $tokenParamCheck = str_replace('Bearer ' , '' , $tokenParamCheck);

        try {
            JWTAuth::setToken($tokenParamCheck);
            $token = JWTAuth::getToken();
            $payload = JWTAuth::decode($token)->toArray();
        } catch (\Exception $e) {
            return $this->responseErrorForbidden(__('auth.token_is_invalid'));
        }
        return $next($request);
    }
}
