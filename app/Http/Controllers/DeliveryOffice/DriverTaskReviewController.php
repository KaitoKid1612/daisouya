<?php

namespace App\Http\Controllers\DeliveryOffice;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use App\Http\Requests\DeliveryOffice\DriverTaskReviewCreateRequest;
use App\Http\Requests\DeliveryOffice\DriverTaskReviewUpdateRequest;

use App\Models\DriverTaskReview;
use App\Models\DriverTaskReviewPublicStatus;
use App\Models\DriverTask;
use App\Models\Driver;

/**
 * ドライバーへのレビュー
 */
class DriverTaskReviewController extends Controller
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
        $request_driver_id = $request->driver_id ?? '';

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

        $review_list_object = DriverTaskReview::select()
            ->with([
                $join_office_column,
                'joinTask:id,task_date,request_date,driver_id,delivery_office_id',
                $join_driver_column,
                'joinPublicStatus'
            ])
            ->where([
                ['driver_task_review_public_status_id', 1],
            ])
            ->where(function ($query) use ($type, $login_id, $request_driver_id) {
                if ($type != "all") {
                    $query->where('delivery_office_id', $login_id);
                }

                if($request_driver_id) {
                    $query->where('driver_id', $request_driver_id);
                }
            })
            ->orderBy("id", "desc");
        $review_list = $review_list_object->paginate(24)->withQueryString();

        $driver = Driver::select()->where('id', $request_driver_id)->first();

        $api_status = true;
        if ($review_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        // logger($review->toArray());
        // exit;


        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $review_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('delivery_office.driver_task_review.index', [
                'review_list' => $review_list,
                'driver' => $driver,
            ]);
        }
    }


    /**
     * 作成画面
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        $driver_task_id = $request->driver_task_id;

        $task = DriverTask::where('id', $driver_task_id)->first();


        /* フォームで使うデータ */
        // 公開ステータス
        $driver_task_review_public_status_list = DriverTaskReviewPublicStatus::select()->get();

        // スコア
        for ($i = 1; $i < 6; $i++) {
            $score_list[] = ["value" => $i, "text" => "★ {$i}", "star" => str_repeat('★', $i)];
        }

        return view('delivery_office.driver_task_review.create', [
            'score_list' => $score_list,
            'task' => $task,
            'driver_task_review_public_status_list' => $driver_task_review_public_status_list
        ]);
    }

    /**
     * 作成
     * +API機能
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DriverTaskReviewCreateRequest $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $score = $request->score ?? '';
        $title = $request->title ?? '';
        $text = $request->text ?? '';
        $driver_task_id = $request->driver_task_id ?? '';
        $delivery_office_id = $login_id;

        $msg = "";

        $task = DriverTask::select()
            ->where([
                ['id', $driver_task_id],
                ['delivery_office_id', $login_id], // ログインしている営業所ユーザが管理しているデータのみ
            ])
            ->first();

        if ($task) {
            $driver_task_status_id = $task->driver_task_status_id; // 稼働ステータス
        }

        $reviewCreate = '';
        // ログインユーザーが依頼した稼働のみレビュー作成する
        // 4,8の意味 - 稼働後にレビューできるようにする
        if ($task && in_array($driver_task_status_id, [4, 8])) {
            $reviewCreate = DriverTaskReview::create([
                'score' => $score,
                'title' => $title,
                'text' => $text,
                'driver_id' => $task->driver_id,
                'driver_task_id' => $driver_task_id,
                'delivery_office_id' => $task->delivery_office_id,
                'driver_task_review_public_status_id' => 1,
            ]);
        }

        if ($reviewCreate) {
            $msg = "レビューしました。";
        } else {
            $msg = "レビューできませんでした。";
            $api_status = false;
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
                "message" => $msg,
                "data" => [
                    "task_id" => $driver_task_id
                ],
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return redirect()->route('delivery_office.driver_task.show', [
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
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $driver_task_id = $request->driver_task_id ?? '';

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

        $review_object = DriverTaskReview::select()
            ->with([
                $join_office_column,
                'joinTask:id,task_date,request_date,driver_id,delivery_office_id',
                $join_driver_column,
                'joinPublicStatus'
            ])
            ->where([
                ['driver_task_review_public_status_id', 1],
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

        // logger($review->toArray());
        // exit;

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
            return view('delivery_office.driver_task_review.show', [
                'review' => $review
            ]);
        }
    }
}
