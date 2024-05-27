<?php

namespace App\Http\Controllers\DeliveryOffice;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\Driver;
use App\Models\DriverRegisterDeliveryOffice;
use App\Models\DriverTaskReview;
use App\Models\DriverTask;
use App\Models\Gender;
use App\Models\Prefecture;
use App\Models\DriverSchedule;
use App\Models\DriverTaskPlanAllowDriver;

use App\Libs\Calendar\DriverScheduleCalendarWatch;

class DriverController extends Controller
{
    /**
     * 一覧
     * +API機能
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword ?? ''; // 検索ワード
        $search_gender = $request->gender_id ?? ''; // 性別
        $search_addr1_id = $request->addr1_id ?? ''; // 都道府県ID

        $search_from_age = $request->from_age ?? '';
        $search_to_age = $request->to_age ?? '';

        $search_from_review_avg_score = $request->from_review_avg_score ?? 0; // レビュー平均評価点 以上
        $search_to_review_avg_score = $request->to_review_avg_score ?? 5; // レビュー平均評価点 以下

        $search_from_task_count = $request->from_task_count ?? 0; // 稼働数 以上
        $search_to_task_count = $request->to_task_count ?? 999999; // 稼働数 以下

        $orderby = $request->orderby ?? ''; // 並び替え

        $search_driver_task_plan_id =  $request->driver_task_plan_id ?? ''; // 稼働依頼プランで検索

        $select_column = [
            "id",
            "user_type_id",
            "driver_plan_id",
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
        /* ドライバー一覧 & 検索 */
        $driver_list_object = Driver::select($select_column)
            ->where('driver_entry_status_id', 1) // 通過のみ
            ->with(['joinUserType', 'joinGender', 'joinAddr1', 'joinRegisterOfficeMemo'])
            ->withAvg('joinDriverReview', 'score') // 平均評価点
            ->withCount(['joinTask']); // 稼働数

        // キーワードで検索
        if (isset($request->keyword) && $request->keyword != '') {

            $driver_list_object->Where(function ($query) use ($keyword) {
                $query
                    ->orWhere('id', 'LIKE', "%{$keyword}%")
                    ->orWhere('name_sei', 'LIKE', "%{$keyword}%")
                    ->orWhere('name_mei', 'LIKE', "%{$keyword}%")
                    ->orWhere('name_sei_kana', 'LIKE', "%{$keyword}%")
                    ->orWhere('name_mei_kana', 'LIKE', "%{$keyword}%")
                    ->orWhere('addr2', 'LIKE', "%{$keyword}%");
            });
        }

        /* 絞り込み検索 */
        // 性別
        if (isset($request->gender_id)) {
            $driver_list_object->where([['gender_id', $search_gender]]);
        }
        // 都道府県
        if (isset($request->addr1_id)) {
            $driver_list_object->where([['addr1_id', $search_addr1_id]]);
        }

        // 年齢 範囲 絞り込み
        // 20歳上というのは 現在 - 20の日付 以下という意味
        if (isset($request->from_age)) {
            $date = new \DateTime();
            $search_from_birthday = $date->modify("-{$search_from_age} year")->format('Y-m-d');

            $driver_list_object->whereDate('birthday', '<=', $search_from_birthday);
        }
        if (isset($request->to_age)) {
            $date = new \DateTime();
            $search_to_birthday = $date->modify("-{$search_to_age} year")->format('Y-m-d');

            $driver_list_object->whereDate('birthday', '<=', $search_to_birthday);
        }

        //  評価 絞り込み
        if (isset($request->from_review_avg_score)) {
            $driver_list_object->havingBetween('join_driver_review_avg_score', [$search_from_review_avg_score, $search_to_review_avg_score]);
        }

        //  稼働数 絞り込み
        if (isset($request->from_task_count) || isset($request->to_task_count)) {
            // 稼働数 以上 ~ 以下 で絞り込み処理 
            $driver_list_object->havingBetween('join_task_count', [$search_from_task_count, $search_to_task_count]);
        }

        // 指定された稼働依頼プランに対応しているドライバーで絞り込み
        $allow_driver_plan_list = []; // 稼働を許可できるドライバープランリスト
        if ($search_driver_task_plan_id) {
            $driver_task_plan_allow_driver_list = DriverTaskPlanAllowDriver::select()->where('driver_task_plan_id', $search_driver_task_plan_id)->get();

            foreach ($driver_task_plan_allow_driver_list as $driver_task_plan_allow_driver) {
                $allow_driver_plan_list [] = $driver_task_plan_allow_driver->driver_plan_id;
            }

            $driver_list_object->whereIn('driver_plan_id', $allow_driver_plan_list);
        }
        
        /* 並び替え */
        if ($orderby === 'id_desc') {
            $driver_list_object->orderBy('id', 'desc');
        } elseif ($orderby === 'id_asc') {
            $driver_list_object->orderBy('id', 'asc');
        } elseif ($orderby === 'join_driver_review_avg_score_desc') {
            $driver_list_object->orderBy('join_driver_review_avg_score', 'desc');
        } elseif ($orderby === 'join_driver_review_avg_score_asc') {
            $driver_list_object->orderBy('join_driver_review_avg_score', 'asc');
        } elseif ($orderby === 'join_task_count_desc') {
            $driver_list_object->orderBy('join_task_count', 'desc');
        } elseif ($orderby === 'join_task_count_asc') {
            $driver_list_object->orderBy('join_task_count', 'asc');
        } else {
            $driver_list_object->orderBy('id', 'desc');
        }


        // データ取得
        $driver_list = $driver_list_object->paginate(24)->withQueryString();


        $join_office_column_list = [
            "id",
            'name',
            'user_type_id',
            "delivery_company_id",
            "delivery_company_name",
            "delivery_office_type_id",
        ];
        $join_office_column = "joinOffice:" . implode(',', $join_office_column_list);
        $driver_list->each(function ($driver) use ($join_office_column) {
            # 年齢を追加
            $driver->age = $driver->getAgeAttribute();

            // ドライバーの登録済み営業所 取得
            $register_office_list = DriverRegisterDeliveryOffice::select()
                ->with([$join_office_column])
                ->where('driver_id', $driver->id)
                ->get();
            $driver->join_register_office = $register_office_list;
        });


        /* フォーム検索に使うデータ */
        // 都道府県取得
        $prefecture_list = Prefecture::select()->get();
        // logger($prefecture_list->toArray());
        $gender_list = Gender::select()->get();

        // 評価 from
        $from_review_avg_score_list = [];
        $from_review_avg_score_list[] = ["value" => "", "text" => "指定なし"];
        for ($i = 0; $i < 5; $i++) {
            $from_review_avg_score_list[] = ["value" => $i, "text" => "★ {$i} 以上"];
        }

        /* 並び順 */
        $orderby_list = [
            ['value' => '', 'text' => '指定なし'],
            // ['value' => 'id_desc', 'text' => 'ID大きい順',],
            // ['value' => 'id_asc', 'text' => 'ID小さい順',],
            ['value' => 'join_driver_review_avg_score_desc', 'text' => '評価高い順',],
            // ['value' => 'join_driver_review_avg_score_asc', 'text' => '評価低い順',],
            ['value' => 'join_task_count_desc', 'text' => '稼働数が多い順',],
            // ['value' => 'join_task_count_asc', 'text' => '稼働数が少ない順',],
        ];


        $api_status = true;
        if ($driver_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('*api.*')) {
            return response()->json([
                'status' => $api_status,
                'orderby_list' => $orderby_list,
                'from_review_avg_score_list' => $from_review_avg_score_list,
                'data' => $driver_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('delivery_office.driver.index', [
                'driver_list' => $driver_list, // 取得データ
                'prefecture_list' => $prefecture_list, //都道府県
                'gender_list' => $gender_list,
                'orderby_list' => $orderby_list,
                'from_review_avg_score_list' => $from_review_avg_score_list,
            ]);
        }
    }

    /**
     * 取得
     * +API機能
     *
     * @param  int  $driver_id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $driver_id)
    {
        $select_column = [
            "id",
            "user_type_id",
            "driver_plan_id",
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

        // ドライバー情報 取得
        $driver = Driver::select($select_column)
            ->with(['joinUserType', 'joinGender', 'joinAddr1', 'joinRegisterOfficeMemo'])
            ->withAvg('joinDriverReview', 'score')
            ->withCount(['joinTask', 'joinDriverReview'])
            ->where([
                ['id', $driver_id],
                ['driver_entry_status_id', 1]
            ])
            ->first();

        if ($driver) {
            $driver->age = $driver->getAgeAttribute() ?? '';
        }



        $join_office_column_list = [
            "id",
            'name',
            'user_type_id',
            "delivery_company_id",
            "delivery_company_name",
            "delivery_office_type_id",
        ];
        $join_office_column = "joinOffice:" . implode(',', $join_office_column_list);

        // ドライバーの登録済み営業所 取得
        $register_office_list = DriverRegisterDeliveryOffice::select()
            ->with([$join_office_column])
            ->where('driver_id', $driver_id)
            ->get();

        if ($driver) {
            $driver->join_register_office = $register_office_list;
        }

        // ドライバーの稼働一覧 取得
        $task_list = DriverTask::select()
            ->with(['joinOffice', 'joinTaskStatus'])
            ->where('driver_id', $driver_id)
            ->orderBy('task_date', 'asc')
            ->get();
        // logger($task_list->toArray());
        // exit;


        // ドライバーレビュー一覧 取得
        $review_list = DriverTaskReview::select()
            ->with(['joinOffice'])
            ->where('driver_id', $driver_id)
            ->limit(5)
            ->get();
        // logger($review_list->toArray());
        // exit;


        // カレンダー取得
        $t = $request->calendar_month ?? '';

        $schedule_list_object = DriverSchedule::select()
            ->with(['joinDriver'])
            ->where([
                'driver_id' => $driver_id
            ]);

        $schedule_list = $schedule_list_object->get();
        // logger($schedule_list->toArray());

        $task_list_object = DriverTask::select()
            ->with(['joinDriver', 'joinOffice', 'joinTaskStatus'])
            ->where([
                ['driver_id', $driver_id]
            ])->where('driver_task_status_id', '=', 3);
        $task_list = $task_list_object->get();
        // logger($task_list);



        // カレンダー生成
        $calendar =  new DriverScheduleCalendarWatch($t, $schedule_list, $task_list);

        $api_status = true;
        if ($driver) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $driver
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('delivery_office.driver.show', [
                'driver' => $driver,
                'register_office_list' => $register_office_list,
                'task_list' => $task_list,
                'review_list' => $review_list,
                'schedule_list' => $schedule_list,
                'calendar' => $calendar
            ]);
        }
    }
}
