<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DriverPlan;

class DriverPlanSeeder extends Seeder
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
                'name' => 'プレミアム',
                'label' => 'premium',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                "id" => 2,
                'name' => 'スタンダード',
                'label' => 'standard',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                "id" => 3,
                'name' => 'ライト',
                'label' => 'lite',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
        ];

        $result = DriverPlan::insertOrIgnore($data);
        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
