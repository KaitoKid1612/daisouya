<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Driver;
use App\Models\RegisterRequestDriver;

class RegisterRequestDriverUpdateRequest extends FormRequest
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

        $register_request_id = $this->route('register_request_id');

        $register_request = RegisterRequestDriver::select()
            ->where('id', $register_request_id)
            ->first();

        $driver = Driver::select()->where('email', $register_request->email)->first();

        $driver_plan_id_rule = []; // ドライバープランIDのルール

        // ドライバーが存在していない or ドライバーは存在するがドライバープランが指定されていない
        if (!$driver || ($driver && !$driver->driver_plan_id)) {
            $driver_plan_id_rule = ['required', 'exists:driver_plans,id'];
        }

        // ドライバープラン指定されたドライバーが存在するなら入力は必要ない。
        if ($driver && $driver->driver_plan_id) {
            $driver_plan_id_rule = [];
        }

        // 不可だったら、プランは指定する必要ない。
        if ($this->register_request_status_id == 3) {
            $driver_plan_id_rule = ['exists:driver_plans,id'];
        }

        return [
            'register_request_status_id' => 'required|exists:register_request_statuses,id',
            'driver_plan_id' => $driver_plan_id_rule,
        ];
    }

    /**
     * name を変更
     */
    public function attributes()
    {
        return [
            'register_request_status_id' => '登録申請ステータス',
            'driver_plan_id' => 'ドライバープラン',
        ];
    }
}
