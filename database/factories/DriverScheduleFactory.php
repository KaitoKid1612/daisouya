<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DriverScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $nextYear = date('Y', strtotime('+1 year'));
        $lastYear = date('Y', strtotime('-1 year'));

        $year = mt_rand($lastYear, $nextYear);  // ランダムな年
        $month = mt_rand(1, 12);      // 1から12までのランダムな月
        $day = mt_rand(1, 28);        // 1から28までのランダムな日（適宜調整）
        $available_date = new \DateTime("$year-$month-$day");
        return [
            "driver_id" => mt_rand(1, 2),
            "available_date" => "$year-$month-$day",
            "created_at" => $this->faker->dateTimeThisDecade,
            "updated_at" => $this->faker->dateTimeThisYear,
        ];
    }
}
