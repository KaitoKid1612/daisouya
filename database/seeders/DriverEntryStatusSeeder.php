<?php

namespace Database\Seeders;

use App\Models\DriverEntryStatus;

use Illuminate\Database\Seeder;

class DriverEntryStatusSeeder extends Seeder
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
                'name' => "通過",
                'label' => "passing",
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                'id' => 2,
                'name' => "審査中",
                'label' => "waiting",
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
            [
                'id' => 3,
                'name' => "不通過",
                'label' => "reject",
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ],
        ];

        $result = DriverEntryStatus::insertOrIgnore($data);

        if (!$result) {
            echo "\033[31mSkip!!\033[0m\n";
        } else {
            echo "\033[34mDone!!\033[0m\n";
        }
    }
}
