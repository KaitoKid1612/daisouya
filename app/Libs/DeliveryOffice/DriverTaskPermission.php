<?php

namespace App\Libs\DeliveryOffice;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\DriverTask;
use App\Models\DriverTaskReview;

/**
 * 依頼者の稼働依頼で行える許可権限を扱う
 */
class DriverTaskPermission
{
    /**
     * 許可権限の内容を返す
     */
    public function get(int $task_id)
    {
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // logger($login_id);
        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $task = DriverTask::select()
            ->where([
                ['id', $task_id],
            ])
            ->first();

        $driver_task_status_id = $task->driver_task_status_id ?? '';

        // create permission
        $is_create_pdf_receipt = false; // PDF
        $is_create_review = false; // レビュー

        // update permission
        $is_update_complete = false; // 完了
        $is_update_failure = false; // ドライバーの不履行
        $is_update_cancel = false; // キャンセル
        $is_update_payment_method = false; // 支払い方法

        if ($task && $task->delivery_office_id == $login_id) {

            /* create */
            // PDF receipt
            if ($driver_task_status_id == 4) {
                $is_create_pdf_receipt = true;
            }

            /* レビュー */
            // 依頼者レビュー取得
            // すでにレビューしてあるか、確認する。
            $driver_review = DriverTaskReview::select()
                ->where([
                    ['driver_task_id', $task_id],
                ])->first();
            if (!$driver_review && in_array($driver_task_status_id, [4, 8])) {
                $is_create_review = true;
            }


            /* update */
            // 完了 ドライバーの不履行
            if ($driver_task_status_id == 3 && new \DateTime() >= new \DateTime($task->task_date)) {
                $is_update_complete = true;
                $is_update_failure = true;
            }

            // キャンセル
            $datetime =  new \DateTime();
            $datetime_today = $datetime->format('Y-m-d');
            if ($task->task_date > $datetime_today && in_array($task->driver_task_status_id, [1, 2])) {
                $is_update_cancel = true;
            }

            // 支払い方法再設定
            if (in_array($task->driver_task_status_id, [10]) && $task->driver_task_payment_status_id == 1) {
                $is_update_payment_method = true;
            }
        }


        $permission_create_list = [
            'driver_task_review' => $is_create_review,
            'pdf_receipt' => $is_create_pdf_receipt,
        ];

        $permission_update_list = [
            'complete' => $is_update_complete,
            'failure' => $is_update_failure,
            'cancel' => $is_update_cancel,
            'payment_method' => $is_update_payment_method,
        ];

        $result = [
            'create' => $permission_create_list,
            'update' => $permission_update_list,
        ];

        return $result;
    }
}
