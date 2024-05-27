<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\DeliveryCompany;

/**
 * 配送会社
 */
class DeliveryCompanyController extends Controller
{
    /**
     * 一覧
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->type ?? '';

        $company_list = DeliveryCompany::select(['id', 'name'])
            ->get();
       
        if ($type === 'other') {
            $company_list[] = ["id" => "None", "name" => "その他"];
        } else if ($type === 'belong') {
            $company_list[] = ["id" => "None", "name" => "所属なし"];
        }

        $api_status = true;
        if ($company_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $company_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 取得
     *
     * @param  int  $company_id
     * @return \Illuminate\Http\Response
     */
    public function show($company_id)
    {
        $company = DeliveryCompany::select(['id', 'name'])
            ->where('id', $company_id)
            ->first();

        $api_status = true;
        if ($company) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $company,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
