<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DriverTaskReviewUpdateRequest extends FormRequest
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
            'driver_task_id' => ['required', 'exists:driver_tasks,id', Rule::unique('driver_task_reviews', 'driver_task_id')->ignore(request()->driver_task_id, 'driver_task_id')],
            'score' => 'numeric|integer|between:1,5',
            'title' => 'nullable|string|max:255',
            'text' => 'nullable|string|max:2000',
            'driver_task_review_public_status' => 'required|exists:driver_task_review_public_statuses,id',
        ];
    }

    /**
     * name を変更(デフォルトname属性値)
     *
     */
    public function attributes()
    {
        return [
            'score' => '評価点',
            'title' => 'レビュータイトル',
            'text' => 'レビュー本文',
            'driver_id' => 'ドライバーID',
            'driver_task_id' => '稼働ID',
            'delivery_office_id' => '営業所ID',
            'driver_task_review_public_status' => '公開ステータス',
        ];
    }
}
