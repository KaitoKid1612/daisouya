<?php

namespace App\Http\Controllers\DeliveryOffice\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\DeliveryOffice\PasswordResetLinkCreateRequest;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

use App\Models\DeliveryOffice;
use App\Models\PasswordResetDeliveryOffice;
use App\Models\WebConfigBase;
use App\Models\WebConfigSystem;


class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('delivery_office.auth.forgot-password');
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
        $office = DeliveryOffice::where('email', $email)->first();

        // 送られたメアドのアカウントが存在する場合
        if ($office) {
            // パスワード再設定トークン作成
            $repass_token = uniqid(bin2hex(random_bytes(16)), true);
            $hash_token = Hash::make($repass_token);

            // 過去に発行したトークン削除
            PasswordResetDeliveryOffice::where('email', $email)->delete();

            // トークン登録
            $result_create = PasswordResetDeliveryOffice::create([
                'email' => $email,
                'token' => $hash_token,
            ]);

            // logger($result_create);

            // メールテンプレートで利用するデータ
            $data = [
                'token' =>  $repass_token
            ];

            // メール送信
            $mail = Mail::send('delivery_office.emails.reset_password', $data, function ($message) use ($email, $office, $config_base, $config_system) {
                $message
                    ->from($config_system->email_from, $config_base->site_name)
                    ->replyTo($config_system->email_reply_to, $config_base->site_name)
                    ->to($email, "{$office->name} {$office->manager_name_sei} {$office->manager_name_mei}")
                    ->subject("{$config_base->site_name} 営業所 パスワード再設定");
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
                return redirect()->route('delivery_office.password.request')->with('msg', $message);
            }

            // $mail = Mail::to($to)->send(new SendTestMail($repass_token), );
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        // $status = Password::sendResetLink(
        //     $request->only('email')
        // );

        // return $status == Password::RESET_LINK_SENT
        //             ? back()->with('status', __($status))
        //             : back()->withInput($request->only('email'))
        //                     ->withErrors(['email' => __($status)]);
    }
}
