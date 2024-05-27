<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class BasicAuthMiddleware
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
        $host = $_SERVER['HTTP_HOST'] ?? '';

        /* ステージング環境でベーシック認証をかける /api は除外 */
        if (preg_match("/52\.196\.20\.105/", $host) || $host == 'waocontest003.jp') {
            if (!Route::is('api.*')) {
                $username = $request->getUser();
                $password = $request->getPassword();

                if ($username == 'waocon' && $password == 'Nj8cf9ed') {
                    return $next($request);
                }

                abort(401, "Enter username and password.", [
                    header('WWW-Authenticate: Basic realm="Sample Private Page"'),
                    header('Content-Type: text/plain; charset=utf-8')
                ]);
            }
        }

        return $next($request);
    }
}
