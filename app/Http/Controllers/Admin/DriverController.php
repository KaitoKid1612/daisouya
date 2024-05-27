<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;

use Illuminate\Http\Request;
use App\Http\Requests\Admin\DriverCreateRequest;
use App\Http\Requests\Admin\DriverUpdateRequest;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Mail;
use App\Mail\DriverRegisterSendDriverMail;

use App\Models\Driver;
use App\Models\DriverRegisterDeliveryOffice;
use App\Models\DriverTask;
use App\Models\DriverTaskReview;
use App\Models\Prefecture;
use App\Models\Gender;
use App\Models\WebConfigBase;
use App\Models\WebConfigSystem;
use App\Models\WebNoticeLog;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Admin\DriverExport;

use App\Libs\Server\Analysis;
use App\Models\DriverPlan;
use App\Models\DriverSchedule;
use Illuminate\Support\Facades\Hash;
use Throwable;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword ?? ''; // 検索ワード
        $search_gender = $request->gender_id ?? ''; // 性別
        $search_addr1_id = $request->addr1_id ?? ''; // 都道府県ID

        $search_from_age = $request->from_age ?? ''; // 年齢 以上
        $search_to_age = $request->from_age ?? ''; // 年齢 以下

        $search_from_review_avg_score = $request->from_review_avg_score ?? 0; // レビュー平均評価点 以上
        $search_to_review_avg_score = $request->to_review_avg_score ?? 5; // レビュー平均評価点 以下

        $search_from_task_count = $request->from_task_count ?? 0; // 稼働数 以上
        $search_to_task_count = $request->to_task_count ?? 999999; // 稼働数 以下
        $search_from_created_at = $request->from_created_at ?? ''; // 作成日 以上
        $search_to_created_at = $request->to_created_at ?? ''; // 作成日 以下
        $search_from_updated_at = $request->from_updated_at ?? ''; // 更新日 以上
        $search_to_updated_at = $request->to_updated_at ?? ''; // 更新日 以下
        $orderby = $request->orderby ?? ''; // 並び替え

        // logger($request);
        // exit;

        /* ドライバー一覧 & 検索 */
        $driver_list_object = Driver::select()
            ->with(['joinAddr1']) // 結合
            ->withAvg('joinDriverReview', 'score') // 平均評価点
            ->withTrashed();

        // キーワードで検索
        if (isset($request->keyword) && $request->keyword != '') {

            $driver_list_object->Where(function ($query) use ($keyword) {
                $query
                    ->orWhere('name_sei', 'LIKE', "%{$keyword}%")
                    ->orWhere('name_mei', 'LIKE', "%{$keyword}%")
                    ->orWhere('name_sei_kana', 'LIKE', "%{$keyword}%")
                    ->orWhere('name_mei_kana', 'LIKE', "%{$keyword}%")
                    ->orWhere('email', 'LIKE', "%{$keyword}%")
                    ->orWhere('tel', 'LIKE', "%{$keyword}%");
            });
        }

        /* 絞り込み検索 */
        // 性別
        if (isset($request->gender_id)) {
            $driver_list_object->where([['gender_id', $search_gender]]);
        }

        // 都道府県
        if (isset($request->addr1_id)) {
            $driver_list_object->where([['addr1_id', $search_addr1_id]]);
        }

        // 年齢 範囲 絞り込み
        // 20歳上というのは 現在 - 20の日付 以下という意味
        if (isset($request->from_age)) {
            $date = new \DateTime();
            $search_from_birthday = $date->modify("-{$search_from_age} year")->format('Y-m-d');

            $driver_list_object->whereDate('birthday', '<=', $search_from_birthday);
        }
        if (isset($request->to_age)) {
            $date = new \DateTime();
            $search_to_birthday = $date->modify("-{$search_to_age} year")->format('Y-m-d');

            $driver_list_object->whereDate('birthday', '<=', $search_to_birthday);
        }


        // 作成日 範囲 絞り込み
        if (isset($request->from_created_at)) {
            $driver_list_object->where('created_at', '>=', $search_from_created_at);
        }
        if (isset($request->to_created_at)) {
            $driver_list_object->where('created_at', '<=', $search_to_created_at);
        }

        // 更新日 範囲 絞り込み
        if (isset($request->from_updated_at)) {
            $driver_list_object->where('updated_at', '>=', $search_from_updated_at);
        }
        if (isset($request->to_updated_at)) {
            $driver_list_object->where('updated_at', '<=', $search_to_updated_at);
        }

        // 平均評価点 絞り込み
        if (isset($request->from_review_avg_score) || isset($request->to_review_avg_score)) {

            // 平均評価以上 ~ 以下 で絞り込み処理
            $driver_list_object->havingBetween('join_driver_review_avg_score', [$search_from_review_avg_score, $search_to_review_avg_score]);
        }

        //  稼働数 絞り込み
        if (isset($request->from_task_count) || isset($request->to_task_count)) {

            // 稼働数 以上 ~ 以下 で絞り込み処理
            $driver_list_object->havingBetween('join_task_count', [$search_from_task_count, $search_to_task_count]);
        }

        /* 並び替え */
        if ($orderby === 'id_desc') {
            $driver_list_object->orderBy('id', 'desc');
        } elseif ($orderby === 'id_asc') {
            $driver_list_object->orderBy('id', 'asc');
        } elseif ($orderby === 'join_driver_review_avg_score_desc') {
            $driver_list_object->orderBy('join_driver_review_avg_score', 'desc');
        } elseif ($orderby === 'join_driver_review_avg_score_asc') {
            $driver_list_object->orderBy('join_driver_review_avg_score', 'asc');
        } elseif ($orderby === 'join_task_count_desc') {
            $driver_list_object->orderBy('join_task_count', 'desc');
        } elseif ($orderby === 'join_task_count_asc') {
            $driver_list_object->orderBy('join_task_count', 'asc');
        } else {
            $driver_list_object->orderBy('id', 'desc');
        }

        // データ取得
        $driver_list = $driver_list_object->paginate(50)->withQueryString();
        // $driver_list = $driver_list_object->get();

        // logger($driver_list->toArray());
        // exit;


        /* フォーム検索に使うデータ */
        // 都道府県取得
        $prefecture_list = Prefecture::select()->get();
        // 性別
        $gender_list = Gender::select()->get();
        // 並び順
        $orderby_list = [
            ['value' => 'id_desc', 'text' => 'ID大きい順',],
            ['value' => 'id_asc', 'text' => 'ID小さい順',],
            ['value' => 'join_driver_review_avg_score_desc', 'text' => '評価高い順',],
            ['value' => 'join_driver_review_avg_score_asc', 'text' => '評価低い順',],
            ['value' => 'join_task_count_desc', 'text' => '稼働数が多い順',],
            ['value' => 'join_task_count_asc', 'text' => '稼働数が少ない順',],
        ];


        return view('admin.driver.index', [
            'driver_list' => $driver_list, // 取得データ
            'prefecture_list' => $prefecture_list,
            'gender_list' => $gender_list,
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
        /* フォーム検索に使うデータ */
        // 都道府県取得
        $prefecture_list = Prefecture::select()->get();
        // logger($prefecture_list->toArray());
        $gender_list = Gender::select()->get();
        $driver_plan_list = DriverPlan::select()->get();

        return view('admin.driver.create', [
            'prefecture_list' => $prefecture_list,
            'gender_list' => $gender_list,
            'driver_plan_list' => $driver_plan_list,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DriverCreateRequest $request)
    {
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
        $icon_img = $request->file('icon_img') ?? '';
        $driver_plan_id = $request->driver_plan_id ?? '';

        $avatar = $request->avatar ?? null;
        $bank = $request->bank ?? null;
        $driving_license_front = $request->driving_license_front ?? null;
        $driving_license_back = $request->driving_license_back ?? null;
        $auto_insurance = $request->auto_insurance ?? null;
        $voluntary_insurance = $request->voluntary_insurance ?? null;
        $inspection_certificate = $request->inspection_certificate ?? null;
        $license_plate_front = $request->license_plate_front ?? null;
        $license_plate_back = $request->license_plate_back ?? null;

        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報

        if ($icon_img) {
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


            try {
                $icon_img_path = Storage::disk('s3')->putFileAs($storage_path, $icon_img, $filename); // ファイルを保存
            } catch (\InvalidArgumentException | \Aws\S3\Exception\S3Exception $e) {
                Log::info("S3 No Connection!! InvalidArgumentException or S3Exception");
                $icon_img_path = Storage::disk('public')->putFileAs($storage_path, $icon_img, $filename); // ファイルを保存
            }


            if ($icon_img_path) {
                $driver_update = Driver::where('id', $login_id)->update([
                    'icon_img' => $icon_img_path
                ]);
            }
        }

        $driver_create = Driver::create([
            'user_type_id' => 3,
            'driver_plan_id' => $driver_plan_id,
            'driver_entry_status_id' => 1, // 通過
            'name_sei' => $name_sei,
            'name_mei' => $name_mei,
            'name_sei_kana' => $name_sei_kana,
            'name_mei_kana' => $name_mei_kana,
            'email' => $email,
            'password' => Hash::make($password),
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
            'icon_img' => $icon_img_path ?? '',
            'avatar' => $this->saveImageToStorage($avatar, $email),
            'bank' => $this->saveImageToStorage($bank, $email),
            'driving_license_front' => $this->saveImageToStorage($driving_license_front, $email),
            'driving_license_back' => $this->saveImageToStorage($driving_license_back, $email),
            'auto_insurance' => $this->saveImageToStorage($auto_insurance, $email),
            'voluntary_insurance' => $this->saveImageToStorage($voluntary_insurance, $email),
            'inspection_certificate' => $this->saveImageToStorage($inspection_certificate, $email),
            'license_plate_front' => $this->saveImageToStorage($license_plate_front, $email),
            'license_plate_back' => $this->saveImageToStorage($license_plate_back, $email),
        ]);


        $msg = '';
        if ($driver_create) {
            $msg = "ドライバーを作成しました。";
            $config_base = WebConfigBase::where('id', 1)->first();
            $config_system = WebConfigSystem::where('id', 1)->first();
            $driver = Driver::where('id', $driver_create->id)->first();
            $driver->init_password = $password;

            // メールで利用するデータ
            $data_mail = [
                "config_base" => $config_base,
                "config_system" => $config_system,
                'driver' => $driver,
            ];

            $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
            $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
            $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
            $file_path = __FILE__; // ファイルパス

            /* ドライバーへのメール */
            // 送り先
            $to_driver = [
                [
                    'email' => $driver->email,
                    'name' => "{$driver->name_sei} {$driver->name_mei}",
                ],
            ];

            // メールをブラウザで確認
            // return view('emails.driver.store_register_driver',[
            //     ['data' => $data_mail]
            // ]);
            // exit;

            $msg_mail = ''; // メール可否メッセージ
            try {
                Mail::to($to_driver)->send(new DriverRegisterSendDriverMail($data_mail)); // 送信
                $msg_mail = 'メールを送信しました。';
                $log_level = 7;
                $notice_type = 1;
            } catch (\Throwable $e) {
                $msg_mail = 'メール送信エラー';
                $msg .= $msg_mail;
                $log_level = 4;
                $notice_type = 1;

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
            } finally {
                WebNoticeLog::create([
                    'web_log_level_id' => $log_level,
                    'web_notice_type_id' => 1,
                    'task_id' => null,
                    'to_user_id' => $driver->id,
                    'to_user_type_id' => $driver->user_type_id,
                    'to_user_info' => ($driver->joinUserType->name ?? '') . " / email:" . ($driver->email ?? ''),
                    'user_id' => $login_id,
                    'user_type_id' => $login_user->user_type_id ?? 4,
                    'user_info' => $login_user->joinUserType->name ?? '',
                    'text' => "ドライバー登録",
                    'remote_addr' => $remote_addr,
                    'http_user_agent' => $http_user_agent,
                    'url' => $url,
                ]);
            }
        } else {
            $msg = "ドライバーを作成できませんでした!";
        }

        return redirect()->route("admin.driver.index")->with([
            'msg' => $msg
        ]);
    }

    public function saveImageToStorage($image, $driver_email)
    {
        $storage_path = "/driver/user_information/{$driver_email}";

        $unique_name = $image->hashName();
        $storage_date = new \DateTime();
        $storage_date = $storage_date->format('d_H_i_s_v');
        $filename = "{$storage_date}_{$unique_name}";

        try {
            $image_path = Storage::disk('s3')->putFileAs($storage_path, $image, $filename); //
        } catch (\InvalidArgumentException | \Aws\S3\Exception\S3Exception $e) {
            Log::info($e);
            $image_path = '';
        }

        return $image_path ?? '';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $driver_id
     * @return \Illuminate\Http\Response
     */
    public function show($driver_id)
    {
        // ドライバー情報 取得
        $driver = Driver::select()
            ->with(['joinGender', 'joinAddr1'])
            ->withAvg('joinDriverReview', 'score')
            ->withCount(['joinTask'])
            ->where('id', $driver_id)
            ->withTrashed()
            ->first();


        if ($driver) {
            $birthday = new \DateTime($driver->birthday);
            $now = new \DateTime();
            $interval = $now->diff($birthday);
            $age = $interval->y;
            $driver->age = $age;
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
            ->get();
        // logger($review_list->toArray());
        // exit;

        return view('admin.driver.show', [
            'driver' => $driver,
            'register_office_list' => $register_office_list,
            'task_list' => $task_list,
            'review_list' => $review_list,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $driver_id
     * @return \Illuminate\Http\Response
     */
    public function edit($driver_id)
    {
        // ドライバー情報 取得
        $driver = Driver::where('id', $driver_id)
            ->withTrashed()
            ->first();

        /* フォーム検索に使うデータ */
        // 都道府県取得
        $prefecture_list = Prefecture::select()->get();
        // logger($prefecture_list->toArray());
        $gender_list = Gender::select()->get();
        $driver_plan_list = DriverPlan::select()->get();

        return view('admin.driver.edit', [
            'driver' => $driver,
            'prefecture_list' => $prefecture_list,
            'gender_list' => $gender_list,
            'driver_plan_list' => $driver_plan_list,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $driver_id
     * @return \Illuminate\Http\Response
     */
    public function update(DriverUpdateRequest $request, $driver_id)
    {
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
        $driver_plan_id = $request->driver_plan_id ?? '';

        $unsubscribe = $request->action === 'unsubscribe';
        $remove = $request->action === 'remove';

        if ($unsubscribe) {
            $msg = '';

            DB::beginTransaction();
            try {
                Driver::where('id', $driver_id)->forceDelete();
                DriverSchedule::where('driver_id', $driver_id)->forceDelete();

                DB::commit();
                $msg = "退会処理が完了しました。";
            } catch (\Throwable $e) {
                DB::rollback();

                $msg = "退会処理に失敗しました。";
            }

            return redirect()->route("admin.driver.unsubscribe")->with([
                'msg' => $msg
            ]);
        }

        if ($remove) {
            $msg = '';

            DB::beginTransaction();
            try {
                Driver::withTrashed()->where('id', $driver_id)->update(['deleted_at' => null]);

                DB::commit();
                $msg = "復元処理が完了しました。";
            } catch (\Throwable $e) {
                DB::rollback();

                $msg = "復元処理に失敗しました。";
            }

            return redirect()->route("admin.driver.index")->with([
                'msg' => $msg
            ]);
        }

        // logger($request);
        // exit;

        /* 更新処理 */
        $driver = Driver::where('id', $driver_id)->first();
        if ($driver) {
            $driver->driver_plan_id = $driver_plan_id;
            $driver->name_sei = $name_sei;
            $driver->name_mei = $name_mei;
            $driver->name_sei_kana = $name_sei_kana;
            $driver->name_mei_kana = $name_mei_kana;
            $driver->email = $email;

            // パスワードは入力されていた場合のみ変更する
            if ($password) {
                $driver->password = Hash::make($password);
            }
            $driver->gender_id = $gender_id;
            $driver->birthday = $birthday;
            $driver->post_code1 = $post_code1;
            $driver->post_code2 = $post_code2;
            $driver->addr1_id = $addr1_id;
            $driver->addr2 = $addr2;
            $driver->addr3 = $addr3;
            $driver->addr4 = $addr4;
            $driver->tel = $tel;
            $driver->career = $career;
            $driver->introduction = $introduction;
            $driver_update =  $driver->save();

            if ($icon_img) {
                /* 現在設定されている画像を削除する */
                $driver = Driver::select('icon_img')->where('id', $driver_id)->first();

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
                $icon_img_path = '';
                try {
                    $icon_img_path = Storage::disk('s3')->putFileAs($storage_path, $icon_img, $filename);
                } catch (\InvalidArgumentException | \Aws\S3\Exception\S3Exception $e) {
                    Log::info("S3 No Connection!! InvalidArgumentException or S3Exception");
                    $icon_img_path = Storage::disk('public')->putFileAs($storage_path, $icon_img, $filename);
                }

                if ($icon_img_path) {
                    $driver_update = Driver::where('id', $driver_id)->update([
                        'icon_img' => $icon_img_path
                    ]);
                }
            }

            $msg = $driver_update ? "ドライバーを更新しました。" : "ドライバーを更新できませんでした!";
        } else {
            $msg = "ドライバーを更新できませんでした!";
        }

        return redirect()->route(
            "admin.driver.show",
            ['driver_id' => $driver_id]
        )->with([
            'msg' => $msg
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $driver_id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $driver_id)
    {
        $type = $request->type; //削除タイプ

        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $msg = ''; // 削除メッセージ

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        try {
            if ($type === 'soft') {
                $result = Driver::where('id', '=', $driver_id)->delete($driver_id);
                $msg = 'ソフト削除に成功';
            } elseif ($type === 'force') {
                $result = Driver::where('id', '=', $driver_id)->forceDelete($driver_id);
                $msg = '完全削除に成功';
            }

            if (!$result) {
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

        return redirect()->route('admin.driver.index')->with([
            'msg' => $msg,
        ]);
        // return view('admin.driver.destroy');
    }

    /**
     * ソフト削除したアカウント復元
     */
    public function restoreDelete($driver_id)
    {
        $login_id = Auth::guard('admins')->id(); // ログインユーザーID
        $login_user = auth('admins')->user(); // ログインユーザーの情報
        $msg = ''; // 削除メッセージ

        // 復元
        $restore_delete = Driver::where('id', $driver_id)
            ->restore();

        if ($restore_delete) {
            $msg = 'ソフト削除から復元しました。';
        } else {
            $msg = 'ソフト削除から復元に失敗しました。';
        }

        return redirect()->route(
            "admin.driver.show",
            ['driver_id' => $driver_id]
        )->with([
            'msg' => $msg
        ]);
    }

    /**
     * ドライバーデータエクスポート フォーム入力
     */
    public function export_index()
    {
        /* フォーム検索に使うデータ */
        // 都道府県取得
        $prefecture_list = Prefecture::select()->get();
        // 性別
        $gender_list = Gender::select()->get();
        // 並び順
        $orderby_list = [
            ['value' => 'id_desc', 'text' => 'ID大きい順',],
            ['value' => 'id_asc', 'text' => 'ID小さい順',],
            ['value' => 'join_driver_review_avg_score_desc', 'text' => '評価高い順',],
            ['value' => 'join_driver_review_avg_score_asc', 'text' => '評価低い順',],
            ['value' => 'join_task_count_desc', 'text' => '稼働数が多い順',],
            ['value' => 'join_task_count_asc', 'text' => '稼働数が少ない順',],
        ];

        return view('admin.driver.export.index', [
            'prefecture_list' => $prefecture_list, //都道府県
            'gender_list' => $gender_list,
            'orderby_list' => $orderby_list,
        ]);
    }

    /**
     * ドライバーデータエクスポート ダウンロード
     */
    public function export_read(Request $request)
    {
        $orderby = $request->orderby ?? ''; // 並び順
        $addr1_id = $request->addr1_id ?? ''; // 都道府県
        $gender_id = $request->gender_id ?? ''; // 性別
        $from_age = $request->from_age ?? ''; // 年齢 以上
        $to_age = $request->from_age ?? ''; // 年齢 以下
        $from_review_avg_score = $request->from_review_avg_score ?? ''; // スコア 以上
        $to_review_avg_score = $request->to_review_avg_score ?? ''; // スコア 以下
        $from_task_count = $request->from_task_count ?? ''; // 稼働数 以上
        $to_task_count = $request->to_task_count ?? ''; //稼働数 以下
        $is_soft_delete = $request->is_soft_delete ?? ''; // ソフトデリートも含めるか


        return Excel::download(new DriverExport([
            'orderby' => $orderby,
            'addr1_id' => $addr1_id,
            'gender_id' => $gender_id,
            'from_age' => $from_age,
            'to_age' => $to_age,
            'from_review_avg_score' => $from_review_avg_score,
            'to_review_avg_score' => $to_review_avg_score,
            'from_task_count' => $from_task_count,
            'to_task_count' => $to_task_count,
            'is_soft_delete' => $is_soft_delete,
        ]), 'drivers.csv');
    }

    public function unsubscribe()
    {
        $drivers = Driver::onlyTrashed()->get();

        return view('admin.driver.unsubscribe', [
            'drivers' => $drivers
        ]);
    }
}
