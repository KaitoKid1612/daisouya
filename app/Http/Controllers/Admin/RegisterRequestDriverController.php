<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\RegisterRequestDriverUpdateRequest;

use App\Models\Driver;
use App\Models\DriverPlan;
use App\Models\RegisterRequestDriver;
use App\Models\RegisterRequestStatus;
use App\Models\Prefecture;
use App\Models\WebConfigBase;
use App\Models\WebConfigSystem;
use App\Models\WebNoticeLog;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendTestMail;
use App\Mail\RegisterRequestDriverUpdateStatusSendGuest;

class RegisterRequestDriverController extends Controller
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

        $register_request_object = RegisterRequestDriver::select()
            ->with(['joinAddr1', 'joinGender',]); // 結合

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


        return view('admin.register_request_driver.index', [
            'register_request_list' => $register_request_list,
            'register_request_status_list' => $register_request_status_list,
            'prefecture_list' => $prefecture_list,
            'orderby_list' => $orderby_list,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $register_request_id
     * @return \Illuminate\Http\Response
     */
    public function show($register_request_id)
    {
        $register_request_driver = RegisterRequestDriver::select()
            ->where('id', $register_request_id)
            ->first();
        return view('admin.register_request_driver.show', [
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

        $register_request = RegisterRequestDriver::select()
            ->where('id', $register_request_id)
            ->first();

        $register_request_status_list = '';

        // 審査中、審査中(登録処理済み)の場合
        if (in_array($register_request->register_request_status_id, [6, 7])) {
            /* フォーム検索に使うデータ */
            $register_request_status_list = RegisterRequestStatus::select()
                ->where(function ($query) {
                    $query->orWhere('id', 2);
                    $query->orWhere('id', 3);
                })
                ->get();
        } elseif (in_array($register_request->register_request_status_id, [1])) {
            /* フォーム検索に使うデータ */
            $register_request_status_list = RegisterRequestStatus::select()
                ->where(function ($query) {
                    $query->orWhere('id', 2);
                    $query->orWhere('id', 3);
                    $query->orWhere('id', 6);
                })
                ->get();
        }

        $driver = Driver::select()->where('email', $register_request->email)->first();
        $driver_plan_list = '';
        if ($driver && $driver->driver_plan_id) {
            $driver_plan_list = DriverPlan::select()->get();
        }

        $driver_plan_list = DriverPlan::select()->get();

        //  管理者が対応できることがない or すでに申請の結果が確定しているときは、編集不可
        if (
            !in_array($register_request->register_request_status_id, [1, 6, 7]) ||
            $driver && in_array($driver->driver_entry_status_id, [1, 3])
        ) {
            abort(403, 'Access denied 編集することができません');
        }

        return view('admin.register_request_driver.edit', [
            'register_request' => $register_request,
            'register_request_status_list' => $register_request_status_list,
            'driver_plan_list' => $driver_plan_list,
            'driver' => $driver,
        ]);
    }

    /**
     * 登録申請の対応
     * 
     * 許可、申請中、不可。
     * ドライバープランの選定。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $register_request_id
     * @return \Illuminate\Http\Response
     */
    public function update(RegisterRequestDriverUpdateRequest $request, $register_request_id)
    {
        $register_request_status_id = $request->register_request_status_id ?? '';
        $driver_plan_id = $request->driver_plan_id ?? null;

        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報

        /* 更新処理 */
        $register_request_driver = RegisterRequestDriver::where('id', $register_request_id)->first();

        $register_request_driver->register_request_status_id = $register_request_status_id; // 登録申請ステータスid

        // すでに登録された審査中ユーザーが存在する場合は、ユーザーデータを更新。
        $driver = Driver::withTrashed()->where('email', $register_request_driver->email)->first();

        if ($driver) {
            if ($driver->driver_entry_status_id == 2) {
                if ($register_request_status_id == 2) {
                    // 許可の場合
                    $driver->update([
                        "driver_entry_status_id" =>  1 // 通過
                    ]);
                } elseif ($register_request_status_id == 6) {
                    // 審査中の場合
                    $driver->update([
                        "driver_entry_status_id" => 2 // 審査中
                    ]);
                } elseif ($register_request_status_id == 3) {
                    // 不可の場合
                    $driver->update([
                        "driver_entry_status_id" => 3 // 不通過
                    ]);

                    $driver->forceDelete(); // 完全に削除
                }
            }
        }

        $register_request_driver->driver_plan_id = $driver_plan_id; // 登録申請にドライバープランを登録

        // Create token
        /* ユーザーが存在していない場合、ユーザーを作成する段取りを行う */
        $token = '';
        if ($driver || $register_request_status_id == 2 || $register_request_status_id == 6) {
            /* ステータスが 2(許可) or 6(審査中)だったら、登録用トークンの発行*/
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
            $register_request = RegisterRequestDriver::select()->where('id', $register_request_id)->first();

            // メールで利用するデータ
            $data_mail = [
                "config_base" => $config_base,
                "config_system" => $config_system,
                'register_request' => $register_request,
                'driver' => $driver,
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
                Mail::to($to_guest)->send(new RegisterRequestDriverUpdateStatusSendGuest($data_mail)); // 送信
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
                    'text' => "ドライバー登録申請",
                    'remote_addr' => $remote_addr,
                    'http_user_agent' => $http_user_agent,
                    'url' => $url,
                ]);
            }
        } else {
            $msg = "登録申請ステータスを更新できませんでした!";
        }

        $msg .= "({$msg_mail})"; // メッセージ結合

        return redirect()->route("admin.register_request_driver.show", [
            'register_request_id' => $register_request_id,
        ])->with([
            'msg' => $msg
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
            $result = RegisterRequestDriver::where('id', $register_request_id)->delete($register_request_id);
            if ($result) {
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

        return redirect()->route('admin.register_request_driver.index')->with([
            'msg' => $msg,
        ]);
    }
}
