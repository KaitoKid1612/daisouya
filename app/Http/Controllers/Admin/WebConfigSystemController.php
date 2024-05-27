<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\WebConfigSystemUpdateRequest;
use App\Models\Admin;

use App\Models\WebConfigSystem;

/**
 * システム設定
 */
class WebConfigSystemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $config_system = WebConfigSystem::select()->where('id', 1)->first();

        return view('admin.web_config_system.index', [
            "config_system" => $config_system,
        ]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $config_system = WebConfigSystem::select()->where('id', 1)->first();

        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $admin = Admin::select()->where('id', $login_id)->first();

        // アクセス権限をかける。開発者と管理者のみアクセス可能
        if (!in_array($admin->admin_permission_group_id, [1, 2], true)) {
            return redirect()->route('admin.web_config_system.index')->with([
                'msg' => 'アクセス権限がありません。'
            ]);
        }

        $config_system = WebConfigSystem::select()->where('id', 1)->first();

        return view('admin.web_config_system.edit', [
            "config_system" => $config_system,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(WebConfigSystemUpdateRequest $request)
    {
        $email_notice = $request->email_notice;
        $email_from = $request->email_from;
        $email_reply_to = $request->email_reply_to;
        $email_no_reply = $request->email_no_reply;
        $create_task_time_limit_from = $request->create_task_time_limit_from;
        $create_task_time_limit_to = $request->create_task_time_limit_to;
        $create_task_hour_limit = $request->create_task_hour_limit;
        $register_request_token_time_limit = $request->register_request_token_time_limit;
        $default_price = $request->default_price;
        $default_emergency_price = $request->default_emergency_price;
        $default_tax_rate = $request->default_tax_rate;
        $default_stripe_payment_fee_rate = $request->default_stripe_payment_fee_rate;
        $soon_price_time_limit_from = $request->soon_price_time_limit_from;
        $soon_price_time_limit_to = $request->soon_price_time_limit_to;

        $config_system_update = WebConfigSystem::where('id', 1)->update([
            'email_notice' => $email_notice,
            'email_from' => $email_from,
            'email_reply_to' => $email_reply_to,
            'email_no_reply' => $email_no_reply,
            'create_task_time_limit_from' => $create_task_time_limit_from,
            'create_task_time_limit_to' => $create_task_time_limit_to,
            'create_task_hour_limit' => $create_task_hour_limit,
            'register_request_token_time_limit' => $register_request_token_time_limit,
            'default_price' => $default_price,
            'default_emergency_price' => $default_emergency_price,
            'default_tax_rate' => $default_tax_rate,
            'default_stripe_payment_fee_rate' => $default_stripe_payment_fee_rate,
            'soon_price_time_limit_from' => $soon_price_time_limit_from,
            'soon_price_time_limit_to' => $soon_price_time_limit_to,
        ]);
        $msg = '';
        if ($config_system_update) {
            $msg = "システム設定を更新しました。";
        } else {
            $msg = "システム設定を更新できませんでした!";
        }

        return redirect()->route("admin.web_config_system.index")->with([
            'msg' => $msg
        ]);
    }
}
