<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\Driver\RegisterRequestCreateRequest;
use App\Http\Requests\Driver\RegisterRequestUpdateRequest;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendTestMail;
use App\Mail\RegisterRequestDriverStoreSendGuest;
use App\Mail\RegisterRequestDriverStoreSendAdmin;

use App\Models\Prefecture;
use App\Models\Gender;
use App\Models\WebConfigBase;
use App\Models\WebConfigSystem;
use App\Models\WebNoticeLog;
use App\Models\RegisterRequestDriver;
use App\Models\Driver;
use Illuminate\Support\Facades\Storage;

/**
 * 登録申請
 */
class RegisterRequestController extends Controller
{
    /**
     * Show the form for creating a new resource.
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
        return view('driver.register_request.create', [
            'prefecture_list' => $prefecture_list,
            'gender_list' => $gender_list,

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
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        // POST送信の場合
        if ($request->isMethod('POST')) {
            // 登録申請の処理
            $name_sei = $request->name_sei ?? '';
            $name_mei = $request->name_mei ?? '';
            $name_sei_kana = $request->name_sei_kana ?? '';
            $name_mei_kana = $request->name_mei_kana ?? '';
            $email = $request->email ?? '';
            $gender_id = $request->gender_id ?? '';
            $birthday = $request->birthday ?? '';
            $post_code1 = $request->post_code1 ?? '';
            $post_code2 = $request->post_code2 ?? '';
            $addr1_id = $request->addr1_id ?? '';
            $addr2 = $request->addr2 ?? '';
            $addr3 = $request->addr3 ?? '';
            $addr4 = $request->addr4 ?? '';
            $tel = $request->tel ?? '';
            $career = $request->career ?? '';
            $introduction = $request->introduction ?? '';
            $message = $request->message ?? '';

            $avatar = $request->avatar ?? null;
            $bank = $request->bank ?? null;
            $driving_license_front = $request->driving_license_front ?? null;
            $driving_license_back = $request->driving_license_back ?? null;
            $auto_insurance = $request->auto_insurance ?? null;
            $voluntary_insurance = $request->voluntary_insurance ?? null;
            $inspection_certificate = $request->inspection_certificate ?? null;
            $license_plate_front = $request->license_plate_front ?? null;
            $license_plate_back = $request->license_plate_back ?? null;

            
            $register_request_create = RegisterRequestDriver::create([
                'register_request_status_id' => 1,
                'name_sei' => $name_sei,
                'name_mei' => $name_mei,
                'name_sei_kana' => $name_sei_kana,
                'name_mei_kana' => $name_mei_kana,
                'email' => $email,
                'gender_id' => $gender_id,
                'birthday' => $birthday,
                'post_code1' => $post_code1,
                'post_code2' => $post_code2,
                'addr1_id' => $addr1_id,
                'addr2' => $addr2,
                'addr3' => $addr3,
                'addr4' => $addr4,
                'tel' => $tel,
                'career' => $career,
                'introduction' => $introduction,
                'message' => $message,
                'avatar' => $this->saveImageToStorage($avatar, $email),
                'bank' => $this->saveImageToStorage($bank, $email),
                'driving_license_front' => $this->saveImageToStorage($driving_license_front, $email),
                'driving_license_back' => $this->saveImageToStorage($driving_license_back, $email),
                'auto_insurance' => $this->saveImageToStorage($auto_insurance, $email),
                'voluntary_insurance' => $this->saveImageToStorage($voluntary_insurance, $email),
                'inspection_certificate' => $this->saveImageToStorage($inspection_certificate, $email),
                'license_plate_front' => $this->saveImageToStorage($license_plate_front, $email),
                'license_plate_back' => $this->saveImageToStorage($license_plate_back, $email),
            ]);

            $msg = '';
            if ($register_request_create) {
                $msg = 'ドライバー登録申請を受け付けました。自動配信メールの受信を確認してください。受信までにしばらく時間がかかる場合があります。';
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
                    Mail::to($to_guest)->send(new RegisterRequestDriverStoreSendGuest($data_mail)); // 送信
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
                        'text' => "ドライバー登録申請",
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
                    Mail::to($to_admin)->send(new RegisterRequestDriverStoreSendAdmin($data_mail)); // 送信
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
                        'text' => "登録申請",
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
                    ]
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return redirect()->route('driver.register_request.store_get')->with([
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
                return view('driver.register_request.store');
            }
        }
    }

    public function saveImageToStorage($image, $driver_email)
    {
        $storage_path = "/driver/user_information/{$driver_email}";

        $unique_name = $image->hashName();
        $storage_date = new \DateTime();
        $storage_date = $storage_date->format('d_H_i_s_v');
        $filename = "{$storage_date}_{$unique_name}";

        try {
            $image_path = Storage::disk('s3')->putFileAs($storage_path, $image, $filename); //
        } catch (\InvalidArgumentException | \Aws\S3\Exception\S3Exception $e) {
            Log::info($e);
            $image_path = '';
        }

        return $image_path ?? '';
    }

    /**
     * 編集画面
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $token = $request->token ?? '';

        $register_request = RegisterRequestDriver::select()->where(
            [
                ['token', $token],
                ['time_limit_at', '>=', new \DateTime()],
            ]
        )->whereIn('register_request_status_id', [2, 6]) // 2(許可) or 6(審査中)
            ->first();

        return view('driver.register_request.edit', [
            'token' => $token,
            'email' => $register_request->email ?? '',
            'is_register_request' => $register_request,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(RegisterRequestUpdateRequest $request)
    {
        $token = $request->register_request_token ?? '';
        $email = $request->email ?? '';
        $password = $request->password ?? '';

        $register_request = RegisterRequestDriver::select()->where(
            [
                ['token', $token],
                ['email', $email],
                ['time_limit_at', '>=', new \DateTime()],
            ]
        )->first();


        // 登録申請の該当レコードがあれば、ユーザー登録をする
        $driver_create = '';
        if ($register_request) {

            // 登録申請ステータス
            $driver_entry_status_id = null;
            if ($register_request->register_request_status_id == 2) {
                // 許可なら、通過。
                $driver_entry_status_id = 1;
            } elseif ($register_request->register_request_status_id == 6) {
                // 審査中なら、審査中。
                $driver_entry_status_id = 2;
            }

            // Check if exist user deleted
            $driver = Driver::withTrashed()->where('email', $email)->first();

            if ($driver && $driver->deleted_at !== null && $driver->email === $email) {
                $driver->fill([
                    'user_type_id' => 3,
                    'driver_plan_id' => $register_request->driver_plan_id,
                    'driver_entry_status_id' => $driver_entry_status_id,
                    'name_sei' => $register_request->name_sei,
                    'name_mei' => $register_request->name_mei,
                    'name_sei_kana' => $register_request->name_sei_kana,
                    'name_mei_kana' => $register_request->name_mei_kana,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'gender_id' => $register_request->gender_id,
                    'birthday' => $register_request->birthday,
                    'post_code1' => $register_request->post_code1,
                    'post_code2' => $register_request->post_code2,
                    'addr1_id' => $register_request->addr1_id,
                    'addr2' => $register_request->addr2,
                    'addr3' => $register_request->addr3,
                    'addr4' => $register_request->addr4,
                    'tel' => $register_request->tel,
                    'career' => $register_request->career,
                    'introduction' => $register_request->introduction,
                    'icon_img' => $register_request->icon_img_path ?? '',
                    'avatar' => $register_request->avatar,
                    'bank' => $register_request->bank,
                    'driving_license_front' => $register_request->driving_license_front,
                    'driving_license_back' => $register_request->driving_license_back,
                    'auto_insurance' => $register_request->auto_insurance,
                    'voluntary_insurance' => $register_request->voluntary_insurance,
                    'inspection_certificate' => $register_request->inspection_certificate,
                    'license_plate_front' => $register_request->license_plate_front,
                    'license_plate_back' => $register_request->license_plate_back,
                    'deleted_at' => null,
                ])->save();

                $driver_create = $driver;
            } else {
                $driver_create = Driver::create([
                    'user_type_id' => 3,
                    'driver_plan_id' => $register_request->driver_plan_id,
                    'driver_entry_status_id' => $driver_entry_status_id,
                    'name_sei' => $register_request->name_sei,
                    'name_mei' => $register_request->name_mei,
                    'name_sei_kana' => $register_request->name_sei_kana,
                    'name_mei_kana' => $register_request->name_mei_kana,
                    'email' => $register_request->email,
                    'password' => Hash::make($password),
                    'gender_id' => $register_request->gender_id,
                    'birthday' => $register_request->birthday,
                    'post_code1' => $register_request->post_code1,
                    'post_code2' => $register_request->post_code2,
                    'addr1_id' => $register_request->addr1_id,
                    'addr2' => $register_request->addr2,
                    'addr3' => $register_request->addr3,
                    'addr4' => $register_request->addr4,
                    'tel' => $register_request->tel,
                    'career' => $register_request->career,
                    'introduction' => $register_request->introduction,
                    'icon_img' => $register_request->icon_img_path ?? '',
                    'avatar' => $register_request->avatar,
                    'bank' => $register_request->bank,
                    'driving_license_front' => $register_request->driving_license_front,
                    'driving_license_back' => $register_request->driving_license_back,
                    'auto_insurance' => $register_request->auto_insurance,
                    'voluntary_insurance' => $register_request->voluntary_insurance,
                    'inspection_certificate' => $register_request->inspection_certificate,
                    'license_plate_front' => $register_request->license_plate_front,
                    'license_plate_back' => $register_request->license_plate_back,
                ]);
            }
        }

        $msg = '';
        if ($driver_create) {
            $register_request->token = null;
            if ($register_request->register_request_status_id == 2) {
                // 許可の場合
                $register_request->register_request_status_id = 4; // 4(登録処理済み)に変更
                $driver_create->update(
                    [
                        "driver_entry_status_id" => 1 // 通過
                    ], 
                );
            } elseif ($register_request->register_request_status_id == 6) {
                // 審査中の場合
                $register_request->register_request_status_id = 7; // 7(審査中(登録処理済み))に変更
                $driver_create->update(
                    [
                        "driver_entry_status_id" => 2 // 審査中
                    ],
                );
            }

            $register_request->save();

            $msg = 'アカウント登録が完了しました。ログインをしてください。';
        }

        $api_status = true;
        if ($driver_create) {
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
            return redirect()->route('driver.login')->with([
                'msg' => $msg,
            ]);
        }
    }
}