<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DriverTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = new \DateTime(date("Y-m-d")); //今日の日付 時分秒無視
        $rand_task_date = mt_rand(1, 45);
        $rand_plus_minus = mt_rand(0, 1) ? "+" : '-';
        $rand_request_date = mt_rand(0, 5);


        $rand_weights = [1, 1, 1, 2, 3, 3, 3, 4, 4, 4, 5, 6, 7, 8, 8, 9, 10, 11]; // 重みをつけで出やすい数字を調整
        // $rand_driver_task_status_id = mt_rand(1, 11);
        $rand_driver_task_status_id = (int)$rand_weights[array_rand($rand_weights)];
        if ($rand_driver_task_status_id == 1) {
            $rand_driver_id = null;
        } else {
            $rand_driver_id = mt_rand(1, 2);
        }

        $driver_task_plan_id_list = [null, 1, 2, 3];

        return [
            'task_date' => clone $date->modify($rand_plus_minus . $rand_task_date . " days"),
            'request_date' => $date->modify('-' . $rand_request_date . " days"),
            'driver_id' => $rand_driver_id,
            'delivery_office_id' => mt_rand(1, 2),
            'driver_task_status_id' => $rand_driver_task_status_id,
            'driver_task_plan_id' => $driver_task_plan_id_list[mt_rand(0, 3)],
            'rough_quantity' => mt_rand(1, 100),
            'delivery_route' => '配送コース配送コース' . $this->faker->realText(mt_rand(10, 300)),
            'task_memo' => 'タスクメモ' . $this->faker->realText(mt_rand(10, 300)),
            'task_delivery_company_name' => 'アマゾン',
            'task_delivery_office_name' => $this->faker->city . '営業所',
            'task_email' => 'test@x.com',
            'task_tel' => '090' . sprintf('%04d', mt_rand(1, 9999)) . sprintf('%04d', mt_rand(1, 9999)),
            'task_post_code1' => sprintf('%03d', mt_rand(1, 999)),
            'task_post_code2' => sprintf('%04d', mt_rand(1, 9999)),
            'task_addr1_id' => mt_rand(1, 47),
            'task_addr2' => $this->faker->city,
            'task_addr3' => $this->faker->streetAddress,
            'task_addr4' => $this->faker->secondaryAddress,
            'system_price' => 10000,
            'emergency_price' => 0,
            'discount' => 0,
            'tax' => 1000,
            'tax_rate' => 10.00,
            'refund_amount' => 0,
            'payment_fee_rate' => 3.60,
            'stripe_payment_method_id' => '',
            'stripe_payment_intent_id' => '',
            'stripe_payment_refund_id' => '',
            'driver_task_payment_status_id' => 1,
            'driver_task_refund_status_id' => 1,
            'created_at' => $this->faker->dateTimeThisDecade,
            'updated_at' => $this->faker->dateTimeThisYear,
        ];
    }
}
