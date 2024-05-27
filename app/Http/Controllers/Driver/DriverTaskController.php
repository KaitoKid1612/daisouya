<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Driver\DriverTaskUpdateRequest;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\DB;
use App\Models\Driver;
use App\Models\DeliveryOffice;
use App\Models\DriverTask;
use App\Models\DriverTaskStatus;
use App\Models\DriverTaskReview;
use App\Models\DeliveryOfficeTaskReview;
use App\Models\WebConfigBase;
use App\Models\WebConfigSystem;
use App\Models\WebNoticeLog;
use App\Models\Prefecture;
use App\Models\WebPaymentLog;
use App\Models\FcmDeviceTokenDeliveryOffice;
use App\Models\FcmDeviceTokenDriver;
use App\Models\DriverTaskPlanAllowDriver;

use Illuminate\Support\Facades\Mail;
use App\Mail\DriverTaskUpdateSendDeliveryOfficeMail;
use App\Mail\DriverTaskUpdateSendDriverMail;
use App\Mail\DriverTaskUpdateSendAdminMail;
use App\Mail\DriverTaskPaymentSendDeliveryOfficeMail;

use App\Jobs\Mail\DriverTaskPaymentSendDeliveryOfficeMailJob;
use App\Jobs\Mail\DriverTaskUpdateSendDeliveryOfficeMailJob;
use App\Jobs\Mail\DriverTaskUpdateSendDriverMailJob;
use App\Jobs\Mail\DriverTaskUpdateSendAdminMailJob;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;

use App\Jobs\Push\PushNotificationJob;

use App\Libs\Driver\DriverTaskPermission;
use App\Libs\Driver\DriverTaskUI;

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
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
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
        $search_addr1_id = $request->addr1_id ?? ''; // 都道府県
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
                $join_driver_task_plan_column,
            ]);

        // キーワードで検索
        if (isset($request->keyword) && $request->keyword != '') {
            $task_list_object->orWhere(function ($query) use ($keyword) {
                $query
                    ->orWhere('task_delivery_company_name', 'LIKE', "%{$keyword}%")
                    ->orWhere('task_memo', 'LIKE', "%{$keyword}%")
                    ->orWhere('task_addr2', 'LIKE', "%{$keyword}%")
                    ->orWhere('task_addr3', 'LIKE', "%{$keyword}%")
                    ->orWhere('task_addr4', 'LIKE', "%{$keyword}%");
            });
        }

        // ログインユーザーで絞り込み My稼働一覧
        if (isset($request->who) && $request->who === 'myself') {
            $task_list_object->where([
                ['driver_id', '=', $login_id],
            ]);
        }

        // ログインドライバーが、許可されている稼働依頼プランのみに絞り込み。 
        // My稼働一覧はプラン変更や過去のプランがnullのものに対応するため、適用しない
        if (!isset($request->who) && $request->who !== 'myself') {
            $allow_driver_task_plan_list = []; // 稼働を許可できる稼働依頼プランリスト
            $driver_plan_id = $login_user->driver_plan_id;
            $driver_task_plan_allow_driver_list = DriverTaskPlanAllowDriver::select()->where('driver_plan_id', $driver_plan_id)->get();

            if ($driver_task_plan_allow_driver_list) {
                foreach ($driver_task_plan_allow_driver_list as $driver_task_plan_allow_driver) {
                    $allow_driver_task_plan_list[] = $driver_task_plan_allow_driver->driver_task_plan_id;
                }
                // $task_list_object->whereIn('driver_task_plan_id', $allow_driver_task_plan_list);

                $task_list_object->whereIn('driver_task_plan_id', $allow_driver_task_plan_list);
            }
        }

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

        // 都道府県 絞り込み
        if (isset($request->addr1_id)) {
            $task_list_object->where('task_addr1_id', '=', $search_addr1_id);
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

        // Get all driver task and is_template is false
        $task_list_object = $task_list_object->where('is_template', false);

        $task_list = $task_list_object->paginate(24)->withQueryString();
        // $task_list = $task_list_object->get();
        // logger($task_list->toArray());
        // exit;

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

        /* フォーム検索に使うデータ */
        $task_status_list = DriverTaskStatus::get(); // 稼働ステータス一覧
        $prefecture_list = Prefecture::select()->get();
        // logger($task_status_list->toArray());
        // exit;

        /* 並び順 */
        $orderby_list = [
            ['value' => '', 'text' => '指定なし'],
            // ['value' => 'id_desc', 'text' => 'ID大きい順',],
            // ['value' => 'id_asc', 'text' => 'ID小さい順',],
            ['value' => 'created_at_desc', 'text' => '新着順'],
            ['value' => 'task_date_desc', 'text' => '稼働日 昇順'],
            ['value' => 'task_date_asc', 'text' => '稼働日 降順'],
            // ['value' => 'created_at_asc', 'text' => '作成日 降順'],
            // ['value' => 'updated_at_desc', 'text' => '更新日 昇順'],
            // ['value' => 'updated_at_asc', 'text' => '更新日 降順'],
        ];
        // logger($task_list->toArray());

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
            return view('driver.driver_task.index', [
                'task_list' => $task_list,
                'orderby_list' => $orderby_list,
                'task_status_list' => $task_status_list,
                'prefecture_list' => $prefecture_list,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $task_id
     * @return \Illuminate\Http\Response
     */
    public function show($task_id)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $task_temp = DriverTask::select()->where([
            ['id', $task_id],
            ['driver_id', $login_id],
        ]);

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
            'name',
            "manager_name_sei",
            "manager_name_mei",
            "manager_name_sei_kana",
            "manager_name_mei_kana",
            "delivery_company_id",
            "delivery_company_name",
            "delivery_office_type_id",
            "charge_user_type_id",
        ];

        if ($task_temp) {
            array_push($join_office_column_list, 'email', 'manager_tel');
        }
        $join_office_column = "joinOffice:" . implode(',', $join_office_column_list);

        $join_driver_task_plan_column_list = [
            'id',
            'name',
            'label'
        ];
        $join_driver_task_plan_column = "joinDriverTaskPlan:" . implode(',', $join_driver_task_plan_column_list);

        // ドライバーの稼働 取得
        $task_object = DriverTask::select()
            ->with([
                $join_driver_column,
                $join_office_column,
                'joinDriverReview',
                'joinDeliveryOfficeReview',
                'joinTaskStatus',
                'joinDriverTaskPlan',
                $join_driver_task_plan_column,
            ])
            ->where([
                ['id', $task_id],
                // ['driver_id', $login_id],
            ]);



        // ログインドライバーが、許可されている稼働依頼プランのみに絞り込み。 
        $allow_driver_task_plan_list = []; // 稼働を許可できる稼働依頼プランリスト
        $driver_plan_id = $login_user->driver_plan_id;
        $driver_task_plan_allow_driver_list = DriverTaskPlanAllowDriver::select()->where('driver_plan_id', $driver_plan_id)->get();

        if ($driver_task_plan_allow_driver_list) {
            foreach ($driver_task_plan_allow_driver_list as $driver_task_plan_allow_driver) {
                $allow_driver_task_plan_list[] = $driver_task_plan_allow_driver->driver_task_plan_id;
            }
            $task_object->where(function ($query) use ($allow_driver_task_plan_list) {
                if ($allow_driver_task_plan_list) {
                    $query->whereIn('driver_task_plan_id', $allow_driver_task_plan_list);
                    $query->orWhere('driver_task_plan_id', null);
                }
            });
        }
        $task = $task_object->first();

        /* ドライバーの表記 */
        if ($task) {
            $task->driver_nameplate = '';
            if ($task->driver_id && $task->joinDriver) {
                if ($task->driver_id == $login_id) {
                    $task->driver_nameplate = $task->joinDriver->full_name ?? '';
                } else {
                    $task->driver_nameplate = "自分以外のドライバー";
                }
            } elseif ($task->driver_id && !$task->joinDriver) {
                $task->driver_nameplate = "データなしorソフト削除済み ";
            } else {
                $task->driver_nameplate = "未定";
            }
        }

        // ドライバーレビュー取得
        $review = DriverTaskReview::select()
            ->where([
                ['driver_task_id', $task_id],
                ['driver_task_review_public_status_id', 1] // 公開
            ])->first();
        // logger($review->toArray());

        // 依頼者レビュー取得
        $office_review = DeliveryOfficeTaskReview::select()
            ->where([
                ['driver_task_id', $task_id],
                ['review_public_status_id', 1] // 公開
            ])->first();

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
            return view('driver.driver_task.show', [
                'task' => $task,
                'review' => $review,
                'office_review' => $office_review
            ]);
        }
    }


    /**
     * 稼働依頼を却下
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $task_id
     * @return \Illuminate\Http\Response
     * @todo リファクタリング
     */
    public function update(DriverTaskUpdateRequest $request, $task_id)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $type = $request->type;

        $msg = '';
        $is_payment = false; // 宣言 支払い結果

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

        /* 稼働依頼を却下 */
        if ($type === 'reject') {
            $task_update = DriverTask::where([
                ['id', '=', $task_id],
                ['driver_id', '=', $login_id],
            ])->where(function ($query) {
                $query->where('driver_task_status_id', 2)
                    ->orWhere('driver_task_status_id', 11);
            })->update([
                'driver_task_status_id' => 5, // 却下
            ]);
            if ($task_update) {
                $msg = '稼働依頼が却下されました。';
            }
        } elseif ($type === 'accept') { /* 受諾 */

            // 更新するデータの情報取得
            $task_select = DriverTask::where([
                ['id', '=', $task_id],
            ])->first();

            // ドライバーが同日にすでに受諾している稼働がないかチェック
            $task_duplicate_select = DriverTask::where([
                ['driver_id', $login_id],
                ['task_date', $task_select->task_date],
                ['driver_task_status_id', '=', 3],
            ])->first();

            if ($task_duplicate_select) {
                $msg = "同日にすでに受諾している稼働があります。";
            } else {

                DB::beginTransaction();

                // 稼働依頼を受諾ステータスに更新
                try {

                    $task_update = DriverTask::where([
                        ['id', '=', $task_id],
                    ])
                        ->where(function ($query) use ($task_select, $login_id) {
                            // ドライバーIDがnullの場合はnullで検索、それ以外だったらログインIDで検索
                            if (is_null($task_select->driver_id)) {
                                $query->where('driver_id', '=', null);
                            } else {
                                $query->where('driver_id', '=', $login_id);
                            }
                        })->where(function ($query) use ($task_select) {
                            if (is_null($task_select->driver_id)) {
                                $query->where('driver_task_status_id', 1);
                            } else {
                                $query->where('driver_task_status_id', 2)
                                    ->orWhere('driver_task_status_id', 11);
                            }
                        })
                        ->update([
                            'driver_task_status_id' => 3, // 受諾
                            'driver_id' => $login_id, // 受諾
                        ]);
                } catch (\Exception $e) {
                    DB::rollback();
                    unset($task_update);
                    $msg = '受諾処理に失敗しました';
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
            }

            $delivery_office_id = $task_select->delivery_office_id; // 購入者の営業所ID
            $delivery_office = DeliveryOffice::select()->where('id', $delivery_office_id)->first(); // 購入した営業所ユーザー

            /* 稼働が受諾されたら決済を行う */
            if (isset($task_update) && $task_update) {
                $msg .= '稼働依頼が受諾されました。';

                /* 請求に関するユーザの種類が無料の場合は、購入処理を行わない */
                // 無料ユーザーは決済なし。
                if ($delivery_office->charge_user_type_id == 2) {
                    $task_select->driver_task_payment_status_id = 2; // 支払い済みに更新
                    $task_select->stripe_payment_intent_id = '無料ユーザーのため支払い免除'; // 支払いインテントIDの登録。返金するときに必要になる。
                    $task_select->save();
                    DB::commit(); // 決済が成功したら、稼働依頼をコミット。
                } else {
                    /* 購入処理 */
                    try {
                        $total_price = $task_select->TotalPrice ?? 0; // 請求金額

                        // 明細書表記
                        $config_base = WebConfigBase::select()->where('id', 1)->first();
                        $domain = $_SERVER['SERVER_NAME'];
                        $statement_descriptor_suffix = "daisoya";
                        $description = "{$config_base->site_name} 稼働依頼 TASK_ID:{$task_id} {$domain}";

                        // 決済
                        $stripe_charge = $delivery_office->charge($total_price, $task_select->stripe_payment_method_id ?? '', [
                            'statement_descriptor_suffix' => $statement_descriptor_suffix,
                            'description' => $description,
                        ]);

                        $is_payment = true;
                        // logger($stripe_charge);

                        if ($stripe_charge) {
                            $task_select->driver_task_payment_status_id = 2; // 支払い済みに更新
                            $task_select->stripe_payment_intent_id = $stripe_charge->id ?? ''; // 支払いインテントIDの登録。返金するときに必要になる。
                            $task_select->save();
                        }

                        DB::commit(); // 決済が成功したら、稼働依頼をコミット。
                    } catch (\Throwable $e) {
                        $is_payment = false;
                        $msg = '決済準備のため待機中です。決済の準備が整いましたら通知致します。';

                        DB::rollback(); // 決済失敗したら、稼働依頼をロールバック。

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

                        unset($task_update);

                        $task_update = DriverTask::where([
                            ['id', '=', $task_id],
                        ])
                            ->where(function ($query) use ($task_select, $login_id) {
                                // ドライバーIDがnullの場合はnullで検索、それ以外だったらログインIDで検索
                                if (is_null($task_select->driver_id)) {
                                    $query->where('driver_id', '=', null);
                                } else {
                                    $query->where('driver_id', '=', $login_id);
                                }
                            })->where(function ($query) use ($task_select) {
                                if (is_null($task_select->driver_id)) {
                                    $query->where('driver_task_status_id', 1);
                                } else {
                                    $query->where('driver_task_status_id', 2)
                                        ->orWhere('driver_task_status_id', 11);
                                }
                            })
                            ->update([
                                'driver_task_status_id' => 10, // 決済待機中
                                'driver_id' => $login_id, // 担当ドライバー
                            ]);
                    } finally {
                        $payment_log = WebPaymentLog::create([
                            'date' => new \DateTime(),
                            'amount_money' => $total_price ?? 0,
                            'driver_task_id' => $task_select->id,
                            'web_payment_log_status_id' => $is_payment ? 1 : 2,
                            'web_payment_reason_id' => 1,
                            'message' => '',
                            'pay_user_id' => $task_select->joinOffice->id,
                            'pay_user_type_id' => $task_select->joinOffice->user_type_id,
                            // 'receive_user_id' => '',
                            'receive_user_type_id' => 1,
                        ]);
                    }
                }
            }

            /* 支払い成否に関する通知 */
            // 無料ユーザー以外に通知する
            if ($delivery_office->charge_user_type_id !== 2) {
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

                DriverTaskPaymentSendDeliveryOfficeMailJob::dispatch($to_office, $data_mail, $login_user, $info_list);
            }
        }


        $task = DriverTask::select()
            ->with(['joinDriver', 'joinOffice', 'joinTaskStatus'])
            ->where('id', $task_id)
            ->first();
        $config_base = WebConfigBase::where('id', 1)->first();
        $config_system = WebConfigSystem::where('id', 1)->first();
        $office = DeliveryOffice::where('id', $task->delivery_office_id)->first();
        $driver = Driver::where('id', $login_id)->first();

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
                    'name' => "{$office->name}",
                ],
            ];
            DriverTaskUpdateSendDeliveryOfficeMailJob::dispatch($to_office, $data_mail, $login_user, $info_list);


            /* ドライバーへのメール */
            // 送り先
            $to_driver = [
                [
                    'email' => $driver->email,
                    'name' => "{$driver->name_sei} {$driver->name_mei}",
                ],
            ];
            DriverTaskUpdateSendDriverMailJob::dispatch($to_driver, $data_mail, $login_user, $info_list);

            /* 管理者へのメール */
            $to_admin = [
                [
                    'email' => $config_system->email_notice,
                    'name' => "{$config_base->site_name}",
                ],
            ];
            DriverTaskUpdateSendAdminMailJob::dispatch($to_admin, $data_mail, $login_user, $info_list);


            /* Push通知 依頼者 */
            $fcm_token_delivery_office_list =  FcmDeviceTokenDeliveryOffice::where("delivery_office_id", $task->delivery_office_id)->get();
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
            $fcm_token_driver_list =  FcmDeviceTokenDriver::where("driver_id", $login_id)->get();
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
        } else {
            $msg .= '稼働依頼の更新ができませんでした。';
        }


        if (isset($task_update) && $task_update) {
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
            return redirect()->route('driver.driver_task.show', [
                'task_id' => $task_id,
            ])->with([
                'msg' => $msg,
            ]);
        }
    }
}