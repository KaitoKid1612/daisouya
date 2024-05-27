<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Driver\DriverRegisterDeliveryOfficeUpsertRequest;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Libs\Log\LogFormat;
use App\Libs\Server\Analysis;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\DB;
use App\Models\DriverRegisterDeliveryOffice;

class DriverRegisterDeliveryOfficeController extends Controller
{
    /**
     * 一覧
     * +API機能
     */
    public function index(Request $request)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }


        // 結合先の取得カラム
        $join_driver_column_list = [
            "id",
            "name",
        ];
        $join_driver_column = "joinOffice:" . implode(',', $join_driver_column_list);
        $select_register_office_list = DriverRegisterDeliveryOffice::select('delivery_office_id')
            ->with([$join_driver_column])
            ->where('driver_id', $login_id)->get();

        $api_status = true;
        if ($select_register_office_list) {
            $api_status = true;
        } else {
            $api_status = false;
        }
        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $select_register_office_list,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 登録営業所更新&作成
     * +API機能
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upsert(DriverRegisterDeliveryOfficeUpsertRequest $request)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $api_status = true;

        $register_office_list = $request->register_office;

        $remote_addr = Analysis::getClientIpAddress() ?? ''; //IPアドレス
        $http_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? ''; // OSブラウザ
        $url = $_SERVER['REQUEST_URI'] ?? ''; // URL ドメイン以降
        $file_path = __FILE__; // ファイルパス

        $msg = '';

        $msg = "";

        if ($register_office_list) {
            // 登録済みの営業所
            $select_register_office_list = DriverRegisterDeliveryOffice::select('delivery_office_id')->where('driver_id', $login_id)->get();

            // 登録済み営業所ID 一覧
            $register_office_id_list  = [];
            foreach ($select_register_office_list as $register) {
                $register_office_id_list[] = $register->delivery_office_id;
            }

            // logger(($register_office_id_list));
            // logger(($register_office_list));
            DB::beginTransaction();

            try {
                // 削除する営業所
                // リクエストされた営業所ID一覧の中に、登録済みのIDがなくなっていれば、その登録済み営業所は削除する
                foreach ($register_office_id_list as $office_id) {
                    if (!in_array((string)$office_id, $register_office_list, true)) {
                        DriverRegisterDeliveryOffice::where([
                            ['driver_id', $login_id],
                            ['delivery_office_id', $office_id],
                        ])->delete();
                    }
                }

                // 営業登録処理
                foreach ($register_office_list as $office_id) {
                    $register_upsert = DriverRegisterDeliveryOffice::updateOrCreate(
                        ['driver_id' => $login_id, 'delivery_office_id' => $office_id],
                        ['driver_id' => $login_id, 'delivery_office_id' => $office_id],
                    );
                }
                DB::commit();
                $msg = '営業所を登録しました。';
                $api_status = true;
            } catch (\Throwable $e) {
                DB::rollback();

                $msg = '営業所を登録に失敗。';
                $api_status = false;

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

                $msg = "登録営業所を登録できませんでした!";
            }
        } else {
            $delete_register_office = DriverRegisterDeliveryOffice::where([
                ['driver_id', $login_id],
            ])->delete();
            if ($delete_register_office) {
                $msg = "登録営業所全て取り消しました。";
                $api_status = true;
            }
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'message' => $msg
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return redirect()->route('driver.user.show', ['driver_id' => $login_id])->with([
                'msg' => $msg
            ]);
        }
    }
}
