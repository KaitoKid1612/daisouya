<?php

namespace App\Rules\DeliveryOffice;

use Illuminate\Contracts\Validation\Rule;

use App\Models\DeliveryPickupAddr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * (依頼者)ログインユーザーが所持していない集荷先IDを指定した場合にバリデートされる。
 */
class HasPickupAddrRule implements Rule
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

        $pickup_addr = DeliveryPickupAddr::select()
        ->where([
            ['id', $value],
            ['delivery_office_id', $login_id]
        ])
        ->first();

        if($pickup_addr){
            return true;
        }else {
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
        return '指定した:attributeを利用できるユーザーではありません';
    }
}
