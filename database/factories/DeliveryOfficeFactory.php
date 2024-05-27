<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\DeliveryOffice;

class DeliveryOfficeFactory extends Factory
{
    protected $model = DeliveryOffice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_type_id' => 2,
            'name' => $this->faker->lastName,
            'manager_name_sei' => $this->faker->lastName,
            'manager_name_mei' => $this->faker->firstName,
            'manager_name_sei_kana' => $this->faker->lastKanaName(),
            'manager_name_mei_kana' => $this->faker->firstKanaName(),
            'email' => mt_rand(1, 9999) . $this->faker->unique()->email(),
            'password' => Hash::make('test1234'),
            'delivery_company_id' => mt_rand(1,4),
            'delivery_company_name' => '',
            'delivery_office_type_id' => 1,
            'post_code1' => sprintf('%03d', mt_rand(1, 999)),
            'post_code2' => sprintf('%04d', mt_rand(1, 9999)),
            'addr1_id' => mt_rand(1, 47),
            'addr2' => $this->faker->city,
            'addr3' => $this->faker->streetAddress,
            'addr4' => $this->faker->secondaryAddress,
            'manager_tel' => '01'. sprintf('%04d', mt_rand(1, 9999)) . sprintf('%04d', mt_rand(1, 9999)),
            'charge_user_type_id' => 1,
            'created_at' => $this->faker->dateTimeThisDecade,
            'updated_at' => $this->faker->dateTimeThisYear,
        ];
    }
}
