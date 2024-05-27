<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\DeliveryOfficeType;

/**
 * 依頼者タイプ
 */
class DeliveryOfficeTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $delivery_office_type_list = DeliveryOfficeType::select(['id', 'name', 'label'])->get();

        $api_status = true;
        if ($delivery_office_type_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $delivery_office_type_list,
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
        $delivery_office_type_list = DeliveryOfficeType::select(['id', 'name', 'label'])->where('id', $type_id)->first();

        $api_status = true;
        if ($delivery_office_type_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $delivery_office_type_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
