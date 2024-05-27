<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\DriverTaskRefundStatus;

/**
 * 稼働依頼返金ステータス
 */
class DriverTaskRefundStatusController extends Controller
{
    /**
     * 一覧
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $refund_status_list = DriverTaskRefundStatus::select(['id', 'name', 'label'])->get();

        $api_status = true;
        if ($refund_status_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $refund_status_list,
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
        $refund_status = DriverTaskRefundStatus::select(['id', 'name', 'label'])
            ->where('id', $status_id)
            ->first();

        $api_status = true;
        if ($refund_status) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $refund_status,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
