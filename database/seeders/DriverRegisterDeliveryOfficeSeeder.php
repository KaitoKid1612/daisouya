<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * ドライバーが登録した配送営業所シーダー
 */
class DriverRegisterDeliveryOfficeSeeder extends Seeder
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
            $data = [
                [
                    'id' => 1,
                    'driver_id' => 1,
                    'delivery_office_id' => 1,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 2,
                    'driver_id' => 2,
                    'delivery_office_id' => 1,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
            ];
            $result = DB::table('driver_register_delivery_offices')->insertOrIgnore($data);
        }

        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
