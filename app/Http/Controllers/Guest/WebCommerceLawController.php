<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WebConfigBase;
use Illuminate\Support\Facades\Route;

/**
 * 特定商取引法に基づく表記
 */
class WebCommerceLawController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->type ?? '';
        $is_html = $request->is_html ?? true;
        $commerce_law = '';
        $name = 'who';

        if ($type === 'driver') {
            $config_base =  WebConfigBase::select('commerce_law_driver')
                ->where('id', 1)
                ->first();
            $commerce_law = $config_base->commerce_law_driver;
            $name = 'ドライバー';
        } elseif ($type === 'office' || $type == '') {
            $config_base =  WebConfigBase::select('commerce_law_delivery_office')
                ->where('id', 1)
                ->first();
            $commerce_law = $config_base->commerce_law_delivery_office;
            $name = '依頼者';
        }

        if (!$is_html) {
            $commerce_law = preg_replace('/(<\/\w+>)/', "$1\n", $commerce_law);
            $commerce_law = strip_tags($commerce_law);
        }

        $api_status = true;
        if ($commerce_law) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {

            return response()->json([
                'status' => $api_status,
                'data' => ['commerce_law' => $commerce_law],
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('guest.web_commerce_law.index', [
                'commerce_law' => $commerce_law,
                'name' => $name,
            ]);
        }
    }
}
