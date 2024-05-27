<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\WebConfigSystem;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $config_system = WebConfigSystem::where('id', 1)->first();

        $schedule->command('command:cron_test')->hourlyAt(34); // 毎時34分にcronのテスト
        $schedule->command('command:system_report')->dailyAt('11:34'); // 毎日11:34にシステムレポート

        $create_task_hour_limit = $config_system->create_task_hour_limit ?? 5; 
        $schedule->command('driver_task:time_out')
            ->dailyAt($create_task_hour_limit . ':00')
            ->environments(['testing', 'production']);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
