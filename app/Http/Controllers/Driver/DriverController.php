<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Requests\Driver\DriverUpdateRequest;
use App\Http\Requests\Driver\DriverUpdatePasswordRequest;
use App\Http\Requests\driver\DriverUpdateRegisterOfficeRequest;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

use App\Models\Driver;
use App\Models\DriverRegisterDeliveryOffice;
use App\Models\DriverTask;
use App\Models\DriverTaskStatus;
use App\Models\DriverTaskReview;
use App\Models\DriverTaskReviewPublicStatus;
use App\Models\Prefecture;
use App\Models\Gender;
use App\Models\DeliveryCompany;
use App\Models\DeliveryOffice;
use App\Models\DriverRegisterDeliveryOfficeMemo;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Admin\DriverExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * ドライバーアカウント
 */
class DriverController extends Controller
{

    /**
     * 取得
     * +API機能
     *
     * @param  int  $driver_id
     * @return \Illuminate\Http\Response
     */
    public function show($driver_id = null, Request $request)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        if (!$driver_id) {
            $driver_id = $login_id;
        }

        // 自分(ログインユーザー)の情報とその他のユーザーで取得するデータを分ける。
        $select_column = [];
        if ($driver_id == $login_id) {
            $select_column = [
                "id",
                "user_type_id",
                "driver_plan_id",
                "driver_entry_status_id",
                "name_sei",
                "name_mei",
                "name_sei_kana",
                "name_mei_kana",
                "email",
                "email_verified_at",
                "gender_id",
                "birthday",
                "post_code1",
                "post_code2",
                "addr1_id",
                "addr2",
                "addr3",
                "addr4",
                "tel",
                "icon_img",
                "career",
                "introduction",
                "created_at",
                "updated_at",
                "deleted_at",
            ];
        } else {
            $select_column = [
                "id",
                "user_type_id",
                "driver_plan_id",
                "driver_entry_status_id",
                "name_sei",
                "name_mei",
                "name_sei_kana",
                "name_mei_kana",
                "gender_id",
                "birthday",
                "post_code1",
                "post_code2",
                "addr1_id",
                "icon_img",
                "career",
                "introduction",
                "created_at",
                "updated_at",
                "deleted_at",
            ];
        }

        // ドライバー情報 取得
        $driver = Driver::select($select_column)
            ->with(['joinUserType', 'joinGender', 'joinAddr1', 'joinDriverPlan'])
            ->withAvg('joinDriverReview', 'score')
            ->withCount(['joinTask', 'joinDriverReview'])
            ->where('id', $driver_id)
            ->first();


        if ($driver) {
            $driver->age = $driver->getAgeAttribute();
        }

        // logger($driver);

        $allow_path_list = [];
        if ($driver) {
            if ($driver->driver_entry_status_id == 2) {
                // アクセス許可のリスト
                $allow_path_list = config('constants.DRIVER_WAITING_ALLOW_PATH_LIST');
                $driver->allow_path_list = $allow_path_list;;
            } else {
                $driver->allow_path_list = null;
            }
        }



        // ドライバーの登録済み営業所 取得
        $register_office_list = DriverRegisterDeliveryOffice::select()
            ->with(['joinOffice'])
            ->where('driver_id', $driver_id)
            ->get();


        // logger($register_office_list->toArray());
        // exit;


        // ドライバーの稼働一覧 取得
        $task_list = DriverTask::select()
            ->with(['joinOffice', 'joinTaskStatus'])
            ->where('driver_id', $driver_id)
            ->orderBy('task_date', 'asc')
            ->get();
        // logger($task_list->toArray());
        // exit;


        // ドライバーレビュー一覧 取得
        $review_list = DriverTaskReview::select()
            ->with(['joinOffice'])
            ->where('driver_id', $driver_id)
            ->limit(10)
            ->get();
        // logger($review_list->toArray());
        // exit;

        $api_status = true;
        if ($driver) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $driver,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('driver.user.show', [
                'driver' => $driver,
                'register_office_list' => $register_office_list,
                'task_list' => $task_list,
                'review_list' => $review_list,
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $driver_id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        $type = $request->type; // 編集内容を切り替えるパラメータ。

        // ドライバー情報 取得
        $driver = Driver::where('id', $login_id)->first();

        /* フォーム検索に使うデータ */
        // 都道府県取得
        $prefecture_list = Prefecture::select()->get();
        // logger($prefecture_list->toArray());
        $gender_list = Gender::select()->get();


        if ($type === 'office') {

            // 登録中の営業所ID
            $select_register_office_list = DriverRegisterDeliveryOffice::select('delivery_office_id')->where('driver_id', $login_id)->get();

            // 登録中営業所ID 一覧
            $register_office_id_list  = [];
            foreach ($select_register_office_list as $register) {
                $register_office_id_list[] = $register->delivery_office_id;
            }

            // 会社別営業所 一覧
            $delivery_multi_list = [];
            $company_list = DeliveryCompany::get()->toArray();

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
        }


        // logger($office_list->toArray());

        return view('driver.user.edit', [
            'driver' => $driver,
            'prefecture_list' => $prefecture_list,
            'gender_list' => $gender_list,
            'delivery_multi_list' => $delivery_multi_list ?? '',
            'register_office_id_list' => $register_office_id_list ?? '',
            'type' => $type
        ]);
    }

    /**
     * 更新
     * +API機能
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(DriverUpdateRequest $request)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $type = $request->type;

        $name_sei = $request->name_sei ?? '';
        $name_mei = $request->name_mei ?? '';
        $name_sei_kana = $request->name_sei_kana ?? '';
        $name_mei_kana = $request->name_mei_kana ?? '';
        $email = $request->email ?? '';
        $password = $request->password ?? '';
        $gender_id = $request->gender_id ?? '';
        $birthday = $request->birthday ?? '';
        $post_code1 = $request->post_code1 ?? '';
        $post_code2 = $request->post_code2 ?? '';
        $addr1_id = $request->addr1_id ?? '';
        $addr2 = $request->addr2 ?? '';
        $addr3 = $request->addr3 ?? '';
        $addr4 = $request->addr4 ?? '';
        $tel = $request->tel ?? '';
        $career = $request->career ?? '';
        $introduction = $request->introduction ?? '';
        $icon_img = $request->icon_img ?? '';

        $msg = '';
        // logger($request);

        /* 更新処理 */
        $driver_update = null;
        if ($type === 'email') {
            $driver_update = Driver::where('id', $login_id)->update([
                'email' => $email
            ]);
            $msg = 'メールアドレスを更新しました。';
        } elseif ($type === 'password') {
            $driver_update = Driver::where('id', $login_id)->update([
                'password' => Hash::make($password),
            ]);
            $msg = 'パスワードを更新しました';
        } elseif ($type === 'user') {
            $driver_update = Driver::where('id', $login_id)->update([
                'name_sei' => $name_sei,
                'name_mei' => $name_mei,
                'name_sei_kana' => $name_sei_kana,
                'name_mei_kana' => $name_mei_kana,
                'gender_id' => $gender_id,
                'birthday' => $birthday,
                'post_code1' => $post_code1,
                'post_code2' => $post_code2,
                'addr1_id' => $addr1_id,
                'addr2' => $addr2,
                'addr3' => $addr3,
                'addr4' => $addr4,
                'tel' => $tel,
                'career' => $career,
                'introduction' => $introduction,
                'icon_img' => $icon_img,
            ]);
            $msg = 'ユーザー情報を更新しました';
        } elseif ($type === 'icon') {

            /* 現在設定されている画像を削除する */
            $driver = Driver::select('icon_img')->where('id', $login_id)->first();

            // 既存ファイル削除
            try {
                Storage::disk('s3')->delete($driver->icon_img);
            } catch (\InvalidArgumentException | \Aws\S3\Exception\S3Exception $e) {
                Log::info("S3 No Connection!! InvalidArgumentException or S3Exception");
                Storage::disk('public')->delete($driver->icon_img);
            }

            /**
             * 画像保存処理
             */
            // ストレージのパスを、保存した西暦/月のディレクトリ構造にする
            $date = new \DateTime();
            $y = $date->format('Y');
            $m = $date->format('m');
            $storage_path = "/driver/user_icon/{$y}/{$m}"; //保存先パス

            // ファイルの名前を生成
            $unique_name = $icon_img->hashName(); // ユニークでランダムな文字列
            $storage_date = new \DateTime();
            $storage_date = $storage_date->format('d_H_i_s_v'); // 日付
            $filename = "{$storage_date}{$unique_name}"; // 日付 ランダム 拡張子 といるフォーマットのファイル名

            // ファイルを保存
            try {
                $icon_img_path = Storage::disk('s3')->putFileAs($storage_path, $icon_img, $filename);
            } catch (\InvalidArgumentException | \Aws\S3\Exception\S3Exception $e) {
                Log::info("S3 No Connection!! InvalidArgumentException or S3Exception");
                $icon_img_path = Storage::disk('public')->putFileAs($storage_path, $icon_img, $filename);
            }

            if ($icon_img_path) {
                $driver_update = Driver::where('id', $login_id)->update([
                    'icon_img' => $icon_img_path
                ]);
            }
            $msg = 'アイコン画像を変更しました。';
        } elseif ($type === 'delete') {
            DB::beginTransaction();
            try {
                // Check for active driver tasks
                $hasActiveDriverTasks = DriverTask::where('driver_id', $login_id)
                    ->whereNotNull('driver_id')
                    ->whereIn('driver_task_status_id', [3, 10, 11])
                    ->where('is_template', 0)
                    ->exists();

                if ($hasActiveDriverTasks) {
                    // If there are active driver tasks, prevent deletion
                    return redirect()->back()->with([
                        'msg' => '稼働依頼をご確認ください。',
                    ]);
                }

                // Soft delete the driver
                $driver_update = Driver::where('id', $login_id)->update([
                    'deleted_at' => Carbon::now(),
                ]);

                // Update driver id of driver task
                // DriverTask::where('driver_id', $login_id)->update([
                //     'driver_id' => null
                // ]);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollback();
                $api_status = false;
            }
        }

        if (!$driver_update) {
            $api_status = false;
            $msg = '更新に失敗しました。';
        }

        $api_status = true;
        if ($driver_update) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                "message" => $msg
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            if ($type === 'delete') {
                Auth::logout();
                return redirect()->route('driver.login')->with([
                    'msg' => '退会申請が完了しました。',
                ]);
            } else {
                return redirect()->route('driver.user.show', ['driver_id' => $login_id])->with([
                    'msg' => $msg
                ]);
            }
        }
    }
}
