<?php

namespace App\Http\Middleware;

use Closure;

class PerformanceDebug
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response =  $next($request);

        if (app()->isLocal()) {
            // 计算包含了多少文件
            $include_files_count = count(get_included_files());

            dd($include_files_count);
        }

        return $response;
    }
}
