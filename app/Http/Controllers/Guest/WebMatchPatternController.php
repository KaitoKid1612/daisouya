<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * 文字列のマッチパターンの方法
 */
class WebMatchPatternController extends Controller
{
    /**
     * 一覧
     */
    public function index()
    {
        // マッチパターンのリスト
        $match_pattern_list = config('constants.MATCH_PATTERN_LIST');

        $api_status = true;
        if ($match_pattern_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $match_pattern_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
