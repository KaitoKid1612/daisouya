<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\WebNoticeLog;
use App\Models\WebNoticeType;
use App\Models\WebLogLevel;

/**
 * Undocumented class
 */
class WebNoticeLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword ?? ''; // 検索ワード
        $search_driver_task_id = $request->driver_task_id ?? ''; // 稼働ID
        $search_notice_type_id_list = $request->notice_type_id ?? ''; // 通知種類
        $search_log_level_id_list = $request->log_level_id ?? ''; // 通知レベル
        $search_from_created_at = $request->from_created_at ?? ''; // 作成日 以上
        $search_to_created_at = $request->to_created_at ?? ''; // 作成日 以下
        $orderby = $request->orderby ?? ''; // 並び替え
        $web_notice_log_object = WebNoticeLog::select()
            ->with([]);

        // キーワードで検索
        if ($keyword) {
            $web_notice_log_object->where(function ($query) use ($keyword) {
                $query
                    ->orWhere('to_user_info', 'LIKE', "%{$keyword}%")
                    ->orWhere('user_info', 'LIKE', "%{$keyword}%")
                    ->orWhere('remote_addr', 'LIKE', "%{$keyword}%")
                    ->orWhere('http_user_agent', 'LIKE', "%{$keyword}%")
                    ->orWhere('url', 'LIKE', "%{$keyword}%");
            });
        }

        // 稼働IDで検索
        if ($search_driver_task_id) {
            $web_notice_log_object->where(function ($query) use ($search_driver_task_id) {
                $query
                    ->orWhere('task_id', $search_driver_task_id);
            });
        }

        // 通知の種類で検索
        if ($search_notice_type_id_list) {
            $web_notice_log_object->where(function ($query) use ($search_notice_type_id_list) {
                foreach ($search_notice_type_id_list as $notice_type_id) {
                    $query
                        ->orWhere('web_notice_type_id', $notice_type_id);
                }
            });
        }

        // 通知レベルで検索
        if ($search_log_level_id_list) {
            $web_notice_log_object->where(function ($query) use ($search_log_level_id_list) {
                foreach ($search_log_level_id_list as $log_level_id) {
                    $query
                        ->orWhere('web_log_level_id', $log_level_id);
                }
            });
        }

        // 作成日 範囲 絞り込み
        if ($search_from_created_at) {
            $web_notice_log_object->where('created_at', '>=', $search_from_created_at);
        }
        if ($search_to_created_at) {
            $web_notice_log_object->where('created_at', '<=', $search_to_created_at);
        }

        /* フォーム検索に使うデータ */
        $web_notice_type_list =  WebNoticeType::select()->get();
        $web_log_level_list =  WebLogLevel::select()->get();


        /* 並び替え */
        if ($orderby === 'id_desc') {
            $web_notice_log_object->orderBy('id', 'desc');
        } elseif ($orderby === 'id_asc') {
            $web_notice_log_object->orderBy('id', 'asc');
        } else {
            $web_notice_log_object->orderBy('id', 'desc');
        }

        // logger($web_notice_log_object->get()->toArray());
        $web_notice_log_list = $web_notice_log_object->paginate(50)->withQueryString();

        // 並び順
        $orderby_list = [
            ['value' => 'id_desc', 'text' => 'ID大きい順'],
            ['value' => 'id_asc', 'text' => 'ID小さい順'],
        ];

        // logger($web_notice_log_list);
        return view('admin.web_notice_log.index', [
            'web_notice_log_list' => $web_notice_log_list,
            'web_notice_type_list' => $web_notice_type_list,
            'web_log_level_list' => $web_log_level_list,
            'orderby_list' => $orderby_list,
        ]);
    }
}
