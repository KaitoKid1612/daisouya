<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Driver;
use Database\Factories\DriverFactory;

/**
 * ドライバーシーダー
 */
class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $result = '';

        // テスト環境の場合
        if (config("app.env") === "testing") {
            $data = [
                [
                    'id' => 1,
                    'user_type_id' => 3,
                    'driver_plan_id' => 1,
                    'name_sei' => 'name_sei test',
                    'name_mei' => 'name_mei test',
                    'name_sei_kana' => 'name_sei_kana test',
                    'name_mei_kana' => 'name_sei_kana test',
                    'email' => 'yamada@waocon.com',
                    'password' => Hash::make('test1234'),
                    'post_code1' => '111',
                    'post_code2' => '2222',
                    'addr1_id' => 20,
                    'addr2' => '杉並区笹塚',
                    'addr3' => '88-88-88',
                    'addr4' => '建物名 部屋番号',
                    'tel' => '09012345678',
                    'birthday' => new \DateTime('1987-01-01'),
                    'gender_id' => 1,
                    'icon_img' => 'driver/user_icon/2022/08/test.png',
                    'career' => '経歴経歴経歴経歴経歴経歴',
                    'introduction' => '紹介文紹介文紹介文紹介文紹介文',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
            ];
            $result = DB::table('drivers')->insertOrIgnore($data);
        } elseif (config("app.env") === "local") {

            $data = [
                [
                    'id' => 1,
                    'user_type_id' => 3,
                    'driver_plan_id' => 1,
                    'name_sei' => '山田',
                    'name_mei' => '太郎',
                    'name_sei_kana' => 'ヤマダ',
                    'name_mei_kana' => 'タロウ',
                    'email' => 't@x.com',
                    'password' => Hash::make('test1234'),
                    'post_code1' => '111',
                    'post_code2' => '2222',
                    'addr1_id' => 20,
                    'addr2' => '杉並区笹塚',
                    'addr3' => '88-88-88',
                    'addr4' => 'test 901',
                    'tel' => '09012345678',
                    'birthday' => new \DateTime('1987-01-01'),
                    'gender_id' => 1,
                    'icon_img' => 'delivery_office/user_icon/2022/08/test.png',
                    'career' => '経歴経歴経歴経歴経歴経歴',
                    'introduction' => '紹介文紹介文紹介文紹介文紹介文',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 2,
                    'user_type_id' => 3,
                    'driver_plan_id' => 2,
                    'name_sei' => '佐藤',
                    'name_mei' => '花子',
                    'name_sei_kana' => 'さとう',
                    'name_mei_kana' => 'はなこ',
                    'email' => 't2@x.com',
                    'password' => Hash::make('test1234'),
                    'post_code1' => '111',
                    'post_code2' => '2222',
                    'addr1_id' => 20,
                    'addr2' => '渋谷区道玄坂',
                    'addr3' => '88-88-88',
                    'addr4' => 'test 901',
                    'tel' => '09012345678',
                    'birthday' => new \DateTime('1987-01-01'),
                    'gender_id' => 2,
                    'icon_img' => 'delivery_office/user_icon/2022/08/test.png',
                    'career' => '経歴経歴経歴経歴経歴経歴',
                    'introduction' => '紹介文紹介文紹介文紹介文紹介文',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
            ];
            $result = DB::table('drivers')->insertOrIgnore($data);

            Driver::factory()->count(30)->create();
        }

        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
