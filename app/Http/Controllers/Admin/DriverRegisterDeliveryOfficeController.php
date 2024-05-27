<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;

use Illuminate\Http\Request;
use App\Http\Requests\Admin\DriverRegisterDeliveryOfficeUpsertRequest;

use Illuminate\Support\Facades\DB;
use App\Models\DeliveryCompany;
use App\Models\DeliveryOffice;
use App\Models\Driver;

use App\Models\DriverRegisterDeliveryOffice;

class DriverRegisterDeliveryOfficeController extends Controller
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
        $search_driver_id = $request->driver_id ?? ''; // ドライバーID
        $search_delivery_office_id = $request->delivery_office_id ?? ''; // 営業所ID
        $orderby = $request->orderby ?? ''; // 並び替え


        $register_office_object = DriverRegisterDeliveryOffice::select()->with(['joinDriver', 'joinOffice']);

        // 作成日 範囲 絞り込み
        if (isset($request->from_created_at)) {
            $register_office_object->where('created_at', '>=', $search_from_created_at);
        }
        if (isset($request->to_created_at)) {
            $register_office_object->where('created_at', '<=', $search_to_created_at);
        }

        // 更新日 範囲 絞り込み
        if (isset($request->from_updated_at)) {
            $register_office_object->where('updated_at', '>=', $search_from_updated_at);
        }
        if (isset($request->to_updated_at)) {
            $register_office_object->where('updated_at', '<=', $search_to_updated_at);
        }

        // ドライバー 絞り込み
        if ($search_driver_id) {
            $register_office_object->where('driver_id', $search_driver_id);
        }

        // 営業所 絞り込み
        if ($search_delivery_office_id) {
            $register_office_object->where('delivery_office_id', $search_delivery_office_id);
        }


        /* 並び替え */
        if ($orderby === 'id_desc') {
            $register_office_object->orderBy('id', 'desc');
        } elseif ($orderby === 'id_asc') {
            $register_office_object->orderBy('id', 'asc');
        } elseif ($orderby === 'created_at_desc') {
            $register_office_object->orderBy('created_at', 'desc');
        } elseif ($orderby === 'created_at_asc') {
            $register_office_object->orderBy('created_at', 'asc');
        } elseif ($orderby === 'updated_at_desc') {
            $register_office_object->orderBy('updated_at', 'desc');
        } elseif ($orderby === 'updated_at_asc') {
            $register_office_object->orderBy('updated_at', 'asc');
        } else {
            $register_office_object->orderBy('id', 'desc');
        }

        $register_office_list = $register_office_object->paginate(50)->withQueryString();
        // $delivery_office_id_list = $register_office_object->get();
        // logger($delivery_office_id_list->toArray());


        /* フォーム検索に使うデータ */
        // 並び順
        $orderby_list = [
            ['value' => 'id_desc', 'text' => 'ID大きい順',],
            ['value' => 'id_asc', 'text' => 'ID小さい順',],
            ['value' => 'created_at_desc', 'text' => '作成日 降順',],
            ['value' => 'created_at_asc', 'text' => '作成日 昇順',],
            ['value' => 'updated_at_desc', 'text' => '更新日 降順',],
            ['value' => 'updated_at_asc', 'text' => '更新日 昇順',],
        ];

        return view('admin.driver_register_delivery_office.index', [
            'register_office_list' => $register_office_list,
            'orderby_list' => $orderby_list,
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $register_office_id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $driver_id = $request->driver_id ?? '';

        /* フォームで使うデータ */
        // ドライバー
        $driver = Driver::select()->where('id', $driver_id)->first();

        // 会社別営業所 一覧
        $delivery_multi_list = [];
        $company_list = DeliveryCompany::get()->toArray();

        $count = 0;
        foreach ($company_list as $company) {
            $office_list = DeliveryOffice::with('joinCompany')
                ->where('delivery_company_id', $company['id'])
                ->orderBy('delivery_company_id', 'asc')
                ->get()
                ->toArray();
            $delivery_multi_list[$count]['office_list'] = $office_list;
            $delivery_multi_list[$count]['company'] = $company;
            $count++;
        }

        // 登録中の営業所ID
        $select_register_office_list = DriverRegisterDeliveryOffice::select('delivery_office_id')->where('driver_id', $driver_id)->get();

        // 登録中営業所ID 一覧
        $register_office_id_list  = [];
        foreach ($select_register_office_list as $register) {
            $register_office_id_list[] = $register->delivery_office_id;
        }
        // logger($register_office_id_list);

        return view('admin.driver_register_delivery_office.edit', [
            'driver' => $driver,
            'delivery_multi_list' => $delivery_multi_list,
            'register_office_id_list' => $register_office_id_list ?? '',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $register_office_id
     * @return \Illuminate\Http\Response
     */
    public function Upsert(DriverRegisterDeliveryOfficeUpsertRequest $request)
    {
        $driver_id = $request->driver_id ?? '';
        $delivery_office_id_list = $request->delivery_office_id ?? ''; // 選択した営業所リスト

        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報

        // logger($request);
        // exit;

        $msg = '';

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        if ($delivery_office_id_list) {
            // 登録済みの営業所
            $select_register_office_list = DriverRegisterDeliveryOffice::select('delivery_office_id')->where('driver_id', $driver_id)->get();

            // 登録済み営業所ID 一覧
            $register_office_id_list  = [];
            foreach ($select_register_office_list as $register) {
                $register_office_id_list[] = $register->delivery_office_id;
            }

            DB::beginTransaction();
            try {
                // 削除する営業所
                // リクエストされた営業所ID一覧の中に、登録済みのIDがなくなっていれば、その登録済み営業所は削除する
                foreach ($register_office_id_list as $office_id) {
                    if (!in_array((string)$office_id, $delivery_office_id_list, true)) {
                        DriverRegisterDeliveryOffice::where([
                            ['driver_id', $driver_id],
                            ['delivery_office_id', $office_id],
                        ])->delete();
                    }
                }

                // 営業登録処理
                foreach ($delivery_office_id_list as $office_id) {
                    $register_upsert = DriverRegisterDeliveryOffice::updateOrCreate(
                        ['driver_id' => $driver_id, 'delivery_office_id' => $office_id],
                        ['driver_id' => $driver_id, 'delivery_office_id' => $office_id],
                    );
                }
                DB::commit();
                $msg = "登録営業所を登録しました。";
            } catch (\Throwable $e) {
                DB::rollback();

                $msg = '登録営業所を登録に失敗';

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

                $msg = "登録営業所を登録できませんでした!";
            }
        } else {
            $delete_register_office = DriverRegisterDeliveryOffice::where([
                ['driver_id', $driver_id],
            ])->delete();
            if ($delete_register_office) {
                $msg = "登録営業所全て取り消しました。";
            }
        }


        return redirect()->route("admin.driver_register_delivery_office.index", [
            'driver_id' => $driver_id,
        ])->with([
            'msg' => $msg
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $register_office_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($register_office_id)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $msg = ''; // 削除メッセージ

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        try {
            $result = DriverRegisterDeliveryOffice::destroy($register_office_id);

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

        return redirect()->route('admin.driver_register_delivery_office.index')->with([
            'msg' => $msg,
        ]);
    }
}
