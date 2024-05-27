<?php

namespace App\Http\Controllers\DeliveryOffice;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\DriverTask;
use App\Models\WebConfigBase;
use PDF;

/**
 * 領収書PDF
 */
class PdfReceiptController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        $driver_task_id = $request->driver_task_id ?? '';

        return view('delivery_office.pdf_receipt.create', [
            "office" => $login_user,
        ]);
    }

    /**
     * 領収書 PDF
     * +API機能
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $api_status = true;

        $type = $request->type;
        $driver_task_id = $request->driver_task_id ?? '';

        $config_base =  WebConfigBase::select()->where('id', 1)->first();

        $task = DriverTask::select()->where('id', $driver_task_id)->with(
            ['joinOffice', 'joinDriver', 'joinTaskStatus', 'joinDriverReview', 'joinDeliveryOfficeReview', 'joinTaskRefundStatus', 'joinAddr1']
        )->where([
            ['id', $driver_task_id],
            ['delivery_office_id', $login_id],
            ['driver_task_status_id', 4],
        ])->first();

        if ($task) {
            $task_date = new \DateTime($task->task_date);
            $task->task_date = $task_date->format("Y-m-d");

            // HTMLプレビュー,PDFプレビュー,PDFダウンロード分岐
            if ($type === 'html_preview') {
                return view('delivery_office.pdf_receipt.pdf', [
                    'task' => $task,
                    'config' => $config_base
                ]);
            } elseif ($type === 'pdf_preview') {
                $pdf = PDF::loadView('delivery_office.pdf_receipt.pdf', [
                    'task' => $task,
                    'config' => $config_base
                ]);
                $pdf->setPaper('A4');
                return $pdf->stream("task_receipt_id{$task->id}.pdf");
            } elseif ($type === 'pdf_download') {
                $pdf = PDF::loadView('delivery_office.pdf_receipt.pdf', [
                    'task' => $task,
                    'config' => $config_base
                ]);
                $pdf->setPaper('A4');

                $pdf_data = $pdf->download("task_receipt_id{$task->id}.pdf");

                if (Route::is('api.*')) {
                    return response($pdf_data)->header('Content-Type', 'application/pdf')->header('Content-Disposition', "attachment; filename=task_receipt_id{$task->id}.pdf");
                } else {
                    return $pdf_data;
                }
            }
        } else {
            $api_status = false;
            $msg = 'PDF作成できませんでした。';

            if (Route::is('api.*')) {
                return response()->json([
                    'status' => $api_status,
                    "message" => $msg
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return redirect()->route("delivery_office.pdf_receipt.create", [
                    'driver_task_id' => $driver_task_id,
                ])->with([
                    'msg' => $msg
                ]);
            }
        }
    }
}
