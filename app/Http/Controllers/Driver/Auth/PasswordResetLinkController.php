<?php

namespace App\Http\Controllers\Driver\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

use App\Models\Driver;
use App\Models\PasswordResetDriver;
use App\Models\WebConfigBase;
use App\Models\WebConfigSystem;

use App\Http\Requests\Driver\PasswordResetLinkCreateRequest;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('driver.auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(PasswordResetLinkCreateRequest $request)
    {
        $email = $request->email ?? '';

        $config_base = WebConfigBase::where('id', 1)->first();
        $config_system = WebConfigSystem::where('id', 1)->first();
        $driver = Driver::where('email', $email)->first();

        // 送られたメアドのアカウントが存在する場合
        if ($driver) {
            // パスワード再設定トークン作成
            $repass_token = uniqid(bin2hex(random_bytes(16)), true);
            $hash_token = Hash::make($repass_token);

            // 過去に発行したトークン削除
            PasswordResetDriver::where('email', $email)->delete();

            // トークン登録
            $result_create = PasswordResetDriver::create([
                'email' => $email,
                'token' => $hash_token,
            ]);

            // logger($result_create);

            // メールテンプレートで利用するデータ
            $data = [
                'token' =>  $repass_token
            ];

            // メール送信
            $mail = Mail::send('driver.emails.reset_password', $data, function ($message) use ($email, $driver, $config_base, $config_system) {
                $message
                    ->from($config_system->email_from, $config_base->site_name)
                    ->replyTo($config_system->email_reply_to, $config_base->site_name)
                    ->to($email, "{$driver->name_sei} {$driver->name_mei}")
                    ->subject("{$config_base->site_name} ドライバー パスワード再設定");
            });

            $api_status = false;
            if ($mail) {
                $api_status = true;
            } else {
                $api_status = false;
            }

            $message = "パスワード再設定メールを送信しました。";

            // APIの時のログインユーザーの情報
            if (Route::is('api.*')) {
                return response()->json([
                    'status' => $api_status,
                    'data' => ["message" => $message]
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return redirect()->route('driver.password.request')->with('msg', $message);
            }
        }
    }
}
