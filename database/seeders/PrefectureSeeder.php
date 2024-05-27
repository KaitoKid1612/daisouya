<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrefectureSeeder extends Seeder
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
                'name_romaji' => 'Hokkaido',
                'region_id' => 1,
            ],
            [
                'id' => 2,
                'name' => '青森県',
                'name_kana' => 'アオモリ',
                'name_romaji' => 'Aomori',
                'region_id' => 2,
            ],
            [
                'id' => 3,
                'name' => '岩手県',
                'name_kana' => 'イワテ',
                'name_romaji' => 'Iwate',
                'region_id' => 2,
            ],
            [
                'id' => 4,
                'name' => '宮城県',
                'name_kana' => 'ミヤギ',
                'name_romaji' => 'Miyagi',
                'region_id' => 2,
            ],
            [
                'id' => 5,
                'name' => '秋田県',
                'name_kana' => 'アキタ',
                'name_romaji' => 'Akita',
                'region_id' => 2,
            ],
            [
                'id' => 6,
                'name' => '山形県',
                'name_kana' => 'ヤマガタ',
                'name_romaji' => 'Yamagata',
                'region_id' => 2,
            ],
            [
                'id' => 7,
                'name' => '福島県',
                'name_kana' => 'フクシマ',
                'name_romaji' => 'Fukushima',
                'region_id' => 2,
            ],
            [
                'id' => 8,
                'name' => '茨城県',
                'name_kana' => 'イバラキ',
                'name_romaji' => 'Ibaraki',
                'region_id' => 3,
            ],
            [
                'id' => 9,
                'name' => '栃木県',
                'name_kana' => 'トチギ',
                'name_romaji' => 'Tochigi',
                'region_id' => 3,
            ],
            [
                'id' => 10,
                'name' => '群馬県',
                'name_kana' => 'グンマ',
                'name_romaji' => 'Gunma',
                'region_id' => 3,
            ],
            [
                'id' => 11,
                'name' => '埼玉県',
                'name_kana' => 'サイタマ',
                'name_romaji' => 'Saitama',
                'region_id' => 3,
            ],
            [
                'id' => 12,
                'name' => '千葉県',
                'name_kana' => 'チバ',
                'name_romaji' => 'Chiba',
                'region_id' => 3,
            ],
            [
                'id' => 13,
                'name' => '東京都',
                'name_kana' => 'トウキョウ',
                'name_romaji' => 'Tokyo',
                'region_id' => 3,
            ],
            [
                'id' => 14,
                'name' => '神奈川県',
                'name_kana' => 'カナガワ',
                'name_romaji' => 'Kanagawa',
                'region_id' => 3,
            ],
            [
                'id' => 15,
                'name' => '新潟県',
                'name_kana' => 'ニイガタ',
                'name_romaji' => 'Niigata',
                'region_id' => 4,
            ],
            [
                'id' => 16,
                'name' => '富山県',
                'name_kana' => 'トヤマ',
                'name_romaji' => 'Toyama',
                'region_id' => 4,
            ],
            [
                'id' => 17,
                'name' => '石川県',
                'name_kana' => 'イシカワ',
                'name_romaji' => 'Ishikawa',
                'region_id' => 4,
            ],
            [
                'id' => 18,
                'name' => '福井県',
                'name_kana' => 'フクイ',
                'name_romaji' => 'Fukui',
                'region_id' => 4,
            ],
            [
                'id' => 19,
                'name' => '山梨県',
                'name_kana' => 'ヤマナシ',
                'name_romaji' => 'Yamanashi',
                'region_id' => 4,
            ],
            [
                'id' => 20,
                'name' => '長野県',
                'name_kana' => 'ナガノ',
                'name_romaji' => 'Nagano',
                'region_id' => 4,
            ],
            [
                'id' => 21,
                'name' => '岐阜県',
                'name_kana' => 'ギフ',
                'name_romaji' => 'Gifu',
                'region_id' => 4,
            ],
            [
                'id' => 22,
                'name' => '静岡県',
                'name_kana' => 'シズオカ',
                'name_romaji' => 'Shizuoka',
                'region_id' => 4,
            ],
            [
                'id' => 23,
                'name' => '愛知県',
                'name_kana' => 'アイチ',
                'name_romaji' => 'Aichi',
                'region_id' => 4,
            ],
            [
                'id' => 24,
                'name' => '三重県',
                'name_kana' => 'ミエ',
                'name_romaji' => 'Mie',
                'region_id' => 5,
            ],
            [
                'id' => 25,
                'name' => '滋賀県',
                'name_kana' => 'シガ',
                'name_romaji' => 'Shiga',
                'region_id' => 5,
            ],
            [
                'id' => 26,
                'name' => '京都府',
                'name_kana' => 'キョウト',
                'name_romaji' => 'Kyoto',
                'region_id' => 5,
            ],
            [
                'id' => 27,
                'name' => '大阪府',
                'name_kana' => 'オオサカ',
                'name_romaji' => 'Osaka',
                'region_id' => 5,
            ],
            [
                'id' => 28,
                'name' => '兵庫県',
                'name_kana' => 'ヒョウゴ',
                'name_romaji' => 'Hyogo',
                'region_id' => 5,
            ],
            [
                'id' => 29,
                'name' => '奈良県',
                'name_kana' => 'ナラ',
                'name_romaji' => 'Nara',
                'region_id' => 5,
            ],
            [
                'id' => 30,
                'name' => '和歌山県',
                'name_kana' => 'ワカヤマ',
                'name_romaji' => 'Wakayama',
                'region_id' => 5,
            ],
            [
                'id' => 31,
                'name' => '鳥取県',
                'name_kana' => 'トットリ',
                'name_romaji' => 'Tottori',
                'region_id' => 6,
            ],
            [
                'id' => 32,
                'name' => '島根県',
                'name_kana' => 'シマネ',
                'name_romaji' => 'Shimane',
                'region_id' => 6,
            ],
            [
                'id' => 33,
                'name' => '岡山県',
                'name_kana' => 'オカヤマ',
                'name_romaji' => 'Okayama',
                'region_id' => 6,
            ],
            [
                'id' => 34,
                'name' => '広島県',
                'name_kana' => 'ヒロシマ',
                'name_romaji' => 'Hiroshima',
                'region_id' => 6,
            ],
            [
                'id' => 35,
                'name' => '山口県',
                'name_kana' => 'ヤマグチ',
                'name_romaji' => 'Yamaguchi',
                'region_id' => 6,
            ],
            [
                'id' => 36,
                'name' => '徳島県',
                'name_kana' => 'トクシマ',
                'name_romaji' => 'Tokushima',
                'region_id' => 7,
            ],
            [
                'id' => 37,
                'name' => '香川県',
                'name_kana' => 'カガワ',
                'name_romaji' => 'Kagawa',
                'region_id' => 7,
            ],
            [
                'id' => 38,
                'name' => '愛媛県',
                'name_kana' => 'エヒメ',
                'name_romaji' => 'Ehime',
                'region_id' => 7,
            ],
            [
                'id' => 39,
                'name' => '高知県',
                'name_kana' => 'コウチ',
                'name_romaji' => 'Kochi',
                'region_id' => 7,
            ],
            [
                'id' => 40,
                'name' => '福岡県',
                'name_kana' => 'フクオカ',
                'name_romaji' => 'Fukuoka',
                'region_id' => 8,
            ],
            [
                'id' => 41,
                'name' => '佐賀県',
                'name_kana' => 'サガ',
                'name_romaji' => 'Saga',
                'region_id' => 8,
            ],
            [
                'id' => 42,
                'name' => '長崎県',
                'name_kana' => 'ナガサキ',
                'name_romaji' => 'Nagasaki',
                'region_id' => 8,
            ],
            [
                'id' => 43,
                'name' => '熊本県',
                'name_kana' => 'クマモト',
                'name_romaji' => 'Kumamoto',
                'region_id' => 8,
            ],
            [
                'id' => 44,
                'name' => '大分県',
                'name_kana' => 'オオイタ',
                'name_romaji' => 'Oita',
                'region_id' => 8,
            ],
            [
                'id' => 45,
                'name' => '宮崎県',
                'name_kana' => 'ミヤザキ',
                'name_romaji' => 'Miyazaki',
                'region_id' => 8,
            ],
            [
                'id' => 46,
                'name' => '鹿児島県',
                'name_kana' => 'カゴシマ',
                'name_romaji' => 'Kagoshima',
                'region_id' => 8,
            ],
            [
                'id' => 47,
                'name' => '沖縄県',
                'name_kana' => 'オキナワ',
                'name_romaji' => 'Okinawa',
                'region_id' => 8,
            ],
        ];
        
        $result = DB::table('prefectures')->insertOrIgnore($data);
        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
