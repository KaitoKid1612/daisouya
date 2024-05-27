<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;
use App\Http\Requests\Admin\WebConfigBaseUpdateRequest;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\DB;
use App\Models\WebConfigBase;
use App\Models\Prefecture;

class WebConfigBaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $config_base =  WebConfigBase::select()->where('id', 1)->first();
        return view('admin.web_config_base.index', [
            "config_base" => $config_base,
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
        $config_base =  WebConfigBase::select()->where('id', 1)->first();

        /* フォーム検索に使うデータ */
        // 都道府県取得
        $prefecture_list = Prefecture::select()->get();

        return view('admin.web_config_base.edit', [
            "config_base" => $config_base,
            'prefecture_list' => $prefecture_list,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(WebConfigBaseUpdateRequest $request)
    {
        $site_name = $request->site_name  ?? '';
        $company_name = $request->company_name  ?? '';
        $company_name_kana = $request->company_name_kana  ?? '';
        $post_code1 = $request->post_code1  ?? 0;
        $post_code2 = $request->post_code2  ?? 0;
        $addr1_id = $request->addr1_id  ?? '';
        $addr2 = $request->addr2  ?? '';
        $addr3 = $request->addr3  ?? '';
        $addr4 = $request->addr4  ?? '';
        $tel = $request->tel  ?? '';
        $commerce_law_driver = $request->commerce_law_driver  ?? '';
        $commerce_law_delivery_office = $request->commerce_law_delivery_office  ?? '';
        $terms_service_delivery_office = $request->terms_service_delivery_office  ?? '';
        $terms_service_driver = $request->terms_service_driver  ?? '';
        $user_guide_path_delivery_office = $request->user_guide_path_delivery_office ?? '';
        $user_guide_path_driver = $request->user_guide_path_driver ?? '';
        $privacy_policy_delivery_office = $request->privacy_policy_delivery_office  ?? '';
        $privacy_policy_driver = $request->privacy_policy_driver  ?? '';
        $transfer = $request->transfer ?? '';
        // logger($request);
        // exit;

        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $msg = '';

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        DB::beginTransaction();
        try {
            // 更新処理の記述
            /**
             * PDF保存処理
             */
            // 営業所営業所用 ユーザーガイド
            if ($user_guide_path_delivery_office) {
                $config_base = WebConfigBase::select()->where("id", 1)->first();

                // 既存のデータ削除



                try {
                    Storage::disk('s3')->delete($config_base->user_guide_path_delivery_office);
                } catch (\InvalidArgumentException | \Aws\S3\Exception\S3Exception $e) {
                    Log::info("S3 No Connection!! InvalidArgumentException or S3Exception");
                    Storage::disk('public')->delete($config_base->user_guide_path_delivery_office);
                }

                $storage_path = "/web/config_base"; //保存先パス
                $filename = "user_guide_delivery_office." . $user_guide_path_delivery_office->extension(); //ファイル名.拡張子


                // ファイルを保存
                try {
                    $user_guide_path_delivery_office_pdf = Storage::disk('s3')->putFileAs($storage_path, $user_guide_path_delivery_office, $filename);
                } catch (\InvalidArgumentException | \Aws\S3\Exception\S3Exception $e) {
                    Log::info("S3 No Connection!! InvalidArgumentException or S3Exception");
                    $user_guide_path_delivery_office_pdf = Storage::disk('public')->putFileAs($storage_path, $user_guide_path_delivery_office, $filename);
                }

                $config_base_update = WebConfigBase::where('id', 1)->update([
                    'user_guide_path_delivery_office' => $user_guide_path_delivery_office_pdf,
                ]);
            }

            // ドライバー用 ユーザーガイド
            if ($user_guide_path_driver) {
                $config_base = WebConfigBase::select()->where("id", 1)->first();

                // 既存のデータ削除
                try {
                    Storage::disk('s3')->delete($config_base->user_guide_path_driver);
                } catch (\InvalidArgumentException | \Aws\S3\Exception\S3Exception $e) {
                    Storage::disk('public')->delete($config_base->user_guide_path_driver);
                }

                $storage_path = "/web/config_base"; //保存先パス
                $filename = "user_guide_driver." . $user_guide_path_driver->extension(); //ファイル名.拡張子

                // ファイルを保存
                try {
                    $user_guide_path_driver = Storage::disk('s3')->putFileAs($storage_path, $user_guide_path_driver, $filename);
                } catch (\InvalidArgumentException | \Aws\S3\Exception\S3Exception $e) {
                    Log::info("S3 No Connection!! InvalidArgumentException or S3Exception");
                    $user_guide_path_driver = Storage::disk('public')->putFileAs($storage_path, $user_guide_path_driver, $filename);
                }

                $config_base_update = WebConfigBase::where('id', 1)->update([
                    'user_guide_path_driver' => $user_guide_path_driver,
                ]);
            }

            $config_base_update = WebConfigBase::where('id', '=', 1)->update([
                'site_name' => $site_name,
                'company_name' => $company_name,
                'company_name_kana' => $company_name_kana,
                'post_code1' => $post_code1,
                'post_code2' => $post_code2,
                'addr1_id' => $addr1_id,
                'addr2' => $addr2,
                'addr3' => $addr3,
                'addr4' => $addr4,
                'tel' => $tel,
                'commerce_law_delivery_office' => $commerce_law_delivery_office,
                'commerce_law_driver' => $commerce_law_driver,
                'terms_service_delivery_office' => $terms_service_delivery_office,
                'terms_service_driver' => $terms_service_driver,
                'privacy_policy_delivery_office' => $privacy_policy_delivery_office,
                'privacy_policy_driver' => $privacy_policy_driver,
                'transfer' => $transfer,
            ]);
            $msg = "基本設定を更新しました。";
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $log_format = LogFormat::error(
                $msg,
                '',
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
            $msg = "基本設定を更新できませんでした!";
        }

        return redirect()->route("admin.web_config_base.index")->with([
            'msg' => $msg,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return view('admin.web_config_base.destroy');
    }
}
