<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use App\Models\Driver;
use App\Models\DeliveryOffice;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * ユーザーのパスワードをテスト用に変更する。
 */
class UserTestPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:user_test_password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ユーザーのパスワードをテスト用に変更する';

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
        $app_env = config("app.env");
        if ($app_env !== "production") {
            Log::info("Start command {$class}");

            $password = Hash::make('test1234');
            Admin::query()->update([
                'password' => $password,
            ]);
            Driver::query()->update([
                'password' => $password,
            ]);
            DeliveryOffice::query()->update([
                'password' => $password,
            ]);

            $msg = "Done {$class}\n";
            echo $msg;
            Log::info($msg);
        } else {
            $msg = "{$app_env}!! can't command {$class}\n";
            echo $msg;
            Log::info($msg);
        }
        
        return 0;
    }
}
