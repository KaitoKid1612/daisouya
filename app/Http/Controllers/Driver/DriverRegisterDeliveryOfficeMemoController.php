<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;
use Illuminate\Http\Request;
use App\Http\Requests\Driver\DriverRegisterDeliveryOfficeMemoCreateRequest;
use App\Http\Requests\Driver\DriverRegisterDeliveryOfficeMemoUpdateRequest;
use App\Http\Requests\Driver\DriverRegisterDeliveryOfficeMemoDestroyRequest;
use App\Models\DriverRegisterDeliveryOfficeMemo;
use App\Models\Prefecture;
use App\Models\DeliveryCompany;

/**
 * ドライバー 登録営業所メモ
 */
class DriverRegisterDeliveryOfficeMemoController extends Controller
{
    /**
     * 一覧
     * +API機能
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $register_office_memo_list = DriverRegisterDeliveryOfficeMemo::select()
            ->where('driver_id', $login_id)
            ->paginate(30)->withQueryString();

        // logger($register_office_memo_list);

        $api_status = true;
        if ($register_office_memo_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $register_office_memo_list
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('driver.driver_register_delivery_office_memo.index', [
                'register_office_memo_list' => $register_office_memo_list,
            ]);
        }
    }

    /**
     * 作成画面
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /* フォームに使うデータ */
        $prefecture_list = Prefecture::select()->get(); // 都道府県取得
        $company_list = DeliveryCompany::select()->get(); // 配送会社一覧

        return view('driver.driver_register_delivery_office_memo.create', [
            'prefecture_list' => $prefecture_list,
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
    public function store(DriverRegisterDeliveryOfficeMemoCreateRequest $request)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $api_status = true;

        $delivery_company_id = $request->delivery_company_id ?? '';
        $delivery_office_name = $request->delivery_office_name ?? '';
        $post_code1 = $request->post_code1 ?? '';
        $post_code2 = $request->post_code2 ?? '';
        $addr1_id = $request->addr1_id ?? '';
        $addr2 = $request->addr2 ?? '';
        $addr3 = $request->addr3 ?? '';
        $addr4 = $request->addr4 ?? '';

        $driver_register_office_memo =  DriverRegisterDeliveryOfficeMemo::create([
            'driver_id' => $login_id,
            'delivery_company_id' => $delivery_company_id,
            'delivery_office_name' => $delivery_office_name,
            'post_code1' => $post_code1,
            'post_code2' => $post_code2,
            'addr1_id' => $addr1_id,
            'addr2' => $addr2,
            'addr3' => $addr3,
            'addr4' => $addr4,
        ]);

        $msg = '';
        if ($driver_register_office_memo) {
            $msg = '登録営業所メモを登録しました。';
        } else {
            $msg = '登録営業所メモを登録に失敗しました!';
        }

        $api_status = true;
        if ($driver_register_office_memo) {
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
            return redirect()->route('driver.driver_register_delivery_office_memo.index')->with([
                'msg' => $msg,
            ]);
        }
    }

    /**
     * 取得
     * +API機能
     *
     * @param  int  $register_office_memo_id
     * @return \Illuminate\Http\Response
     */
    public function show($register_office_memo_id, Request $request)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $api_status = true;

        $register_office_memo = DriverRegisterDeliveryOfficeMemo::select()
            ->where([
                ['driver_id', $login_id],
                ['id', $register_office_memo_id],
            ])
            ->first();

        $api_status = true;
        if ($register_office_memo) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $register_office_memo
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $register_office_memo_id
     * @return \Illuminate\Http\Response
     */
    public function edit($register_office_memo_id)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        $register_office_memo = DriverRegisterDeliveryOfficeMemo::select()
            ->where('driver_id', $login_id)
            ->first();

        /* フォーム検索に使うデータ */
        // 都道府県取得
        $prefecture_list = Prefecture::select()->get();
        $company_list = DeliveryCompany::select()->get(); // 配送会社一覧

        return view('driver.driver_register_delivery_office_memo.edit', [
            'register_office_memo' => $register_office_memo,
            'prefecture_list' => $prefecture_list,
            'company_list' => $company_list,

        ]);
    }

    /**
     *  更新
     * +API機能
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $register_office_memo_id
     * @return \Illuminate\Http\Response
     */
    public function update(DriverRegisterDeliveryOfficeMemoUpdateRequest $request, $register_office_memo_id)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $driver_id = $request->driver_id ?? '';
        $delivery_company_id = $request->delivery_company_id ?? '';
        $delivery_office_name = $request->delivery_office_name ?? '';
        $post_code1 = $request->post_code1 ?? '';
        $post_code2 = $request->post_code2 ?? '';
        $addr1_id = $request->addr1_id ?? '';
        $addr2 = $request->addr2 ?? '';
        $addr3 = $request->addr3 ?? '';
        $addr4 = $request->addr4 ?? '';

        $driver_register_office_memo_update =  DriverRegisterDeliveryOfficeMemo::where([
            ['id', '=', $register_office_memo_id],
            ['driver_id', '=', $login_id],
        ])->update([
            'driver_id' => $login_id,
            'delivery_company_id' => $delivery_company_id,
            'delivery_office_name' => $delivery_office_name,
            'post_code1' => $post_code1,
            'post_code2' => $post_code2,
            'addr1_id' => $addr1_id,
            'addr2' => $addr2,
            'addr3' => $addr3,
            'addr4' => $addr4,
        ]);

        $msg = '';
        if ($driver_register_office_memo_update) {
            $msg = '登録営業所メモを更新しました。';
        } else {
            $msg = '登録営業所メモを更新に失敗しました!';
        }

        $api_status = true;
        if ($driver_register_office_memo_update) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'message' => $msg
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return redirect()->route('driver.driver_register_delivery_office_memo.index')->with([
                'msg' => $msg,
            ]);
        }
    }

    /**
     * 削除
     * +API機能
     *
     * @param  int  $register_office_memo_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($register_office_memo_id, DriverRegisterDeliveryOfficeMemoDestroyRequest $request)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $msg = '';
        $api_status = true;

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        try {
            $result = DriverRegisterDeliveryOfficeMemo::where([
                ['id', '=', $register_office_memo_id],
                ['driver_id', '=', $login_id],
            ])
                ->delete($register_office_memo_id);
            $msg = '削除に成功';
            $api_status = true;
        } catch (\Throwable $e) {
            $msg .= '削除に失敗';
            $api_status = false;

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

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'message' => $msg
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return redirect()->route('driver.driver_register_delivery_office_memo.index')->with([
                'msg' => $msg,
            ]);
        }
    }
}
