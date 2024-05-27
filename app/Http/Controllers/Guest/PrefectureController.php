<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Prefecture;

/**
 * 都道府県
 */
class PrefectureController extends Controller
{
    /**
     * 一覧
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $style = $request->style ?? '';

        $prefecture_list = Prefecture::get();

        if ($style === "form") {
            $no_data = [
                'id' => "",
                'name' => '指定なし',
                'label' => '',
            ];
            $prefecture_list->prepend($no_data);
        }

        $api_status = true;
        if ($prefecture_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $prefecture_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 取得
     *
     * @param  int  $prefecture_id
     * @return \Illuminate\Http\Response
     */
    public function show($prefecture_id)
    {
        $prefecture = Prefecture::select(['id', 'name'])
            ->where('id', $prefecture_id)
            ->first();

        $api_status = true;
        if ($prefecture) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $prefecture,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
