<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegisterRequestDeliveryOfficeSeeder extends Seeder
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

            $data = [];
            for ($i = 1; $i < 6; $i++) {
                $data[] =
                    [
                        'id' => $i,
                        'token' => uniqid(bin2hex(random_bytes(16)), true),
                        'register_request_status_id' => mt_rand(1, 4),
                        'name' => 'test営業所',
                        'manager_name_sei' => '山田',
                        'manager_name_mei' => '太郎',
                        'manager_name_sei_kana' => 'ヤマダ',
                        'manager_name_mei_kana' => 'タロウ',
                        'email' => mt_rand(1, 1000) . 'regist@x.com',
                        'delivery_company_id' => 1,
                        'delivery_company_name' => '',
                        'delivery_office_type_id' => 1,
                        'post_code1' => '123',
                        'post_code2' => '4567',
                        'addr1_id' => 1,
                        'addr2' => 'test丁目',
                        'addr3' => '1-2-3',
                        'addr4' => '建物101',
                        'manager_tel' => '09012341234',
                        'message' => '登録申請テストです。',
                        'time_limit_at' => new \DateTime(),
                        'created_at' => new \DateTime(),
                        'updated_at' => new \DateTime(),
                    ];
            }

            $result =  DB::table('register_request_delivery_offices')->insertOrIgnore($data);
        }

        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
