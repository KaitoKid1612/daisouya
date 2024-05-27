<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;

use Illuminate\Support\Facades\Mail;
use App\Mail\WebContactStoreSendAdminMail;
use App\Mail\WebContactStoreSendGuestMail;

use Illuminate\Http\Request;
use App\Http\Requests\Guest\WebContactCreateRequest;
use App\Models\WebContact;
use App\Models\WebContactType;
use App\Models\UserType;
use App\Models\WebConfigBase;
use App\Models\WebConfigSystem;
use App\Models\WebNoticeLog;

use App\Jobs\Mail\WebContactStoreSendGuestMailJob;
use App\Jobs\Mail\WebContactStoreSendAdminMailJob;

/**
 * お問い合わせ
 */
class WebContactController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $login_id = Auth::guard('drivers')->id() ?? Auth::guard('delivery_offices')->id() ?? null;

        /**
         * @todo 一時的にログインユーザーのみアクセスできるようにしている。
         */
        if (!$login_id) {
            return redirect()->route('delivery_office.login');
        }

        // フォームで使うデータ
        $user_type_list = UserType::select()->where('id', '!=', 1)->get(); // 管理者以外のユーザタイプ
        $web_contact_type_list = WebContactType::select()->get();

        return view('guest.web_contact.create', [
            'user_type_list' => $user_type_list,
            'web_contact_type_list' => $web_contact_type_list,
        ]);
    }

    /**
     * 作成
     * +API機能
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WebContactCreateRequest $request)
    {
        $login_id = Auth::guard('drivers')->id() ?? Auth::guard('delivery_offices')->id() ?? null;
        $login_user = auth('drivers')->user() ?? auth('delivery_offices')->user(''); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $user_type_id = $request->user_type_id ?? null;
        $name_sei = $request->name_sei ?? '';
        $name_mei = $request->name_mei ?? '';
        $name_sei_kana = $request->name_sei_kana ?? '';
        $name_mei_kana = $request->name_mei_kana ?? '';
        $email = $request->email ?? '';
        $tel = $request->tel ?? '';
        $web_contact_type_id = $request->web_contact_type_id ?? 1;
        $title = $request->title ?? '';
        $text = $request->text ?? '';
        $type = $request->type ?? '';

        $api_status = true;
        // logger($request);

        // 確認ページ
        if ($type === 'confirm') {
            // フォームで使うデータ
            $user_type_list = UserType::select()->where('id', '!=', 1)->get(); // 管理者以外のユーザタイプ
            $web_contact_type_list = WebContactType::select()->get();
            return view('guest.web_contact.store', [
                'user_type_list' => $user_type_list,
                'web_contact_type_list' => $web_contact_type_list,
                'request' => $request->input(),
            ]);
        } else {
            $contact_create = WebContact::create([
                'user_type_id' => $user_type_id,
                'user_id' => $login_id,
                'name_sei' => $name_sei,
                'name_mei' => $name_mei,
                'name_sei_kana' => $name_sei_kana,
                'name_mei_kana' => $name_mei_kana,
                'email' => $email,
                'tel' => $tel,
                'web_contact_type_id' => $web_contact_type_id,
                'web_contact_status_id' => 1,
                'title' => $title,
                'text' => $text,
            ]);

            if ($contact_create) {
                $msg = 'お問い合わせを受け付けました。自動配信メールの受信を確認してください。受信までにしばらく時間がかかる場合があります。';
                $api_status = true;

                $config_base = WebConfigBase::select([
                    'id',
                    'site_name',
                    'company_name',
                    'company_name_kana',
                    'owner_name',
                    'owner_name',
                    'post_code1',
                    'post_code2',
                    'addr1_id',
                    'addr1_id',
                    'addr2',
                    'addr3',
                    'addr4',
                    'tel',
                ])->where('id', 1)->first();

                $config_system = WebConfigSystem::select([
                    'id',
                    'email_notice',
                    'email_from',
                    'email_reply_to',
                    'email_no_reply',
                ])->where('id', 1)->first();


                // メールで利用するデータ
                $data_mail = [
                    "config_base" => $config_base,
                    "config_system" => $config_system,
                    'contact' => $contact_create,
                ];

                $remote_addr = Analysis::getClientIpAddress() ?? ''; // IPアドレス
                $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
                $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
                $file_path = __FILE__; // ファイルパス

                $info_list = [
                    "remote_addr" => $remote_addr,
                    "http_user_agent" => $http_user_agent,
                    "url" => $url,
                    "file_path" => $file_path,
                ];

                /* ゲストへのメール */
                $to_guest = [
                    [
                        'email' => $contact_create->email,
                        'name' => "{$contact_create->full_name}",
                    ],
                ];
                WebContactStoreSendGuestMailJob::dispatch($to_guest, $data_mail, $login_user, $info_list);

                /* 管理者へのメール */
                $to_admin = [
                    [
                        'email' => $config_system->email_notice,
                        'name' => "{$config_base->site_name}",
                    ],
                ];
                WebContactStoreSendAdminMailJob::dispatch($to_admin, $data_mail, $login_user, $info_list);

            } else {
                $api_status = false;
                $msg = 'お問い合わせが失敗しました。';
            }
        }


        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'message' => $msg,
                'data' => ['id' => $contact_create->id ?? '']
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return redirect()->route('guest.web_contact.done')
                ->with([
                    'msg' => $msg,
                    'web_contact' => $contact_create,
                ]);
        }
    }

    /**
     * お問合せ完了ページ
     */
    public function done()
    {
        return view('guest.web_contact.done');
    }
}
