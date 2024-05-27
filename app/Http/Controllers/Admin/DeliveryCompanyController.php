<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Http\Requests\Admin\DeliveryCompanyCreateRequest;
use App\Http\Requests\Admin\DeliveryCompanyUpdateRequest;

use App\Models\DeliveryCompany;
use App\Models\DeliveryOffice;

/**
 * 配送会社
 */
class DeliveryCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 配送会社一覧 取得
        $company_list = DeliveryCompany::get();
        // logger($company_list->toArray());

        return view('admin.delivery_company.index', [
            'company_list' => $company_list
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.delivery_company.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeliveryCompanyCreateRequest $request)
    {
        $name = $request->name ?? '';
        $company_create = DeliveryCompany::create([
            'name' => $name,
        ]);

        $msg = '';
        if ($company_create) {
            $msg = "配送会社を作成しました。";
        } else {
            $msg = "配送会社を作成できませんでした!";
        }

        return redirect()->route("admin.delivery_company.index")->with([
            'msg' => $msg
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $company_id
     * @return \Illuminate\Http\Response
     */
    public function show($company_id)
    {
        // 配送会社 取得
        $company = DeliveryCompany::where('id', $company_id)
            ->with(['joinOffice'])
            ->first();
        // logger($company->toArray());
        // exit;


        // 所属している配送営業所取得
        $office_list = DeliveryOffice::where('delivery_company_id', $company_id)
            ->get();
        // logger($office_list->toArray());


        return view('admin.delivery_company.show', [
            'company' => $company,
            'office_list' => $office_list,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $company_id
     * @return \Illuminate\Http\Response
     */
    public function edit($company_id)
    {
        $company = DeliveryCompany::select()->where('id', $company_id)->first();
        return view('admin.delivery_company.edit', [
            'company' => $company
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $company_id
     * @return \Illuminate\Http\Response
     */
    public function update(DeliveryCompanyUpdateRequest $request, $company_id)
    {
        $name = $request->name ?? '';

        $company_update = DeliveryCompany::where('id', '=', $company_id)->update([
            'name' => $name,
        ]);

        $msg = '';
        if ($company_update) {
            $msg = "配送会社を更新しました。";
        } else {
            $msg = "配送会社を更新できませんでした!";
        }

        return redirect()->route("admin.delivery_company.index")->with([
            'msg' => $msg
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $company_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($company_id)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $msg = ''; // 削除メッセージ

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        try {
            $result = DeliveryCompany::where('id', '=', $company_id)->delete($company_id);

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
        return redirect()->route('admin.delivery_company.index')->with([
            'msg' => $msg,
        ]);
    }
}
