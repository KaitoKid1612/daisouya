<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\DeliveryOfficeChargeUserType;

/**
 * 請求に関するユーザの種類
 */
class DeliveryOfficeChargeUserTypeController extends Controller
{
    /**
     * 一覧
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $delivery_office_charge_user_type_list = DeliveryOfficeChargeUserType::select(['id', 'name', 'summary'])->get();

        $api_status = true;
        if ($delivery_office_charge_user_type_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $delivery_office_charge_user_type_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 取得
     *
     * @param  int  $type_id
     * @return \Illuminate\Http\Response
     */
    public function show($type_id)
    {
        $delivery_office_charge_user_type = DeliveryOfficeChargeUserType::select(['id', 'name'])->where('id', $type_id)->first();

        $api_status = true;
        if ($delivery_office_charge_user_type) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $delivery_office_charge_user_type,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
