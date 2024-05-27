<?php

namespace App\Http\Controllers\DeliveryOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DeliveryOffice\FcmDeviceTokenDestroyRequest;
use App\Models\FcmDeviceTokenDeliveryOffice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\DeliveryOffice\FcmDeviceTokenUpsertRequest;

/**
 *  FCMデバイストークン
 */
class FcmDeviceTokenController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upsert(FcmDeviceTokenUpsertRequest $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $device_name = $request->device_name;
        $fcm_token = $request->fcm_token;

        $fcm_token_upsert = FcmDeviceTokenDeliveryOffice::updateOrCreate(
            ['delivery_office_id' => $login_id, 'device_name' => $device_name],
            [
                'delivery_office_id' => $login_id,
                'device_name' => $device_name,
                'fcm_token' => $fcm_token,
            ]
        );

        $msg = '';
        if ($fcm_token_upsert) {
            $msg = 'FCMトークンの登録をしました。';
        } else {
            $msg = 'FCMトークンの登録に失敗しました!';
        }

        $api_status = true;
        if ($fcm_token_upsert) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'message' => $msg,
                'data' => $fcm_token_upsert
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            // view なし。
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $fcm_token
     * @return \Illuminate\Http\Response
     */
    public function show($fcm_token)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $fcm_device_token =  FcmDeviceTokenDeliveryOffice::where([
            ['delivery_office_id', $login_id],
            ['fcm_token', $fcm_token],
        ])->first();

        $api_status = true;
        if ($fcm_device_token) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $fcm_device_token
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            // view なし。
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $fcm_token
     * @return \Illuminate\Http\Response
     */
    public function destroy($fcm_token, FcmDeviceTokenDestroyRequest $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $delete = FcmDeviceTokenDeliveryOffice::where([
            ['delivery_office_id', '=', $login_id],
            ['fcm_token', '=', $fcm_token],
        ])->delete();


        $msg = '';
        if ($delete) {
            $msg = 'FCMトークンを削除しました。';
        } else {
            $msg = 'FCMトークンの削除に失敗しました!';
        }

        $api_status = true;
        if ($delete) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'msg' => $msg,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            // view なし。
        }
    }
}
