<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use App\Traits\ApiResponseTrait;

class JwtMiddleware extends BaseMiddleware
{
    use ApiResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if($request->header('OriginalToken')){
                $originaltoken = str_replace('Bearer ' , '' ,$request->header('OriginalToken'));
                JWTAuth::setToken($originaltoken);
                $token = JWTAuth::getToken();
                $payload = JWTAuth::decode($token)->toArray();
                config()->set('global.proxy_sub', $payload['sub']);
            }else{
                config()->set('global.proxy_sub', null);
            }
            
            $user = JWTAuth::parseToken()->authenticate();
            $user_sub = JWTAuth::parseToken()->getPayload()->get('sub');
            // replace normal login to proxy login
            config()->set('global.jwt_sub', $user_sub);
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return $this->responseErrorForbidden(__('auth.token_is_invalid'));
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return $this->responseErrorForbidden(__('auth.token_has_expired'));
            }else{
                return $this->responseErrorForbidden(__('auth.token_not_found'));
            }
        }
        return $next($request);
    }
}