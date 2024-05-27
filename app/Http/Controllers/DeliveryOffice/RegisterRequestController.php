<?php

namespace App\Http\Controllers\DeliveryOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DeliveryOfficeCreateRequest;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\DeliveryOffice\RegisterRequestCreateRequest;
use App\Http\Requests\DeliveryOffice\RegisterRequestUpdateRequest;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendTestMail;
use App\Mail\RegisterRequestDeliveryOfficeStoreSendGuest;
use App\Mail\RegisterRequestDeliveryOfficeStoreSendAdmin;

use App\Models\Prefecture;
use App\Models\Gender;
use App\Models\WebConfigBase;
use App\Models\WebConfigSystem;
use App\Models\WebNoticeLog;
use App\Models\RegisterRequestDeliveryOffice;
use App\Models\DeliveryCompany;
use App\Models\DeliveryOffice;

/**
 * 登録申請
 */
class RegisterRequestController extends Controller
{

    /**
     * 作成画面
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /* フォーム検索に使うデータ */
        // 都道府県取得
        $prefecture_list = Prefecture::select()->get();
        // logger($prefecture_list->toArray());
        $gender_list = Gender::select()->get();
        $company_list = DeliveryCompany::get();

        return view('delivery_office.register_request.create', [
            'prefecture_list' => $prefecture_list,
            'gender_list' => $gender_list,
            'company_list' => $company_list,
        ]);
    }

    /**
     * 作成
     * +API機能
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterRequestCreateRequest $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        // POST送信の場合
        if ($request->isMethod('POST')) {
            // 登録申請の処理
            $name = $request->name ?? '';
            $manager_name_sei = $request->manager_name_sei ?? '';
            $manager_name_mei = $request->manager_name_mei ?? '';
            $manager_name_sei_kana = $request->manager_name_sei_kana ?? '';
            $manager_name_mei_kana = $request->manager_name_mei_kana ?? '';
            $email = $request->email ?? '';
            $delivery_company_id = in_array($request->delivery_company_id, [NULL, 'None'])  ? NULL :  $request->delivery_company_id;
            $delivery_company_name = $request->delivery_company_name ?? '';
            $post_code1 = $request->post_code1 ?? '';
            $post_code2 = $request->post_code2 ?? '';
            $addr1_id = $request->addr1_id ?? '';
            $addr2 = $request->addr2 ?? '';
            $addr3 = $request->addr3 ?? '';
            $addr4 = $request->addr4 ?? '';
            $manager_tel = $request->manager_tel ?? '';
            $message = $request->message ?? '';

            // 配送会社IDが入力されていたら、会社名は空
            if ($delivery_company_id) {
                $delivery_company_name = '';
            }

            $register_request_create = RegisterRequestDeliveryOffice::create([
                'register_request_status_id' => 1,
                'name' => $name,
                'manager_name_sei' => $manager_name_sei,
                'manager_name_mei' => $manager_name_mei,
                'manager_name_sei_kana' => $manager_name_sei_kana,
                'manager_name_mei_kana' => $manager_name_mei_kana,
                'email' => $email,
                'delivery_company_id' => $delivery_company_id,
                'delivery_company_name' => $delivery_company_name,
                'delivery_office_type_id' => isset($delivery_company_id) ? 1 : 2,
                'post_code1' => $post_code1,
                'post_code2' => $post_code2,
                'addr1_id' => $addr1_id,
                'addr2' => $addr2,
                'addr3' => $addr3,
                'addr4' => $addr4,
                'manager_tel' => $manager_tel,
                'message' => $message,
            ]);

            $msg = '';
            if ($register_request_create) {
                $msg = '登録申請を受け付けました。自動配信メールの受信を確認してください。受信までにしばらく時間がかかる場合があります。';
                $config_base = WebConfigBase::where('id', 1)->first();
                $config_system = WebConfigSystem::where('id', 1)->first();

                // メールで利用するデータ
                $data_mail = [
                    "config_base" => $config_base,
                    "config_system" => $config_system,
                    'guest' => $register_request_create,
                ];

                $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
                $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
                $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
                $file_path = __FILE__; // ファイルパス

                /* 登録申請者へのメール */
                $to_guest = [
                    [
                        'email' => $register_request_create->email,
                        'name' => "{$register_request_create->name_sei} {$register_request_create->name_mei}",
                    ],
                ];

                $msg_mail = ''; // メール可否メッセージ
                try {
                    Mail::to($to_guest)->send(new RegisterRequestDeliveryOfficeStoreSendGuest($data_mail)); // 送信
                    $msg_mail = 'メールを送信しました。';
                    $log_level = 7;
                    $notice_type = 1;
                } catch (\Throwable $e) {
                    $msg_mail = 'メール送信エラー';
                    $msg .= $msg_mail;
                    $log_level = 4;
                    $notice_type = 1;

                    $log_format = LogFormat::error(
                        $msg,
                        $login_user->joinUserType->name ?? '',
                        $login_id ?? '',
                        $remote_addr ?? '',
                        $http_user_agent ?? '',
                        $url ?? '',
                        $file_path ?? '',
                        $e->getCode(),
                        $e->getFile(),
                        $e->getLine(),
                        mb_substr($e->__toString(), 0, 1200) . "......\nLog End",
                    );
                    Log::error($log_format);
                } finally {
                    WebNoticeLog::create([
                        'web_log_level_id' => $log_level,
                        'web_notice_type_id' => 1,
                        'task_id' => null,
                        'to_user_id' => null,
                        'to_user_type_id' => 4,
                        'to_user_info' => "ゲスト / email:{$register_request_create->email}",
                        'user_id' => $login_id,
                        'user_type_id' => $login_user->user_type_id ?? 4,
                        'user_info' => $login_user->joinUserType->name ?? 'ゲスト',
                        'text' => "営業所登録申請",
                        'remote_addr' => $remote_addr,
                        'http_user_agent' => $http_user_agent,
                        'url' => $url,
                    ]);
                }
                /* 管理者へのメール */
                $to_admin = [
                    [
                        'email' => $config_system->email_notice,
                        'name' => "{$config_base->site_name}",
                    ],
                ];
                $msg_mail = ''; // メール可否メッセージ
                try {
                    Mail::to($to_admin)->send(new RegisterRequestDeliveryOfficeStoreSendAdmin($data_mail)); // 送信
                    $msg_mail = 'メールを送信しました。';
                    $log_level = 7;
                    $notice_type = 1;
                } catch (\Throwable $e) {
                    $msg_mail = 'メール送信エラー';
                    $msg .= $msg_mail;
                    $log_level = 4;
                    $notice_type = 1;

                    $log_format = LogFormat::error(
                        $msg,
                        $login_user->joinUserType->name ?? '',
                        $login_id ?? '',
                        $remote_addr ?? '',
                        $http_user_agent ?? '',
                        $url ?? '',
                        $file_path ?? '',
                        $e->getCode(),
                        $e->getFile(),
                        $e->getLine(),
                        mb_substr($e->__toString(), 0, 1200) . "......\nLog End",
                    );
                    Log::error($log_format);
                } finally {
                    WebNoticeLog::create([
                        'web_log_level_id' => $log_level,
                        'web_notice_type_id' => 1,
                        'task_id' => null,
                        'to_user_id' => null,
                        'to_user_type_id' => null,
                        'to_user_info' => "管理者 / email:{$config_system->email_notice}",
                        'user_id' => $login_id,
                        'user_type_id' => $login_user->user_type_id ?? 4,
                        'user_info' => $login_user->joinUserType->name ?? 'ゲスト',
                        'text' => "営業所登録申請",
                        'remote_addr' => $remote_addr,
                        'http_user_agent' => $http_user_agent,
                        'url' => $url,
                    ]);
                }
            }

            $api_status = true;
            if ($register_request_create) {
                $api_status = true;
            } else {
                $api_status = false;
            }

            if (Route::is('api.*')) {
                return response()->json([
                    'status' => $api_status,
                    'message' => $msg,
                    'data' => [
                        'id' => $register_request_create->id
                    ],

                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return redirect()->route('delivery_office.register_request.store_get')->with([
                    'msg' => $msg,
                    'id' => $register_request_create->id,
                ]);
            }
        } else {
            // GET通信
            // 登録申請完了ページ
            if (Route::is('api.*')) {
                return '';
            } else {
                return view('delivery_office.register_request.store');
            }
        }
    }

    /**
     * 編集画面
     *
     * @param  int  $register_request_id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $token = $request->token ?? '';

        $register_request = RegisterRequestDeliveryOffice::select()->where(
            [
                ['token', $token],
                ['register_request_status_id', 2], // 2(許可)
                ['time_limit_at', '>=', new \DateTime()],
            ]
        )->first();

        return view('delivery_office.register_request.edit', [
            'token' => $token,
            'email' => $register_request->email,
            'is_register_request' => $register_request,
        ]);
    }

    /**
     * 更新
     * +API機能
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $register_request_id
     * @return \Illuminate\Http\Response
     */
    public function update(RegisterRequestUpdateRequest $request)
    {
        $token = $request->register_request_token ?? '';
        $email = $request->email ?? '';
        $password = $request->password ?? '';

        $register_request = RegisterRequestDeliveryOffice::select()->where(
            [
                ['token', $token],
                ['email', $email],
                ['time_limit_at', '>=', new \DateTime()],
            ]
        )->first();

        // 登録申請の該当レコードがあれば、ユーザー登録をする
        $office_create = '';
        if ($register_request) {

            // 配送会社IDが入力されていたら、会社名は空
            if ($register_request->delivery_company_id) {
                $delivery_company_name = '';
            } else {
                $delivery_company_name = $register_request->delivery_company_name;
            }

            // Check if exist delivery office deleted
            $delivery_office = DeliveryOffice::withTrashed()->where('email', $email)->first();

            if ($delivery_office && $delivery_office->deleted_at !== null && $delivery_office->email === $email) {
                $delivery_office->fill([
                    'user_type_id' => 2,
                    'name' => $register_request->name,
                    'manager_name_sei' => $register_request->manager_name_sei,
                    'manager_name_mei' => $register_request->manager_name_mei,
                    'manager_name_sei_kana' => $register_request->manager_name_sei_kana,
                    'manager_name_mei_kana' => $register_request->manager_name_mei_kana,
                    'password' => Hash::make($password),
                    'delivery_company_id' => $register_request->delivery_company_id,
                    'delivery_company_name' => $delivery_company_name ?? '',
                    'delivery_office_type_id' => isset($register_request->delivery_company_id) ? 1 : 2,
                    'post_code1' => $register_request->post_code1,
                    'post_code2' => $register_request->post_code2,
                    'addr1_id' => $register_request->addr1_id,
                    'addr2' => $register_request->addr2,
                    'addr3' => $register_request->addr3,
                    'addr4' => $register_request->addr4,
                    'manager_tel' => $register_request->manager_tel,
                    'charge_user_type_id' => 1,
                    'deleted_at' => null,
                ])->save();

                $office_create = $delivery_office;
            } else {
                $office_create = DeliveryOffice::create([
                    'user_type_id' => 2,
                    'name' => $register_request->name,
                    'manager_name_sei' => $register_request->manager_name_sei,
                    'manager_name_mei' => $register_request->manager_name_mei,
                    'manager_name_sei_kana' => $register_request->manager_name_sei_kana,
                    'manager_name_mei_kana' => $register_request->manager_name_mei_kana,
                    'email' => $register_request->email,
                    'password' => Hash::make($password),
                    'delivery_company_id' => $register_request->delivery_company_id,
                    'delivery_company_name' => $delivery_company_name ?? '',
                    'delivery_office_type_id' => isset($register_request->delivery_company_id) ? 1 : 2, // 配送会社がnullなら請負(2)
                    'post_code1' => $register_request->post_code1,
                    'post_code2' => $register_request->post_code2,
                    'addr1_id' => $register_request->addr1_id,
                    'addr2' => $register_request->addr2,
                    'addr3' => $register_request->addr3,
                    'addr4' => $register_request->addr4,
                    'manager_tel' => $register_request->manager_tel,
                    'charge_user_type_id' => 1,
                ]);
            }
        }

        $msg = '';
        if ($office_create) {
            $register_request->token = null;
            $register_request->register_request_status_id = 4; // 4(登録処理済み)に変更
            $register_request->save();

            $msg = 'アカウント登録が完了しました。ログインをしてください。';
        } else {
            $msg = 'アカウント登録ができませんでした。';
        }

        $api_status = true;
        if ($office_create) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'message' => $msg,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return redirect()->route('delivery_office.login')->with([
                'msg' => $msg,
            ]);
        }
    }
}
