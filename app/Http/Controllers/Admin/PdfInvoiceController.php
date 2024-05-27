<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebConfigBase;
use Illuminate\Http\Request;
use App\Models\DeliveryCompany;
use App\Models\DeliveryOffice;
use App\Models\DriverTask;
use App\Models\DriverTaskStatus;
use App\Models\Prefecture;

use PDF;

class PdfInvoiceController extends Controller
{
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /* フォーム検索に使うデータ */
        $task_status_list = DriverTaskStatus::get(); // 稼働ステータス一覧

        // 会社別営業所 一覧
        $delivery_multi_list = [];
        $company_list = DeliveryCompany::get()->toArray();
        $company_list[] = [
            'id' => null,
            'name' => '請負業者',
            'created_at' => '',
            'updated_at' => '',
        ];

        $count = 0;
        foreach ($company_list as $company) {
            $office_list = DeliveryOffice::with('joinCompany')
                ->where('delivery_company_id', $company['id'])
                ->orderBy('delivery_company_id', 'asc')
                ->get()
                ->toArray();
            $delivery_multi_list[$count]['office_list'] = $office_list;
            $delivery_multi_list[$count]['company'] = $company;
            $count++;
        }
        // 都道府県取得
        $prefecture_list = Prefecture::select()->get();
        // 基本設定
        $config_base = WebConfigBase::where('id', 1)->first();
        // exit;
        return view('admin.pdf_invoice.create', [
            'delivery_multi_list' => $delivery_multi_list,
            'task_status_list' => $task_status_list,
            "config_base" => $config_base,
            'prefecture_list' => $prefecture_list,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $type = $request->type;

        $task_status_id_list = $request->task_status_id ?? ''; // 稼働ステータス
        $from_task_date = $request->from_task_date ?? ''; //タスク日付 以上
        $to_task_date = $request->to_task_date ?? ''; //タスク日付 以下
        $delivery_office_id_list = $request->delivery_office_id ?? ''; // 営業所ID

        $unit_price = $request->unit_price ?? 0; // 稼働単価
        $product_list = $request->product ?? ""; // 追加明細

        $data = $request; // 加工するとき$requestを汚したくないので代入。
        $total_price_task = 0; // 稼働の合計金額

        if (($task_status_id_list || $from_task_date || $to_task_date) && $delivery_office_id_list) {
            $task_list_object = DriverTask::select()
                ->with(['joinDriver', 'joinOffice', 'joinTaskStatus']);


            // 稼働日 範囲 絞り込み
            if (isset($request->from_task_date)) {
                $task_list_object->where('task_date', '>=', $from_task_date);
            }
            if (isset($request->to_task_date)) {
                $task_list_object->where('task_date', '<=', $to_task_date);
            }

            // 稼働ステータス絞り込み
            if (isset($request->task_status_id)) {
                $task_list_object->where(function ($query) use ($task_status_id_list) {
                    foreach ($task_status_id_list as $status_id) {
                        $query->orWhere('driver_task_status_id', '=', $status_id);
                    }
                });
            }

            // 営業所絞り込み
            if (isset($request->delivery_office_id)) {
                $task_list_object->where(function ($query) use ($delivery_office_id_list) {
                    foreach ($delivery_office_id_list as $office_id) {
                        $query->orWhere('delivery_office_id', '=', $office_id);
                    }
                });
            }

            /* お金の計算 */
            if (isset($task_list_object)) {
                $task_list = $task_list_object->get();
                $total_price_task =  $unit_price * $task_list_object->count();
            }
        }


        $total_price_product = 0; // 追加明細の合計金額
        // 追加明細の計算
        if ($product_list) {
            $product_list = array_map(function ($product) {
                if ($product['unit_price'] && $product['quantity']) {
                    $product['system_price'] = (int)$product['unit_price'] * (int)$product['quantity']; // 単価 * 数
                } else {
                    $product['system_price'] = 0;
                }
                return $product;
            }, $product_list);

            foreach ($product_list as $product) {
                if ($product) {
                    $total_price_product += (int)$product['system_price'];
                }
            }
        }

        $total_tax =  ($total_price_task + $total_price_product) * 0.1; // 税合計
        $data->total_tax = $total_tax;
        $data->total_price_tax = round($total_price_task + $total_price_product + $total_tax); // 税込請求額 四捨五入

        // HTMLプレビュー,PDFプレビュー,PDFダウンロード分岐
        if ($type === 'html_preview') {
            return view('admin.pdf_invoice.pdf', [
                'data' => $data,
                'task_list' => $task_list ?? '',
                'product_list' => $product_list ?? '',
            ]);
        } elseif ($type === 'pdf_preview') {
            $pdf = PDF::loadView('admin.pdf_invoice.pdf', [
                'data' => $data,
                'task_list' => $task_list ?? '',
                'product_list' => $product_list ?? '',
            ]);
            $pdf->setPaper('A4');
            return $pdf->stream();
        } elseif ($type === 'pdf') {
            $pdf = PDF::loadView('admin.pdf_invoice.pdf', [
                'data' => $data,
                'task_list' => $task_list ?? '',
                'product_list' => $product_list ?? '',
            ]);
            $pdf->setPaper('A4');
            return $pdf->download();
        }
    }
}
