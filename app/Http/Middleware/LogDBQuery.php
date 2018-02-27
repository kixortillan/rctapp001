<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Log;

class LogDBQuery
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $response = $next($request);

        //Log SQL Queries
        if (env('APP_ENV')) {
            Log::debug(DB::getQueryLog());
        }

        return $response;
    }
}
