<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\RegisterRequestStatus;

/**
 * 登録申請ステータス
 */
class RegisterRequestStatusController extends Controller
{
    /**
     * 一覧
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $register_request_status_list = RegisterRequestStatus::select(['id', 'name', 'label', 'explanation'])->get();

        $api_status = true;
        if ($register_request_status_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $register_request_status_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 取得
     *
     * @param  int  $status_id
     * @return \Illuminate\Http\Response
     */
    public function show($status_id)
    {
        $review_public_status = RegisterRequestStatus::select(['id', 'name', 'label', 'explanation'])->where('id', $status_id)->first();

        $api_status = true;
        if ($review_public_status) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $review_public_status,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
