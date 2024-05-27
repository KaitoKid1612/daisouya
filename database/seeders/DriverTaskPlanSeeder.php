<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DriverTaskPlan;

class DriverTaskPlanSeeder extends Seeder
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
                'system_price' => 10000,
                'system_price_percent' => null,
                'busy_system_price' => null,
                'busy_system_price_percent' => null,
                'busy_system_price_percent_over' => null,
                'emergency_price' => 20000,
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                "id" => 2,
                'name' => 'スタンダード',
                'label' => 'standard',
                'system_price' => 3000,
                'system_price_percent' => null,
                'busy_system_price' => 6000,
                'busy_system_price_percent' => null,
                'busy_system_price_percent_over' => null,
                'emergency_price' => 0,
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                "id" => 3,
                'name' => 'ライト',
                'label' => 'lite',
                'system_price' => null,
                'system_price_percent' => 20,
                'busy_system_price' => null,
                'busy_system_price_percent' => 40,
                'busy_system_price_percent_over' => 30,
                'emergency_price' => 0,
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
        ];


        $result = DriverTaskPlan::insertOrIgnore($data);
        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
