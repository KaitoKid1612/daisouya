<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SlackNotificationService;

class SlackServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            SlackNotificationService::class // Slack通知のサービスプロバイダーの登録
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
