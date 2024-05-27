<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\DriverTask;

use App\Libs\Driver\DriverTaskPermission;

/**
 * 稼働依頼の権限について返す
 */
class DriverTaskPermissionController extends Controller
{
    /**
     * 詳細
     */
    public function show($task_id)
    {
        // 許可範囲を取得
        $driver_task_permission = new DriverTaskPermission;
        $result = $driver_task_permission->get($task_id);

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
