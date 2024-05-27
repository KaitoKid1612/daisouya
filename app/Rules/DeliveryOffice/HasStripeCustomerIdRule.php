<?php

namespace App\Rules\DeliveryOffice;

use Illuminate\Contracts\Validation\Rule;

use App\Models\DeliveryOffice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Stripe\Stripe;
use Stripe\Customer;

/**
 *　有効なcustomer_id を所持していない場合にバリデートされる。
 */

class HasStripeCustomerIdRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報

        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $customer_id = $login_user->stripe_id;

        try {
            Stripe::setApiKey(
                config('stripe.stripe_secret')
            );
            // retrieve customer 
            $customer = Customer::retrieve(
                $customer_id,
                []
            );
        } catch (\Throwable $e) {
            return false;
        }

        if ($customer) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '有効なstripe customer_idを所持していません！';
    }
}
