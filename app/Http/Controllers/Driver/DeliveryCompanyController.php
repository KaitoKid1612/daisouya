<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\DeliveryCompany;

/**
 * 配送会社
 */
class DeliveryCompanyController extends Controller
{
    /**
     * 一覧
     * +API機能
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_user = Auth::user();
            $login_id = Auth::id();
        }

        $select_column = [
            "id",
            "name",
        ];

        $company_list = DeliveryCompany::select($select_column)->with(['joinOffice' => function ($query) {
            $query->select(
                "id",
                "user_type_id",
                "name",
                "delivery_company_id",
                "delivery_company_name",
                "delivery_office_type_id",
            );
        }])
            ->get();

        if ($company_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $company_list
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 取得
     * +API機能
     *
     * @return \Illuminate\Http\Response
     */
    public function show($company_id)
    {
        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_user = Auth::user();
            $login_id = Auth::id();
        }

        $select_column = [
            "id",
            "name",
        ];

        $company = DeliveryCompany::select($select_column)->where('id', $company_id)->with(['joinOffice' => function ($query) {
            $query->select(
                "id",
                "user_type_id",
                "name",
                "delivery_company_id",
                "delivery_company_name",
                "delivery_office_type_id",
            );
        }])
            ->first();

        if ($company) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $company
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
