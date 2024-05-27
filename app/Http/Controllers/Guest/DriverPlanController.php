<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverPlan;
use Illuminate\Support\Facades\Route;

/**
 * ドライバープラン
 */
class DriverPlanController extends Controller
{
    /**
     * 一覧
     * +API機能
     */
    public function index()
    {
        $driver_plan_select_column = [
            'id',
            'name',
            'label',
        ];
        $driver_plan_list = DriverPlan::select($driver_plan_select_column)->get();

        $api_status = true;
        if ($driver_plan_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $driver_plan_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
