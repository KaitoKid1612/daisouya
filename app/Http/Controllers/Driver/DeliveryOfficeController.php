<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\DeliveryOffice;
use Illuminate\Support\Facades\Auth;

/**
 * 依頼者
 */
class DeliveryOfficeController extends Controller
{
    /**
     * 一覧
     * +API機能
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_user = Auth::user();
            $login_id = Auth::id();
        }

        $api_status = true;

        $select_column = [
            "id",
            "user_type_id",
            "name",
            "manager_name_sei",
            "manager_name_mei",
            "manager_name_sei_kana",
            "manager_name_mei_kana",
            "delivery_company_id",
            "delivery_company_name",
            "delivery_office_type_id",
            "addr1_id",
        ];
        $office_list = DeliveryOffice::select($select_column)
            ->with(['joinCompany', 'joinAddr1'])
            ->paginate(24)->withQueryString();

        if ($office_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $office_list
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 取得
     * +API機能
     *
     * @param  int  $delivery_office_id
     * @return \Illuminate\Http\Response
     */
    public function show($delivery_office_id, Request $request)
    {
        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_user = Auth::user();
            $login_id = Auth::id();
        }

        $select_column = [
            "id",
            "user_type_id",
            "name",
            "manager_name_sei",
            "manager_name_mei",
            "manager_name_sei_kana",
            "manager_name_mei_kana",
            "delivery_company_id",
            "delivery_company_name",
            "delivery_office_type_id",
            "addr1_id",
        ];
        $office = DeliveryOffice::select($select_column)
            ->where('id', $delivery_office_id)
            ->with(['joinCompany', 'joinAddr1'])
            ->first();

        $api_status = true;
        if ($office) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $office
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
