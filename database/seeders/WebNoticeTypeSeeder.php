<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebNoticeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $result = '';

        $data = [
            [
                'id' => 1,
                'name' => 'email',
            ],
            [
                'id' => 2,
                'name' => 'line',
            ],
            [
                'id' => 3,
                'name' => 'slack',
            ],
            [
                'id' => 4,
                'name' => 'push',
            ],
        ];

        $count = 0;
        foreach ($data as $item) {
            $result = DB::table('web_notice_types')
                ->updateOrInsert(['id' => $item['id']], $item);
            if ($result) {
                $count++;
            }
        }
        if($count > 0) {
            $result = true;
        }
        
        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
