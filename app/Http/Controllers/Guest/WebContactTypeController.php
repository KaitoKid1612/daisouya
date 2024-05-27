<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\WebContactType;

/**
 * お問い合わせタイプ */
class WebContactTypeController extends Controller
{
    /**
     * 一覧
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contact_type_list = WebContactType::select(['id', 'name', 'label'])->get();

        $api_status = true;
        if ($contact_type_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $contact_type_list,
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
        $contact_type = WebContactType::select(['id', 'name', 'label'])->where('id', $type_id)->first();

        $api_status = true;
        if ($contact_type) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $contact_type,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
