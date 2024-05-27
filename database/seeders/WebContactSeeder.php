<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebContactSeeder extends Seeder
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
                    'user_type_id' => 4,
                    'user_id' => null,
                    'name_sei' => 'sei',
                    'name_mei' => 'mei',
                    'name_sei_kana' => 'mei kana',
                    'name_mei_kana' => 'mei_kana',
                    'email' => 'email',
                    'tel' => 'tel',

                    'web_contact_type_id' => 1,
                    'web_contact_status_id' => 1,
                    'title' => 'test題目 質問要望',
                    'text' => 'test内容test内容test内容test内容test内容test内容test内容test内容test内容test内容test内容test内容test内容test内容test内容test内容',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 2,
                    'user_type_id' => 2,
                    'user_id' => 1,
                    'name_sei' => 'sei',
                    'name_mei' => 'mei',
                    'name_sei_kana' => 'mei kana',
                    'name_mei_kana' => 'mei_kana',
                    'email' => 'email',
                    'tel' => 'tel',
                    'web_contact_type_id' => 1,
                    'web_contact_status_id' => 4,
                    'title' => 'test題目 質問要望',
                    'text' => 'test内容test内容test内容test内容test内容test内容test内容test内容test内容test内容test内容test内容test内容test内容test内容test内容',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
            ];
            $result = DB::table('web_contacts')->insertOrIgnore($data);
        }

        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
