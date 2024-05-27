<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\RegisterRequestDeliveryOfficeUpdateRequest;

use App\Models\RegisterRequestDeliveryOffice;
use App\Models\RegisterRequestStatus;
use App\Models\Prefecture;
use App\Models\WebConfigBase;
use App\Models\WebConfigSystem;
use App\Models\WebNoticeLog;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendTestMail;
use App\Mail\RegisterRequestDeliveryOfficeUpdateStatusSendGuest;
use App\Mail\RegisterRequestDeliveryOfficeStoreSendAdmin;
use App\Models\DeliveryOffice;

class RegisterRequestDeliveryOfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword ?? ''; // 検索ワード
        $search_register_request_status_id_list = $request->register_request_status_id ?? ''; // ステータスリスト
        $search_addr1 = $request->addr1_id ?? ''; // 都道府県ID
        $search_user_type_id_list = $request->user_type_id ?? ''; // ユーザータイプリスト
        $search_from_created_at = $request->from_created_at ?? ''; // 作成日 以上
        $search_to_created_at = $request->to_created_at ?? ''; // 作成日 以下
        $search_from_updated_at = $request->from_updated_at ?? ''; // 更新日 以上
        $search_to_updated_at = $request->to_updated_at ?? ''; // 更新日 以下
        $orderby = $request->orderby ?? ''; // 並び替え

        $register_request_object = RegisterRequestDeliveryOffice::select()
            ->with(['joinAddr1',]); // 結合

        // キーワードで検索
        if ($keyword) {
            $register_request_object->where(function ($query) use ($keyword) {
                $query
                    ->orWhere('name_sei', 'LIKE', "%{$keyword}%")
                    ->orWhere('name_mei', 'LIKE', "%{$keyword}%")
                    ->orWhere('name_sei_kana', 'LIKE', "%{$keyword}%")
                    ->orWhere('name_mei_kana', 'LIKE', "%{$keyword}%")
                    ->orWhere('email', 'LIKE', "%{$keyword}%")
                    ->orWhere('tel', 'LIKE', "%{$keyword}%")
                    ->orWhere('addr2', 'LIKE', "%{$keyword}%")
                    ->orWhere('addr3', 'LIKE', "%{$keyword}%")
                    ->orWhere('addr4', 'LIKE', "%{$keyword}%");
            });
        }

        // ステータス 絞り込み
        if ($search_register_request_status_id_list) {
            $register_request_object->where(function ($query) use ($search_register_request_status_id_list) {
                foreach ($search_register_request_status_id_list as $status_id) {
                    $query->orWhere('register_request_status_id', $status_id);
                }
            });
        }

        // 都道府県 絞り込み
        if ($search_addr1) {
            $register_request_object->where([['addr1_id', $search_addr1]]);
        }

        /* 並び替え */
        if ($orderby === 'id_desc') {
            $register_request_object->orderBy('id', 'desc');
        } elseif ($orderby === 'id_asc') {
            $register_request_object->orderBy('id', 'asc');
        } else {
            $register_request_object->orderBy('id', 'desc');
        }

        $register_request_list =  $register_request_object->paginate(50)->withQueryString();

        /* フォーム検索に使うデータ */
        // ステータス一覧
        $register_request_status_list = RegisterRequestStatus::select()->get();
        $prefecture_list = Prefecture::select()->get();

        // 並び順
        $orderby_list = [
            ['value' => 'id_desc', 'text' => 'ID大きい順'],
            ['value' => 'id_asc', 'text' => 'ID小さい順'],
        ];


        return view('admin.register_request_delivery_office.index', [
            'register_request_list' => $register_request_list,
            'register_request_status_list' => $register_request_status_list,
            'prefecture_list' => $prefecture_list,
            'orderby_list' => $orderby_list,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $register_request_id
     * @return \Illuminate\Http\Response
     */
    public function show($register_request_id)
    {
        $register_request_driver = RegisterRequestDeliveryOffice::select()
            ->where('id', $register_request_id)
            ->first();
        return view('admin.register_request_delivery_office.show', [
            'register_request' => $register_request_driver
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $register_request_id
     * @return \Illuminate\Http\Response
     */
    public function edit($register_request_id)
    {
        $register_request = RegisterRequestDeliveryOffice::select()
            ->where('id', $register_request_id)
            ->first();

        /* フォーム検索に使うデータ */
        $register_request_status_list = RegisterRequestStatus::select()
            ->where(function ($query) {
                $query->orWhere('id', 2);
                $query->orWhere('id', 3);
            })
            ->get();

        return view('admin.register_request_delivery_office.edit', [
            'register_request' => $register_request,
            'register_request_status_list' => $register_request_status_list,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $register_request_id
     * @return \Illuminate\Http\Response
     */
    public function update(RegisterRequestDeliveryOfficeUpdateRequest $request, $register_request_id)
    {
        $register_request_status_id = $request->register_request_status_id ?? '';

        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報

        /* 更新処理 */
        $register_request_driver = RegisterRequestDeliveryOffice::where('id', $register_request_id)->first();

        $register_request_driver->register_request_status_id = $register_request_status_id; // ステータス

        $delivery_office = DeliveryOffice::withTrashed()->where('email', $register_request_driver->email)->first();

        $token = '';
        if ($register_request_status_id == 2 || $delivery_office) {
            // ステータスが 2(許可) だったら、登録用トークンの発行
            $token = uniqid(bin2hex(random_bytes(16)), true);
            $register_request_driver->token = $token;

            $config_system = WebConfigSystem::select('register_request_token_time_limit')
                ->where('id', 1)
                ->first(); // 制限時間を取得
            $datetime = new \DateTime();
            $datetime->modify("{$config_system->register_request_token_time_limit} hours");
            $register_request_driver->time_limit_at = $datetime;
        } else {
            $register_request_driver->token = null;
        }

        $register_request_driver_update = $register_request_driver->save(); // 更新

        // 登録申請ステータス取得
        $register_request_status = RegisterRequestStatus::where('id', $register_request_status_id)->first();
        $status = $register_request_status->name ?? ''; // 登録申請ステータス名

        $msg = '';
        if ($register_request_driver_update) {
            $msg = "登録申請ステータスを「{$status}」に更新しました。";

            $config_base = WebConfigBase::where('id', 1)->first();
            $config_system = WebConfigSystem::where('id', 1)->first();
            $register_request = RegisterRequestDeliveryOffice::select()->where('id', $register_request_id)->first();

            // メールで利用するデータ
            $data_mail = [
                "config_base" => $config_base,
                "config_system" => $config_system,
                'register_request' => $register_request,
            ];

            $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
            $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
            $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
            $file_path = __FILE__; // ファイルパス

            /* ゲストへのメール */
            $to_guest = [
                [
                    'email' => $register_request->email,
                    'name' => "{$register_request->full_name}",
                ],
            ];

            $msg_mail = ''; // メール可否メッセージ
            try {
                Mail::to($to_guest)->send(new RegisterRequestDeliveryOfficeUpdateStatusSendGuest($data_mail)); // 送信
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
                    'to_user_info' => "ゲスト / email:{$register_request->email}",
                    'user_id' => $login_id,
                    'user_type_id' => $login_user->user_type_id ?? 4,
                    'user_info' => $login_user->joinUserType->name ?? 'ゲスト',
                    'text' => "営業所登録申請",
                    'remote_addr' => $remote_addr,
                    'http_user_agent' => $http_user_agent,
                    'url' => $url,
                ]);
            }
        } else {
            $msg = "登録申請ステータスを更新できませんでした!";
        }

        $msg .= "({$msg_mail})"; // メッセージ結合

        return redirect()->route("admin.register_request_delivery_office.show", [
            'register_request_id' => $register_request_id,
        ])->with([
            'msg' => $msg ?? ''
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $register_request_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($register_request_id)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $msg = ''; // 削除メッセージ

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        try {
            $web_contact_delete = RegisterRequestDeliveryOffice::where('id', $register_request_id)->delete($register_request_id);
            if ($web_contact_delete) {
                $msg = '削除に成功';
            } else {
                $msg = '削除されませんでした。';
            }
        } catch (\Throwable $e) {
            $msg .= '削除に失敗';

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
        }

        return redirect()->route('admin.register_request_delivery_office.index')->with([
            'msg' => $msg,
        ]);
    }
}
