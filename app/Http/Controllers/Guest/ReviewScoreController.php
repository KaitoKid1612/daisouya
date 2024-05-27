<?php

namespace App\Http\Controllers\guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * 
 */
class ReviewScoreController extends Controller
{
    /**
     * 取得
     * * +API機能
     */
    public function index(Request $request)
    {
        $type = $request->type;

        $data = [];
        if ($type === "standard") {
            for ($i = 1; $i < 6; $i++) {
                $data[] = ["value" => $i, "text" => "★ {$i}", "star" => str_repeat('★', $i)];
            }
        }
        else if ($type === "from_avg") {
            $data[] = ["value" => "", "text" => "指定なし"];
            for ($i = 1; $i < 5; $i++) {
                $data[] = ["value" => $i, "text" => "★ {$i} 以上", "star" => str_repeat('★', $i)];
            }
        } else if ($type === "to_avg") {
            $data[] = ["value" => "", "text" => "指定なし"];
            for ($i = 2; $i < 6; $i++) {
                $data[] = ["value" => $i, "text" => "★ {$i} 以下"];
            }
        }

        $api_status = true;
        if ($data) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $data,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
