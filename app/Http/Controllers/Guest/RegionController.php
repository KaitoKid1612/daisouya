<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Region;

/**
 * 地方
 */
class RegionController extends Controller
{
    /**
     * 一覧
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $region_list = Region::get();

        $api_status = true;
        if ($region_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $region_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 取得
     *
     * @param  int  $region_id
     * @return \Illuminate\Http\Response
     */
    public function show($region_id)
    {
        $region = Region::where('id', $region_id)->first();

        $api_status = true;
        if ($region) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $region,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
