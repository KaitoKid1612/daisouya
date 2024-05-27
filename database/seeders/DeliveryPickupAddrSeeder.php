<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryPickupAddrSeeder extends Seeder
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
                    'delivery_office_id' => 1,
                    'delivery_company_name' => 'アマゾン',
                    'delivery_office_name' => '渋谷営業所',
                    'email' => 'test@x.com',
                    'tel' => '1234123412',
                    'post_code1' => '123',
                    'post_code2' => '4567',
                    'addr1_id' => mt_rand(1, 47),
                    'addr2' => 'test区',
                    'addr3' => '1-2-3',
                    'addr4' => 'test 101',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 2,
                    'delivery_office_id' => 2,
                    'delivery_company_name' => 'アマゾン',
                    'delivery_office_name' => '渋谷営業所',
                    'email' => 'test@x.com',
                    'tel' => '1234123412',
                    'post_code1' => '123',
                    'post_code2' => '4567',
                    'addr1_id' => mt_rand(1, 47),
                    'addr2' => 'test区',
                    'addr3' => '1-2-3',
                    'addr4' => 'test 101',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 3,
                    'delivery_office_id' => 1,
                    'delivery_company_name' => 'ヤマトー!',
                    'delivery_office_name' => '新宿営業所',
                    'email' => 'test2@test.test',
                    'tel' => '1234123412',
                    'post_code1' => '123',
                    'post_code2' => '4567',
                    'addr1_id' => mt_rand(1, 47),
                    'addr2' => 'test区',
                    'addr3' => '1-2-3',
                    'addr4' => 'test 101',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 4,
                    'delivery_office_id' => 2,
                    'delivery_company_name' => 'ヤマトー!',
                    'delivery_office_name' => '新宿営業所',
                    'email' => 'test2@test.test',
                    'tel' => '1234123412',
                    'post_code1' => '123',
                    'post_code2' => '4567',
                    'addr1_id' => mt_rand(1, 47),
                    'addr2' => 'test区',
                    'addr3' => '1-2-3',
                    'addr4' => 'test 101',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 5,
                    'delivery_office_id' => 2,
                    'delivery_company_name' => '佐川急便!',
                    'delivery_office_name' => '新宿営業所',
                    'email' => 'test2@test.test',
                    'tel' => '1234123412',
                    'post_code1' => '123',
                    'post_code2' => '4567',
                    'addr1_id' => mt_rand(1, 47),
                    'addr2' => 'test区',
                    'addr3' => '1-2-3',
                    'addr4' => 'test 101',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
            ];

            $result = DB::table('delivery_pickup_addrs')->insertOrIgnore($data);
        }

        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
