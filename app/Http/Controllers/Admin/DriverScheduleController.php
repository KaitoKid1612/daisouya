<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;

use Illuminate\Http\Request;
use App\Http\Requests\Admin\DriverScheduleCreateRequest;
use App\Http\Requests\Admin\DriverScheduleUpdateRequest;

use App\Models\DriverSchedule;
use App\Models\Driver;

/**
 * ドライバーのスケジュール
 */
class DriverScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search_from_available_date = $request->from_available_date ?? '0000-00-00'; // タスク可能日付 以上
        $search_to_available_date = $request->to_available_date ?? ''; //タスク可能日付 以下
        $search_from_created_at = $request->from_created_at ?? ''; // 作成日 以上
        $search_to_created_at = $request->to_created_at ?? ''; // 作成日 以下
        $search_from_updated_at = $request->from_updated_at ?? ''; // 更新日 以上
        $search_to_updated_at = $request->to_updated_at ?? ''; // 更新日 以下
        $search_driver_id = $request->driver_id ?? ''; // ドライバーID

        $orderby = $request->orderby ?? ''; // 並び替え

        $schedule_list_object = DriverSchedule::select()
            ->with(['joinDriver']);


        // タスク可能日付 範囲 絞り込み
        if (isset($request->from_available_date)) {
            $schedule_list_object->where('available_date', '>=', $search_from_available_date);
        }
        if (isset($request->to_available_date)) {
            $schedule_list_object->where('available_date', '<=', $search_to_available_date);
        }

        // 作成日 範囲 絞り込み
        if (isset($request->from_created_at)) {
            $schedule_list_object->where('created_at', '>=', $search_from_created_at);
        }
        if (isset($request->to_created_at)) {
            $schedule_list_object->where('created_at', '<=', $search_to_created_at);
        }

        // 更新日 範囲 絞り込み
        if (isset($request->from_updated_at)) {
            $schedule_list_object->where('updated_at', '>=', $search_from_updated_at);
        }
        if (isset($request->to_updated_at)) {
            $schedule_list_object->where('updated_at', '<=', $search_to_updated_at);
        }

        // ドライバー 絞り込み
        if ($search_driver_id) {
            $schedule_list_object->where('driver_id', $search_driver_id);
        }

        /* 並び替え */
        if ($orderby === 'id_desc') {
            $schedule_list_object->orderBy('id', 'desc');
        } elseif ($orderby === 'id_asc') {
            $schedule_list_object->orderBy('id', 'asc');
        } elseif ($orderby === 'available_date_desc') {
            $schedule_list_object->orderBy('available_date', 'asc');
        } elseif ($orderby === 'available_date_asc') {
            $schedule_list_object->orderBy('available_date', 'desc');
        } elseif ($orderby === 'created_at_desc') {
            $schedule_list_object->orderBy('created_at', 'desc');
        } elseif ($orderby === 'updated_at_asc') {
            $schedule_list_object->orderBy('updated_at', 'asc');
        } else {
            $schedule_list_object->orderBy('id', 'desc');
        }

        /* フォーム検索に使うデータ */
        // 並び順
        $orderby_list = [
            ['value' => 'id_desc', 'text' => 'ID大きい順',],
            ['value' => 'id_asc', 'text' => 'ID小さい順',],
            ['value' => 'available_date_desc', 'text' => '稼働日 昇順'],
            ['value' => 'available_date_asc', 'text' => '稼働日 降順'],
            ['value' => 'created_at_desc', 'text' => '作成日 昇順'],
            ['value' => 'created_at_asc', 'text' => '作成日 降順'],
            ['value' => 'updated_at_desc', 'text' => '更新日 昇順'],
            ['value' => 'updated_at_asc', 'text' => '更新日 降順'],
        ];


        $schedule_list = $schedule_list_object->paginate(50)->withQueryString();
        // $schedule_list = $schedule_list_object->get();

        // logger($schedule_list->toArray());

        return view('admin.driver_schedule.index', [
            'schedule_list' => $schedule_list,
            'orderby_list' => $orderby_list,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $driver_id = $request->driver_id;

        /* フォーム検索に使うデータ */
        $driver = Driver::select()->where('id', $driver_id)->first();


        return view('admin.driver_schedule.create', [
            'driver' => $driver,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DriverScheduleCreateRequest $request)
    {
        $available_date = $request->available_date ?? '';
        $driver_id = $request->driver_id ?? null;

        $msg = '';

        // 複合主キーor複合ユニークの対応
        $schedule_select = DriverSchedule::where([
            ['available_date', $available_date],
            ['driver_id', $driver_id],
        ])->first();

        if ($schedule_select) {
            $msg = 'すでに登録済みです。';
        } else {
            $schedule_create = DriverSchedule::create([
                'available_date' => $available_date,
                'driver_id' => $driver_id,
            ]);


            if ($schedule_create) {
                $msg = "稼働可能日を作成しました。";
            } else {
                $msg = "稼働可能日を作成できませんでした!";
            }
        }


        return redirect()->route("admin.driver_schedule.index")->with([
            'msg' => $msg
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $schedule_id
     * @return \Illuminate\Http\Response
     */
    public function edit($schedule_id)
    {
        $schedule = DriverSchedule::select()->where('id', $schedule_id)->first();
        // logger($schedule->toArray());


        return view('admin.driver_schedule.edit', [
            'schedule' => $schedule,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $schedule_id
     * @return \Illuminate\Http\Response
     */
    public function update(DriverScheduleUpdateRequest $request, $schedule_id)
    {
        $available_date = $request->available_date ?? '';
        $driver_id = $request->driver_id ?? null;

        $msg = '';

        // 複合主キーor複合ユニークの対応
        $schedule_select = DriverSchedule::where([
            ['id', '!=', $schedule_id],
            ['available_date', $available_date],
            ['driver_id', $driver_id],
        ])->first();

        if ($schedule_select) {
            $msg = 'すでに登録済みです。';
        } else {
            $schedule_update = DriverSchedule::where('id', '=', $schedule_id)->update([
                'available_date' => $available_date,
                'driver_id' => $driver_id,
            ]);

            if ($schedule_update) {
                $msg = "稼働可能日を更新しました。";
            } else {
                $msg = "稼働可能日を更新できませんでした!";
            }
        }



        return redirect()->route("admin.driver_schedule.index")->with([
            'msg' => $msg
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $schedule_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($schedule_id)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $msg = ''; // 削除メッセージ

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        try {
            $result = DriverSchedule::where('id', '=', $schedule_id)->delete($schedule_id);

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

        return redirect()->route('admin.driver_schedule.index')->with([
            'msg' => $msg,
        ]);
    }
}
