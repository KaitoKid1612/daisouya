<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\WebContactUpdateRequest;

use App\Models\WebContact;
use App\Models\WebContactStatus;
use App\Models\UserType;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;
use Illuminate\Support\Facades\Auth;

class WebContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword ?? ''; // 検索ワード
        $search_web_contact_status_id_list = $request->web_contact_status_id ?? ''; // ステータスリスト
        $search_user_type_id_list = $request->user_type_id ?? ''; // ユーザータイプリスト
        $search_from_created_at = $request->from_created_at ?? ''; // 作成日 以上
        $search_to_created_at = $request->to_created_at ?? ''; // 作成日 以下
        $search_from_updated_at = $request->from_updated_at ?? ''; // 更新日 以上
        $search_to_updated_at = $request->to_updated_at ?? ''; // 更新日 以下
        $orderby = $request->orderby ?? ''; // 並び替え


        $web_contact_list_object = WebContact::select()
            ->with(['get_web_contact_type', 'joinUserType']);

        // キーワードで検索
        if ($keyword) {
            $web_contact_list_object->where(function ($query) use ($keyword) {
                $query
                    ->orWhere('name_sei', 'LIKE', "%{$keyword}%")
                    ->orWhere('name_mei', 'LIKE', "%{$keyword}%")
                    ->orWhere('name_sei_kana', 'LIKE', "%{$keyword}%")
                    ->orWhere('name_mei_kana', 'LIKE', "%{$keyword}%")
                    ->orWhere('email', 'LIKE', "%{$keyword}%")
                    ->orWhere('tel', 'LIKE', "%{$keyword}%")
                    ->orWhere('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('text', 'LIKE', "%{$keyword}%");
            });
        }

        // ステータス 絞り込み
        if ($search_web_contact_status_id_list) {
            $web_contact_list_object->where(function ($query) use ($search_web_contact_status_id_list) {
                foreach ($search_web_contact_status_id_list as $status_id) {
                    $query->orWhere('web_contact_status_id', $status_id);
                }
            });
        }

        // ユーザータイプ 絞り込み
        if ($search_user_type_id_list) {
            $web_contact_list_object->where(function ($query) use ($search_user_type_id_list) {
                foreach ($search_user_type_id_list as $type_id) {
                    $query->orWhere('user_type_id', $type_id);
                }
            });
        }

        /* 並び替え */
        if ($orderby === 'id_desc') {
            $web_contact_list_object->orderBy('id', 'desc');
        } elseif ($orderby === 'id_asc') {
            $web_contact_list_object->orderBy('id', 'asc');
        } else {
            $web_contact_list_object->orderBy('id', 'desc');
        }

        // logger($web_contact_list_object->get()->toArray());
        // exit;

        // データ取得
        $web_contact_list = $web_contact_list_object->paginate(50)->withQueryString();

        /* フォーム検索に使うデータ */
        // ステータス一覧
        $web_contact_status_list = WebContactStatus::select()->get();
        $user_type_list = UserType::select()
            ->where('id', '!=', 1)
            ->get();


        // 並び順
        $orderby_list = [
            ['value' => 'id_desc', 'text' => 'ID大きい順'],
            ['value' => 'id_asc', 'text' => 'ID小さい順'],
        ];

        return view('admin.web_contact.index', [
            'web_contact_list' => $web_contact_list,
            'web_contact_status_list' => $web_contact_status_list,
            'user_type_list' => $user_type_list,
            'orderby_list' => $orderby_list,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $contact_id
     * @return \Illuminate\Http\Response
     */
    public function show($contact_id)
    {
        $web_contact = WebContact::select()
            ->with(['get_web_contact_type', 'joinUserType'])
            ->where('id', $contact_id)
            ->first();

        return view('admin.web_contact.show', [
            'web_contact' => $web_contact,
        ]);
    }


    /**
     * ステータス変更
     *
     * @param  int  $contact_id
     * @return \Illuminate\Http\Response
     */
    public function edit($contact_id)
    {
        $web_contact = WebContact::select()
            ->with(['get_web_contact_type', 'joinUserType'])
            ->where('id', $contact_id)
            ->first();

        /* フォーム検索に使うデータ */
        // ステータス一覧
        $web_contact_status_list = WebContactStatus::select()->get();

        return view('admin.web_contact.edit', [
            'web_contact' => $web_contact,
            'web_contact_status_list' => $web_contact_status_list,
        ]);
    }

    /**
     * ステータス変更 処理
     *
     * @param  int  $contact_id
     * @return \Illuminate\Http\Response
     */
    public function update($contact_id, WebContactUpdateRequest $request)
    {
        $web_contact_status_id = $request->web_contact_status_id ?? '';

        $web_contact_update = WebContact::where('id', $contact_id)->update([
            'web_contact_status_id' => $web_contact_status_id,
        ]);

        $msg = '';
        if ($web_contact_update) {
            $msg = "お問い合わせステータスを更新しました。";
        } else {
            $msg = "お問い合わせステータスを更新できませんでした!";
        }

        return redirect()->route("admin.web_contact.show", ['contact_id' => $contact_id])->with([
            'msg' => $msg
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $contact_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($contact_id)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $msg = ''; // 削除メッセージ

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        try {
            $result = WebContact::where('id', $contact_id)->delete($contact_id);

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

        return redirect()->route('admin.web_contact.index')->with([
            'msg' => $msg,
        ]);
    }
}
