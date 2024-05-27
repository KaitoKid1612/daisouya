<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\DriverTaskReview;
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
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
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
            'user_type_id',
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

        // ドライバーの稼働 取得
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
                    $query->where('driver_id', $login_id);
                }

                if ($request_driver_id) {
                    $query->where('driver_id', $request_driver_id);
                }
            })
            ->orderBy("id", "desc");

        $review_avg = DriverTaskReview::select()
            ->where([
                ['driver_id', $login_id],
                ['driver_task_review_public_status_id', 1],
            ])
            ->avg('score');
        // logger($review_avg);

        $review_list = $review_list_object->paginate(24)->withQueryString();
        // $review_list = $review_list_object->get();
        // logger($review_list);
        
        $driver = Driver::select()->where('id', $request_driver_id)->first();

        $api_status = true;
        if ($review_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => [
                    'review_list' => $review_list,
                    'review_avg' => $review_avg,
                ],
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('driver.driver_task_review.index', [
                'review_list' => $review_list,
                'review_avg' => $review_avg,
                'driver' => $driver,
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

        $review_object = DriverTaskReview::select()
            ->with(
                [
                    $join_office_column,
                    'joinTask:id,task_date,request_date,driver_id,delivery_office_id',
                    $join_driver_column,
                    'joinPublicStatus'
                ]
            )
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
            return view('driver.driver_task_review.show', [
                'review' => $review
            ]);
        }
    }
}
