<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegisterRequestDriverSeeder extends Seeder
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
                $data[] = [
                    'id' => $i,
                    'token' => uniqid(bin2hex(random_bytes(16)), true),
                    'register_request_status_id' => 1,
                    'name_sei' => 'name_sei test',
                    'name_mei' => 'name_mei test',
                    'name_sei_kana' => 'name_sei_kana test',
                    'name_mei_kana' => 'name_sei_kana test',
                    'email' => mt_rand(1, 1000) . 'regist_driver@x.com',
                    'post_code1' => '111',
                    'post_code2' => '2222',
                    'addr1_id' => 20,
                    'addr2' => '杉並区笹塚',
                    'addr3' => '88-88-88',
                    'addr4' => '建物202',
                    'tel' => '09012345678',
                    'birthday' => new \DateTime('1987-01-01'),
                    'gender_id' => 1,
                    'career' => '経歴経歴経歴経歴経歴経歴',
                    'introduction' => '紹介文紹介文紹介文紹介文紹介文',
                    'message' => '登録申請テストです。',
                    'time_limit_at' => null,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ];
            }

            $result = DB::table('register_request_drivers')->insertOrIgnore($data);
        }

        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
