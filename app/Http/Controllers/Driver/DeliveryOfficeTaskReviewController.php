<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Requests\Driver\DeliveryOfficeTaskReviewCreateRequest;

use App\Models\DeliveryOfficeTaskReview;
use App\Models\DriverTask;
use App\Models\DeliveryOffice;

/**
 * 依頼者へのレビュー
 */
class DeliveryOfficeTaskReviewController extends Controller
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

        $type = $request->type ?? '';
        $request_delivery_office_id = $request->delivery_office_id ?? '';

        $review_list_object = DeliveryOfficeTaskReview::select()
            ->where([
                ['review_public_status_id', 1],
            ])
            ->orderBy('id', "desc");

        // 結合先の取得カラム
        $join_driver_column_list = [
            "id",
            "user_type_id",
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
        $review_list_object->with([
            $join_office_column,
            'joinTask:id,task_date,request_date,driver_id,delivery_office_id,driver_task_status_id',
            $join_driver_column,
            'joinPublicStatus'
        ])
            ->where(function ($query) use ($type, $login_id, $request_delivery_office_id) {
                if ($type != "all") {
                    $query->where('driver_id', $login_id);
                }

                if ($request_delivery_office_id) {
                    $query->where('delivery_office_id', $request_delivery_office_id);
                }
            });

        $office = DeliveryOffice::select()->where('id', $request_delivery_office_id)->first();

        $review_list = $review_list_object->paginate(24)->withQueryString();

        $api_status = true;
        if ($review_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $review_list
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 作成画面
     */
    public function create(Request $request)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        $driver_task_id = $request->driver_task_id ?? '';

        $available_review_date = new \DateTime();
        $available_review_date->modify('-17 hours'); // 稼働日の17時間後、以降かチェックするため。

        $task = DriverTask::select()->where([
            ['id', $driver_task_id],
            ['task_date', '<', $available_review_date], // 稼働日が過ぎている
        ])->where(function ($query) {
            $query->where('driver_task_status_id', 3) // 受諾
                ->orwhere('driver_task_status_id', 4) // 完了
                ->orWhere('driver_task_status_id', 8); // 不履行
        })->first();

        $review = DeliveryOfficeTaskReview::select()->where([
            ['driver_task_id', $driver_task_id],
            ['driver_id', $login_id]
        ])->first();

        return view('driver.delivery_office_task_review.create', [
            'task' => $task,
            'review' => $review,
        ]);
    }

    /**
     * 作成
     * +API機能
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeliveryOfficeTaskReviewCreateRequest $request)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $score = $request->score ?? '';
        $title = $request->title ?? '';
        $text = $request->text ?? '';
        $driver_task_id = $request->driver_task_id ?? '';
        $driver_id = $login_id;

        $msg = "";

        $available_review_date = new \DateTime();
        $available_review_date->modify('-17 hours'); // 稼働日の17時
        $task = DriverTask::select()
            ->where([
                ['id', $driver_task_id],
                ['driver_id', $login_id], // ログインしている営業所ユーザが管理しているデータのみ
                ['task_date', '<', $available_review_date], // 稼働日が過ぎている
            ])
            ->first();

        $reviewCreate = '';
        $driver_task_status_id = $task->driver_task_status_id ?? '';
        // ログインユーザーが依頼したレビュー & 稼働後となるステータスのときにレビューできる
        if ($task && in_array($driver_task_status_id, [3, 4, 8])) {
            $reviewCreate = DeliveryOfficeTaskReview::create([
                'score' => $score,
                'title' => $title,
                'text' => $text,
                'driver_id' => $driver_id,
                'driver_task_id' => $driver_task_id,
                'delivery_office_id' => $task->delivery_office_id,
                'review_public_status_id' => 1,
            ]);
            if ($reviewCreate) {
                $msg = "レビューしました。";
            } else {
                $api_status = false;
                $msg = "レビューできませんでした。";
            }
        } else {
            $api_status = false;
            $msg = "レビューできる稼働がありません";
        }

        $api_status = true;
        if ($reviewCreate) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'message' => $msg,
                'data' => [
                    'task_id' => $driver_task_id,
                    'review_id' => $reviewCreate->id ?? '',
                ]
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return redirect()->route('driver.driver_task.show', [
                'task_id' => $driver_task_id
            ])->with([
                'msg' => $msg,
            ]);
        }
    }

    /**
     * 取得
     * +API機能
     *
     * @param  int  $review_id
     * @return \Illuminate\Http\Response
     */
    public function show($review_id = '', Request $request)
    {
        $driver_task_id = $request->driver_task_id ?? '';


        // 結合先の取得カラム
        $join_driver_column_list = [
            "id",
            "user_type_id",
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

        $join_task_column_list = [
            "id",
            "task_date",
            "request_date",
            "task_delivery_company_name",
            "task_delivery_office_name",
            "driver_id",
            "delivery_office_id",
            "task_post_code1",
            "task_post_code2",
            "task_addr1_id",
            "task_addr2",
            "task_addr3",
            "task_addr4",
        ];

        $join_task_column = "joinTask:" . implode(',', $join_task_column_list);

        $review_object = DeliveryOfficeTaskReview::select()
            ->with([
                $join_office_column,
                $join_driver_column,
                $join_task_column,
                'joinPublicStatus'
            ])
            ->where([
                ['review_public_status_id', 1],
            ])
            ->where(function ($query) use ($review_id, $driver_task_id) {
                if ($review_id) {
                    $query->where('id', $review_id);
                }

                if ($driver_task_id) {
                    $query->where('driver_task_id', $driver_task_id);
                }

                if (!$review_id && !$driver_task_id) {
                    $query->where('id', '');
                }
            });

        $review = $review_object->first();

        $api_status = true;
        if ($review) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $review
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('driver.delivery_office_task_review.show', [
                'review' => $review
            ]);
        }
    }
}
