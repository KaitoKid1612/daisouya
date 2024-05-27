<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
    // protected string $user_route  = 'user.login';
    protected string $driver_route = 'driver.login';
    protected string $admin_route = 'admin.login';
    protected string $delivery_office_route = 'delivery_office.login';

    /**
     * アクセスしたユーザーが未認証の場合のリダイレクト処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            // if (Route::is('user.*')) {
            //     return route($this->user_route);
            // } 
            if (Route::is('delivery_office.*')) {
                return route($this->delivery_office_route);
            } elseif (Route::is('driver.*')) {
                return route($this->driver_route);
            } elseif (Route::is('admin.*')) {
                return route($this->admin_route);
            }
        }
    }
}
