<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\DriverTaskReviewCreateRequest;
use App\Http\Requests\Admin\DriverTaskReviewUpdateRequest;

use App\Models\DriverTaskReview;

use App\Models\DriverTaskReviewPublicStatus;
use App\Models\DriverTask;

class DriverTaskReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword ?? ''; // 検索ワード
        $search_from_score = $request->from_score ?? ''; //評価点 以上
        $search_to_score = $request->to_score ?? ''; //評価点 以下
        $search_from_task_date = $request->from_task_date ?? ''; // タスク日付 以上
        $search_to_task_date = $request->to_task_date ?? ''; //タスク日付 以下
        $search_from_created_at = $request->from_created_at ?? ''; // 作成日 以上
        $search_to_created_at = $request->to_created_at ?? ''; // 作成日 以下
        $search_from_updated_at = $request->from_updated_at ?? ''; // 更新日 以上
        $search_to_updated_at = $request->to_updated_at ?? ''; // 更新日 以下
        $search_driver_id = $request->driver_id ?? ''; // ドライバーID
        $search_delivery_office_id = $request->delivery_office_id ?? ''; // 営業所ID
        $orderby = $request->orderby ?? ''; // 並び替え


        $review_list_object = DriverTaskReview::select()
            ->with(['joinOffice', 'joinTask', 'joinDriver', 'joinPublicStatus']);


        // キーワードで検索
        if (isset($request->keyword) && $request->keyword != '') {

            $review_list_object->Where(function ($query) use ($keyword) {
                $query->orWhere('text', 'LIKE', "%{$keyword}%");
            });
        }

        // 稼働日 絞り込み
        if ($search_from_task_date) {
            $review_list_object->whereHas('get_task', function ($query) use ($search_from_task_date) {
                $query->where('task_date', '>=', $search_from_task_date);
            });
        }
        if ($search_to_task_date) {
            $review_list_object->whereHas('get_task', function ($query) use ($search_to_task_date) {
                $query->where('task_date', '<=', $search_to_task_date);
            });
        }


        // 評価点 範囲 絞り込み
        if (isset($request->from_score)) {
            $review_list_object->where('score', '>=', $search_from_score);
        }
        if (isset($request->to_score)) {
            $review_list_object->where('score', '<=', $search_to_score);
        }

        // 作成日 範囲 絞り込み
        if (isset($request->from_created_at)) {
            $review_list_object->where('created_at', '>=', $search_from_created_at);
        }
        if (isset($request->to_created_at)) {
            $review_list_object->where('created_at', '<=', $search_to_created_at);
        }

        // 更新日 範囲 絞り込み
        if (isset($request->from_updated_at)) {
            $review_list_object->where('updated_at', '>=', $search_from_updated_at);
        }
        if (isset($request->to_updated_at)) {
            $review_list_object->where('updated_at', '<=', $search_to_updated_at);
        }

        // ドライバー 絞り込み
        if ($search_driver_id) {
            $review_list_object->where('driver_id', $search_driver_id);
        }

        // 営業所 絞り込み
        if ($search_delivery_office_id) {
            $review_list_object->where('delivery_office_id', $search_delivery_office_id);
        }

        /* 並び替え */
        if ($orderby === 'id_desc') {
            $review_list_object->orderBy('id', 'desc');
        } elseif ($orderby === 'id_asc') {
            $review_list_object->orderBy('id', 'asc');
        } elseif ($orderby === 'score_desc') {
            $review_list_object->orderBy('score', 'desc');
        } elseif ($orderby === 'score_asc') {
            $review_list_object->orderBy('score', 'asc');
        } else {
            $review_list_object->orderBy('id', 'desc');
        }

        $review_list = $review_list_object->paginate(50)->withQueryString();
        // $review_list = $review_list_object->get();
        // logger($review_list->toArray());
        // exit;

        /* フォーム検索に使うデータ */
        // 都道府県取得

        $orderby_list = [
            ['value' => 'id_desc', 'text' => 'ID大きい順',],
            ['value' => 'id_asc', 'text' => 'ID小さい順',],
            ['value' => 'score_desc', 'text' => '評価高い順',],
            ['value' => 'score_asc', 'text' => '評価低い順',],
        ];



        return view('admin.driver_task_review.index', [
            'review_list' => $review_list,
            'orderby_list' => $orderby_list,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /* フォームで使うデータ */
        // 公開ステータス
        $driver_task_review_public_status_list = DriverTaskReviewPublicStatus::select()->get();

        return view('admin.driver_task_review.create', [
            'driver_task_review_public_status_list' => $driver_task_review_public_status_list
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DriverTaskReviewCreateRequest $request)
    {
        $score = $request->score ?? '';
        $title = $request->title ?? '';
        $text = $request->text ?? '';
        $driver_task_id = $request->driver_task_id ?? '';
        $driver_task_review_public_status_id = $request->driver_task_review_public_status ?? '';

        $task_select = DriverTask::select()->where('id', $driver_task_id)->first();
        $driver_id = $task_select->driver_id ?? '';
        $delivery_office_id = $task_select->delivery_office_id ?? '';

        $review_create = DriverTaskReview::create([
            'score' => $score,
            'title' => $title,
            'text' => $text,
            'driver_id' => $driver_id,
            'driver_task_id' => $driver_task_id,
            'delivery_office_id' => $delivery_office_id,
            'driver_task_review_public_status_id' => $driver_task_review_public_status_id,
        ]);

        $msg = '';
        if ($review_create) {
            $msg = "レビューを作成しました。";
        } else {
            $msg = "レビューを作成できませんでした!";
        }

        return redirect()->route("admin.driver_task_review.index")->with([
            'msg' => $msg
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $review_id
     * @return \Illuminate\Http\Response
     */
    public function show($review_id)
    {
        $review = DriverTaskReview::select()
            ->with(['joinOffice', 'joinTask', 'joinDriver', 'joinPublicStatus'])
            ->where('id', $review_id)
            ->first();
        // logger($review->toArray());
        // exit;
        return view('admin.driver_task_review.show', [
            'review' => $review
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $review_id
     * @return \Illuminate\Http\Response
     */
    public function edit($review_id)
    {
        $review = DriverTaskReview::where('id', $review_id)->first();

        /* フォームで使うデータ */
        // 公開ステータス
        $driver_task_review_public_status_list = DriverTaskReviewPublicStatus::select()->get();

        return view('admin.driver_task_review.edit', [
            'review' => $review,
            'driver_task_review_public_status_list' => $driver_task_review_public_status_list
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $review_id
     * @return \Illuminate\Http\Response
     */
    public function update(DriverTaskReviewUpdateRequest $request, $review_id)
    {
        $score = $request->score ?? '';
        $title = $request->title ?? '';
        $text = $request->text ?? '';
        $driver_task_id = $request->driver_task_id ?? '';
        $driver_task_review_public_status_id = $request->driver_task_review_public_status ?? '';

        $task_select = DriverTask::select()->where('id', $driver_task_id)->first();
        $driver_id = $task_select->driver_id ?? '';
        $delivery_office_id = $task_select->delivery_office_id ?? '';

        $review_update = DriverTaskReview::where([
            ['id', '=', $review_id],
        ])->update([
            'score' => $score,
            'title' => $title,
            'text' => $text,
            'driver_id' => $driver_id,
            'driver_task_id' => $driver_task_id,
            'delivery_office_id' => $delivery_office_id,
            'driver_task_review_public_status_id' => $driver_task_review_public_status_id,
        ]);

        $msg = '';
        if ($review_update) {
            $msg = "レビューを更新しました。";
        } else {
            $msg = "レビューを更新できませんでした!";
        }

        return redirect()->route("admin.driver_task_review.show", [
            'review_id' => $review_id
        ])->with([
            'msg' => $msg
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $review_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($review_id)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $msg = ''; // 削除メッセージ

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        try {
            $result = DriverTaskReview::where('id', '=', $review_id)->delete($review_id);

            if ($result) {
                $msg = '削除に成功';
            } else {
                $msg = '削除されませんでした。';
            }
        } catch (\Throwable $e) {
            $msg .= '削除に失敗';

            $log_format = LogFormat::error(
                $msg,
                $login_user->joinUserType->name ?? '',
                $login_id ?? '',
                $remote_addr ?? '',
                $http_user_agent ?? '',
                $url ?? '',
                $file_path ?? '',
                $e->getCode(),
                $e->getFile(),
                $e->getLine(),
                mb_substr($e->__toString(), 0, 1200) . "......\nLog End",
            );
            Log::error($log_format);
        }

        return redirect()->route('admin.driver_task_review.index')->with([
            'msg' => $msg,
        ]);
        return view('admin.driver_task_review.destroy');
    }
}
