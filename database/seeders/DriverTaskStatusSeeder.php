<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 稼働ステータスシーダー
 */
class DriverTaskStatusSeeder extends Seeder
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
                'explanation' => '営業所が依頼の登録',
            ],
            [
                'id' => 2,
                'name' => '新規(指名)',
                'label' => 'new appointment',
                'explanation' => '営業所がドライバーを指名して依頼が登録された状態',
            ],
            [
                'id' => 3,
                'name' => '受諾',
                'label' => 'accept',
                'explanation' => 'ドライバーが依頼を受諾した状態',
            ],
            [
                'id' => 4,
                'name' => '完了',
                'label' => 'complete',
                'explanation' => 'ドライバーが稼働完了した',
            ],
            [
                'id' => 5,
                'name' => '却下',
                'label' => 'reject',
                'explanation' => 'ドライバーが稼働(指名)を却下',
            ],
            [
                'id' => 6,
                'name' => '時間切れ',
                'label' => 'time out',
                'explanation' => 'ドライバーが期限までに受諾しなかった',
            ],
            [
                'id' => 7,
                'name' => 'キャンセル',
                'label' => 'cancel',
                'explanation' => '営業所が稼働依頼をキャンセル',
            ],
            [
                'id' => 8,
                'name' => '不履行',
                'label' => 'failure',
                'explanation' => 'ドライバーが稼働遂行出来なかった',
            ],
            [
                'id' => 9,
                'name' => '無効',
                'label' => 'invalid',
                'explanation' => '稼働依頼が無効(システムなどの問題)',
            ],
            [
                'id' => 10,
                'name' => '決済未設定',
                'label' => 'waiting payment',
                'explanation' => 'ドライバーが稼働を受諾したが、決済が行われていない',
            ],
            [
                'id' => 11,
                'name' => '決済準備完了',
                'label' => 'ready payment',
                'explanation' => '依頼者の決済の準備が完了',
            ],
        ];

        $result = DB::table('driver_task_statuses')->insertOrIgnore($data);
        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }

    }
}
