<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegisterRequestStatusSeeder extends Seeder
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
                'name' => '新規',
                'label' => 'new',
                'explanation' => '新規の登録申請',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                'id' => 2,
                'name' => '許可',
                'label' => 'pass',
                'explanation' => '登録申請を許可',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                'id' => 3,
                'name' => '不可',
                'label' => 'rejection',
                'explanation' => '登録申請を拒否',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                'id' => 4,
                'name' => '登録処理済み',
                'label' => 'done',
                'explanation' => '申請者が登録処理を行った状態',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                'id' => 5,
                'name' => '無効',
                'label' => 'invalid',
                'explanation' => '登録申請を無効(時間切れ、登録情報不備)',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                'id' => 6,
                'name' => '審査中',
                'label' => 'examination',
                'explanation' => '申請者を審査中',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                'id' => 7,
                'name' => '審査中(登録処理済み)',
                'label' => 'examination',
                'explanation' => '審査中。申請者のユーザーデータが登録処理済み(一部の権限のみ付与されている)。',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
        ];

        $result = DB::table('register_request_statuses')->insertOrIgnore($data);
        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
