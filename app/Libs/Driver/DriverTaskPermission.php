<?php

namespace App\Libs\Driver;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\DriverTask;
use App\Models\DeliveryOfficeTaskReview;

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
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

        // APIの時のログインユーザーの情報
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

        $is_create_review = false; // レビュー
        $is_update_reject = false; // 却下
        $is_update_accept = false; // 受諾

        if ($task && $task->driver_id == $login_id) {
            /* 却下 */
            if (in_array($driver_task_status_id, [2, 11])) {
                $is_update_reject = true;
            }

            /* レビュー */
            $available_review_date = new \DateTime();
            $available_review_date->modify('-17 hours'); // 現在の日時  >  稼働日 + 17時間かチェックするため
            $task_date = new \DateTime($task->task_date ?? '1000-12-31 23:59:59');

            // 依頼者レビュー取得
            // すでにレビューしてあるか、確認する。
            $office_review = DeliveryOfficeTaskReview::select()
                ->where([
                    ['driver_task_id', $task_id],
                ])->first();

            if (!$office_review && in_array($driver_task_status_id, [3, 4, 8]) && $available_review_date > $task_date) {
                $is_create_review = true;
            }
        }

        if ($task && ($task->driver_id == $login_id || is_null($task->driver_id))) {
            /* 受諾 */
            // ドライバーが同日にすでに受諾している稼働がないかチェック
            $task_duplicate_select = DriverTask::where([
                ['driver_id', $login_id],
                ['task_date', $task->task_date],
                ['driver_task_status_id', '=', 3],
            ])->first();

            // 受諾するための条件を満たしているか絞り込んで稼働を取得
            $task_accept_select = DriverTask::where([
                ['id', '=', $task_id],
            ])
                ->where(function ($query) use ($task, $login_id) {
                    // ドライバーIDがnullの場合はnullで検索、それ以外だったらログインIDで検索
                    if (is_null($task->driver_id)) {
                        $query->where('driver_id', '=', null);
                    } else {
                        $query->where('driver_id', '=', $login_id);
                    }
                })->where(function ($query) use ($task) {
                    if (is_null($task->driver_id)) {
                        $query->where('driver_task_status_id', 1);
                    } else {
                        $query->where('driver_task_status_id', 2)
                            ->orWhere('driver_task_status_id', 11);
                    }
                })->first();

            if (!$task_duplicate_select && $task_accept_select) {
                $is_update_accept = true;
            }
        }


        $permission_create_list = [
            'delivery_office_task_review' => $is_create_review,
        ];

        $permission_update_list = [
            'accept' => $is_update_accept,
            'reject' => $is_update_reject,
        ];

        $result = [
            'create' => $permission_create_list,
            'update' => $permission_update_list,
        ];

        return $result;
    }
}
