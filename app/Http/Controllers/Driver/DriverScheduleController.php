<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;
use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use App\Http\Requests\Driver\DriverScheduleCreateRequest;
use App\Http\Requests\Driver\DriverScheduleUpdateRequest;
use App\Http\Requests\Driver\DriverScheduleDestroyRequest;

use App\Models\DriverSchedule;
use App\Models\DriverTask;

use App\Libs\Calendar\DriverScheduleCalendar;

/**
 * ドライバーのスケジュール
 */
class DriverScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     * +API機能
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // カレンダー取得
        $calendar_month = $request->calendar_month ?? '';

        // 日付範囲
        $available_date_from = $request->available_date_from;
        $available_date_to = $request->available_date_to;

        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        // from も to nullなら fromは0000-00-00 toは9999-12-31
        if (!$available_date_from && !$available_date_to) {
            $available_date_from = '0000-00-00';
            $available_date_to = '9999-12-31';
        }

        // fromは指定されていて、toがnullなら、toはfromの月の末日
        if ($available_date_from && !$available_date_to) {
            try {
                $available_date_from_date = new \DateTime($available_date_from);
                // 月末日を求める
                $lastDay = new \DateTime($available_date_from_date->format('Y-m-t'));
                $available_date_to =  $lastDay->format('Y-m-d');
            } catch (\Exception $e) {
                $available_date_from = '0000-00-00';
                $available_date_to = '9999-12-31';
            }
        }

        // fromはnull、toは指定されていれば、fromはtoの月の1日
        if (!$available_date_from && $available_date_to) {
            try {
                // 指定された日付をDateTimeオブジェクトに変換
                $available_date_to_date = new \DateTime($available_date_to);

                // その月の初日を求める
                $firstDay = new \DateTime($available_date_to_date->format('Y-m-01'));

                $available_date_from = $firstDay->format('Y-m-d');
            } catch (\Exception $e) {
                $available_date_from = '0000-00-00';
                $available_date_to = '9999-12-31';
            }
        }

        $schedule_list_object = DriverSchedule::select()
            ->where([
                ['driver_id', $login_id],
                ['available_date', '>=', $available_date_from],
                ['available_date', '<=', $available_date_to]
            ])
            ->orderBy('available_date', 'desc');

        $schedule_list = $schedule_list_object->get();
        // logger($schedule_list->toArray());

        $api_status = true;
        if ($schedule_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $schedule_list
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            $task_list_object = DriverTask::select()
                ->with(['joinDriver', 'joinOffice', 'joinTaskStatus'])
                ->where([
                    ['driver_id', $login_id]
                ])->where('driver_task_status_id', '=', 3);
            $task_list = $task_list_object->get();
            // logger($task_list);
            // カレンダー生成
            $calendar =  new DriverScheduleCalendar($calendar_month, $schedule_list, $task_list);
            return view('driver.driver_schedule.index', [
                'schedule_list' => $schedule_list,
                'calendar' => $calendar
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('driver.driver_schedule.create');
    }

    /**
     * 作成
     * +API機能
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @todo 稼働登録できる期間の範囲 バリデーション
     */
    public function store(DriverScheduleCreateRequest $request)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $available_date_list = $request->available_date ?? '';
        $driver_id = $login_id;
        // logger($request);

        $msg = '';
        foreach ($available_date_list as $available_date) {
            $schedule_upsert =  DriverSchedule::updateOrCreate(
                [
                    'driver_id' => $driver_id,
                    'available_date' => $available_date
                ],
                [
                    'available_date' => $available_date,
                    'driver_id' => $driver_id,
                ]
            );
        }

        if ($schedule_upsert) {
            $msg = 'スケジュール登録しました。';
        } else {
            $msg = 'スケジュール登録できませんでした。';
        }

        $api_status = true;
        if ($schedule_upsert) {
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
            return redirect()->route('driver.driver_schedule.index')->with([
                'msg' => $msg
            ]);
        }
    }

    /**
     * 削除
     * +API機能
     *
     * @param  int  $schedule_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($schedule_id, DriverScheduleDestroyRequest $request)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $api_status = true;

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        try {
            $result = DriverSchedule::where(
                [
                    ['id', '=', $schedule_id],
                    ['driver_id', '=', $login_id],
                ]
            )->delete($schedule_id);

            if ($result) {
                $msg = '削除に成功';
                $api_status = true;
            } else {
                $msg = '削除されませんでした。';
                $api_status = false;
            }
        } catch (\Throwable $e) {
            $msg .= '削除に失敗';
            $api_status = false;

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

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'message' => $msg
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return redirect()->route('driver.driver_schedule.index')->with([
                'msg' => $msg,
            ]);
        }
    }
}
