<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\DeliveryOfficeCreateRequest;
use App\Http\Requests\Admin\DeliveryOfficeUpdateRequest;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;
use Laravel\Cashier\Cashier;

use Illuminate\Support\Facades\Mail;
use App\Mail\DeliveryOfficeRegisterSendDeliveryOfficeMail;

use App\Models\DeliveryOfficeType;
use App\Models\DeliveryOffice;
use App\Models\DeliveryCompany;
use App\Models\Driver;
use App\Models\DriverTaskReview;
use App\Models\DriverRegisterDeliveryOffice;
use App\Models\DriverTask;
use App\Models\Prefecture;
use App\Models\DeliveryOfficeChargeUserType;
use App\Models\DeliveryPickupAddr;
use App\Models\WebConfigBase;
use App\Models\WebConfigSystem;
use App\Models\WebNoticeLog;

use Illuminate\Support\Facades\DB;


class DeliveryOfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword ?? ''; // 検索ワード
        $search_addr1 = $request->addr1_id ?? ''; // 都道府県ID

        $search_from_task_count = $request->from_task_count ?? ''; // 稼働数 以上
        $search_to_task_count = $request->to_task_count ?? ''; // 稼働数 以下

        $search_from_created_at = $request->from_created_at ?? ''; // 作成日 以上
        $search_to_created_at = $request->to_created_at ?? ''; // 作成日 以下

        $search_from_updated_at = $request->from_updated_at ?? ''; // 更新日 以上
        $search_to_updated_at = $request->to_updated_at ?? ''; // 更新日 以下

        $orderby = $request->orderby ?? ''; // 並び替え


        // 配送営業所一覧
        $office_list_object = DeliveryOffice::select()
            ->with(['joinCompany', 'joinAddr1'])
            ->withCount(['joinTask'])
            ->withTrashed();

        // キーワードで検索
        if (isset($request->keyword) && $request->keyword != '') {

            $office_list_object->Where(function ($query) use ($keyword) {
                $query
                    ->orWhere('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('manager_name_sei', 'LIKE', "%{$keyword}%")
                    ->orWhere('manager_name_mei', 'LIKE', "%{$keyword}%")
                    ->orWhere('manager_name_sei_kana', 'LIKE', "%{$keyword}%")
                    ->orWhere('manager_name_mei_kana', 'LIKE', "%{$keyword}%")
                    ->orWhere('email', 'LIKE', "%{$keyword}%");
            });
        }

        // 都道府県
        if (isset($request->addr1_id)) {
            $office_list_object->where([['addr1_id', $search_addr1]]);
        }


        // 稼働数 範囲 絞り込み
        if (isset($request->from_task_count)) {
            $office_list_object->having('join_task_count', '>=', $search_from_task_count);
        }
        if (isset($request->to_task_count)) {
            $office_list_object->having('join_task_count', '<=', $search_to_task_count);
        }

        // 作成日 範囲 絞り込み
        if (isset($request->from_created_at)) {
            $office_list_object->where('created_at', '>=', $search_from_created_at);
        }
        if (isset($request->to_created_at)) {
            $office_list_object->where('created_at', '<=', $search_to_created_at);
        }

        // 更新日 範囲 絞り込み
        if (isset($request->from_updated_at)) {
            $office_list_object->where('updated_at', '>=', $search_from_updated_at);
        }
        if (isset($request->to_updated_at)) {
            $office_list_object->where('updated_at', '<=', $search_to_updated_at);
        }

        /* 並び替え */
        if ($orderby === 'id_desc') {
            // ID desc
            $office_list_object->orderBy('id', 'desc');
        } elseif ($orderby === 'id_asc') {
            // ID asc
            $office_list_object->orderBy('id', 'asc');
        } elseif ($orderby === 'join_task_count_desc') {
            // 稼働数 desc
            $office_list_object->orderBy('join_task_count', 'desc');
        } elseif ($orderby === 'join_task_count_asc') {
            // 稼働数 asc
            $office_list_object->orderBy('join_task_count', 'asc');
        } else {
            // ID desc
            $office_list_object->orderBy('id', 'desc');
        }


        $office_list = $office_list_object->paginate(50)->withQueryString();
        // $office_list = $office_list_object->get();
        // logger($office_list->toArray());
        // exit;

        /* フォームに使うデータ */
        // 都道府県取得
        $prefecture_list = Prefecture::select()->get();

        $orderby_list = [
            ['value' => 'id_desc', 'text' => 'ID大きい順',],
            ['value' => 'id_asc', 'text' => 'ID小さい順',],
            ['value' => 'join_task_count_desc', 'text' => '稼働数が多い順',],
            ['value' => 'join_task_count_asc', 'text' => '稼働数が少ない順',],
        ];

        return view('admin.delivery_office.index', [
            'office_list' => $office_list,
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
        $charge_user_type_list =  DeliveryOfficeChargeUserType::select()->get(); // 請求に関するユーザの種類

        return view('admin.delivery_office.create', [
            'prefecture_list' => $prefecture_list,
            'company_list' => $company_list,
            'charge_user_type_list' => $charge_user_type_list,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeliveryOfficeCreateRequest $request)
    {
        $name = $request->name ?? '';
        $manager_name_sei = $request->manager_name_sei ?? '';
        $manager_name_mei = $request->manager_name_mei ?? '';
        $manager_name_sei_kana = $request->manager_name_sei_kana ?? '';
        $manager_name_mei_kana = $request->manager_name_mei_kana ?? '';
        $email = $request->email ?? '';
        $password = $request->password ?? '';
        $delivery_company_id = in_array($request->delivery_company_id, [NULL, 'None'])  ? NULL :  $request->delivery_company_id;
        $delivery_company_name = $request->delivery_company_name ?? '';
        $post_code1 = $request->post_code1 ?? '';
        $post_code2 = $request->post_code2 ?? '';
        $addr1_id = $request->addr1_id ?? '';
        $addr2 = $request->addr2 ?? '';
        $addr3 = $request->addr3 ?? '';
        $addr4 = $request->addr4 ?? '';
        $manager_tel = $request->manager_tel ?? '';
        $charge_user_type_id = $request->charge_user_type_id ?? ''; // 請求に関するユーザの種類

        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報

        // 配送会社IDがnullなら請負(2)
        $delivery_office_type_id = isset($delivery_company_id) ? 1 : 2;

        // 配送会社IDが入力されていたら、会社名は空
        if ($delivery_company_id) {
            $delivery_company_name = '';
        }

        // logger($request);
        // exit;

        $office_create = DeliveryOffice::create([
            'user_type_id' => 2,
            'name' => $name,
            'manager_name_sei' => $manager_name_sei,
            'manager_name_mei' => $manager_name_mei,
            'manager_name_sei_kana' => $manager_name_sei_kana,
            'manager_name_mei_kana' => $manager_name_mei_kana,
            'email' => $email,
            'password' => Hash::make($password),
            'delivery_company_id' => $delivery_company_id,
            'delivery_company_name' => $delivery_company_name,
            'delivery_office_type_id' => isset($delivery_company_id) ? 1 : 2, // 配送会社がnullなら請負(2)
            'post_code1' => $post_code1,
            'post_code2' => $post_code2,
            'addr1_id' => $addr1_id,
            'addr2' => $addr2,
            'addr3' => $addr3,
            'addr4' => $addr4,
            'manager_tel' => $manager_tel,
            'charge_user_type_id' => $charge_user_type_id
        ]);

        $msg = '';

        if ($office_create) {
            $msg = "営業所を作成しました。";

            $config_base = WebConfigBase::where('id', 1)->first();
            $config_system = WebConfigSystem::where('id', 1)->first();
            $office = DeliveryOffice::where('id', $office_create->id)->first();
            $office->init_password = $password;

            // メールで利用するデータ
            $data_mail = [
                "config_base" => $config_base,
                "config_system" => $config_system,
                'office' => $office,
            ];

            $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
            $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
            $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
            $file_path = __FILE__; // ファイルパス

            /* ドライバーへのメール */
            // 送り先
            $to_office = [
                [
                    'email' => $office->email,
                    'name' => $office->full_name,
                ],
            ];

            $msg_mail = ''; // メール可否メッセージ
            try {
                Mail::to($to_office)->send(new DeliveryOfficeRegisterSendDeliveryOfficeMail($data_mail)); // 送信
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
                    'to_user_id' => $office->id,
                    'to_user_type_id' => $office->user_type_id,
                    'to_user_info' => ($office->joinUserType->name ?? '') . " / email:" . ($office->email ?? ''),
                    'user_id' => $login_id,
                    'user_type_id' => $login_user->user_type_id ?? 4,
                    'user_info' => $login_user->joinUserType->name ?? '',
                    'text' => " 営業所登録",
                    'remote_addr' => $remote_addr,
                    'http_user_agent' => $http_user_agent,
                    'url' => $url,
                ]);
            }
        } else {
            $msg = "営業所を作成できませんでした!";
        }



        return redirect()->route("admin.delivery_office.index")->with([
            'msg' => $msg
        ]);
        return view('admin.delivery_office.store');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $office_id
     * @return \Illuminate\Http\Response
     */
    public function show($office_id)
    {
        // 配送営業所 取得
        $office = DeliveryOffice::select()
            ->with(['joinCompany', 'joinAddr1'])
            ->where('id', $office_id)
            ->withTrashed()
            ->first();
        // logger($office->toArray());
        // exit;

        /**
         * Stripe 顧客時情報取得
         */
        $payment_method_list = '';
        if ($office->hasStripeId()) {
            try {
                $stripe_user =  $office->asStripeCustomer();
                $office->stripe_user = json_encode($stripe_user, JSON_UNESCAPED_UNICODE);

                $invoice_list = $office->invoicesIncludingPending();
                $office->stripe_invoice_list = $invoice_list;

                $payment_method_list = json_encode($office->paymentMethods());
              } catch (\Stripe\Exception\InvalidRequestException $e) {
                $office->stripe_user =  "顧客情報取得できません。 {$e}";
                Log::error($e);
              }
        }


        // 配送営業所に登録しているドライバー一覧 取得
        $driver_list = DriverRegisterDeliveryOffice::select('drivers.*', 'prefectures.id as prefectures_id', 'prefectures.name as prefectures_name')
            ->leftJoin('drivers', 'driver_register_delivery_offices.driver_id', '=', 'drivers.id')
            ->leftJoin('prefectures', 'drivers.addr1_id', '=', 'prefectures.id')
            ->where('delivery_office_id', $office_id)
            ->get()
            ->toArray();
        // logger($driver_list);
        // exit;

        $driver_list = array_map(function ($item) {

            $item['avg_score'] = DriverTaskReview::where('driver_id', $item['id'])
                ->avg('score');
            $item['count_task'] = DriverTask::where('driver_id', $item['id'])
                ->count();

            return $item;
        }, $driver_list);

        // 配列をオブジェクトに変換
        $driver_list = json_decode(json_encode($driver_list));
        // logger($driver_list);
        // exit;


        // 回答したレビュー
        $review_list = DriverTaskReview::select()
            ->with(['joinOffice', 'joinTask', 'joinDriver', 'joinPublicStatus'])
            ->where('delivery_office_id', $office_id)
            ->get();
        // logger($review_list->toArray());
        // exit;

        return view('admin.delivery_office.show', [
            'office' => $office,
            'driver_list' => $driver_list,
            'review_list' => $review_list,
            'payment_method_list' => $payment_method_list,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $office_id
     * @return \Illuminate\Http\Response
     */
    public function edit($office_id)
    {
        $office = DeliveryOffice::select()
            ->where('id', $office_id)
            ->withTrashed()
            ->first();
        // logger($office->toArray());
        // exit;

        /* フォームに使うデータ */
        $prefecture_list = Prefecture::select()->get(); // 都道府県取得
        $company_list = DeliveryCompany::select()->get(); // 配送会社一覧
        $charge_user_type_list =  DeliveryOfficeChargeUserType::select()->get(); // 請求に関するユーザの種類

        return view('admin.delivery_office.edit', [
            'office' => $office,
            'prefecture_list' => $prefecture_list,
            'company_list' => $company_list,
            'charge_user_type_list' => $charge_user_type_list,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $office_id
     * @return \Illuminate\Http\Response
     */
    public function update(DeliveryOfficeUpdateRequest $request, $office_id)
    {
        $name = $request->name ?? '';
        $manager_name_sei = $request->manager_name_sei ?? '';
        $manager_name_mei = $request->manager_name_mei ?? '';
        $manager_name_sei_kana = $request->manager_name_sei_kana ?? '';
        $manager_name_mei_kana = $request->manager_name_mei_kana ?? '';
        $email = $request->email ?? '';
        $password = $request->password ?? '';
        $delivery_company_id = in_array($request->delivery_company_id, [NULL, 'None'])  ? NULL :  $request->delivery_company_id;
        $delivery_company_name = $request->delivery_company_name ?? '';
        $post_code1 = $request->post_code1 ?? '';
        $post_code2 = $request->post_code2 ?? '';
        $addr1_id = $request->addr1_id ?? '';
        $addr2 = $request->addr2 ?? '';
        $addr3 = $request->addr3 ?? '';
        $addr4 = $request->addr4 ?? '';
        $manager_tel = $request->manager_tel ?? '';
        $charge_user_type_id = $request->charge_user_type_id ?? '';

        // Unsubscribe
        $unsubscribe = $request->action === 'unsubscribe';
        $remove = $request->action === 'remove';

        if ($unsubscribe) {
            $msg = '';

            DB::beginTransaction();
            try {
                DriverTask::where('delivery_office_id', $office_id)->forceDelete();
                DeliveryPickupAddr::where('delivery_office_id', $office_id)->forceDelete();
                DeliveryOffice::where('id', $office_id)->forceDelete();

                DB::commit();
                $msg = "退会処理が完了しました。";
            } catch (\Throwable $e) {
                DB::rollback();

                $msg = "退会処理に失敗しました。";
            }

            return redirect()->route("admin.delivery_office.unsubscribe")->with([
                'msg' => $msg
            ]);
        }

        if ($remove) {
            $msg = '';

            DB::beginTransaction();
            try {
                DeliveryOffice::withTrashed()->where('id', $office_id)->update(['deleted_at' => null]);

                DB::commit();
                $msg = "復元処理が完了しました。";
            } catch (\Throwable $e) {
                DB::rollback();

                $msg = "復元処理に失敗しました。";
            }

            return redirect()->route("admin.delivery_office.index")->with([
                'msg' => $msg
            ]);
        }

        // 配送会社がnullなら請負(2)
        $delivery_office_type_id = isset($delivery_company_id) ? 1 : 2;

        // 配送会社IDが入力されていたら、会社名は空
        if ($delivery_company_id) {
            $delivery_company_name = '';
        }

        /* 更新処理 */
        $office = DeliveryOffice::where('id', '=', $office_id)->first();

        $office->name = $name;
        $office->manager_name_sei = $manager_name_sei;
        $office->manager_name_mei = $manager_name_mei;
        $office->manager_name_sei_kana = $manager_name_sei_kana;
        $office->manager_name_mei_kana = $manager_name_mei_kana;
        $office->email = $email;

        // パスワードは入力されている場合のみ変更
        if ($password) {
            $office->password = Hash::make($password);
        }
        $office->delivery_company_id = $delivery_company_id;
        $office->delivery_company_name = $delivery_company_name;

        // 配送会社がnullなら請負(2)
        $office->delivery_office_type_id = isset($delivery_company_id) ? 1 : 2;
        $office->post_code1 = $post_code1;
        $office->post_code2 = $post_code2;
        $office->addr1_id = $addr1_id;
        $office->addr2 = $addr2;
        $office->addr3 = $addr3;
        $office->addr4 = $addr4;
        $office->manager_tel = $manager_tel;
        $office->charge_user_type_id = $charge_user_type_id;

        $office_update = $office->save();

        $msg = '';
        if ($office_update) {
            $msg = "依頼者を更新しました。";
        } else {
            $msg = "依頼者を更新できませんでした!";
        }

        return redirect()->route("admin.delivery_office.show", [
            'office_id' => $office_id
        ])->with([
            'msg' => $msg
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $office_id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $office_id)
    {
        $type = $request->type; //削除タイプ

        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $msg = ''; // 削除メッセージ

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        try {
            if ($type === 'soft') {
                $result = DeliveryOffice::where('id', '=', $office_id)->delete($office_id);
                if ($result) {
                    $msg = 'ソフト削除に成功';
                } else {
                    $msg = '削除されませんでした。';
                }
            } elseif ($type === 'force') {
                $result = DeliveryOffice::where('id', '=', $office_id)->forceDelete($office_id);
                if ($result) {
                    $msg = '完全削除に成功';
                } else {
                    $msg = '削除されませんでした。';
                }
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

        return redirect()->route('admin.delivery_office.index')->with([
            'msg' => $msg,
        ]);
        // return view('admin.delivery_office.destroy');
    }

    /**
     * ソフト削除したアカウント復元
     */
    public function restoreDelete($office_id)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $msg = ''; // 削除メッセージ

        // 復元
        $restore_delete = DeliveryOffice::where('id', $office_id)
            ->restore();

        if ($restore_delete) {
            $msg = 'ソフト削除から復元しました。';
        } else {
            $msg = 'ソフト削除から復元に失敗しました。';
        }

        return redirect()->route("admin.delivery_office.show", [
            'office_id' => $office_id
        ])->with([
            'msg' => $msg
        ]);
    }

    public function unsubscribe()
    {
        $office_list_unsubscribe = DeliveryOffice::onlyTrashed()->get();

        return view('admin.delivery_office.unsubscribe', [
            'office_list_unsubscribe' => $office_list_unsubscribe,
        ]);
    }
}
