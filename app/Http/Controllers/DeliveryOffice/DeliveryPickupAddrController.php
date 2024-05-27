<?php

namespace App\Http\Controllers\DeliveryOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\DeliveryOffice\DeliveryPickupAddrCreateRequest;
use App\Http\Requests\DeliveryOffice\DeliveryPickupAddrUpdateRequest;
use App\Http\Requests\DeliveryOffice\DeliveryPickupDestroyAddrRequest;
use App\Models\DeliveryPickupAddr;
use App\Models\Prefecture;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;
use App\Models\DeliveryCompany;

/**
 *  集荷先住所
 */
class DeliveryPickupAddrController extends Controller
{
    /**
     * 一覧
     * +API機能
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $type = $request->type ?? '';
        $keyword = $request->keyword ?? ''; // 検索ワード

        $api_status = true;

        // 集荷先住所一覧
        $pickup_addr_object = DeliveryPickupAddr::select()
            ->where('delivery_office_id', $login_id);

        // キーワードで検索
        if ($keyword) {
            $pickup_addr_object->Where(function ($query) use ($keyword) {
                $query
                    ->orWhere('delivery_company_name', 'LIKE', "%{$keyword}%")
                    ->orWhere('delivery_office_name', 'LIKE', "%{$keyword}%")
                    ->orWhere('email', 'LIKE', "%{$keyword}%")
                    ->orWhere('tel', 'LIKE', "%{$keyword}%");
            });
        }

        $pickup_addr_list = $pickup_addr_object->paginate(100)->withQueryString();

        if ($type === "form_new") {
            $pickup_addr_list = $pickup_addr_object->get();

            $pickup_addr_list = json_decode(json_encode($pickup_addr_list));

            // 配列の先頭に新しいオブジェクトを追加
            $new_data = [
                'id' => 'new',
                'name' => "新しい集荷先",
            ];
            array_unshift($pickup_addr_list, $new_data);
        }


        if ($pickup_addr_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $pickup_addr_list
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('delivery_office.delivery_pickup_addr.index', [
                'pickup_addr_list' => $pickup_addr_list,
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
        $company_list = DeliveryCompany::get(); // 配送会社一覧 取得

        return view('delivery_office.delivery_pickup_addr.create', [
            'prefecture_list' => $prefecture_list,
            'company_list' => $company_list,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeliveryPickupAddrCreateRequest $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $delivery_company_id = in_array($request->delivery_company_id, [NULL, 'None'])  ? NULL :  $request->delivery_company_id;
        $delivery_company_name = $request->delivery_company_name ?? '';
        $delivery_office_name = $request->delivery_office_name ?? '';
        $email = $request->email ?? '';
        $tel = $request->tel ?? '';
        $post_code1 = $request->post_code1 ?? '';
        $post_code2 = $request->post_code2 ?? '';
        $addr1_id = $request->addr1_id ?? '';
        $addr2 = $request->addr2 ?? '';
        $addr3 = $request->addr3 ?? '';
        $addr4 = $request->addr4 ?? '';
        // logger($request);
        // exit;

        // 配送会社IDが入力されていたら、会社名は配送会社テーブルから取得
        if ($delivery_company_id) {
            $delivery_conpany = DeliveryCompany::select()->where('id', $delivery_company_id)->first();
            $delivery_company_name = $delivery_conpany->name ?? '';
        }

        $pickup_addr_create = DeliveryPickupAddr::create([
            'delivery_office_id' => $login_id,
            'delivery_company_name' => $delivery_company_name,
            'delivery_office_name' => $delivery_office_name,
            'email' => $email,
            'tel' => $tel,
            'post_code1' => $post_code1,
            'post_code2' => $post_code2,
            'addr1_id' => $addr1_id,
            'addr2' => $addr2,
            'addr3' => $addr3,
            'addr4' => $addr4,
        ]);

        $msg = '';
        if ($pickup_addr_create) {
            $msg = '集荷先住所の登録をしました。';
        } else {
            $msg = '集荷先住所の登録に失敗しました!';
        }

        $api_status = true;
        if ($pickup_addr_create) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'message' => $msg,
                'data' => $pickup_addr_create
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return redirect()->route('delivery_office.delivery_pickup_addr.index')->with([
                'msg' => $msg,
            ]);
        }
    }

    /**
     * 取得
     *
     * @param  int  $pickup_id
     * @return \Illuminate\Http\Response
     */
    public function show($pickup_id, Request $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        // 集荷先住所一覧
        $pickup_addr_object = DeliveryPickupAddr::select()
            ->where([
                ['id', $pickup_id],
                ['delivery_office_id', $login_id]
            ]);

        $pickup_addr = $pickup_addr_object->first();

        $api_status = true;
        if ($pickup_addr) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $pickup_addr
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 編集画面
     *
     * @param  int  $pickup_id
     * @return \Illuminate\Http\Response
     */
    public function edit($pickup_id)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // 集荷先住所一覧
        $pickup_addr = DeliveryPickupAddr::select()
            ->where([
                ['delivery_office_id', $login_id],
                ['id', $pickup_id],
            ])
            ->first();

        /* フォームに使うデータ */
        // 都道府県取得
        $prefecture_list = Prefecture::select()->get();
        /* フォームで使うデータ */
        $company_list = DeliveryCompany::get(); // 配送会社一覧 取得

        return view('delivery_office.delivery_pickup_addr.edit', [
            'pickup_addr' => $pickup_addr,
            'prefecture_list' => $prefecture_list,
            'company_list' => $company_list,
        ]);
    }

    /**
     * 更新
     * +API機能
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $pickup_id
     * @return \Illuminate\Http\Response
     */
    public function update(DeliveryPickupAddrUpdateRequest $request, $pickup_id)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $delivery_company_id = in_array($request->delivery_company_id, [NULL, 'None'])  ? NULL :  $request->delivery_company_id;
        $delivery_company_name = $request->delivery_company_name ?? '';
        $delivery_office_name = $request->delivery_office_name ?? '';
        $email = $request->email ?? '';
        $tel = $request->tel ?? '';
        $post_code1 = $request->post_code1 ?? '';
        $post_code2 = $request->post_code2 ?? '';
        $addr1_id = $request->addr1_id ?? '';
        $addr2 = $request->addr2 ?? '';
        $addr3 = $request->addr3 ?? '';
        $addr4 = $request->addr4 ?? '';

        // 配送会社IDが入力されていたら、会社名は配送会社テーブルから取得
        if ($delivery_company_id) {
            $delivery_conpany = DeliveryCompany::select()->where('id', $delivery_company_id)->first();
            $delivery_company_name = $delivery_conpany->name ?? '';
        }

        $pickup_addr_update = DeliveryPickupAddr::where([
            ['id', $pickup_id],
            ['delivery_office_id', $login_id]
        ])->update([
            'delivery_company_name' => $delivery_company_name,
            'delivery_office_name' => $delivery_office_name,
            'email' => $email,
            'tel' => $tel,
            'post_code1' => $post_code1,
            'post_code2' => $post_code2,
            'addr1_id' => $addr1_id,
            'addr2' => $addr2,
            'addr3' => $addr3,
            'addr4' => $addr4,
        ]);

        $msg = '';
        if ($pickup_addr_update) {
            $msg = '集荷先住所の編集をしました。';
        } else {
            $msg = '集荷先住所の編集に失敗しました!';
        }

        $api_status = true;
        if ($pickup_addr_update) {
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
            return redirect()->route('delivery_office.delivery_pickup_addr.index')->with([
                'msg' => $msg
            ]);
        }
    }

    /**
     * 削除
     *
     * @param  int  $pickup_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($pickup_id, DeliveryPickupDestroyAddrRequest $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $api_status = true;
        $msg = ''; // 削除メッセージ

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        try {
            $result = DeliveryPickupAddr::where([
                ['id', '=', $pickup_id],
                ['delivery_office_id', $login_id]
            ])->delete($pickup_id);

            if ($result) {
                $api_status = true;
                $msg = '削除に成功';
            } else {
                $api_status = false;
                $msg = '削除されませんでした。';
            }
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
            return redirect()->route('delivery_office.delivery_pickup_addr.index')->with([
                'msg' => $msg,
            ]);
        }
    }
}
