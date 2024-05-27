<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebPaymentLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $result = '';

        if (config("app.env") === "local" || config("app.env") === "testing") {

            $data = [
                [
                    'id' => 1,
                    'date' => new \DateTime(),
                    'amount_money' => 99999,
                    'driver_task_id' => 2,
                    'web_payment_log_status_id' => 2,
                    'web_payment_reason_id' => 2,
                    'message' => 'testだよ',
                    'pay_user_id' => 2,
                    'pay_user_type_id' => 2,
                    'receive_user_id' => 2,
                    'receive_user_type_id' => 3,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
            ];

            $result = DB::table('web_payment_logs')->insertOrIgnore($data);
        }

        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
