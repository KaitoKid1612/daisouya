<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'name' => '管理者',
                'label' => 'admin',
                'explanation' => '管理者',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                'id' => 2,
                'name' => '依頼者',
                'label' => 'delivery_office',
                'explanation' => 'ドライバーに稼働を依頼する配送営業所や配送請負会社など。',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                'id' => 3,
                'name' => 'ドライバー',
                'label' => 'driver',
                'explanation' => 'ドライバー',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                'id' => 4,
                'name' => '非会員(ゲスト)',
                'label' => 'guest',
                'explanation' => 'ゲスト',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
        ];

        $result = DB::table('user_types')->insertOrIgnore($data);
        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
