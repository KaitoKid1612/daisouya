<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\DriverTaskStatus;

class DriverTaskStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $style = $request->style;

        $driver_task_status_list = DriverTaskStatus::select(['id', 'name', 'label'])->get();

        if ($style === "form") {
            $no_data = [
                'id' => "",
                'name' => '指定なし',
                'label' => '',
            ];
            $driver_task_status_list->prepend($no_data);
        }

        $api_status = true;
        if ($driver_task_status_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $driver_task_status_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $status_id
     * @return \Illuminate\Http\Response
     */
    public function show($status_id)
    {
        $driver_task_status = DriverTaskStatus::select(['id', 'name', 'label'])
            ->where('id', $status_id)
            ->first();

        $api_status = true;
        if ($driver_task_status) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $driver_task_status,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
