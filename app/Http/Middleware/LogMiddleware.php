<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use Request;
use DB;
use Cache;
use App\Http\Service\SearchApi\SrvBaseService;

class LogMiddleware
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
        // 开启sql日志这里只有默认数据库
        DB::enableQueryLog();
        $response = $next($request);
        $url = Request::fullUrl();
        // 数据库查询进行日志
        $queries = DB::getQueryLog();
        $ip = Request::getClientIp();

        $runTime = (round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 8)*1000) . 'ms';
        Log::useFiles(app()->storagePath().'/logs/crawler-job-php-log-'.date("Y-m-d").'.log','info');
        Log::info(
            ' url'.$url.' IP:'.$ip.' runTime:'.$runTime.' sql:'.json_encode($queries)
        );
        return $response;
    }
}
