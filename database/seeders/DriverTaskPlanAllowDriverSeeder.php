<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DriverTaskPlanAllowDriver;

class DriverTaskPlanAllowDriverSeeder extends Seeder
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
                'driver_task_plan_id' => 1,
                'driver_plan_id' => 1,
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                "id" => 2,
                'driver_task_plan_id' => 2,
                'driver_plan_id' => 1,
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                "id" => 3,
                'driver_task_plan_id' => 2,
                'driver_plan_id' => 2,
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                "id" => 4,
                'driver_task_plan_id' => 3,
                'driver_plan_id' => 1,
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                "id" => 5,
                'driver_task_plan_id' => 3,
                'driver_plan_id' => 2,
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                "id" => 6,
                'driver_task_plan_id' => 3,
                'driver_plan_id' => 3,
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],

        ];

        $result = DriverTaskPlanAllowDriver::insertOrIgnore($data);
        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
