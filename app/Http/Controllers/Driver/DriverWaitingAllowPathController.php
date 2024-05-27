<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $allow_path_list = [];
        if ($login_user->driver_entry_status_id == 2) {
            // アクセス許可のリスト
            $allow_path_list = config('constants.DRIVER_WAITING_ALLOW_PATH_LIST');
        } else {
            $allow_path_list = null;
        }

        $api_status = true;

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $allow_path_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
