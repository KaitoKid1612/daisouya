<?php

namespace App\Http\Controllers\DeliveryOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DeliveryOffice\DeliveryOfficeUpdateRequest;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

use App\Models\DeliveryOffice;
use App\Models\DeliveryCompany;
use App\Models\DriverTask;
use App\Models\Prefecture;
use Illuminate\Support\Facades\DB;

/**
 * 依頼者(ログインユーザー)
 */
class DeliveryOfficeController extends Controller
{
    /**
     * 取得
     * +API機能
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $office =  DeliveryOffice::with(['joinUserType', 'joinCompany', 'joinAddr1', 'joinDeliveryOfficeType', 'joinChargeUserType'])->where('id', $login_id)->first();

        // logger($office);

        // logger($office->toArray());

        $api_status = true;
        if ($office) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'data' => $office
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return view('delivery_office.user.index', [
                'office' => $office
            ]);
        }
    }

    /**
     * 編集画面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        $type = $request->type ?? '';

        $office =  DeliveryOffice::with(['joinCompany', 'joinAddr1'])->where('id', $login_id)
            ->first();

        /* フォームで使うデータ */
        $company_list = DeliveryCompany::get();
        $prefecture_list = Prefecture::select()->get();

        // logger($office->toArray());

        return view('delivery_office.user.edit', [
            'office' => $office,
            'company_list' => $company_list,
            'prefecture_list' => $prefecture_list,
            'type' => $type,
        ]);
    }

    /**
     * 編集
     * +API機能
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DeliveryOfficeUpdateRequest $request)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $name = $request->name ?? '';
        $manager_name_sei = $request->manager_name_sei ?? '';
        $manager_name_mei = $request->manager_name_mei ?? '';
        $manager_name_sei_kana = $request->manager_name_sei_kana ?? '';
        $manager_name_mei_kana = $request->manager_name_mei_kana ?? '';
        $email = $request->email ?? '';
        $password = $request->password ?? '';
        $delivery_company_id = in_array($request->delivery_company_id, [NULL, 'None'])  ? NULL :  $request->delivery_company_id;
        $delivery_company_name = $request->delivery_company_name ?? '';
        $post_code1 = $request->post_code1 ?? '';
        $post_code2 = $request->post_code2 ?? '';
        $addr1_id = $request->addr1_id ?? '';
        $addr2 = $request->addr2 ?? '';
        $addr3 = $request->addr3 ?? '';
        $addr4 = $request->addr4 ?? '';
        $manager_tel = $request->manager_tel ?? '';

        $type = $request->type ?? '';

        $msg = '';
        // logger($type);

        if ($type === 'email') {
            $office = DeliveryOffice::where([
                ['id', '=', $login_id],
            ])->update([
                'email' => $email,
            ]);
            if ($office) {
                $msg = "emailを編集しました。";
            } else {
                $msg = "編集に失敗しました。";
            }
        } elseif ($type === 'password') {
            $office = DeliveryOffice::where([
                ['id', '=', $login_id],
            ])->update([
                'password' => Hash::make($password),
            ]);
            if ($office) {
                $msg = "パスワードを編集しました。";
            } else {
                $msg = "編集に失敗しました。";
            }
        } elseif ($type === 'user') {
            // 配送会社IDが入力されていたら、会社名は空
            if ($delivery_company_id) {
                $delivery_company_name = '';
            }

            $office = DeliveryOffice::where([
                ['id', '=', $login_id],
            ])->update([
                'name' => $name,
                'manager_name_sei' => $manager_name_sei,
                'manager_name_mei' => $manager_name_mei,
                'manager_name_sei_kana' => $manager_name_sei_kana,
                'manager_name_mei_kana' => $manager_name_mei_kana,
                'delivery_company_id' => $delivery_company_id,
                'delivery_company_name' => $delivery_company_name,
                'delivery_office_type_id' => isset($delivery_company_id) ? 1 : 2,
                'post_code1' => $post_code1,
                'post_code2' => $post_code2,
                'addr1_id' => $addr1_id,
                'addr2' => $addr2,
                'addr3' => $addr3,
                'addr4' => $addr4,
                'manager_tel' => $manager_tel,
            ]);

            if ($office) {
                $msg = "ユーザー情報を編集しました。";

                $login_user = $login_user->fresh(); // 最新の状態を取得
                /**
                 * Stripeと同期
                 */
                if ($login_user->hasStripeId()) {
                    $login_user->syncStripeCustomerDetails();
                }
            } else {
                $msg = "編集に失敗しました。";
            }
        } elseif ($type === 'delete') {
            DB::beginTransaction();
            try {
                // Check for active driver tasks
                $hasActiveDriverTasks = DriverTask::where('delivery_office_id', $login_id)
                    ->whereNotNull('delivery_office_id')
                    ->whereIn('driver_task_status_id', [2, 3, 10, 11])
                    ->where('is_template', 0)
                    ->exists();

                if ($hasActiveDriverTasks) {
                    // If there are active driver tasks, prevent deletion
                    return redirect()->back()->with([
                        'msg' => '稼働依頼をご確認ください。',
                    ]);
                }

                // Soft delete the delivery office
                $office = DeliveryOffice::where('id', $login_id)->update([
                    'deleted_at' => now(),
                ]);

                // Hard delete related driver tasks
                DriverTask::where('delivery_office_id', $login_id)->delete();

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollback();
                $api_status = false;
            }

        }

        if (isset($office) && $office) {
            $api_status = true;
        } else {
            $api_status = false;
        }

        if (Route::is('api.*')) {
            return response()->json([
                'status' => $api_status,
                'message' => $msg
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            if ($type === 'delete') {
                Auth::logout();
                return redirect()->route('delivery_office.login')->with([
                    'msg' => '退会申請が完了しました。',
                ]);
            } else {
                return redirect()->route('delivery_office.user.index')->with([
                    'msg' => $msg
                ]);
            }
        }
    }
}
