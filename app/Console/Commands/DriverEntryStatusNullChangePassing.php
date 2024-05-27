<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use App\Models\Driver;

/**
 * driver_entry_status_id が null の既存ドライバーユーザーを 通過ステータス に変更
 * 
 * 審査中機能をデプロイするときの、一度しか使わない。
 */
class DriverEntryStatusNullChangePassing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:driver_entry_status_null_change_passing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'driver_entry_status_id が null の既存ドライバーユーザーを 通過ステータス に変更';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $class = __CLASS__;
        Log::info("Start Cron {$class}");

        $flg = true; // 実行するかどうかの判定。一度しか利用する予定ないので、使ったらfalseにする。
        if ($flg) {


            try {
                Driver::withTrashed()->where('driver_entry_status_id', '=', null)->update([
                    'driver_entry_status_id' => '1',
                ]);
            } catch (\Throwable $e) {
                Log::error($e);
            }

            
        } else {
            Log::info("利用不可: {$class}");
        }

        Log::info("End Cron {$class}");
        return 0;
    }
}
