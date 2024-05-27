<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\DriverRegisterDeliveryOfficeMemoCreateRequest;
use App\Http\Requests\Admin\DriverRegisterDeliveryOfficeMemoUpdateRequest;
use App\Models\DriverRegisterDeliveryOfficeMemo;
use App\Models\Prefecture;
use App\Models\DeliveryCompany;

class DriverRegisterDeliveryOfficeMemoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword ?? ''; // 検索ワード

        $search_addr1_id = $request->addr1_id ?? ''; // 都道府県ID
        $search_from_created_at = $request->from_created_at ?? ''; // 作成日 以上
        $search_to_created_at = $request->to_created_at ?? ''; // 作成日 以下
        $search_from_updated_at = $request->from_updated_at ?? ''; // 更新日 以上
        $search_to_updated_at = $request->to_updated_at ?? ''; // 更新日 以下
        $orderby = $request->orderby ?? ''; // 並び替え

        $register_office_memo_object = DriverRegisterDeliveryOfficeMemo::select();

        // キーワードで検索
        if (isset($request->keyword) && $request->keyword != '') {

            $register_office_memo_object->Where(function ($query) use ($keyword) {
                $query
                    ->orWhere('delivery_company_name', 'LIKE', "%{$keyword}%")
                    ->orWhere('delivery_office_name', 'LIKE', "%{$keyword}%")
                    ->orWhere('addr2', 'LIKE', "%{$keyword}%")
                    ->orWhere('addr3', 'LIKE', "%{$keyword}%")
                    ->orWhere('addr4', 'LIKE', "%{$keyword}%");
            });
        }

        // 都道府県
        if ($search_addr1_id) {
            $register_office_memo_object->where([['addr1_id', $search_addr1_id]]);
        }

        // 作成日 範囲 絞り込み
        if ($search_from_created_at) {
            $register_office_memo_object->where('created_at', '>=', $search_from_created_at);
        }
        if ($search_to_created_at) {
            $register_office_memo_object->where('created_at', '<=', $search_to_created_at);
        }

        // 更新日 範囲 絞り込み
        if ($search_from_updated_at) {
            $register_office_memo_object->where('updated_at', '>=', $search_from_updated_at);
        }
        if ($search_to_updated_at) {
            $register_office_memo_object->where('updated_at', '<=', $search_to_updated_at);
        }

        /* 並び替え */
        if ($orderby === 'id_desc') {
            $register_office_memo_object->orderBy('id', 'desc');
        } elseif ($orderby === 'id_asc') {
            $register_office_memo_object->orderBy('id', 'asc');
        } elseif ($orderby === 'created_at_desc') {
            $register_office_memo_object->orderBy('created_at', 'desc');
        } elseif ($orderby === 'created_at_asc') {
            $register_office_memo_object->orderBy('created_at', 'asc');
        } elseif ($orderby === 'updated_at_desc') {
            $register_office_memo_object->orderBy('updated_at', 'desc');
        } elseif ($orderby === 'updated_at_asc') {
            $register_office_memo_object->orderBy('updated_at', 'asc');
        } else {
            $register_office_memo_object->orderBy('id', 'desc');
        }

        $register_office_memo_list = $register_office_memo_object->paginate(100)->withQueryString();

        /* フォーム検索に使うデータ */
        // 都道府県取得
        $prefecture_list = Prefecture::select()->get();

        // 並び順
        $orderby_list = [
            ['value' => 'id_desc', 'text' => 'ID大きい順',],
            ['value' => 'id_asc', 'text' => 'ID小さい順',],
            ['value' => 'created_at_desc', 'text' => '作成日 降順',],
            ['value' => 'created_at_asc', 'text' => '作成日 昇順',],
            ['value' => 'updated_at_desc', 'text' => '更新日 降順',],
            ['value' => 'updated_at_asc', 'text' => '更新日 昇順',],
        ];

        return view('admin.driver_register_delivery_office_memo.index', [
            'register_office_memo_list' => $register_office_memo_list,
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
        $prefecture_list = Prefecture::select()->get(); // 都道府県取得
        $company_list = DeliveryCompany::select()->get(); // 配送会社一覧

        return view('admin.driver_register_delivery_office_memo.create', [
            'prefecture_list' => $prefecture_list,
            'company_list' => $company_list,
        ]);

        //テンプレート側で {{$msg}}で出力
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DriverRegisterDeliveryOfficeMemoCreateRequest $request)
    {
        $driver_id = $request->driver_id ?? '';
        $delivery_company_id = $request->delivery_company_id ?? '';
        $delivery_office_name = $request->delivery_office_name ?? '';
        $post_code1 = $request->post_code1 ?? '';
        $post_code2 = $request->post_code2 ?? '';
        $addr1_id = $request->addr1_id ?? '';
        $addr2 = $request->addr2 ?? '';
        $addr3 = $request->addr3 ?? '';
        $addr4 = $request->addr4 ?? '';

        $driver_register_office_memo =  DriverRegisterDeliveryOfficeMemo::create([
            'driver_id' => $driver_id,
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

        return redirect()->route('admin.driver.show', [
            'driver_id' => $driver_register_office_memo->driver_id,
        ])->with([
            'msg' => $msg,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $register_office_memo_id
     * @return \Illuminate\Http\Response
     */
    public function edit($register_office_memo_id)
    {
        $register_office_memo = DriverRegisterDeliveryOfficeMemo::select()
            ->where('id', $register_office_memo_id)
            ->first();

        /* フォーム検索に使うデータ */
        // 都道府県取得
        $prefecture_list = Prefecture::select()->get();
        $company_list = DeliveryCompany::select()->get(); // 配送会社一覧

        return view('admin.driver_register_delivery_office_memo.edit', [
            'register_office_memo' => $register_office_memo,
            'prefecture_list' => $prefecture_list,
            'company_list' => $company_list,

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  
     * @return \Illuminate\Http\Response
     */
    public function update(DriverRegisterDeliveryOfficeMemoUpdateRequest $request, $register_office_memo_id)
    {
        $driver_id = $request->driver_id ?? '';
        $delivery_company_id = $request->delivery_company_id ?? '';
        $delivery_office_name = $request->delivery_office_name ?? '';
        $post_code1 = $request->post_code1 ?? '';
        $post_code2 = $request->post_code2 ?? '';
        $addr1_id = $request->addr1_id ?? '';
        $addr2 = $request->addr2 ?? '';
        $addr3 = $request->addr3 ?? '';
        $addr4 = $request->addr4 ?? '';

        $driver_register_office_memo_update =  DriverRegisterDeliveryOfficeMemo::where('id', $register_office_memo_id)->update([
            'driver_id' => $driver_id,
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

        return redirect()->route('admin.driver.show', [
            'driver_id' => $driver_id,
        ])->with([
            'msg' => $msg,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $register_office_memo_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($register_office_memo_id)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $msg = ''; // 削除メッセージ

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        try {
            $result = DriverRegisterDeliveryOfficeMemo::where('id', '=', $register_office_memo_id)->delete($register_office_memo_id);
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

        return redirect()->route('admin.driver_register_delivery_office_memo.index')->with([
            'msg' => $msg,
        ]);
    }
}
