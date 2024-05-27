<?php

namespace Database\Seeders;

use App\Models\DriverSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DriverSchedulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $result = '';

        if (config("app.env") === "local") {
            $data = [
                [
                    'id' => 1,
                    'driver_id' => '1',
                    'available_date' => '2022-09-01',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 2,
                    'driver_id' => '1',
                    'available_date' => '2022-09-02',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 3,
                    'driver_id' => '2',
                    'available_date' => '2023-09-01',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 4,
                    'driver_id' => '2',
                    'available_date' => '2023-12-01',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'id' => 5,
                    'driver_id' => '2',
                    'available_date' => '2024-04-04',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
            ];
            $result = DB::table('driver_schedules')->insertOrIgnore($data);

            for ($i = 0; $i < 200; $i++) {
                try {
                    DriverSchedule::factory()->create();
                } catch (\Illuminate\Database\QueryException $e) {
                    Log::warning($e);
                }
            }
        }

        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
