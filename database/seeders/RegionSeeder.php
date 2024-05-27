<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
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
                'name' => '北海道',
                'name_kana' => 'ホッカイドウ',
                'name_romaji' => 'hokkaido',
            ],
            [
                'id' => 2,
                'name' => '東北',
                'name_kana' => 'トウホク',
                'name_romaji' => 'tohoku',
            ],
            [
                'id' => 3,
                'name' => '関東',
                'name_kana' => 'カントウ',
                'name_romaji' => 'kanto',
            ],
            [
                'id' => 4,
                'name' => '中部',
                'name_kana' => 'チュウブ',
                'name_romaji' => 'Chubu',
            ],
            [
                'id' => 5,
                'name' => '近畿',
                'name_kana' => 'カンサイ',
                'name_romaji' => 'kansai',
            ],
            [
                'id' => 6,
                'name' => '中国',
                'name_kana' => 'チュウゴク',
                'name_romaji' => 'chugoku',
            ],
            [
                'id' => 7,
                'name' => '四国',
                'name_kana' => 'シコク',
                'name_romaji' => 'shikoku',
            ],
            [
                'id' => 8,
                'name' => '九州',
                'name_kana' => 'キュウシュウ',
                'name_romaji' => 'kyusyu',
            ],
        ];

        $result = DB::table('regions')->insertOrIgnore($data);
        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
