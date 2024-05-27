<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DriverRegisterDeliveryOfficeUpsertRequest extends FormRequest
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
            'driver_id' => 'required|nullable|exists:drivers,id',
            'register_office' => 'array',
            'register_office.*' => 'exists:delivery_offices,id',
        ];
    }
    /**
     * name を変更
     */
    public function attributes()
    {
        return [
            'driver_id' => 'ドライバー',
            'register_office' => '営業所',
            'register_office.*' => '営業所リスト',
        ];
    }
}
