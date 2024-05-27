<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebConfigBase;
use Illuminate\Support\Facades\Route;

/**
 * 利用規約
 */
class WebTermsServiceController extends Controller
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
        $terms_service = '';
        $name = 'who';

        if ($type == 'driver') {
            $config_base =  WebConfigBase::select('terms_service_driver')
                ->where('id', 1)
                ->first();
            $terms_service = $config_base->terms_service_driver;
            $name = 'ドライバー';
        } elseif ($type === 'office' || $type == '') {
            $config_base =  WebConfigBase::select('terms_service_delivery_office')
                ->where('id', 1)
                ->first();
            $terms_service = $config_base->terms_service_delivery_office;
            $name = '依頼者';
        }
        // logger($terms_service);

        if (!$is_html) {
            $terms_service = preg_replace('/(<\/\w+>)/', "$1\n", $terms_service);
            $terms_service = strip_tags($terms_service);
        }

        $api_status = true;
        if ($terms_service) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {

            return response()->json([
                'status' => $api_status,
                'data' => ['terms_service' => $terms_service],
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('guest.web_terms_service.index', [
                'terms_service' => $terms_service,
                'name' => $name,
            ]);
        }
    }
}
