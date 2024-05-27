<?php

namespace App\Http\Controllers\DeliveryOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\DriverTask;
use App\Models\DriverTaskReview;
use App\Libs\DeliveryOffice\DriverTaskUI;

/**
 * 稼働依頼のUIの表示時のデータを扱う
 */
class DriverTaskUIController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $task_id
     * @return \Illuminate\Http\Response
     */
    public function show(int $task_id)
    {
        $driver_task_ui = new DriverTaskUI;
        $result = $driver_task_ui->get($task_id);

        $api_status = true;
        if ($result) {
            $api_status = true;
        } else {
            $api_status = false;
        }


        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $result
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
