<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;

use Illuminate\Http\Request;


use App\Http\Requests\Admin\DriverTaskCreateRequest;
use App\Http\Requests\Admin\DriverTaskUpdateRequest;

use Illuminate\Support\Facades\Mail;
use App\Mail\DriverTaskStoreSendDeliveryOfficeMail;
use App\Mail\DriverTaskStoreSendDriverMail;
use App\Mail\DriverTaskStoreSendAdminMail;
use App\Mail\DriverTaskUpdateSendDriverMail;
use App\Mail\DriverTaskUpdateSendDeliveryOfficeMail;
use App\Mail\DriverTaskUpdateSendAdminMail;
use App\Mail\DriverTaskPaymentRefundSendDeliveryOfficeMail;

use App\Models\Driver;
use App\Models\DeliveryCompany;
use App\Models\DeliveryOffice;
use App\Models\DriverTask;
use App\Models\DriverTaskPlan;
use App\Models\DriverTaskStatus;
use App\Models\Prefecture;
use App\Models\WebConfigBase;
use App\Models\WebConfigSystem;
use App\Models\WebNoticeLog;
use App\Models\WebPaymentLog;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Admin\DriverTaskExport;

class DriverTaskController extends Controller
{
    /**
     * 稼働依頼一覧
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword ?? ''; // 検索ワード
        $search_from_task_date = $request->from_task_date ?? ''; //タスク日付 以上
        $search_to_task_date = $request->to_task_date ?? ''; //タスク日付 以下
        $search_task_status_id_list = $request->task_status_id ?? ''; // タスクステータス
        $search_from_created_at = $request->from_created_at ?? ''; // 作成日 以上
        $search_to_created_at = $request->to_created_at ?? ''; // 作成日 以下
        $search_from_updated_at = $request->from_updated_at ?? ''; // 更新日 以上
        $search_to_updated_at = $request->to_updated_at ?? ''; // 更新日 以下
        $search_driver_id = $request->driver_id ?? ''; // ドライバーID
        $search_delivery_office_id = $request->delivery_office_id ?? ''; // 営業所ID
        $orderby = $request->orderby ?? ''; // 並び替え

        // ドライバーの稼働一覧 取得
        $task_list_object = DriverTask::select()
            ->with(['joinDriver', 'joinOffice', 'joinTaskStatus', 'joinDriverTaskPlan']);
        // キーワードで検索
        if (isset($request->keyword) && $request->keyword != '') {
            $task_list_object->orWhere(function ($query) use ($keyword) {
                $query
                    ->orWhere('task_memo', 'LIKE', "%{$keyword}%");
            });
        }

        // 稼働ステータス絞り込み
        if (isset($request->task_status_id)) {
            $task_list_object->where(function ($query) use ($search_task_status_id_list) {
                foreach ($search_task_status_id_list as $status_id) {
                    $query->orWhere('driver_task_status_id', '=', $status_id);
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

        // ドライバー 絞り込み
        if ($search_driver_id) {
            $task_list_object->where('driver_id', $search_driver_id);
        }

        // 営業所 絞り込み
        if ($search_delivery_office_id) {
            $task_list_object->where('delivery_office_id', $search_delivery_office_id);
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

        // Only template = 0
        $task_list = $task_list_object->where('is_template', 0)->get();


        $task_list = $task_list_object->paginate(50)->withQueryString();
        // $task_list = $task_list_object->get();
        // logger($task_list->toArray());
        // exit;

        /* フォーム検索に使うデータ */
        $task_status_list = DriverTaskStatus::get(); // 稼働ステータス一覧
        // logger($task_status_list->toArray());
        // exit;

        // 並び順
        $orderby_list = [
            ['value' => 'id_desc', 'text' => 'ID大きい順',],
            ['value' => 'id_asc', 'text' => 'ID小さい順',],
            ['value' => 'task_date_desc', 'text' => '稼働日 昇順'],
            ['value' => 'task_date_asc', 'text' => '稼働日 降順'],
            ['value' => 'created_at_desc', 'text' => '作成日 昇順'],
            ['value' => 'created_at_asc', 'text' => '作成日 降順'],
            ['value' => 'updated_at_desc', 'text' => '更新日 昇順'],
            ['value' => 'updated_at_asc', 'text' => '更新日 降順'],
        ];

        return view('admin.driver_task.index', [
            'task_list' => $task_list,
            'orderby_list' => $orderby_list,
            'task_status_list' => $task_status_list,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $delivery_office_id = $request->delivery_office_id;
        $driver_id = $request->driver_id;

        /* フォーム検索に使うデータ */
        $office = DeliveryOffice::select()->where('id', $delivery_office_id)->first();
        $driver = Driver::select()->where('id', $driver_id)->first();
        $prefecture_list = Prefecture::select()->get(); // 都道府県取得
        $company_list = DeliveryCompany::select()->get(); // 配送会社一覧
        $driver_task_plan_list = DriverTaskPlan::select()->get(); // 稼働依頼プラン一覧

        $task_status_list = DriverTaskStatus::where(function ($query) {
            $query->where('id', 1)
                ->orWhere('id', 2);
        })->get(); // 稼働ステータス一覧


        return view('admin.driver_task.create', [
            'office' => $office,
            'driver' => $driver,
            'task_status_list' => $task_status_list,
            'prefecture_list' => $prefecture_list,
            'company_list' => $company_list,
            'driver_task_plan_list' => $driver_task_plan_list,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DriverTaskCreateRequest $request)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報

        $task_date = $request->task_date ?? '';
        $driver_id = $request->driver_id ?? null;
        $delivery_office_id = $request->delivery_office_id ?? '';
        $driver_task_status_id = $request->driver_task_status_id ?? '';
        $delivery_route = $request->delivery_route ?? '';
        $rough_quantity = $request->rough_quantity ?? '';
        $task_memo = $request->task_memo ?? '';
        $task_delivery_company_id = in_array($request->task_delivery_company_id, [NULL, 'None'])  ? NULL :  $request->task_delivery_company_id;
        $task_delivery_company_name = $request->task_delivery_company_name ?? '';
        $task_delivery_office_name = $request->task_delivery_office_name ?? '';
        $task_email = $request->task_email ?? '';
        $task_tel = $request->task_tel ?? '';
        $task_post_code1 = $request->task_post_code1 ?? '';
        $task_post_code2 = $request->task_post_code2 ?? '';
        $task_addr1_id = $request->task_addr1_id ?? '';
        $task_addr2 = $request->task_addr2 ?? '';
        $task_addr3 = $request->task_addr3 ?? '';
        $task_addr4 = $request->task_addr4 ?? '';

        $driver_task_plan_id =  $request->driver_task_plan_id ?? null;
        $system_price = $request->system_price ?? 0;
        $busy_system_price = $request->busy_system_price ?? 0;
        $freight_cost = $request->freight_cost ?? 0;
        $emergency_price = $request->emergency_price ?? 0;
        $discount = $request->discount ?? 0;
        $tax_rate = $request->tax_rate ?? '';
        $payment_fee_rate = $request->payment_fee_rate ?? '';
        $stripe_payment_method_id = $request->payment_method_id ?? ''; // stripe支払いメソッドID

        // logger($request);
        // exit;

        // 配送会社IDが入力されていたら、会社名は配送会社テーブルから取得
        if ($task_delivery_company_id) {
            $delivery_conpany = DeliveryCompany::select()->where('id', $task_delivery_company_id)->first();
            $task_delivery_company_name = $delivery_conpany->name ?? '';
        }

        $tax = ceil(($system_price + $busy_system_price + $freight_cost + $emergency_price - $discount) * ($tax_rate / 100)); // 消費税

        $task_create = DriverTask::create([
            'task_date' => $task_date,
            'request_date' => new \DateTime(),
            'driver_id' => $driver_id,
            'delivery_office_id' => $delivery_office_id,
            'driver_task_status_id' => $driver_task_status_id,
            'driver_task_plan_id' => $driver_task_plan_id,
            'rough_quantity' => $rough_quantity,
            'delivery_route' => $delivery_route,
            'task_memo' => $task_memo,
            'task_delivery_company_name' => $task_delivery_company_name,
            'task_delivery_office_name' => $task_delivery_office_name,
            'task_email' => $task_email,
            'task_tel' => $task_tel,
            'task_post_code1' => $task_post_code1,
            'task_post_code2' => $task_post_code2,
            'task_addr1_id' => $task_addr1_id,
            'task_addr2' => $task_addr2,
            'task_addr3' => $task_addr3,
            'task_addr4' => $task_addr4,
            'system_price' => $system_price,
            'busy_system_price' => $busy_system_price,
            'freight_cost' => $freight_cost,
            'discount' => $discount,
            'emergency_price' => $emergency_price ?? '',
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

        if ($task_create) {
            $msg = '稼働依頼の登録をしました。';

            $task = DriverTask::select()
                ->with(['joinDriver', 'joinOffice', 'joinTaskStatus'])
                ->where('id', $task_create->id)
                ->first();

            $config_base = WebConfigBase::where('id', 1)->first();
            $config_system = WebConfigSystem::where('id', 1)->first();
            $office = DeliveryOffice::where('id', $task->delivery_office_id)->first();
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

            $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
            $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
            $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
            $file_path = __FILE__; // ファイルパス

            $msg_mail = ''; // メール可否メッセージ
            try {
                Mail::to($to_office)->send(new DriverTaskStoreSendDeliveryOfficeMail($data_mail)); // 送信
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
                // 通知ログ
                WebNoticeLog::create([
                    'web_log_level_id' => $log_level,
                    'web_notice_type_id' => $notice_type,
                    'task_id' => $task->id,
                    'to_user_id' => $office->id,
                    'to_user_type_id' => $office->user_type_id,
                    'to_user_info' => ($office->joinUserType->name ?? '') . " / email:" . ($office->email ?? ''),
                    'user_id' => $login_id,
                    'user_type_id' => $login_user->user_type_id ?? 4,
                    'user_info' => $login_user->joinUserType->name ?? '',
                    'text' => "稼働依頼{$task->joinTaskStatus->name}",
                    'remote_addr' => $remote_addr,
                    'http_user_agent' => $http_user_agent,
                    'url' => $url,
                ]);
            }


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
                try {
                    Mail::to($to_driver)->send(new DriverTaskStoreSendDriverMail($data_mail)); // 送信
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
                        'task_id' => $task->id,
                        'to_user_id' => $driver->id,
                        'to_user_type_id' => $driver->user_type_id,
                        'to_user_info' => ($driver->joinUserType->name ?? '') . " / email:" . ($driver->email ?? ''),
                        'user_id' => $login_id,
                        'user_type_id' => $login_user->user_type_id ?? 4,
                        'user_info' => $login_user->joinUserType->name ?? '',
                        'text' => "稼働依頼{$task->joinTaskStatus->name}",
                        'remote_addr' => $remote_addr,
                        'http_user_agent' => $http_user_agent,
                        'url' => $url,
                    ]);
                }
            }


            /* 管理者へのメール */
            $to_admin = [
                [
                    'email' => $config_system->email_notice,
                    'name' => "{$config_base->site_name}",
                ],
            ];
            try {
                Mail::to($to_admin)->send(new DriverTaskStoreSendAdminMail($data_mail)); // 送信
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
                    'task_id' => $task->id,
                    'to_user_id' => null,
                    'to_user_type_id' => null,
                    'to_user_info' => "管理者 / email:{$config_system->email_notice}",
                    'user_id' => $login_id,
                    'user_type_id' => $login_user->user_type_id ?? 4,
                    'user_info' => $login_user->joinUserType->name ?? '',
                    'text' => "稼働依頼{$task->joinTaskStatus->name}",
                    'remote_addr' => $remote_addr,
                    'http_user_agent' => $http_user_agent,
                    'url' => $url,
                ]);
            }
        } else {
            $msg = '稼働依頼の登録ができませんでした。';
        }
        return redirect()->route('admin.driver_task.show', [
            'task_id' => $task_create->id
        ])->with([
            'msg' => $msg,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $task_id
     * @return \Illuminate\Http\Response
     */
    public function show($task_id)
    {
        // ドライバーの稼働 取得
        $task = DriverTask::select()
            ->with(['joinDriver', 'joinOffice', 'joinTaskStatus', 'joinDriverReview', 'joinAddr1'])
            ->where('id', $task_id)
            ->first();

        // logger($task->totalPrice);
        // logger($task->toArray());
        // exit;
        return view('admin.driver_task.show', [
            'task' => $task
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $task_id
     * @return \Illuminate\Http\Response
     */
    public function edit($task_id)
    {

        $task = DriverTask::select()->where('id', $task_id)->first();
        // logger($task);

        /* フォーム検索に使うデータ */
        $task_status_list = DriverTaskStatus::get(); // 稼働ステータス一覧
        $prefecture_list = Prefecture::select()->get(); // 都道府県取得
        $company_list = DeliveryCompany::select()->get(); // 配送会社一覧
        $driver_task_plan_list = DriverTaskPlan::select()->get(); // 稼働依頼プラン一覧
        // logger($task_status_list->toArray());
        // exit;

        return view('admin.driver_task.edit', [
            'task' => $task,
            'task_status_list' => $task_status_list,
            'prefecture_list' => $prefecture_list,
            'company_list' => $company_list,
            'driver_task_plan_list' => $driver_task_plan_list,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $task_id
     * @return \Illuminate\Http\Response
     */
    public function update(DriverTaskUpdateRequest $request, $task_id)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報

        $task_date = $request->task_date ?? '';
        $driver_id = $request->driver_id ?? null;
        $delivery_office_id = $request->delivery_office_id ?? '';
        $driver_task_status_id = $request->driver_task_status_id ?? '';
        $rough_quantity = $request->rough_quantity ?? '';
        $delivery_route = $request->delivery_route ?? '';
        $task_memo = $request->task_memo ?? '';
        $task_delivery_company_id = in_array($request->task_delivery_company_id, [NULL, 'None'])  ? NULL :  $request->task_delivery_company_id;
        $task_delivery_company_name = $request->task_delivery_company_name ?? '';
        $task_delivery_office_name = $request->task_delivery_office_name ?? '';
        $task_email = $request->task_email ?? '';
        $task_tel = $request->task_tel ?? '';
        $task_post_code1 = $request->task_post_code1 ?? '';
        $task_post_code2 = $request->task_post_code2 ?? '';
        $task_addr1_id = $request->task_addr1_id ?? '';
        $task_addr2 = $request->task_addr2 ?? '';
        $task_addr3 = $request->task_addr3 ?? '';
        $task_addr4 = $request->task_addr4 ?? '';

        $driver_task_plan_id =  $request->driver_task_plan_id ?? null;
        $system_price = $request->system_price ?? 0;
        $busy_system_price = $request->busy_system_price ?? 0;
        $freight_cost = $request->freight_cost ?? 0;
        $emergency_price = $request->emergency_price ?? 0;
        $discount = $request->discount ?? 0;
        $tax_rate = $request->tax_rate ?? '';
        $payment_fee_rate = $request->payment_fee_rate ?? '';
        $stripe_payment_method_id = $request->payment_method_id ?? ''; // stripe支払いメソッドID

        // logger($request);
        // exit;
        // 配送会社IDが入力されていたら、会社名は配送会社テーブルから取得
        if ($task_delivery_company_id) {
            $delivery_conpany = DeliveryCompany::select()->where('id', $task_delivery_company_id)->first();
            $task_delivery_company_name = $delivery_conpany->name ?? '';
        }

        $tax = ceil(($system_price + $busy_system_price + $freight_cost + $emergency_price - $discount) * ($tax_rate / 100)); // 消費税

        $task_update = DriverTask::where('id', '=', $task_id)->update([
            'task_date' => $task_date,
            'driver_id' => $driver_id,
            'delivery_office_id' => $delivery_office_id,
            'driver_task_status_id' => $driver_task_status_id,
            'rough_quantity' => $rough_quantity,
            'delivery_route' => $delivery_route,
            'task_memo' => $task_memo,
            'task_delivery_company_name' => $task_delivery_company_name,
            'task_delivery_office_name' => $task_delivery_office_name,
            'task_email' => $task_email,
            'task_tel' => $task_tel,
            'task_post_code1' => $task_post_code1,
            'task_post_code2' => $task_post_code2,
            'task_addr1_id' => $task_addr1_id,
            'task_addr2' => $task_addr2,
            'task_addr3' => $task_addr3,
            'task_addr4' => $task_addr4,
            'driver_task_plan_id' => $driver_task_plan_id,
            'system_price' => $system_price,
            'busy_system_price' => $busy_system_price,
            'freight_cost' => $freight_cost,
            'discount' => $discount,
            'emergency_price' => $emergency_price ?? '',
            'tax' => $tax,
            'tax_rate' => $tax_rate,
            'payment_fee_rate' => $payment_fee_rate,
            'stripe_payment_method_id' => $stripe_payment_method_id ?? '', // Stripe支払い方法ID
        ]);

        if ($task_update) {
            $msg = '稼働依頼の更新をしました。';

            $task = DriverTask::select()
                ->with(['joinDriver', 'joinOffice', 'joinTaskStatus'])
                ->where('id', $task_id)
                ->first();

            $config_base = WebConfigBase::where('id', 1)->first();
            $config_system = WebConfigSystem::where('id', 1)->first();
            $office = DeliveryOffice::where('id', $task->delivery_office_id)->withTrashed()->first();
            $driver = Driver::where('id', $task->driver_id)->withTrashed()->first();


            // 削除済みユーザーがいる場合は通知しない
            if (($office && $office->trashed()) || ($driver && $driver->trashed())) {
                return redirect()->route('admin.driver_task.index')->with([
                    'msg' => '更新しましたが、ソフトディレートデータがあるので通知はスキップしました。'
                ]);
            }

            /* 通知処理 */
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
                    'email' => $office->email ?? '',
                    'name' => $office->name ?? '',
                ],
            ];

            $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
            $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
            $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
            $file_path = __FILE__; // ファイルパス

            $msg_mail = ''; // メール可否メッセージ
            try {
                Mail::to($to_office)->send(new DriverTaskUpdateSendDeliveryOfficeMail($data_mail)); // 送信
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
                // 通知ログ
                WebNoticeLog::create([
                    'web_log_level_id' => $log_level,
                    'web_notice_type_id' => $notice_type,
                    'task_id' => $task->id,
                    'to_user_id' => $office->id,
                    'to_user_type_id' => $office->user_type_id,
                    'to_user_info' => ($office->joinUserType->name ?? '') . " / email:" . ($office->email ?? ''),
                    'user_id' => $login_id,
                    'user_type_id' => $login_user->user_type_id ?? 4,
                    'user_info' => $login_user->joinUserType->name ?? '',
                    'text' => "稼働依頼{$task->joinTaskStatus->name}",
                    'remote_addr' => $remote_addr,
                    'http_user_agent' => $http_user_agent,
                    'url' => $url,
                ]);
            }


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
                try {
                    Mail::to($to_driver)->send(new DriverTaskUpdateSendDriverMail($data_mail)); // 送信
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
                        'task_id' => $task->id,
                        'to_user_id' => $driver->id,
                        'to_user_type_id' => $driver->user_type_id,
                        'to_user_info' => ($driver->joinUserType->name ?? '') . " / email:" . ($driver->email ?? ''),
                        'user_id' => $login_id,
                        'user_type_id' => $login_user->user_type_id ?? 4,
                        'user_info' => $login_user->joinUserType->name ?? '',
                        'text' => "稼働依頼{$task->joinTaskStatus->name}",
                        'remote_addr' => $remote_addr,
                        'http_user_agent' => $http_user_agent,
                        'url' => $url,
                    ]);
                }
            }


            /* 管理者へのメール */
            $to_admin = [
                [
                    'email' => $config_system->email_notice,
                    'name' => "{$config_base->site_name}",
                ],
            ];
            try {
                Mail::to($to_admin)->send(new DriverTaskUpdateSendAdminMail($data_mail)); // 送信
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
                    'task_id' => $task->id,
                    'to_user_id' => null,
                    'to_user_type_id' => null,
                    'to_user_info' => "管理者 / email:{$config_system->email_notice}",
                    'user_id' => $login_id,
                    'user_type_id' => $login_user->user_type_id ?? 4,
                    'user_info' => $login_user->joinUserType->name ?? '',
                    'text' => "稼働依頼{$task->joinTaskStatus->name}",
                    'remote_addr' => $remote_addr,
                    'http_user_agent' => $http_user_agent,
                    'url' => $url,
                ]);
            }
        } else {
            $msg = '稼働依頼の更新ができませんでした。';
        }

        return redirect()->route('admin.driver_task.show', [
            'task_id' => $task_id
        ])->with([
            'msg' => $msg
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $task_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($task_id)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $msg = ''; // 削除メッセージ

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        try {
            $result = DriverTask::where('id', '=', $task_id)->delete($task_id);

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

        return redirect()->route('admin.driver_task.index')->with([
            'msg' => $msg,
        ]);
        // return view('admin.driver_task.destroy');
    }

    /**
     * エクスポート 表示
     */
    public function export_index()
    {
        /* フォーム検索に使うデータ */
        $task_status_list = DriverTaskStatus::get(); // 稼働ステータス一覧

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

        // 並び順
        $orderby_list = [
            ['value' => 'id_asc', 'text' => 'ID昇順',],
            ['value' => 'id_desc', 'text' => 'ID降順',],
            ['value' => 'task_date_desc', 'text' => '稼働日 昇順'],
            ['value' => 'task_date_asc', 'text' => '稼働日 降順'],
            // ['value' => 'created_at_desc', 'text' => '作成日 昇順'],
            // ['value' => 'created_at_asc', 'text' => '作成日 降順'],
            // ['value' => 'updated_at_desc', 'text' => '更新日 昇順'],
            // ['value' => 'updated_at_asc', 'text' => '更新日 降順'],
        ];

        return view('admin.driver_task.export.index', [
            'task_status_list' => $task_status_list,
            'delivery_multi_list' => $delivery_multi_list,
            'orderby_list' => $orderby_list,
        ]);
    }

    /**
     * エクスポート 処理
     */
    public function export_read(Request $request)
    {
        $search_from_task_date = $request->from_task_date ?? ''; //タスク日付 以上
        $search_to_task_date = $request->to_task_date ?? ''; //タスク日付 以下
        $search_task_status_id_list = $request->task_status_id ?? ''; // タスクステータス
        $search_delivery_office_id = $request->delivery_office_id ?? ''; // 営業所リスト

        $orderby = $request->orderby ?? ''; // 並び替え

        return Excel::download(new DriverTaskExport([
            'from_task_date' => $search_from_task_date,
            'to_task_date' => $search_to_task_date,
            'task_status_id_list' => $search_task_status_id_list,
            'delivery_office_id' => $search_delivery_office_id,
            'orderby' => $orderby,
        ]), 'driver_tasks.csv');
    }


    /**
     * 返金処理
     */
    public function paymentRefund(Request $request, $task_id)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $msg = '';
        $is_refund = false; // 宣言 返金結果

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        $task_select = DriverTask::where([
            ['id', '=', $task_id],
            ['driver_task_payment_status_id', '=', 2], // 料金支払い済み
            ['driver_task_status_id', '=', 8], // ドライバー不履行
        ])->where(function ($query) {
            $query->where('driver_task_refund_status_id', '=', 1) // 返金なし
                ->orWhere('driver_task_refund_status_id', '=', 2); // 返金前
        })->first();

        // 稼働依頼した営業所ユーザ取得
        $office = DeliveryOffice::select()
            ->where('id', $task_select->delivery_office_id)
            ->first();

        try {
            $stripe_refund = $office->refund($task_select->stripe_payment_intent_id); //全額返金
            $msg = '返金しました。';
            $is_refund = true;

            // 返金ステータス更新
            $task_select->driver_task_refund_status_id = 3;
            $task_select->refund_amount = $stripe_refund->amount ?? 0;
            $task_select->stripe_payment_refund_id = $stripe_refund->id ?? '';
            $task_update = $task_select->save();
        } catch (\Throwable $e) {
            $msg = '返金に失敗しました。';
            $is_refund = false;
            $log_format = LogFormat::error(
                $msg,
                '',
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

        // 返金成功の場合
        if (isset($stripe_refund) && $stripe_refund) {

            /* 支払いに関する通知 */
            $task = DriverTask::select()
                ->with(['joinDriver', 'joinOffice', 'joinTaskStatus'])
                ->where('id', $task_id)
                ->first();

            $config_base = WebConfigBase::where('id', 1)->first();
            $config_system = WebConfigSystem::where('id', 1)->first();
            $office = DeliveryOffice::where('id', $task->delivery_office_id)->first();

            // メールで利用するデータ
            $data_mail = [
                "config_base" => $config_base,
                "config_system" => $config_system,
                'office' => $office,
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

            $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
            $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
            $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
            $file_path = __FILE__; // ファイルパス

            $msg_mail = ''; // メール可否メッセージ

            try {
                Mail::to($to_office)->send(new DriverTaskPaymentRefundSendDeliveryOfficeMail($data_mail)); // 送信
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
                // 通知ログ
                WebNoticeLog::create([
                    'web_log_level_id' => $log_level,
                    'web_notice_type_id' => $notice_type,
                    'task_id' => $task->id,
                    'to_user_id' => $office->id,
                    'to_user_type_id' => $office->user_type_id,
                    'to_user_info' => ($office->joinUserType->name ?? '') . " / email:" . ($office->email ?? ''),
                    'user_id' => $login_id,
                    'user_type_id' => $login_user->user_type_id ?? 4,
                    'user_info' => $login_user->joinUserType->name ?? '',
                    'text' => "稼働依頼{$task->joinTaskStatus->name}",
                    'remote_addr' => $remote_addr,
                    'http_user_agent' => $http_user_agent,
                    'url' => $url,
                ]);
            }
        }

        return redirect()->route('admin.driver_task.show', [
            'task_id' => $task_id
        ])->with([
            'msg' => $msg
        ]);
    }
}
