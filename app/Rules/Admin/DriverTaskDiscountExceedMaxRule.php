<?php

namespace App\Rules\Admin;

use Illuminate\Contracts\Validation\Rule;

/**
 * 値引き額が大きすぎて、料金がマイナスになっていないか判定
 */
class DriverTaskDiscountExceedMaxRule implements Rule
{

    private $system_price;
    private $busy_system_price;
    private $freight_cost;
    private $emergency_price;
    private $discount;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(int $system_price, int  $busy_system_price, int $freight_cost, int $emergency_price, int $discount)
    {
        $this->system_price = $system_price;
        $this->busy_system_price = $busy_system_price;
        $this->freight_cost = $freight_cost;
        $this->emergency_price = $emergency_price;
        $this->discount = $discount;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $system_price = $this->system_price;
        $busy_system_price = $this->busy_system_price;
        $freight_cost = $this->freight_cost;
        $emergency_price = $this->emergency_price;
        $discount = $this->discount;

        $total_price = $system_price + $busy_system_price + $freight_cost + $emergency_price;

        $result = false;

        // 値引き額の方が大きければvalidate.
        if ($discount > $total_price) {
            $result = false;
        } else {
            $result = true;
        }

        return $result;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute が大きすぎます。料金がマイナスにならない値を設定してください。';
    }
}
