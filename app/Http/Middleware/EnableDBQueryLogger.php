<?php

namespace App\Http\Middleware;

use Closure;
use DB;

class EnableDBQueryLogger
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
        if (env('APP_DEBUG')) {
            DB::enableQueryLog();
        }

        return $next($request);
    }
}
