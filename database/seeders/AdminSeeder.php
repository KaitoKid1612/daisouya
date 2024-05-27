<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * 管理者シーダー
 */
class AdminSeeder extends Seeder
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
                    'user_type_id' => 1,
                    'name' => 'テスト 管理者',
                    'email' => 'yamada@waocon.com',
                    'password' => Hash::make('test1234'),
                    'admin_permission_group_id' => 1,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 2,
                    'user_type_id' => 1,
                    'name' => 'テスト名前',
                    'email' => 't@x.com',
                    'password' => Hash::make('test1234'),
                    'admin_permission_group_id' => 1,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
            ];
            $result = DB::table('admins')->insertOrIgnore($data);
        }

        if (config("app.env") === "local") {
            $data = [
                [
                    'id' => 1,
                    'user_type_id' => 1,
                    'name' => 'テスト名前',
                    'email' => 't@x.com',
                    'password' => Hash::make('test1234'),
                    'admin_permission_group_id' => 1,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 2,
                    'user_type_id' => 1,
                    'name' => 'テスト2名前',
                    'email' => 't2@x.com',
                    'password' => Hash::make('test1234'),
                    'admin_permission_group_id' => 2,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 3,
                    'user_type_id' => 1,
                    'name' => 'テスト3名前',
                    'email' => 't3@x.com',
                    'password' => Hash::make('test1234'),
                    'admin_permission_group_id' => 3,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
            ];
            $result = DB::table('admins')->insertOrIgnore($data);
        }

        if (config("app.env") === "production") {
            $data = [
                [
                    'id' => 1,
                    'user_type_id' => 1,
                    'name' => 'エンジニア YAMADA',
                    'email' => 'yamada@waocon.com',
                    'password' => Hash::make('init_q6P897ys'),
                    'admin_permission_group_id' => 1,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 2,
                    'user_type_id' => 1,
                    'name' => 'ケーズリング',
                    'email' => 'info@ks-ring.co.jp',
                    'password' => Hash::make('init_dH4Qytu6'),
                    'admin_permission_group_id' => 2,
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
            ];

            $result = DB::table('admins')->insertOrIgnore($data);
        }

        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
