<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WebContactUpdateRequest extends FormRequest
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
            'web_contact_status_id' => 'required|integer|exists:web_contact_statuses,id',
        ];
    }

    /**
     * name を変更
     */
    public function attributes()
    {
        return [
            'web_contact_status_id' => 'ステータス',
        ];
    }
}
