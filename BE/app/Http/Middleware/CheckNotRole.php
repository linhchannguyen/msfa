<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use App\Traits\ApiResponseTrait;
use App\Traits\RolePolicyTrait;
use App\Traits\AuthTrait;

class CheckNotRole extends BaseMiddleware
{
    use ApiResponseTrait, RolePolicyTrait, AuthTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if($this->hasAnyRole($this->getCurrentUser(), $roles)) {
            return $this->responseErrorForbidden(config('messages.MSFA0033'));
        }
        return $next($request);
    }
}