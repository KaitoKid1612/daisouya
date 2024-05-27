<?php

namespace App\Http\Requests\Auth;

use App\Models\Admin;
use App\Models\DeliveryOffice;
use App\Models\Driver;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     * Admin用とUser用のどちらのフォームからログインしてきたかを判別して、Auth::guard()に渡す
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();
        $user = null;
        $guard = '';

        if ($this->routeIs('admin.*')) {
            $guard = 'admins';
            $user = Admin::where('email', $this->input('email'))->first();
        } elseif ($this->routeIs('driver.*')) {
            $guard = 'drivers';
            $user = Driver::withTrashed()->where('email', $this->input('email'))->first();
        } else {
            $guard = 'delivery_offices';
            $user = DeliveryOffice::withTrashed()->where('email', $this->input('email'))->first();
        }

        if ($user && $user->deleted_at) {
            throw ValidationException::withMessages([
                'email' => __('auth.account_deleted'),
            ]);
        }

        if (!Auth::guard($guard)->attempt($this->only('email', 'password'), $this->filled('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }


    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('email')) . '|' . $this->ip();
    }
}
