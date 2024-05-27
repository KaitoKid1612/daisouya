<?php

namespace App\Rules\Driver;

use Illuminate\Contracts\Validation\Rule;

use App\Models\DriverTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * (ドライバー)ログインユーザーが所持していない稼働IDを指定した場合にバリデートされる。
 */
class HasDriverTaskRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $driver_task = DriverTask::select()
        ->where([
            ['id', $value],
            ['driver_id', $login_id]
        ])
        ->first();

        if($driver_task){
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
