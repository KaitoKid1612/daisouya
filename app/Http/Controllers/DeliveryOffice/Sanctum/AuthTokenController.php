<?php

namespace App\Http\Controllers\DeliveryOffice\Sanctum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DeliveryOffice\SanctumAuthTokenCreateRequest;
use App\Http\Requests\DeliveryOffice\SanctumAuthTokenDestroyRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

use App\Models\DeliveryOffice;


class AuthTokenController extends Controller
{
    /**
     * APIトークン発行 ログイン
     * +API機能
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function store(SanctumAuthTokenCreateRequest $request)
    {
        $email = $request->email;
        $password = $request->password;
        $device_name = $request->device_name;

        $user = DeliveryOffice::where('email', $email)->first();

        try {
            if(!$user) {
                throw ValidationException::withMessages([
                    'email' => ['ログインできません'],
                ]);
            }

            if (Hash::check($password, $user->password)) {
                return response()->json($user->createToken($device_name, ['delivery_office'])->plainTextToken);
            } else {
                throw ValidationException::withMessages([
                    'email' => ['ログインできません'],
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
            ], 401, [], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            Log::error($e->__toString());
            return response()->json(['msg' => 'Throwableエラー'], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * APIトークン削除 ログアウト
     * +API機能
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JSON
     */
    public function destroy(SanctumAuthTokenDestroyRequest $request)
    {
        $login_user = Auth::user();
        $login_id = Auth::id();

        $device_name = $request->device_name ?? '';

        $api_status = true;

        try {
            $result = $login_user->tokens()->where([
                ['tokenable_id', $login_id],
                ['name', $device_name],
            ])->delete();

            if ($result) {
                $api_status = true;
                return response()->json([
                    'status' => $api_status,
                    'message' => 'APIトークン削除しました。'
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                $api_status = false;
                return response()->json([
                    'status' => $api_status,
                    'message' => 'APIトークン削除できませんでした。'
                ], 200, [], JSON_UNESCAPED_UNICODE);
            }
        } catch (\Throwable $e) {
            $api_status = true;
            Log::error($e->__toString());
            return response()->json([
                'status' => $api_status,
                'message' => 'APIトークン削除できませんでした。'
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
