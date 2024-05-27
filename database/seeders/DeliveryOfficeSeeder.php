<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\DeliveryOffice;

/**
 * 配送営業所シーダー
 */
class DeliveryOfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $result = '';
        if (config("app.env") === "testing") {
            $data = [
                [
                    'id' => 1,
                    'user_type_id' => 2,
                    'name' => '渋谷営業所',
                    'manager_name_sei' => '山田',
                    'manager_name_mei' => '太郎',
                    'manager_name_sei_kana' => 'ヤマダ',
                    'manager_name_mei_kana' => 'タロウ',
                    'email' => 'yamada@waocon.com',
                    'password' => Hash::make('test1234'),
                    'delivery_company_id' => 1,
                    'delivery_company_name' => '',
                    'delivery_office_type_id' => 1,
                    'post_code1' => '123',
                    'post_code2' => '4567',
                    'addr1_id' => '33',
                    'addr2' => '新宿',
                    'addr3' => 't-t-t',
                    'addr4' => 'マンション101',
                    'manager_tel' => '0101010101',
                    'charge_user_type_id' => 1,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
            ];

            $result = DB::table('delivery_offices')->insertOrIgnore($data);
        } elseif (config("app.env") === "local") {
            $data = [
                [
                    'id' => 1,
                    'user_type_id' => 2,
                    'name' => '渋谷営業所',
                    'manager_name_sei' => '山田',
                    'manager_name_mei' => '太郎',
                    'manager_name_sei_kana' => 'ヤマダ',
                    'manager_name_mei_kana' => 'タロウ',
                    'email' => 't@x.com',
                    'password' => Hash::make('test1234'),
                    'delivery_company_id' => 1,
                    'delivery_company_name' => '',
                    'delivery_office_type_id' => 2,
                    'post_code1' => '123',
                    'post_code2' => '4567',
                    'addr1_id' => '33',
                    'addr2' => '渋谷',
                    'addr3' => 't-t-t',
                    'addr4' => 'マンション101',
                    'manager_tel' => '0101010101',
                    // 'stripe_id' => 'cus_N6dFSlMrS1Suvt', // 個人用
                    'stripe_id' => 'cus_OX2LmY9NETn9db', // チーム開発用
                    'pm_type' => 'visa',
                    'pm_last_four' => '1111',
                    'charge_user_type_id' => 1,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 2,
                    'user_type_id' => 2,
                    'name' => '新宿テスト営業所',
                    'manager_name_sei' => '佐藤',
                    'manager_name_mei' => '花子',
                    'manager_name_sei_kana' => '佐藤',
                    'manager_name_mei_kana' => '花子',
                    'email' => 't2@x.com',
                    'password' => Hash::make('test1234'),
                    'delivery_company_id' => 1,
                    'delivery_company_name' => '',
                    'delivery_office_type_id' => 2,
                    'post_code1' => '123',
                    'post_code2' => '4567',
                    'addr1_id' => '33',
                    'addr2' => '新宿',
                    'addr3' => 't-t-t',
                    'addr4' => 'マンション101',
                    'manager_tel' => '0101010101',
                    // 'stripe_id' => 'cus_NkJQp19LHT1wUE',
                    'stripe_id' => null,
                    'pm_type' => 'visa',
                    'pm_last_four' => '1111',
                    'charge_user_type_id' => 2,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
            ];
            
            $result = DB::table('delivery_offices')->insertOrIgnore($data);
            DeliveryOffice::factory()->count(101)->create();
        }

        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }

        /* nullの請求タイプを一般にする */
        DB::table('delivery_offices')->whereNull('charge_user_type_id')->update([
            'charge_user_type_id' => 1,
        ]);
    }
}
