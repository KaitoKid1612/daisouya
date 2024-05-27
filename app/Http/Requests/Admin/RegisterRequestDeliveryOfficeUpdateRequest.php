<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequestDeliveryOfficeUpdateRequest extends FormRequest
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
            'register_request_status_id' => 'required|exists:register_request_statuses,id',
        ];
    }

    /**
     * name を変更
     */
    public function attributes()
    {
        return [
            'register_request_status_id' => '登録申請ステータス',
        ];
    }
}
