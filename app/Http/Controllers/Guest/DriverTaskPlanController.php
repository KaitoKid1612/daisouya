<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverTaskPlan;
use Illuminate\Support\Facades\Route;

/**
 * 稼働依頼プラン
 */
class DriverTaskPlanController extends Controller
{
    /**
     * 一覧
     */
    public function index()
    {
        $driver_task_plan_select_column = [
            'id',
            'name',
            'label',
        ];
        $driver_task_plan_list = DriverTaskPlan::select($driver_task_plan_select_column)->get();

        $api_status = true;
        if ($driver_task_plan_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $driver_task_plan_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
