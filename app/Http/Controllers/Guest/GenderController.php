<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Gender;

/**
 * 性別
 */
class GenderController extends Controller
{
    /**
     * 一覧
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gender_list = Gender::select(['id', 'name'])->get();

        $api_status = true;
        if ($gender_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $gender_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
