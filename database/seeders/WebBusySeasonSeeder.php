<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WebBusySeason;
use Illuminate\Support\Facades\Log;

class WebBusySeasonSeeder extends Seeder
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
                    'busy_date' => '2024-01-01',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
                [
                    'busy_date' => '2024-02-01',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                ],
            ];


            for ($i = 0; $i < 200; $i++) {
                try {
                    WebBusySeason::factory()->create();
                } catch (\Illuminate\Database\QueryException $e) {
                    Log::warning($e);
                }
            }
            $result = WebBusySeason::insertOrIgnore($data);
        }

        
        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
