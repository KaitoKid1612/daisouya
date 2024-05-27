<?php

namespace App\Libs\Driver;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\DriverTask;
use App\Models\DeliveryOfficeTaskReview;

/**
 * 稼働依頼のUIの操作
 */
class DriverTaskUI
{
    public function get(int $task_id)
    {
        $login_id = Auth::guard('drivers')->id(); // ログインユーザーのID
        $login_user = auth('drivers')->user(); // ログインユーザーの情報

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

        if ($task && $task->driver_id == $login_id) {
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
                $review_text_status = [
                    'text' => 'レビューをする',
                    'is_create_review' => true,
                ];
            } else if ($office_review && $office_review->review_public_status_id == 1) {
                $review_text_status = [
                    'text' => 'レビュー済み',
                    'is_create_review' => false,
                ];
            } else if ($office_review && $office_review->review_public_status_id == 2) {
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
            'delivery_office_task_review_text_status' => $review_text_status
        ];

        return $result;
    }
}
