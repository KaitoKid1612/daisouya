<?php

namespace App\Http\Controllers\Driver\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\Driver\NewPasswordCreateRequest;

use App\Models\Driver;
use App\Models\PasswordResetDriver;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view('driver.auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(NewPasswordCreateRequest $request)
    {
        $email = $request->email ?? '';
        $password = $request->password ?? '';
        $token = $request->token ?? '';

        $api_status = false;

        $password_reset = PasswordResetDriver::where('email', $email)->first();
        // logger($password_reset);

        $created_at = $password_reset->created_at ?? "0001-01-01 00:00:00";
        // logger($created_at);

        $datetime_created = new \DateTime($created_at); // トークン発行時間
        $datetime_limit = $datetime_created->modify('+1 hour'); // トークン有効時間
        $datetime_now = new \DateTime(); // 現在時間

        // トークン発行後1時間以上経過していたら、パスワード再設定へリダイレクト
        if ($datetime_limit < $datetime_now) {

            // 発行したトークンは利用できないので削除
            $password_reset =  PasswordResetDriver::where('email', $email)->delete();

            if ($password_reset) {
                $api_status = true;
            } else {
                $api_status = false;
            }

            $message = "パスワードを再発行できませんでした。有効期限切れか、メールアドレスが間違っている可能性があります。お手数ですがもう一度やり直してください。";

            // APIの時のログインユーザーの情報
            if (Route::is('api.*')) {
                return response()->json([
                    'status' => $api_status,
                    'data' => ["message" => $message]
                ], 403, [], JSON_UNESCAPED_UNICODE);
            } else {
                return redirect()->route('driver.password.request')->with('msg', $message);
            }
        }

        // パスワードとトークンの検証
        $result_password = password_verify($token, $password_reset->token);

        // パスワードとトークンの検証がtrueだったらパスワード更新
        if ($result_password) {
            $office_update = Driver::where('email', $email)
                ->update([
                    'password' => Hash::make($password),
                ]);

            // パスワード更新に成功したら、トークン削除
            if ($office_update) {
                PasswordResetDriver::where('email', $email)->delete();

                $api_status = true;
            }

            $message = "パスワードの変更が完了しました。";

            // APIの時のログインユーザーの情報
            if (Route::is('api.*')) {
                return response()->json([
                    'status' => $api_status,
                    'data' => ["message" => $message]
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return redirect()->route('driver.login')->with('msg_new_password', $message);
            }
        } else {
            $api_status = false;

            $message = "パスワードを再発行できませんでした。有効期限切れか、メールアドレスが間違っている可能性があります。お手数ですがもう一度やり直してください。";

            // APIの時のログインユーザーの情報
            if (Route::is('api.*')) {
                return response()->json([
                    'status' => $api_status,
                    'data' => ["message" => $message]
                ], 403, [], JSON_UNESCAPED_UNICODE);
            } else {
                return redirect()->route('driver.password.request')->with('msg', $message);
            }
        }


        return redirect()->route('driver.login');


        /*         $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]); */
    }
}
