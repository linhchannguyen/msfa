<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AuthTrait;

class ActionLog
{
    use AuthTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        DB::enableQueryLog();
        return $next($request);
    }

    /**
     * Handle tasks after the response hes been sent to the browser
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function terminate($request, $response)
    {
        $queryLog = DB::getQueryLog();
        $thread = config('global.thread');
        if ($thread == null) {
            $thread = randomStringUnique();
            config(['global.thread' => $thread]);
        }
        // mt hiện tai , có status INFO : CHẮC CHẮN LÀ XUẤT
        // mt hiện tại ko có info : INFO STATUS == 200 sẽ không xuất
        $statusCode = $response->status();
        // save log for api log success
        if ($statusCode >= 200 && $statusCode <= 299) {
            saveInfoLog('PROCESS', $response->status(), '', NULL, NULL, NULL); // write log for api
        }

        saveInfoLog('PROCESS', $response->status(), '', $queryLog, NULL, NULL, TRUE); // write log for sql
    }
}
