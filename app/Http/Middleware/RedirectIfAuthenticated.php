<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    // private const GUARD_USER = 'users';
    private const GUARD_DRIVER = 'drivers';
    private const GUARD_DELIVERY_OFFICE = 'delivery_offices';
    private const GUARD_ADMIN = 'admins';

    /**
     * Handle an incoming request.
     * 「ログイン済みのユーザーがアクセスしてきたらリダイレクトする」処理。
     *  例えばログイン済みユーザーがログインフォームにアクセスした時。
     *  リダイレクト先のURLはルートプロバイダ(app/Providers/RouteServiceProvider.php)で定義したもの
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // if (Auth::guard(self::GUARD_USER)->check() && $request->routeIs('user.*')) {
        //     return redirect(RouteServiceProvider::HOME);
        // }

        if (Auth::guard(self::GUARD_DRIVER)->check() && $request->routeIs('driver.*')) {
            return redirect(RouteServiceProvider::DRIVER_HOME);
        }
        
        if (Auth::guard(self::GUARD_ADMIN)->check() && $request->routeIs('admin.*')) {
            return redirect(RouteServiceProvider::ADMIN_HOME);
        }

        if (Auth::guard(self::GUARD_DELIVERY_OFFICE)->check() && $request->routeIs('delivery_office.*') || $request->routeIs('login')) {
            return redirect(RouteServiceProvider::DELIVERY_OFFICE_HOME);
        }

        return $next($request);
    }
}
