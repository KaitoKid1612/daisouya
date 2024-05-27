<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Factories\DriverTaskFactory;

class WebConfigSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (config("app.env") === "local") {
            $data = [
                [
                    'id' => 1,
                    'email_notice' => 'email_notice@x.com',
                    'email_from' => 'email_from@x.com',
                    'email_reply_to' => 'email_reply_to@x.com',
                    'email_no_reply' => 'daisouya-noreply@x.com',
                    'create_task_time_limit_from' => '1',
                    'create_task_time_limit_to' => '45',
                    'create_task_hour_limit' => '10',
                    'task_time_out_later' => '0',
                    'register_request_token_time_limit' => '72',
                    'default_price' => 10000,
                    'default_emergency_price' => 10000,
                    'default_tax_rate' => 10.0,
                    'default_stripe_payment_fee_rate' => 3.6,
                    'soon_price_time_limit_from' => -5,
                    'soon_price_time_limit_to' => 4,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ]
            ];
        } elseif (config("app.env") === "testing") {
            $data = [
                [
                    'id' => 1,
                    'email_notice' => 'yamada@waocon.com',
                    'email_from' => 'aws-ksring-test@waocontest003.jp',
                    'email_reply_to' => 'reply@waocontest003.jp',
                    'email_no_reply' => 'daisouya-noreply@waocontest003.jp',
                    'create_task_time_limit_from' => '3',
                    'create_task_time_limit_to' => '45',
                    'create_task_hour_limit' => '10',
                    'task_time_out_later' => '12',
                    'register_request_token_time_limit' => '72',
                    'default_price' => 10000,
                    'default_emergency_price' => 10000,
                    'default_tax_rate' => 10.0,
                    'default_stripe_payment_fee_rate' => 3.6,
                    'soon_price_time_limit_from' => -5,
                    'soon_price_time_limit_to' => 4,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ]
            ];
        } elseif (config("app.env") === "production") {
            $data = [
                [
                    'id' => 1,
                    'email_notice' => 'yamada@waocon.com',
                    'email_from' => 'aws-ksring-ses@daisouya.com',
                    'email_reply_to' => 'yamada@waocon.com',
                    'email_no_reply' => 'daisouya-noreply@daisouya.com',
                    'create_task_time_limit_from' => '0',
                    'create_task_time_limit_to' => '45',
                    'create_task_hour_limit' => '6',
                    'task_time_out_later' => '0',
                    'register_request_token_time_limit' => '72',
                    'default_price' => 10000,
                    'default_emergency_price' => 10000,
                    'default_tax_rate' => 10.0,
                    'default_stripe_payment_fee_rate' => 3.6,
                    'soon_price_time_limit_from' => -6,
                    'soon_price_time_limit_to' => 6,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
            ];
        }
        $result = DB::table('web_config_systems')->insertOrIgnore($data);
        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
