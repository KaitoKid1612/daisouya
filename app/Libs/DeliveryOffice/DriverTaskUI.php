<?php

namespace App\Libs\DeliveryOffice;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\DriverTask;
use App\Models\DriverTaskReview;

/**
 * 稼働依頼のUIの操作
 */
class DriverTaskUI
{
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

        $review_text_status = [
            'text' => '',
            'is_create_review' => false,
        ];

        if ($task && $task->delivery_office_id == $login_id) {
            /* レビュー */
            // 依頼者レビュー取得
            // すでにレビューしてあるか、確認する。
            $driver_review = DriverTaskReview::select()
                ->where([
                    ['driver_task_id', $task_id],
                ])->first();
            if (!$driver_review && in_array($driver_task_status_id, [4, 8])) {
                $review_text_status = [
                    'text' => 'レビューをする',
                    'is_create_review' => true,
                ];
            } else if ($driver_review && $driver_review->driver_task_review_public_status_id == 1) {
                $review_text_status = [
                    'text' => 'レビュー済み',
                    'is_create_review' => false,
                ];
            } else if ($driver_review && $driver_review->driver_task_review_public_status_id == 2) {
                $review_text_status = [
                    'text' => '非表示',
                    'is_create_review' => false,
                ];
            }
        } else {
            $review_text_status = [
                'text' => '',
                'is_create_review' => false,
            ];
        }

        $result = [
            'driver_task_review_text_status' => $review_text_status
        ];

        return $result;
    }
}
