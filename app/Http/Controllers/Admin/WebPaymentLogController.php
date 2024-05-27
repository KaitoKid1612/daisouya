<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\WebPaymentLog;
use App\Models\WebPaymentLogStatus;

/**
 * Undocumented class
 */
class WebPaymentLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword ?? ''; // 検索ワード
        $search_driver_task_id = $request->driver_task_id ?? ''; // 検索ワード
        $search_web_payment_log_status_list = $request->payment_log_status ?? ''; // 決済ステータスリスト
        $search_from_date = $request->from_date ?? ''; // 決済日 以上
        $search_to_date = $request->to_date ?? ''; // 決済日 以下
        $orderby = $request->orderby ?? ''; // 並び替え


        $web_payment_log_object = WebPaymentLog::select()
            ->with([
                'joinPaymentLogStatus',
                'joinPaymentReason',
                'joinPayUserType',
            ]);

        // キーワードで検索
        if ($keyword) {
            $web_payment_log_object->where(function ($query) use ($keyword) {
                $query
                    ->orWhere('message', 'LIKE', "%{$keyword}%");
            });
        }

        // 稼働IDで検索
        if ($search_driver_task_id) {
            $web_payment_log_object->where(function ($query) use ($search_driver_task_id) {
                $query
                    ->orWhere('driver_task_id', $search_driver_task_id);
            });
        }


        // 決済日 範囲 絞り込み
        if ($search_from_date) {
            $web_payment_log_object->where('date', '>=', $search_from_date);
        }
        if ($search_to_date) {
            $web_payment_log_object->where('date', '<=', $search_to_date);
        }

        // ステータス 絞り込み
        if ($search_web_payment_log_status_list) {
            $web_payment_log_object->where(function ($query) use ($search_web_payment_log_status_list) {
                foreach ($search_web_payment_log_status_list as $status_id) {
                    $query->orWhere('web_payment_log_status_id', $status_id);
                }
            });
        }

        /* フォーム検索に使うデータ */
        $web_payment_log_status_list =  WebPaymentLogStatus::select()->get();


        /* 並び替え */
        if ($orderby === 'id_desc') {
            $web_payment_log_object->orderBy('id', 'desc');
        } elseif ($orderby === 'id_asc') {
            $web_payment_log_object->orderBy('id', 'asc');
        } elseif ($orderby === 'date_desc') {
            $web_payment_log_object->orderBy('date', 'desc');
        } elseif ($orderby === 'date_asc') {
            $web_payment_log_object->orderBy('date', 'asc');
        } else {
            $web_payment_log_object->orderBy('id', 'desc');
        }

        // logger($web_payment_log_object->get()->toArray());
        $web_payment_log_list = $web_payment_log_object->paginate(50)->withQueryString();

        // 並び順
        $orderby_list = [
            ['value' => 'id_desc', 'text' => 'ID大きい順'],
            ['value' => 'id_asc', 'text' => 'ID小さい順'],
            ['value' => 'date_desc', 'text' => '決済日 降順'],
            ['value' => 'date_asc', 'text' => '決済日 昇順'],
        ];

        // logger($web_payment_log_list);
        return view('admin.web_payment_log.index', [
            'web_payment_log_list' => $web_payment_log_list,
            'web_payment_log_status_list' => $web_payment_log_status_list,
            'orderby_list' => $orderby_list,
        ]);
    }
}
