<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\DeliveryPickupAddrCreateRequest;
use App\Http\Requests\Admin\DeliveryPickupAddrUpdateRequest;
use App\Models\DeliveryPickupAddr;
use App\Models\Prefecture;
use App\Models\DeliveryCompany;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;

class DeliveryPickupAddrController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword ?? ''; // 検索ワード
        $search_from_created_at = $request->from_created_at ?? ''; // 作成日 以上
        $search_to_created_at = $request->to_created_at ?? ''; // 作成日 以下

        $search_from_updated_at = $request->from_updated_at ?? ''; // 更新日 以上
        $search_to_updated_at = $request->to_updated_at ?? ''; // 更新日 以下


        // 集荷先住所一覧
        $pickup_addr_object = DeliveryPickupAddr::select()
            ->with(['joinOffice']);

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

        // 作成日 範囲 絞り込み
        if ($search_from_created_at) {
            $pickup_addr_object->where('created_at', '>=', $search_from_created_at);
        }
        if ($search_to_created_at) {
            $pickup_addr_object->where('created_at', '<=', $search_to_created_at);
        }

        // 更新日 範囲 絞り込み
        if ($search_from_updated_at) {
            $pickup_addr_object->where('updated_at', '>=', $search_from_updated_at);
        }
        if ($search_to_updated_at) {
            $pickup_addr_object->where('updated_at', '<=', $search_to_updated_at);
        }

        $pickup_addr_list = $pickup_addr_object->paginate(100)->withQueryString();

        // logger($pickup_addr_list);
        /* フォームに使うデータ */
        // 都道府県取得
        $prefecture_list = Prefecture::select()->get();

        $orderby_list = [
            ['value' => 'id_desc', 'text' => 'ID大きい順',],
            ['value' => 'id_asc', 'text' => 'ID小さい順',],
        ];

        return view('admin.delivery_pickup_addr.index', [
            'pickup_addr_list' => $pickup_addr_list,
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
        /* フォームに使うデータ */
        // 都道府県取得
        $prefecture_list = Prefecture::select()->get();
        $company_list = DeliveryCompany::get(); // 配送会社一覧 取得

        return view('admin.delivery_pickup_addr.create', [
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
        $delivery_office_id = $request->delivery_office_id ?? '';
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

        $pickup_addr_create = DeliveryPickupAddr::create([
            'delivery_office_id' => $delivery_office_id,
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
            $msg = '集荷先住所の作成をしました。';
            return redirect()->route('admin.delivery_pickup_addr.show', [
                'pickup_id' => $pickup_addr_create->id
            ])->with([
                'msg' => $msg
            ]);
        } else {
            $msg = '集荷先住所の作成に失敗しました!';
        }

        return redirect()->route('admin.delivery_pickup_addr.index')->with([
            'msg' => $msg
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $pickup_id
     * @return \Illuminate\Http\Response
     */
    public function show($pickup_id)
    {
        // 集荷先住所一覧
        $pickup_addr = DeliveryPickupAddr::select()
            ->where('id', $pickup_id)
            ->with(['joinOffice'])
            ->first();

        return view('admin.delivery_pickup_addr.show', [
            'pickup_addr' => $pickup_addr,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $pickup_id
     * @return \Illuminate\Http\Response
     */
    public function edit($pickup_id)
    {
        // 集荷先住所一覧
        $pickup_addr = DeliveryPickupAddr::select()
            ->where('id', $pickup_id)
            ->with(['joinOffice'])
            ->first();

        /* フォームに使うデータ */
        // 都道府県取得
        $prefecture_list = Prefecture::select()->get();
        $company_list = DeliveryCompany::get(); // 配送会社一覧 取得


        return view('admin.delivery_pickup_addr.edit', [
            'pickup_addr' => $pickup_addr,
            'prefecture_list' => $prefecture_list,
            'company_list' => $company_list,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $pickup_id
     * @return \Illuminate\Http\Response
     */
    public function update(DeliveryPickupAddrUpdateRequest $request, $pickup_id)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報

        $delivery_office_id = $request->delivery_office_id ?? '';
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

        $pickup_addr_update = DeliveryPickupAddr::where('id', '=', $pickup_id)->update([
            'delivery_office_id' => $delivery_office_id,
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

        if ($pickup_addr_update) {
            $msg = '集荷先住所の更新をしました。';
        } else {
            $msg = '稼働依頼の更新ができませんでした。';
        }

        return redirect()->route('admin.delivery_pickup_addr.show', [
            'pickup_id' => $pickup_id
        ])->with([
            'msg' => $msg
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $pickup_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($pickup_id)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報 
        $msg = ''; // 削除メッセージ

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        try {
            $result = DeliveryPickupAddr::where('id', '=', $pickup_id)->delete($pickup_id);
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

        return redirect()->route('admin.delivery_pickup_addr.index')->with([
            'msg' => $msg,
        ]);
    }
}
