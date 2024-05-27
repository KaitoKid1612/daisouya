<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DriverRegisterDeliveryOfficeMemoSeeder extends Seeder
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
                    'delivery_company_id' => '1',
                    'delivery_office_name' => 'test営業所',
                    'post_code1' => '012',
                    'post_code2' => '0123',
                    'addr1_id' => 1,
                    'addr2' => '札幌市',
                    'addr3' => 'test町1-1-1',
                    'addr4' => 'testビル101',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 2,
                    'driver_id' => 1,
                    'delivery_company_id' => '2',
                    'delivery_office_name' => 'test営業所2',
                    'post_code1' => '012',
                    'post_code2' => '0123',
                    'addr1_id' => 2,
                    'addr2' => '津軽',
                    'addr3' => 'test町1-1-1',
                    'addr4' => 'testビル101',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 3,
                    'driver_id' => 2,
                    'delivery_company_id' => '3',
                    'delivery_office_name' => 'test営業所3',
                    'post_code1' => '012',
                    'post_code2' => '0123',
                    'addr1_id' => 2,
                    'addr2' => 'りんご農園',
                    'addr3' => 'test町1-1-1',
                    'addr4' => 'testビル101',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 4,
                    'driver_id' => 2,
                    'delivery_company_id' => '1',
                    'delivery_office_name' => 'test営業所4',
                    'post_code1' => '012',
                    'post_code2' => '0123',
                    'addr1_id' => 47,
                    'addr2' => 'バナナ農園',
                    'addr3' => 'test町1-1-1',
                    'addr4' => 'testビル101',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
            ];

            $result = DB::table('driver_register_delivery_office_memos')->insertOrIgnore($data);
        }

        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
