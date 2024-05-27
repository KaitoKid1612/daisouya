<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebConfigBase;
use Illuminate\Support\Facades\Route;

/**
 * プライバシーポリシー
 */
class WebPrivacyPolicyController extends Controller
{
    /**
     * 取得
     * +API機能
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->type ?? '';
        $is_html = $request->is_html ?? true;
        $privacy_policy = '';
        $name = 'who';

        if ($type == 'driver') {
            $config_base =  WebConfigBase::select('privacy_policy_driver')
                ->where('id', 1)
                ->first();
            $privacy_policy = $config_base->privacy_policy_driver;
            $name = 'ドライバー';
        } elseif ($type === 'office' || $type == '') {
            $config_base =  WebConfigBase::select('privacy_policy_delivery_office')
                ->where('id', 1)
                ->first();
            $privacy_policy = $config_base->privacy_policy_delivery_office;
            $name = '依頼者';
        }

        if (!$is_html) {
            $privacy_policy = preg_replace('/(<\/\w+>)/', "$1\n", $privacy_policy);
            $privacy_policy = strip_tags($privacy_policy);
        }

        $api_status = true;
        if ($privacy_policy) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => ['privacy_policy' => $privacy_policy],
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('guest.web_privacy_policy.index', [
                'privacy_policy' => $privacy_policy,
                'name' => $name,
            ]);
        }
    }
}
