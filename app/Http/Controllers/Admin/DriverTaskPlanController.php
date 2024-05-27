<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverTaskPlan;
use App\Http\Requests\Admin\DriverTaskPlanUpdateRequest;

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
        $driver_task_plan_list =  DriverTaskPlan::select()->where('id', "!=", 2)->paginate(50);

        return view('admin.driver_task_plan.index', [
            'driver_task_plan_list' => $driver_task_plan_list,
        ]);
    }

    /**
     * 編集
     */
    public function edit($driver_task_plan_id)
    {
        if ($driver_task_plan_id == 2) {
            return redirect()->route("admin.driver_task_plan.index", [])->with([
                'msg' => "このプランは編集できません。"
            ]);
        };
        $driver_task_plan =  DriverTaskPlan::select()->where("id", $driver_task_plan_id)->first();

        return view('admin.driver_task_plan.edit', [
            'driver_task_plan' => $driver_task_plan,
        ]);
    }

    /**
     * 更新
     */
    public function update(DriverTaskPlanUpdateRequest $request, $driver_task_plan_id)
    {
        $name = $request->name;
        $system_price = $request->system_price;
        $system_price_percent = $request->system_price_percent;
        $busy_system_price = $request->busy_system_price;
        $busy_system_price_percent = $request->busy_system_price_percent;
        $busy_system_price_percent_over = $request->busy_system_price_percent_over;
        $emergency_price = $request->emergency_price;

        $driver_task_plan = DriverTaskPlan::where('id', $driver_task_plan_id)->update([
            'name' => $name,
            'system_price' => $system_price,
            'system_price_percent' => $system_price_percent,
            'busy_system_price' => $busy_system_price,
            'busy_system_price_percent' => $busy_system_price_percent,
            'busy_system_price_percent_over' => $busy_system_price_percent_over,
            'emergency_price' => $emergency_price,
        ]);

        $msg = '';
        if ($driver_task_plan) {
            $msg = "稼働依頼プランを更新しました。";
        } else {
            $msg = "稼働依頼プランを更新できませんでした!";
        }

        return redirect()->route("admin.driver_task_plan.index", [])->with([
            'msg' => $msg
        ]);
    }
}
