<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FcmDeviceTokenDriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $result = '';
        if (config("app.env") === "local") {
            $data = [];
            for ($i = 1; $i < 10; $i++) {
                $data[] = [
                    'id' => $i,
                    'driver_id' => mt_rand(1, 4),
                    'device_name' => "device_name_test_{$i}",
                    'fcm_token' => "fcm_token_test_{$i}",
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ];
            }
            $result = DB::table('fcm_device_token_drivers')->insertOrIgnore($data);
        }

        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
