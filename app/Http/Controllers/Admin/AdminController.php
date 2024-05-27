<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;

use Illuminate\Http\Request;
use App\Http\Requests\Admin\AdminCreateRequest;
use App\Http\Requests\Admin\AdminUpdateRequest;
use App\Models\Admin;
use App\Models\AdminPermissionGroup;



class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $admin = Admin::select()->where('id', $login_id)->first();
        // アクセス権限をかける。開発者と管理者のみアクセス可能
        if (!in_array($admin->admin_permission_group_id, [1, 2], true)) {
            return redirect()->route('admin.dashboard.index')->with([
                'msg' => 'アクセス権限がありません。'
            ]);
        }

        $admin_list = Admin::select()
            ->with(['joinAdminPermissionGroup'])
            ->get();

        // logger($admin_list->toArray());

        return view('admin.user.index', [
            'admin_list' => $admin_list,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $login_admin = Admin::select()->where('id', $login_id)->first();
        // アクセス権限をかける。開発者と管理者のみアクセス可能
        if (!in_array($login_admin->admin_permission_group_id, [1, 2], true)) {
            return redirect()->route('admin.dashboard.index')->with([
                'msg' => 'アクセス権限がありません。'
            ]);
        }

        /* フォームで使うデータ */
        $admin_permission_group_list =  AdminPermissionGroup::select()->get();

        return view('admin.user.create', [
            'admin_permission_group_list' => $admin_permission_group_list,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminCreateRequest $request)
    {
        $name = $request->name ?? '';
        $email = $request->email ?? '';
        $password = $request->password ?? '';
        $admin_permission_group_id = $request->admin_permission_group_id ?? '';

        // logger($request);
        // exit;


        // logger($request);
        // exit;

        $admin_create = Admin::create([
            'user_type_id' => 1,
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'admin_permission_group_id' => $admin_permission_group_id,
        ]);

        $msg = '';
        if ($admin_create) {
            $msg = "管理者ユーザーを作成しました。";
        } else {
            $msg = "管理者ユーザーを作成できませんでした!";
        }

        return redirect()->route('admin.user.index')->with([
            'msg' => $msg
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $admin_id
     * @return \Illuminate\Http\Response
     */
    public function show($admin_id)
    {
        return view('admin.user.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $admin_id
     * @return \Illuminate\Http\Response
     */
    public function edit($admin_id)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $login_admin = Admin::select()->where('id', $login_id)->first();
        // アクセス権限をかける。開発者と管理者のみアクセス可能
        if (!in_array($login_admin->admin_permission_group_id, [1, 2], true)) {
            return redirect()->route('admin.dashboard.index')->with([
                'msg' => 'アクセス権限がありません。'
            ]);
        }

        /* フォームで使うデータ */
        $admin = Admin::select()->where('id', $admin_id)->first();
        $admin_permission_group_list =  AdminPermissionGroup::select()->get();


        return view('admin.user.edit', [
            'admin' => $admin,
            'admin_permission_group_list' => $admin_permission_group_list,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $admin_id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminUpdateRequest $request, $admin_id)
    {
        $name = $request->name ?? '';
        $email = $request->email ?? '';
        $password = $request->password ?? '';
        $admin_permission_group_id = $request->admin_permission_group_id ?? '';
        // logger($request);
        // exit;

        $admin = Admin::where('id', $admin_id)->first();

        $admin->name = $name;
        $admin->email = $email;
        $admin->admin_permission_group_id = $admin_permission_group_id;

        // パスワードが入力された場合のみ更新
        if ($password) {
            $admin->password = Hash::make($password);
        }
        $admin_update = $admin->save();

        $msg = '';
        if ($admin_update) {
            $msg = "管理者ユーザーを更新しました。";
        } else {
            $msg = "管理者ユーザーを更新できませんでした!";
        }

        return redirect()->route("admin.user.index")->with([
            'msg' => $msg
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $admin_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($admin_id)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $msg = ''; // 削除メッセージ

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        try {
            $result = Admin::where('id', '=', $admin_id)->delete($admin_id);
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



        return redirect()->route('admin.user.index')->with([
            'msg' => $msg,
        ]);
        // return view('admin.user.destroy');
    }
}
