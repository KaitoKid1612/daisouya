<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DriverTaskRefundStatusSeeder extends Seeder
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
                "id" => 1,
                'name' => '返金なし',
                'label' => 'none',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                "id" => 2,
                'name' => '返金前',
                'label' => 'before_refund',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                "id" => 3,
                'name' => '返金済み',
                'label' => 'refunded',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
        ];

        $result = DB::table('driver_task_refund_statuses')->insertOrIgnore($data);
        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
