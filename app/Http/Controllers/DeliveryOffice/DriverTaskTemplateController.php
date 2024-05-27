<?php

namespace App\Http\Controllers\DeliveryOffice;

use App\Http\Controllers\Controller;
use App\Models\DriverTask;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverTaskTemplateController extends Controller
{
    public function index(Request $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $order_by = $request->orderby;

        $task_templates = [];
        switch ($order_by) {
            case 1:
                $task_templates = DriverTask::where('is_template', true)->where('delivery_office_id', $login_id)->whereNotNull('driver_id')->get();
                break;
            case 2:
                $task_templates = DriverTask::where('is_template', true)->where('delivery_office_id', $login_id)->orderBy('task_date', 'ASC')->get();
                break;
            case 3:
                $task_templates = DriverTask::where('is_template', true)->where('delivery_office_id', $login_id)->orderBy('created_at', 'DESC')->get();
                break;
            default:
                $task_templates = DriverTask::where('is_template', true)->where('delivery_office_id', $login_id)->get();
        }

        $api_status = !!$task_templates;
    
        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $task_templates
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('delivery_office.driver_task_template.index', [
                'task_templates' => $task_templates,
            ]);
        }
    }

    public function delete(Request $request)
    {
        try {
            $template_id = $request->id;

            DriverTask::where('is_template', true)->findOrFail($template_id)->delete();
            $task_templates = DriverTask::where('is_template', true)->get();
    
            $msg = '削除に成功しました。';

            if (Route::is('api.*')) {
                return response()->json([
                    'status' => true,
                    'data' => $task_templates,
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return redirect()->route('delivery_office.driver_task_template.index', [
                    'task_templates' => $task_templates,
                ]) ->with('msg', $msg);
            }
    
        } catch (\Exception $e) {
            $msg = '削除に失敗しました。';
            
            if (Route::is('api.*')) {
                return response()->json([
                    'status' => false,
                    'data' => []
                ], 500, [], JSON_UNESCAPED_UNICODE);
            } else {
                return redirect()->route('delivery_office.driver_task_template.index', [
                    'task_templates' => $task_templates,
                ]) ->with('msg', $msg);
            }
        }
    }
}