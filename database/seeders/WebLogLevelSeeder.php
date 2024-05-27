<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebLogLevelSeeder extends Seeder
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
                'name' => 'emergency'
            ],
            [
                'id' => 2,
                'name' => 'alert'
            ],
            [
                'id' => 3,
                'name' => 'critical'
            ],
            [
                'id' => 4,
                'name' => 'error'
            ],
            [
                'id' => 5,
                'name' => 'warning'
            ],
            [
                'id' => 6,
                'name' => 'notice'
            ],
            [
                'id' => 7,
                'name' => 'info'
            ],
            [
                'id' => 8,
                'name' => 'debug'
            ],
        ];
        
        $result = DB::table('web_log_levels')->insertOrIgnore($data);
        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
