<?php

namespace App\Rules\DeliveryOffice;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

/**
 *  パスワードが一致するか
 */
class PasswordMatchRule implements Rule
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

        $current_password = $login_user->password;

        if(Hash::check($value, $current_password)) {
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
        return ':attribute が一致しません';
    }
}
