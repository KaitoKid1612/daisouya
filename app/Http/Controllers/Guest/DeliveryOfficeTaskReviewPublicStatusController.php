<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\DeliveryOfficeTaskReviewPublicStatus;

/**
 * 依頼者へのレビュー公開ステータス
 */
class DeliveryOfficeTaskReviewPublicStatusController extends Controller
{
    /**
     * 一覧
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $review_public_status_list = DeliveryOfficeTaskReviewPublicStatus::select(['id', 'name'])->get();

        $api_status = true;
        if ($review_public_status_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $review_public_status_list,
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
        $review_public_status = DeliveryOfficeTaskReviewPublicStatus::select(['id', 'name'])->where('id', $status_id)->first();

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
