<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverPlan;

/**
 * ドライバープラン
 */
class DriverPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $driver_plan_list =  DriverPlan::select()->paginate(50);

        return view('admin.driver_plan.index', [
            'driver_plan_list' => $driver_plan_list,
        ]);
    }
}
