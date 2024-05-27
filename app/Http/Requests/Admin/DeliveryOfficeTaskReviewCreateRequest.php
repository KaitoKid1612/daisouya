<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryOfficeTaskReviewCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'driver_task_id' => 'required|exists:driver_tasks,id|unique:driver_task_reviews,driver_task_id',
            'score' => 'required|numeric|integer|between:1,5',
            'title' => 'nullable|string|max:255',
            'text' => 'nullable|string|max:2000',
            'review_public_status_id' => 'required|exists:delivery_office_task_review_public_statuses,id',
        ];
    }

    /**
     * name を変更(デフォルトname属性値)
     *
     */
    public function attributes()
    {
        return [
            'driver_task_id' => '稼働ID',
            'score' => '評価点',
            'title' => 'レビュータイトル',
            'text' => 'レビュー本文',
            'driver_task_review_public_status' => '公開ステータス',
        ];
    }
}
