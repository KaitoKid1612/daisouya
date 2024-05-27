<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryOfficeChargeUserTypeSeeder extends Seeder
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
                'name' => '一般',
                'summary' => '通常に料金が発生するユーザ',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                'id' => 2,
                'name' => '無料',
                'summary' => '支払いを必要としないユーザ',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
        ];
        $result = DB::table('delivery_office_charge_user_types')->insertOrIgnore($data);

        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }

    }
}
