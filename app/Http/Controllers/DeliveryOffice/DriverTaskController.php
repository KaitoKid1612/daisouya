<?php

namespace App\Http\Controllers\DeliveryOffice;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;

use Illuminate\Http\Request;
use App\Http\Requests\DeliveryOffice\DriverTaskCreateRequest;
use App\Http\Requests\DeliveryOffice\DriverTaskUpdateRequest;
use App\Http\Requests\DeliveryOffice\DriverTaskCalcPriceRequest;

use Illuminate\Support\Facades\Mail;
use App\Mail\DriverTaskStoreSendDeliveryOfficeMail;
use App\Mail\DriverTaskStoreSendDriverMail;
use App\Mail\DriverTaskStoreSendAdminMail;
use App\Mail\DriverTaskUpdateSendDriverMail;
use App\Mail\DriverTaskUpdateSendDeliveryOfficeMail;
use App\Mail\DriverTaskUpdateSendAdminMail;

use Illuminate\Support\Facades\DB;
use App\Models\DeliveryCompany;
use App\Models\Driver;
use App\Models\DeliveryOffice;
use App\Models\DeliveryPickupAddr;
use App\Models\DriverTaskReview;
use App\Models\DeliveryOfficeTaskReview;
use App\Models\DriverTask;
use App\Models\DriverTaskPlan;
use App\Models\DriverTaskStatus;
use App\Models\Prefecture;
use App\Models\WebConfigBase;
use App\Models\WebConfigSystem;
use App\Models\WebNoticeLog;
use App\Models\WebPaymentLog;
use App\Models\FcmDeviceTokenDeliveryOffice;
use App\Models\FcmDeviceTokenDriver;

use DateInterval;
use DatePeriod;

use App\Jobs\Mail\DriverTaskStoreSendDeliveryOfficeMailJob;
use App\Jobs\Mail\DriverTaskStoreSendDriverMailJob;
use App\Jobs\Mail\DriverTaskStoreSendAdminMailJob;
use App\Jobs\Mail\DriverTaskUpdateSendDeliveryOfficeMailJob;
use App\Jobs\Mail\DriverTaskUpdateSendDriverMailJob;
use App\Jobs\Mail\DriverTaskUpdateSendAdminMailJob;

use App\Jobs\Push\PushNotificationJob;

use App\Libs\DeliveryOffice\DriverTaskPermission;
use App\Libs\DeliveryOffice\DriverTaskUI;
use App\Libs\Price\DriverTaskPriceSupport;

/**
 * 稼働依頼
 */
class DriverTaskController extends Controller
{
    /**
     * 稼働依頼一覧
     * +API機能
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報


        // logger($login_id);
        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }


        $keyword = $request->keyword ?? ''; // 検索ワード
        $search_from_task_date = $request->from_task_date ?? ''; //タスク日付 以上
        $search_to_task_date = $request->to_task_date ?? ''; //タスク日付 以下
        $search_task_status_id_list = $request->task_status_id && is_array($request->task_status_id) ? $request->task_status_id  : []; // タスクステータス
        $search_from_created_at = $request->from_created_at ?? ''; // 作成日 以上
        $search_to_created_at = $request->to_created_at ?? ''; // 作成日 以下
        $search_from_updated_at = $request->from_updated_at ?? ''; // 更新日 以上
        $search_to_updated_at = $request->to_updated_at ?? ''; // 更新日 以下
        $orderby = $request->orderby ?? ''; // 並び替え

        // 結合先の取得カラム
        $join_driver_column_list = [
            "id",
            'user_type_id',
            "name_sei",
            "name_mei",
            "name_sei_kana",
            "name_mei_kana",
            "gender_id",
            "birthday",
            "addr1_id",
            "addr2",
            "icon_img",
            "career",
            "introduction",
        ];
        $join_driver_column = "joinDriver:" . implode(',', $join_driver_column_list);

        $join_office_column_list = [
            "id",
            "user_type_id",
            "name",
            "manager_name_sei",
            "manager_name_mei",
            "manager_name_sei_kana",
            "manager_name_mei_kana",
            "delivery_company_id",
            "delivery_company_name",
            "delivery_office_type_id",
            "charge_user_type_id",
        ];
        $join_office_column = "joinOffice:" . implode(',', $join_office_column_list);

        $join_driver_task_plan_column_list = [
            'id',
            'name',
            'label'
        ];
        $join_driver_task_plan_column = "joinDriverTaskPlan:" . implode(',', $join_driver_task_plan_column_list);

        // ドライバーの稼働一覧 取得
        $task_list_object = DriverTask::select()
            ->with([
                $join_driver_column,
                $join_office_column,
                'joinTaskStatus',
                'joinDriverReview',
                'joinDeliveryOfficeReview',
                'joinTaskPaymentStatus',
                'joinTaskRefundStatus',
                'joinAddr1',
                'joinDriverTaskPlan',
                $join_driver_task_plan_column,
            ])

            ->where([
                ['delivery_office_id', '=', $login_id], //ログインしている営業所ユーザが管理しているデータのみ
            ]);

        // キーワードで検索
        if (isset($request->keyword) && $request->keyword != '') {
            $task_list_object->orWhere(function ($query) use ($keyword) {
                $query
                    ->orWhere('task_memo', 'LIKE', "%{$keyword}%");
            });
        }

        // if (isset($request->task_status_id)) {
        //     $task_list_object->where('driver_task_status_id', '=', $search_task_status_id);
        // }

        // 指定したステータスで絞り込み
        if ($search_task_status_id_list) {
            $task_list_object->where(function ($query) use ($search_task_status_id_list) {
                foreach ($search_task_status_id_list as $status_id) {
                    if ($status_id) {
                        $query->orWhere('driver_task_status_id', $status_id);
                    }
                }
            });
        }

        // 稼働日 範囲 絞り込み
        if (isset($request->from_task_date)) {
            $task_list_object->where('task_date', '>=', $search_from_task_date);
        }
        if (isset($request->to_task_date)) {
            $task_list_object->where('task_date', '<=', $search_to_task_date);
        }

        // 作成日 範囲 絞り込み
        if (isset($request->from_created_at)) {
            $task_list_object->where('created_at', '>=', $search_from_created_at);
        }
        if (isset($request->to_created_at)) {
            $task_list_object->where('created_at', '<=', $search_to_created_at);
        }

        // 更新日 範囲 絞り込み
        if (isset($request->from_updated_at)) {
            $task_list_object->where('updated_at', '>=', $search_from_updated_at);
        }
        if (isset($request->to_updated_at)) {
            $task_list_object->where('updated_at', '<=', $search_to_updated_at);
        }

        /* 並び替え */
        if ($orderby === 'id_desc') {
            $task_list_object->orderBy('id', 'desc');
        } elseif ($orderby === 'id_asc') {
            $task_list_object->orderBy('id', 'asc');
        } elseif ($orderby === 'task_date_desc') {
            $task_list_object->orderBy('task_date', 'asc');
        } elseif ($orderby === 'task_date_asc') {
            $task_list_object->orderBy('task_date', 'desc');
        } elseif ($orderby === 'created_at_desc') {
            $task_list_object->orderBy('created_at', 'desc');
        } elseif ($orderby === 'updated_at_asc') {
            $task_list_object->orderBy('updated_at', 'asc');
        } else {
            $task_list_object->orderBy('id', 'desc');
        }

        if (true) {
            $task_list_object->where('is_template', false);
        }

        $task_list = $task_list_object->paginate(24)->withQueryString();

        // 稼働依頼で行える許可権限
        $driver_task_permission = new DriverTaskPermission();
        // 稼働依頼のUIの操作
        $driver_task_ui = new DriverTaskUI;
        $task_list->each(function ($item) use ($driver_task_permission, $driver_task_ui) {
            $driver_task_permission_result = $driver_task_permission->get($item->id);
            $item->driver_task_permission = $driver_task_permission_result;

            $driver_task_ui_result = $driver_task_ui->get($item->id);
            $item->driver_task_ui = $driver_task_ui_result;
        });
        // $task_list = $task_list_object->get();
        // logger($task_list->toArray());
        // exit;

        /* フォーム検索に使うデータ */
        $task_status_list = DriverTaskStatus::get(); // 稼働ステータス一覧
        // logger($task_status_list->toArray());
        // exit;

        // 並び順
        $orderby_list = [
            ['value' => '', 'text' => '指定なし'],
            // ['value' => 'id_desc', 'text' => 'ID大きい順'],
            // ['value' => 'id_asc', 'text' => 'ID小さい順'],
            // ['value' => 'task_date_desc', 'text' => '稼働日 昇順'],
            ['value' => 'task_date_asc', 'text' => '稼働日順'],
            // ['value' => 'created_at_desc', 'text' => '作成日 昇順'],
            ['value' => 'created_at_asc', 'text' => '作成日順'],
            // ['value' => 'updated_at_desc', 'text' => '更新日 昇順'],
            // ['value' => 'updated_at_asc', 'text' => '更新日 降順'],
        ];


        $api_status = true;
        if ($task_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'orderby_list' => $orderby_list,
                'data' => $task_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('delivery_office.driver_task.index', [
                'task_list' => $task_list,
                'orderby_list' => $orderby_list,
                'task_status_list' => $task_status_list,
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        // If exist template id => edit mode
        $template_id = $request->template;

        if ($template_id) {
            $template = DriverTask::select()->where('id', $template_id)->where('is_template', true)->where('delivery_office_id', $login_id)->first();

            if (!$template) {
                return redirect()->route('delivery_office.driver_task_template.index')->with([
                    'msg' => 'エラーが発生しました',
                ]);
            }
        }

        $driver_id = $request->driver_id ?? '';
        // logger($driver_id);

        $office = DeliveryOffice::select()->where('id', $login_id)->first();

        $driver = Driver::select()->where('id', $driver_id)->first();

        $config_system = WebConfigSystem::select()->where('id', 1)->first();
        $create_task_time_limit_from = $config_system->create_task_time_limit_from;
        $create_task_time_limit_to = $config_system->create_task_time_limit_to;
        $create_task_hour_limit = $config_system->create_task_hour_limit;


        // 稼働日の 登録可能範囲
        $today_datetime =  new \DateTime(); // 本日
        $from_datetime =  clone $today_datetime; // 何日後から
        $to_datetime =  clone $today_datetime; // 何日まで
        $hour_limit = 0; // 何時まで登録できるか

        // 9時まで登録可能にする場合、9時以降は +15をして、翌日以降から登録できるようにする。
        // 24 - 時間
        if ($create_task_hour_limit < 24) {
            $hour_limit = 24 - $create_task_hour_limit;
        }


        // 登録できる範囲 指定日 + $hour_limit 時間後 から
        $from_datetime->modify("+{$create_task_time_limit_from} day");
        $from_datetime->modify("+{$hour_limit} hour");

        // 登録できる範囲 指定日 + $hour_limit 時間後 まで
        $to_datetime->modify("+{$create_task_time_limit_to} day");
        $to_datetime->modify("+{$hour_limit} hour");


        // 何日 ~ 何日 のDateTimeを取得
        $interval = new DateInterval('P1D');
        $date_period = new DatePeriod($from_datetime, $interval, $to_datetime);

        $date_list = [];
        $week_list = ['日', '月', '火', '水', '木', '金', '土'];
        $i = 0;
        foreach ($date_period as $date) {
            $date_list[$i]['value'] = $date->format("Y-m-d");
            $date_list[$i]['week'] = $week_list[$date->format("w")];
            $i++;
        }

        // logger(print_r($date_list,true));

        /* フォーム検索に使うデータ */
        $prefecture_list = Prefecture::select()->get(); // 都道府県取得
        $company_list = DeliveryCompany::select()->get(); // 配送会社一覧
        $pickup_addr_list = DeliveryPickupAddr::select()->where([ // 登録集荷先一覧
            ['delivery_office_id', $login_id]
        ])->get();
        $company_list = DeliveryCompany::get(); // 配送会社一覧
        $driver_task_plan_list = DriverTaskPlan::select()->get(); // 稼働依頼プラン一覧

        try {
            $payment_method_list = $login_user->paymentMethods(); // 支払い方法一覧
        } catch (\Throwable $e) {
            $payment_method_list = [];
            log::error($e);
        }


        // 無料ユーザーの場合はクレカ登録を強制しない。
        if ($login_user->charge_user_type_id !== 2) {
            /* クレジットカードを登録してなければ、クレカ登録画面にリダイレクト */
            if (count($payment_method_list) == 0) {
                return redirect()->route('delivery_office.payment_config.index')->with([
                    'msg' => 'クレジットカードを登録してください。',
                ]);
            }
        }
        // logger($payment_method_list);

        return view('delivery_office.driver_task.create', [
            'driver' => $driver,
            'office' => $office,
            'date_list' => $date_list,
            'prefecture_list' => $prefecture_list,
            'company_list' => $company_list,
            'pickup_addr_list' => $pickup_addr_list,
            'payment_method_list' => $payment_method_list,
            'driver_task_plan_list' => $driver_task_plan_list,
            'template' => $template ?? null,
        ]);
    }

    /**
     * 稼働依頼の登録
     * +API機能
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DriverTaskCreateRequest $request, $action = '', $id = null)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $is_template = $action === 'create-template';

        // If exist id => update template mode
        $is_update_template = $id !== null;

        $api_status = true;

        $task_date = $request->task_date ?? '';
        $driver_task_plan_id =  $request->driver_task_plan_id ?? null;
        $driver_id = $request->driver_id ?? null;
        $rough_quantity = $request->rough_quantity;
        $delivery_route = $request->delivery_route ?? '';
        $task_memo = $request->task_memo ?? '';
        $task_delivery_company_id = in_array($request->task_delivery_company_id, [NULL, 'None'])  ? NULL :  $request->task_delivery_company_id;
        $task_delivery_company_name = $request->task_delivery_company_name ?? '';
        $task_delivery_office_name = $request->task_delivery_office_name ?? '';
        $task_email = $request->task_email ?? '';
        $task_tel = $request->task_tel ?? '';
        $task_post_code1 = $request->task_post_code1 ?? '';
        $task_post_code2 = $request->task_post_code2 ?? '';
        $task_addr1_id = $request->task_addr1_id ?? 1;
        $task_addr2 = $request->task_addr2 ?? '';
        $task_addr3 = $request->task_addr3 ?? '';
        $task_addr4 = $request->task_addr4 ?? '';
        $freight_cost = $request->freight_cost ?? 0;
        $pickup_addr_id = $request->pickup_addr_id ?? ''; // 集荷先ID
        $is_create_pickup_addr = $request->is_create_pickup_addr ?? ''; // 集荷先として保存するかフラグ
        $stripe_payment_method_id = $request->payment_method_id ?? ''; // stripe支払いメソッドID

        $request_system_price = $request->system_price;
        $request_busy_system_price = $request->busy_system_price;
        $request_emergency_price = $request->emergency_price;
        $request_tax = $request->tax;
        $request_total_price = $request->total_price;

        $config_system = WebConfigSystem::select()->where('id', 1)->first();

        /* 料金 */
        // $system_price = $config_system->default_price; // 価格
        $tax_rate = $config_system->default_tax_rate; // 税率
        $payment_fee_rate = $config_system->default_stripe_payment_fee_rate; // 決済手数料
        // $emergency_price = 0; // 緊急依頼料金

        /* 価格計算 */
        $driver_task_price_support = new DriverTaskPriceSupport();
        $plan_price_data = $driver_task_price_support->getPrice($driver_task_plan_id, $freight_cost, $task_date, $request_system_price);

        $total_including_tax = $plan_price_data['total_including_tax'];
        $total_excluding_tax = $plan_price_data['total_excluding_tax'];
        $tax = $plan_price_data['tax'];
        $system_price = $plan_price_data['system_price'];
        $busy_system_price = $plan_price_data['busy_system_price'];
        $freight_cost = $plan_price_data['freight_cost'];
        $emergency_price = $plan_price_data['emergency_price'];

        /**
         * 申し込む前に計算した料金と申し込み後に計算した料金が違う場合は、登録画面にリダイレクト
         */
        if (
            $request_system_price != $system_price || $request_busy_system_price != $busy_system_price ||
            $request_tax != $tax ||
            $request_emergency_price != $emergency_price ||
            $request_total_price != $total_including_tax
        ) {
            $msg = "申し訳ございません。料金が切り替わりましたので、登録に失敗しました。もう一度を申し込んでください。";
            $api_status = false;
            if (Route::is('api.*')) {
                return response()->json([
                    'status' => $api_status,
                    "msg" => $msg
                ], 422, [], JSON_UNESCAPED_UNICODE);
            } else {
                return redirect()->route('delivery_office.driver_task.create')->with([
                    'msg' => $msg,
                ]);
            }
        }

        // 割引額
        // 無料ユーザーは全額無料にする
        $discount = 0;
        if ($login_user->charge_user_type_id == 2) {
            $discount = $total_excluding_tax;
            $tax = 0;
        }

        // ドライバーを指名した場合
        if ($driver_id) {
            $driver_task_status_id = 2; // 新規(指名)
        } else {
            $driver_task_status_id = 1; //新規
        }

        // 配送会社IDが入力されていたら、会社名は配送会社テーブルから取得
        if ($task_delivery_company_id) {
            $delivery_conpany = DeliveryCompany::select()->where('id', $task_delivery_company_id)->first();
            $task_delivery_company_name = $delivery_conpany->name ?? '';
        }

        // 登録済みの集荷先を利用した場合
        if ($pickup_addr_id && is_numeric($pickup_addr_id)) {
            $pickup_addr = DeliveryPickupAddr::select()
                ->where([
                    ['id', $pickup_addr_id],
                    ['delivery_office_id', $login_id]
                ])
                ->first();

            $task_delivery_company_name = $pickup_addr->delivery_company_name;
            $task_delivery_office_name = $pickup_addr->delivery_office_name;
            $task_email = $pickup_addr->email;
            $task_tel = $pickup_addr->tel;
            $task_post_code1 = $pickup_addr->post_code1;
            $task_post_code2 = $pickup_addr->post_code2;
            $task_addr1_id = $pickup_addr->addr1_id;
            $task_addr2 = $pickup_addr->addr2;
            $task_addr3 = $pickup_addr->addr3;
            $task_addr4 = $pickup_addr->addr4;
            $pickup_addr_id = $pickup_addr_id;
        } else {

            // 集荷先保存
            if ($is_create_pickup_addr && !$is_update_template) {
                DeliveryPickupAddr::create([
                    'delivery_office_id' => $login_id,
                    'delivery_company_name' => $task_delivery_company_name,
                    'delivery_office_name' => $task_delivery_office_name,
                    'email' => $task_email,
                    'tel' => $task_tel,
                    'post_code1' => $task_post_code1,
                    'post_code2' => $task_post_code2,
                    'addr1_id' => $task_addr1_id,
                    'addr2' => $task_addr2,
                    'addr3' => $task_addr3,
                    'addr4' => $task_addr4,
                ]);
            }
        }

        // Update template
        if ($is_template && $is_update_template) {
            $template_update = DriverTask::where('id', $id)->update([
                'is_template' => $is_template ? 1 : 0,
                'task_date' => $task_date ?? '',
                'request_date' => new \DateTime(),
                'driver_id' => $driver_id ?? null,
                'delivery_office_id' => $login_id ?? '',
                'driver_task_status_id' => $driver_task_status_id ?? '',
                'driver_task_plan_id' => $driver_task_plan_id ?? null,
                'rough_quantity' => $rough_quantity ?? '',
                'delivery_route' => $delivery_route ?? '',
                'task_memo' => $task_memo ?? '',
                'pickup_addr_id' => $pickup_addr_id === 'is_new' ? 0 : $pickup_addr_id,
                'task_delivery_company_name' => $task_delivery_company_name ?? '',
                'task_delivery_office_name' => $task_delivery_office_name ?? '',
                'task_email' => $task_email ?? '',
                'task_tel' => $task_tel ?? '',
                'task_post_code1' => $task_post_code1 ?? '',
                'task_post_code2' => $task_post_code2 ?? '',
                'task_addr1_id' => $task_addr1_id ?? '',
                'task_addr2' => $task_addr2 ?? '',
                'task_addr3' => $task_addr3 ?? '',
                'task_addr4' => $task_addr4 ?? '',
                'system_price' => $system_price ?? 0,
                'busy_system_price' => $busy_system_price ?? 0,
                'freight_cost' => $freight_cost ?? 0,
                'emergency_price' => $emergency_price ?? 0,
                'discount' => $discount ?? 0,
                'tax' => $tax,
                'tax_rate' => $tax_rate,
                'refund_amount' => 0,
                'payment_fee_rate' => $payment_fee_rate,
                'stripe_payment_method_id' => $stripe_payment_method_id ?? '', // Stripe支払い方法ID
                'stripe_payment_intent_id' => '', // Stripe支払いインテントID
                'stripe_payment_refund_id' => '', // Stripe返金ID
                'driver_task_payment_status_id' => 1, // 未払い
                'driver_task_refund_status_id' => 1, // 返金なし
            ]);

            if ($template_update) {
                $api_status = false;
                $msg = '';

                if ($template_update) {
                    $api_status = true;
                    $msg = 'テーマ作成に成功しました。';
                } else {
                    $api_status = false;
                    $msg = 'テーマ作成に失敗しました。';
                }

                if (Route::is('api.*')) {
                    return response()->json([
                        'status' => $api_status,
                        "message" => $msg,
                        "data" => $template_update
                    ], 200, [], JSON_UNESCAPED_UNICODE);
                } else {
                    return redirect()->back()->with([
                        'msg' => $msg,
                    ]);
                }
            }
        }

        // Create template or task driver
        // 稼働の登録
        $task_create = DriverTask::create([
            'is_template' => $is_template ? 1 : 0,
            'task_date' => $task_date ?? '',
            'request_date' => new \DateTime(),
            'driver_id' => $driver_id ?? null,
            'delivery_office_id' => $login_id ?? '',
            'driver_task_status_id' => $driver_task_status_id ?? '',
            'driver_task_plan_id' => $driver_task_plan_id ?? null,
            'rough_quantity' => $rough_quantity ?? '',
            'delivery_route' => $delivery_route ?? '',
            'task_memo' => $task_memo ?? '',
            'pickup_addr_id' => $pickup_addr_id === 'is_new' ? 0 : $pickup_addr_id,
            'task_delivery_company_name' => $task_delivery_company_name ?? '',
            'task_delivery_office_name' => $task_delivery_office_name ?? '',
            'task_email' => $task_email ?? '',
            'task_tel' => $task_tel ?? '',
            'task_post_code1' => $task_post_code1 ?? '',
            'task_post_code2' => $task_post_code2 ?? '',
            'task_addr1_id' => $task_addr1_id ?? '',
            'task_addr2' => $task_addr2 ?? '',
            'task_addr3' => $task_addr3 ?? '',
            'task_addr4' => $task_addr4 ?? '',
            'system_price' => $system_price ?? 0,
            'busy_system_price' => $busy_system_price ?? 0,
            'freight_cost' => $freight_cost ?? 0,
            'emergency_price' => $emergency_price ?? 0,
            'discount' => $discount ?? 0,
            'tax' => $tax,
            'tax_rate' => $tax_rate,
            'refund_amount' => 0,
            'payment_fee_rate' => $payment_fee_rate,
            'stripe_payment_method_id' => $stripe_payment_method_id ?? '', // Stripe支払い方法ID
            'stripe_payment_intent_id' => '', // Stripe支払いインテントID
            'stripe_payment_refund_id' => '', // Stripe返金ID
            'driver_task_payment_status_id' => 1, // 未払い
            'driver_task_refund_status_id' => 1, // 返金なし
        ]);

        if (!$is_template) {
            if ($task_create) {
                $msg = '稼働依頼の登録をしました。';

                $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
                $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
                $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
                $file_path = __FILE__; // ファイルパス

                $info_list = [
                    "remote_addr" => $remote_addr,
                    "http_user_agent" => $http_user_agent,
                    "url" => $url,
                    "file_path" => $file_path,
                ];

                $task = DriverTask::select()
                    ->with(['joinDriver', 'joinOffice', 'joinTaskStatus'])
                    ->where('id', $task_create->id)
                    ->first();

                $config_base = WebConfigBase::where('id', 1)->first();
                $config_system = WebConfigSystem::where('id', 1)->first();
                $office = DeliveryOffice::where('id', $login_id)->first();
                $driver = Driver::where('id', $driver_id)->first();

                // メールで利用するデータ
                $data_mail = [
                    "config_base" => $config_base,
                    "config_system" => $config_system,
                    'office' => $office,
                    'driver' => $driver,
                    'task' => $task,
                ];

                /* 営業所へのメール */
                // 送り先
                $to_office = [
                    [
                        'email' => $office->email,
                        'name' => "{$office->name}",
                    ],
                ];
                DriverTaskStoreSendDeliveryOfficeMailJob::dispatch($to_office, $data_mail, $login_user, $info_list);


                // ドライバーが指定されている場合にメール送信
                if ($driver) {
                    /* ドライバーへのメール */
                    $to_driver = [
                        [
                            'email' => $driver->email,
                            'name' => "{$driver->name}",
                        ],
                    ];
                    DriverTaskStoreSendDriverMailJob::dispatch($to_driver, $data_mail, $login_user, $info_list);
                }


                /* 管理者へのメール */
                $to_admin = [
                    [
                        'email' => $config_system->email_notice,
                        'name' => "{$config_base->site_name}",
                    ],
                ];
                DriverTaskStoreSendAdminMailJob::dispatch($to_admin, $data_mail, $login_user, $info_list);
            } else {
                $msg = '稼働依頼の登録ができませんでした。';
            }

            /* Push通知 依頼者 */
            $fcm_token_delivery_office_list =  FcmDeviceTokenDeliveryOffice::where("delivery_office_id", $login_id)->get();
            if ($fcm_token_delivery_office_list) {

                foreach ($fcm_token_delivery_office_list as $fcm_token_delivery_office) {
                    $data_push = [
                        'fcm_token' => $fcm_token_delivery_office->token,
                        'title' => $msg,
                        'custom_data' => [],
                    ];

                    $data_other = [
                        'task' => $task,
                    ];

                    PushNotificationJob::dispatch($office, $data_push, $login_user, $info_list, $data_other);
                }
            }

            /* Push通知 ドライバー */
            $fcm_token_driver_list =  FcmDeviceTokenDriver::where("driver_id", $task->driver_id)->get();
            if ($fcm_token_driver_list) {
                foreach ($fcm_token_driver_list as $fcm_token_driver) {
                    $data_push = [
                        'fcm_token' => $fcm_token_driver->token,
                        'title' => $msg,
                    ];

                    $data_other = [
                        'task' => $task,
                    ];

                    PushNotificationJob::dispatch($driver, $data_push, $login_user, $info_list, $data_other);
                }
            }

            if ($task_create) {
                $api_status = true;
            } else {
                $api_status = false;
            }

            if (Route::is('api.*')) {
                return response()->json([
                    'status' => $api_status,
                    "message" => $msg,
                    "data" => $task_create
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return redirect()->route('delivery_office.driver_task.index')->with([
                    'msg' => $msg,
                ]);
            }
        } else {
            $api_status = false;
            $msg = '';

            if ($task_create) {
                $api_status = true;
                $msg = 'テーマ作成に成功しました。';
            } else {
                $api_status = false;
                $msg = 'テーマ作成に失敗しました。';
            }

            if (Route::is('api.*')) {
                return response()->json([
                    'status' => $api_status,
                    "message" => $msg,
                    "data" => $task_create
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return redirect()->route('delivery_office.driver_task_template.index')->with([
                    'msg' => $msg,
                ]);
            }
        }
    }

    /**
     * 取得
     * +API機能
     *
     * @param  int  $task_id
     * @return \Illuminate\Http\Response
     */
    public function show($task_id, Request $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        // 結合先の取得カラム
        $join_driver_column_list = [
            "id",
            'user_type_id',
            "name_sei",
            "name_mei",
            "name_sei_kana",
            "name_mei_kana",
            "gender_id",
            "birthday",
            "addr1_id",
            "addr2",
            "icon_img",
            "career",
            "introduction",
        ];
        $join_driver_column = "joinDriver:" . implode(',', $join_driver_column_list);

        $join_office_column_list = [
            "id",
            'user_type_id',
            "manager_name_sei",
            "manager_name_mei",
            "manager_name_sei_kana",
            "manager_name_mei_kana",
            "delivery_company_id",
            "delivery_company_name",
            "delivery_office_type_id",
            "charge_user_type_id",
        ];
        $join_office_column = "joinOffice:" . implode(',', $join_office_column_list);
        // ドライバーの稼働 取得

        $join_driver_task_plan_column_list = [
            'id',
            'name',
            'label'
        ];
        $join_driver_task_plan_column = "joinDriverTaskPlan:" . implode(',', $join_driver_task_plan_column_list);

        $task = DriverTask::select()
            ->with([
                $join_driver_column,
                $join_office_column,
                'joinTaskStatus',
                'joinDriverReview',
                'joinDeliveryOfficeReview',
                'joinTaskPaymentStatus',
                'joinTaskRefundStatus',
                'joinAddr1',
                'joinOffice',
                'joinDriverTaskPlan',
                $join_driver_task_plan_column,
            ])
            ->where([
                ['id', $task_id],
                ['delivery_office_id', $login_id], // ログインしている営業所ユーザが管理しているデータのみ
            ])
            ->first();
        // logger($task->toArray());

        // ドライバーレビュー取得
        $review = DriverTaskReview::select()
            ->where([
                ['driver_task_id', $task_id],
                ['delivery_office_id', $login_id], // ログインしている営業所ユーザが管理しているデータのみ
                ['driver_task_review_public_status_id', 1] // 公開
            ])->first();
        // logger($review->toArray());

        // 依頼者レビュー取得
        $office_review = DeliveryOfficeTaskReview::select()
            ->where([
                ['driver_task_id', $task_id],
                ['delivery_office_id', $login_id], // ログインしている営業所ユーザが管理しているデータのみ
                ['review_public_status_id', 1] // 公開
            ])->first();
        // logger($office_review);
        // exit;

        // 稼働依頼で行える許可権限
        $driver_task_permission = new DriverTaskPermission();
        $driver_task_permission_result = $driver_task_permission->get($task_id);
        // 稼働依頼のUIの操作
        $driver_task_ui = new DriverTaskUI;
        $driver_task_ui_result = $driver_task_ui->get($task_id);

        if ($task) {
            $task->driver_task_permission = $driver_task_permission_result;

            $task->driver_task_ui = $driver_task_ui_result;
        }

        $api_status = true;
        if ($task) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $task
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('delivery_office.driver_task.show', [
                'task' => $task,
                'review' => $review,
                'office_review' => $office_review,
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $task_id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $task_id)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報
        $type = $request->type ?? '';

        $payment_method_list = []; // クレジットカード一覧
        if ($type === 'payment_method') {
            // 支払い方法一覧
            $payment_method_ary = $login_user->paymentMethods()->toArray();

            /* 期限が切れていないクレジットカード一覧のリストを生成 */
            foreach ($payment_method_ary as $item) {
                $exp_year = $item['card']['exp_year'];
                $exp_month = $item['card']['exp_month'];

                // 本日
                $today_dt = new \DateTime();

                // クレジットカード期限
                $exp_dt = new \DateTime("{$exp_year}-{$exp_month}");
                $exp_dt->modify('last day of');

                if ($exp_dt >= $today_dt) {
                    $payment_method_list[] = $item;
                }
            }

            // 配列をオブジェクトに変換
            $payment_method_list = json_decode(json_encode($payment_method_list));
        }

        $task = DriverTask::select()
            ->where([
                ['id', $task_id],
                ['delivery_office_id', $login_id], // ログインしている営業所ユーザが管理しているデータのみ
                ['driver_task_status_id', '10'],
            ])
            ->first();

        /* フォーム検索に使うデータ */
        $task_status_list = DriverTaskStatus::get(); // 稼働ステータス一覧
        // logger($task_status_list->toArray());
        // exit;

        return view('delivery_office.driver_task.edit', [
            'task' => $task,
            'task_status_list' => $task_status_list,
            'payment_method_list' => $payment_method_list ?? '',

        ]);
    }

    /**
     * 更新
     * +API機能
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $task_id
     * @return \Illuminate\Http\Response
     */
    public function update(DriverTaskUpdateRequest $request, $task_id)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $task_date = $request->task_date ?? '';
        $driver_id = $request->driver_id ?? null;
        $delivery_office_id = $login_id;
        $driver_task_status_id = $request->driver_task_status_id ?? '';
        $rough_quantity = $request->rough_quantity;
        $task_memo = $request->task_memo ?? '';
        $type = $request->type ?? '';
        $stripe_payment_method_id = $request->payment_method_id ?? '';

        $msg = '';

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        $info_list = [
            "remote_addr" => $remote_addr,
            "http_user_agent" => $http_user_agent,
            "url" => $url,
            "file_path" => $file_path,
        ];

        $config_base = WebConfigBase::where('id', 1)->first();
        $config_system = WebConfigSystem::where('id', 1)->first();

        // logger($request);
        // exit;

        /* 完了 */
        if ($type === 'complete') {
            $task_update = DriverTask::where([
                ['id', '=', $task_id],
                ['delivery_office_id', '=', $login_id], // ログインしている営業所ユーザが管理しているデータのみ
                ['driver_task_status_id', '=', 3],
            ])
                ->update([
                    'driver_task_status_id' => 4, // 完了のステータス
                ]);

            if ($task_update) {
                $msg = '稼働依頼が完了しました。';
            }
        }

        /* キャンセル */
        if ($type === 'cancel') {
            $is_refund = false; // 宣言 返金結果

            $datetime =  new \DateTime();
            $datetime_today = $datetime->format('Y-m-d');
            $task_select = DriverTask::where([
                ['id', '=', $task_id],
                ['delivery_office_id', '=', $login_id], // ログインしている営業所ユーザが管理しているデータのみ
                ['task_date', '>', $datetime_today], // 稼働日が未来
            ])->where(function ($query) {
                $query->where('driver_task_status_id', '=', 1)
                    ->orWhere('driver_task_status_id', '=', 2);
            })->first();

            if ($task_select) {
                $task_select->driver_task_status_id = 7; // キャンセル
                $task_update = $task_select->save();

                // 料金支払い済みの場合のみ返金を行う
                if ($task_select->driver_task_payment_status_id == 2 && ($task_select->stripe_payment_intent_id || $task_select->stripe_payment_intent_id == '無料ユーザーのため支払い免除')) {
                    /* 返金処理 */
                    try {
                        $stripe_refund = $login_user->refund($task_select->stripe_payment_intent_id); // 全額返金
                        $msg .= "返金しました。\n";
                        $is_refund = true;

                        // 返金ステータス更新
                        $task_select->driver_task_refund_status_id = 3;
                        $task_select->refund_amount = $stripe_refund->amount ?? 0;
                        $task_select->stripe_payment_refund_id = $stripe_refund->id ?? '';
                        $task_update = $task_select->save();
                    } catch (\Throwable $e) {
                        $msg .= "返金に失敗しました! \n";
                        $is_refund = false;
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
                        $payment_log = WebPaymentLog::create([
                            'date' => new \DateTime(),
                            'amount_money' => $stripe_refund->amount ?? 0,
                            'driver_task_id' => $task_select->id,
                            'web_payment_log_status_id' => $is_refund ? 1 : 2,
                            'web_payment_reason_id' => 2, // 返金
                            'message' => '',
                            'pay_user_id' => $task_select->joinOffice->id,
                            'pay_user_type_id' => $task_select->joinOffice->user_type_id,
                            // 'receive_user_id' => '',
                            'receive_user_type_id' => 1,
                        ]);
                    }
                }
            }


            if (isset($task_update) && $task_update) {
                $msg .= "稼働依頼をキャンセルしました。";
            } else {
                $msg .= "稼働依頼をキャンセルできませんでした!";
            }
        }

        // 不履行
        /**
         * @todo 時間の絞り込み
         */
        if ($type === 'failure') {
            $task_select = DriverTask::select()->where([
                ['id', '=', $task_id],
                ['delivery_office_id', '=', $login_id], // ログインしている営業所ユーザが管理しているデータのみ
                ['driver_task_status_id', '=', 3],
            ])->first();
            $task_select->driver_task_status_id = 8; // 不履行のステータス
            $task_update = $task_select->save();


            if ($task_update) {
                $msg = '稼働依頼をドライバーの不履行としました。';

                $task = DriverTask::select()
                    ->with(['joinDriver', 'joinOffice', 'joinTaskStatus'])
                    ->where('id', $task_id)
                    ->first();

                $office = DeliveryOffice::where('id', $login_id)->first();
                $driver = Driver::where('id', $task->driver_id)->first();
                // メールで利用するデータ
                $data_mail = [
                    "config_base" => $config_base,
                    "config_system" => $config_system,
                    'office' => $office,
                    'driver' => $driver,
                    'task' => $task,
                ];
            }
        }

        // 支払い方法変更
        if ($type === 'payment_method') {
            $task_update = DriverTask::where([
                ['id', '=', $task_id],
                ['delivery_office_id', '=', $login_id], // ログインしている営業所ユーザが管理しているデータのみ
                ['driver_task_status_id', '=', 10],
            ])->update([
                'stripe_payment_method_id' => $stripe_payment_method_id,
                'driver_task_status_id' => 11,
            ]);

            // 支払い方法変更 処理
            if ($task_update) {
                $msg = '支払い方法を変更しました。';
            }
        }

        $task = DriverTask::select()
            ->with(['joinDriver', 'joinOffice', 'joinTaskStatus'])
            ->where('id', $task_id)
            ->first();

        $office = DeliveryOffice::where('id', $login_id)->first();
        $driver = Driver::where('id', $task->driver_id)->first();

        /* 通知処理 */
        if (isset($task_update) && $task_update) {
            // メールで利用するデータ
            $data_mail = [
                "config_base" => $config_base,
                "config_system" => $config_system,
                'office' => $office,
                'driver' => $driver,
                'task' => $task,
            ];

            /* 営業所へのメール */
            // 送り先
            $to_office = [
                [
                    'email' => $office->email,
                    'name' => $office->name,
                ],
            ];
            $job = DriverTaskUpdateSendDeliveryOfficeMailJob::dispatch($to_office, $data_mail, $login_user, $info_list);



            // ドライバーが指定されている場合にメール送信
            if ($driver) {
                /* ドライバーへのメール */
                // 送り先
                $to_driver = [
                    [
                        'email' => $driver->email,
                        'name' => "{$driver->name_sei} {$driver->name_mei}",
                    ],
                ];
                DriverTaskUpdateSendDriverMailJob::dispatch($to_driver, $data_mail, $login_user, $info_list);
            }


            /* 管理者へのメール */
            $to_admin = [
                [
                    'email' => $config_system->email_notice,
                    'name' => "{$config_base->site_name}",
                ],
            ];
            DriverTaskUpdateSendAdminMailJob::dispatch($to_admin, $data_mail, $login_user, $info_list);
        } else {
            $msg .= '稼働依頼の更新ができませんでした。';
        }

        /* Push通知 依頼者 */
        $fcm_token_delivery_office_list =  FcmDeviceTokenDeliveryOffice::where("delivery_office_id", $login_id)->get();
        if ($fcm_token_delivery_office_list) {

            foreach ($fcm_token_delivery_office_list as $fcm_token_delivery_office) {
                $data_push = [
                    'fcm_token' => $fcm_token_delivery_office->token,
                    'title' => $msg,
                    "config_base" => $config_base,
                    "config_system" => $config_system,
                    'office' => $office,
                    'driver' => $driver,
                ];

                $data_other = [
                    'task' => $task,
                ];

                PushNotificationJob::dispatch($office, $data_push, $login_user, $info_list, $data_other);
            }
        }

        $api_status = true;
        if (isset($task_update) && $task_update) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                "status" => $api_status,
                "message" => $msg,
                "data" => [
                    "task_id" => $task_id
                ],
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return redirect()->route('delivery_office.driver_task.show', [
                'task_id' => $task_id,
            ])->with([
                'msg' => $msg
            ]);
        }
    }

    /**
     * API 価格の計算
     * @return string
     */
    public function calcPrice(DriverTaskCalcPriceRequest $request)
    {
        $task_date = $request->task_date ?? ''; // 稼働日
        $freight_cost = intval($request->freight_cost) ?? 0; // 運賃
        $driver_task_plan_id = $request->driver_task_plan_id; // 稼働依頼プランID
        $system_price = $request->system_price ?? null; // システム価格

        /* 価格計算 */
        $driver_task_price_support = new DriverTaskPriceSupport();
        $plan_price_data = $driver_task_price_support->getPrice($driver_task_plan_id, $freight_cost, $task_date, $system_price);

        $api_status = false;
        if (isset($plan_price_data) && $plan_price_data) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                "status" => $api_status,
                "data" => $plan_price_data,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode($plan_price_data, JSON_UNESCAPED_UNICODE);
        }
    }
}
