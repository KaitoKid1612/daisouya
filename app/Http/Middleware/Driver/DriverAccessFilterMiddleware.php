<?php

namespace App\Http\Middleware\Driver;

use Closure;
use Illuminate\Http\Request;
use App\Libs\Driver\DriverAccessFilter;
use Illuminate\Support\Facades\Route;

/**
 * ドライバーのアクセスフィルター
 */
class DriverAccessFilterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $is_access = DriverAccessFilter::checkCurrentUrl();

        // アクセス不可の場合
        if (!$is_access) {

            // 稼働詳細ページは専用の403ページ
            $preg_path = preg_quote('driver/driver-task/show', '/');
            $path = $request->path();
            $pattern = "/^{$preg_path}/";
            if (preg_match($pattern, $path)) {
                return response()->view('errors.driver.403_driver_task_show', [], 403);
            }
            abort(403, 'Access denied アクセス権限がありません DriverAccessFilter');
        }

        return $next($request);
    }
}
