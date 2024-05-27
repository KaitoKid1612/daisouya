<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:drivers');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $link_list = [
            ['href' => route('driver.driver_task.index'), 'text' => '稼働依頼一覧'],
            ['href' => route('driver.driver_task.index',[
                'who' => 'myself',
              ]), 'text' => 'My稼働依頼一覧'],
            ['href' => route('driver.driver_schedule.index'), 'text' => 'スケジュール'],
            ['href' => route('driver.driver_task_review.index') , 'text' => 'レビュー'],
            ['href' => route('driver.user.show', [
                'driver_id' => auth('drivers')->id(),
            ]), 'text' => 'アカウント情報'],
        ];

        $link_obj = json_decode(json_encode($link_list));

        return view('driver.dashboard.index', [
            "link_list" => $link_obj,
        ]);
    }
}
