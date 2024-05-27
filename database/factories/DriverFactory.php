<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Driver;
use Illuminate\Support\Facades\Hash;

class DriverFactory extends Factory
{

    protected $model = Driver::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $driver_plan_id_list = [null, 1 , 2 , 3];
        $driver_entry_status_id_list = [null, 1 , 2 , 3];

        return [
            'user_type_id' => 3,
            'driver_entry_status_id' => $driver_entry_status_id_list[mt_rand(0, 1)],
            'driver_plan_id' =>  $driver_plan_id_list[mt_rand(0, 3)],
            'name_sei' => $this->faker->lastName,
            'name_mei' => $this->faker->firstName,
            'name_sei_kana' => $this->faker->lastKanaName(),
            'name_mei_kana' => $this->faker->firstKanaName(),
            'email' =>  mt_rand(1, 1000) . $this->faker->unique()->email(),
            'password' => Hash::make('test1234'),
            'post_code1' => sprintf('%03d', mt_rand(1, 999)),
            'post_code2' => sprintf('%04d', mt_rand(1, 9999)),
            'addr1_id' => mt_rand(1, 47),
            'addr2' => $this->faker->city,
            'addr3' => $this->faker->streetAddress,
            'addr4' => $this->faker->secondaryAddress,
            'tel' => '090'. sprintf('%04d', mt_rand(1, 9999)) . sprintf('%04d', mt_rand(1, 9999)),
            'birthday' => $this->faker->dateTime,
            'gender_id' => mt_rand(1, 2),
            'icon_img' => 'driver/user_icon/2022/08/test.png',
            'career' => $this->faker->realText(mt_rand(10, 1000)),
            'introduction' => $this->faker->realText(mt_rand(10, 1000)),
            'created_at' => $this->faker->dateTimeThisDecade,
            'updated_at' => $this->faker->dateTimeThisYear,
        ];
    }
}
