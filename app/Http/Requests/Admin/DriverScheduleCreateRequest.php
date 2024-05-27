<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DriverScheduleCreateRequest extends FormRequest
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
            'available_date' => 'required|date_format:Y-m-d',
        ];
    }

    /**
     * name を変更
     */
    public function attributes()
    {
        return [
            'driver_id' => 'ドライバー',
            'available_date' => '稼働日',
        ];
    }
}
