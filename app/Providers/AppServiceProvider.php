<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use App\Models\DeliveryOffice;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Cashier::ignoreMigrations(); // Cashierのデフォルトのマイグレーションを無視
        Sanctum::ignoreMigrations(); // Sanctumのデフォルトマイグレーションを無視
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Cashier::useCustomerModel(DeliveryOffice::class); // Cashierのモデル変更

        // @todo ローカル以外なら常時SSL化 
        // if(request()->ip() != "172.18.0.1" && request()->ip() != "127.0.0.1" && request()->ip() != "::1" && request()->getHost() != "localhost"){
        //     URL::forceScheme('https');
        // }     
    }
}
