<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * 登録審査中ドライバーのアクセス権限パス
 */
class DriverWaitingAllowPathController extends Controller
{
    /**
     * 一覧
     */
    public function index()
    {
        // アクセス許可のリスト
        $allow_path_list = config('constants.DRIVER_WAITING_ALLOW_PATH_LIST');

        $api_status = true;
        if ($allow_path_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $allow_path_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
