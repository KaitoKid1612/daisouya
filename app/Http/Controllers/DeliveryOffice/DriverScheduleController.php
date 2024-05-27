<?php

namespace App\Http\Controllers\DeliveryOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

use App\Models\DriverSchedule;
use App\Models\DriverTask;
use App\Libs\Calendar\DriverScheduleCalendar;

/**
 * ドライバースケジュール
 */
class DriverScheduleController extends Controller
{
    /**
     * 一覧
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $available_date_from = $request->available_date_from;
        $available_date_to = $request->available_date_to;

        $driver_id =  $request->driver_id ?? '';

        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        // from と to が nullなら fromは0000-00-00 toは9999-12-31
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
                ['driver_id', $driver_id],
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
        }
    }
}
