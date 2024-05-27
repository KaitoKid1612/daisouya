<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\UserType;

/**
 * ユーザータイプ
 */
class UserTypeController extends Controller
{
    /**
     * 一覧
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id_list = is_array($request->id_list) ? $request->id_list : [];

        $user_type_list = UserType::select(['id', 'name', 'label', 'explanation'])->where(function ($query) use ($id_list) {
            if ($id_list) {
                foreach ($id_list as $val) {
                    $query->orWhere('id', $val);
                }
            }
        })->get();

        $api_status = true;
        if ($user_type_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $user_type_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 取得
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($type_id)
    {
        $user_type = UserType::select(['id', 'name', 'label', 'explanation'])
            ->where('id', $type_id)
            ->get();

        $api_status = true;
        if ($user_type) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $user_type,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
