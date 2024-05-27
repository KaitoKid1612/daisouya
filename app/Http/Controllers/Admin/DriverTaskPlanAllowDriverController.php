<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverTaskPlanAllowDriver;

/**
 * その稼働依頼プランで対応可能なドライバープラン
 */
class DriverTaskPlanAllowDriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $driver_task_plan_allow_driver_list =  DriverTaskPlanAllowDriver::select()->paginate(50);

        return view('admin.driver_task_plan_allow_driver.index', [
            'driver_task_plan_allow_driver_list' => $driver_task_plan_allow_driver_list,
        ]);
    }
}
