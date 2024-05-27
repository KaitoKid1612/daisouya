<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebBusySeason;
use App\Libs\Calendar\BusySeasonCalendarWatch;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Libs\Server\Analysis;
use App\Libs\Log\LogFormat;

class WebBusySeasonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $from_data = $request->from_date ?? '0000-00-00';
        $to_data = $request->to_date ?? '9999-12-31';

        $date_now = new \DateTime();
        $date_now_y_m = $date_now->format('Y-m'); // 現在の年月
        $calendar_month = $request->calendar_month ?? $date_now_y_m; // Y-m

        if ($request->from_date && $to_data) {
            $start_date_text = $from_data;
            $end_date_text = $to_data;
        } else {
            $start_date_text = "$calendar_month-01"; // 月始め
            $date_end = \DateTime::createFromFormat('Y-m-d', ($start_date_text));

            // DateTimeの生成に失敗した場合
            if (!$date_end) {
                $date_end = new \DateTime('9999-12-31');
            }
            $date_end->modify('last day of this month');
            $end_date_text = $date_end->format('Y-m-d'); // 末日
        }



        $busy_season_list = WebBusySeason::select()
            ->whereBetween('busy_date', [$start_date_text, $end_date_text])
            ->get();

        $calendar = new BusySeasonCalendarWatch($calendar_month, $busy_season_list);

        $api_status = true;
        if ($busy_season_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('*api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $busy_season_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('admin.web_busy_season.index', [
                'busy_season_list' => $busy_season_list,
                'calendar' => $calendar,
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
        return view('admin.web_busy_season.create', []);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス
        $msg = '';


        $busy_date_list = $request->busy_date ?? [];

        try {
            foreach ($busy_date_list as $busy_date) {
                $busy_season = WebBusySeason::firstOrCreate(
                    [
                        'busy_date' => $busy_date
                    ],
                    [
                        'busy_date' => $busy_date
                    ],
                );
            }
            $msg = '正常に実行されました。';
        } catch (\Throwable $e) {
            $msg = '処理に失敗しました!';

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

        return redirect()->route("admin.web_busy_season.index")->with([
            'msg' => $msg
        ]);
    }

    /**
     * 削除
     */
    public function destroy(Request $request, $busy_season_id)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $msg = ''; // 削除メッセージ

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        $previous_url = url()->previous(); // 送信元のURL
        $url_data = parse_url($previous_url); // urlを分解

        $query_params = []; // クエリパラメーターリスト
        if (isset($url_data['query'])) {
            parse_str($url_data['query'], $query_params);
        }

        try {
            $result = WebBusySeason::where('id', $busy_season_id)->delete($busy_season_id);

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

        // logger($busy_season_id);

        return redirect()->route("admin.web_busy_season.index", $query_params)->with([
            'msg' => $msg
        ]);
    }
}
