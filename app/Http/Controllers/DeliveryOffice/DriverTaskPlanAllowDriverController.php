<?php

namespace App\Http\Controllers\DeliveryOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libs\DriverTask\DriverTaskPlanAllowDriverSupport;
use App\Http\Requests\DeliveryOffice\DriverTaskPlanAllowDriverCheckRequest;

class DriverTaskPlanAllowDriverController extends Controller
{
    /**
     * 指定した稼働依頼プランが、指定したドライバーで稼働できるか判定
     * +API機能
     */
    public function check(DriverTaskPlanAllowDriverCheckRequest $request)
    {
        $driver_task_plan_id = $request->driver_task_plan_id;
        $driver_id = $request->driver_id;

        $result = false;

        if ($driver_id) {
            $driver_task_plan_allow_driver_support = new DriverTaskPlanAllowDriverSupport();

            $result = $driver_task_plan_allow_driver_support->checkAllow($driver_task_plan_id, $driver_id);
        } else {
            // driverが指定されていない場合は、指名なし依頼なので、どの稼働依頼プランでも依頼できる。
            $result = true;
        }

        $api_status = true;
        if ($result) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        return response()->json([
            'status' => $api_status,
            'data' => $result,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
