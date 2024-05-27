<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DriverTaskPlanUpdateRequest extends FormRequest
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
        $driver_task_plan_id = $this->route('driver_task_plan_id');

        $rule = [
            'driver_task_plan_id' => ['required'],
            'name' => ['required', 'string'],
        ];
        if (in_array($driver_task_plan_id, [1, 2])) {
            $add_rule = [
                'system_price' => ['required', 'integer'],
                'system_price_percent' => ['nullable', 'integer'],
                'busy_system_price' => ['nullable', 'integer'],
                'busy_system_price_percent' => ['nullable', 'integer'],
                'busy_system_price_percent_over' => ['nullable', 'integer'],
                'emergency_price' => ['required', 'integer'],
            ];
            $rule = array_merge($rule, $add_rule);
        } elseif ($driver_task_plan_id == 3) {
            $add_rule = [
                'system_price' => ['nullable', 'integer'],
                'system_price_percent' => ['required', 'integer'],
                'busy_system_price' => ['nullable', 'integer'],
                'busy_system_price_percent' => ['required', 'integer'],
                'busy_system_price_percent_over' => ['required', 'integer'],
                'emergency_price' => ['required', 'integer'],
            ];
            $rule = array_merge($rule, $add_rule);
        }

        return $rule;
    }

    /** 
     * パスパラメータのidをバリデーションに含める 
     */
    public function validationData()
    {
        return array_merge($this->request->all(), [
            'driver_task_plan_id' => $this->route('driver_task_plan_id'),
        ]);
    }

    /**
     * name を変更(デフォルトname属性値)
     *
     */
    public function attributes()
    {
        return [
            'name' => 'プラン名',
            'system_price' => 'システム利用料金',
            'system_price_percent' => 'システム利用料金(運賃の%)',
            'busy_system_price' => 'システム料金(繁忙期)',
            'busy_system_price_percent' => 'システム料金(繁忙期,運賃の%)',
            'busy_system_price_percent_over' => 'システム料金(繁忙期,運賃の%,既定運賃以上の場合)',
            'emergency_price' => '緊急依頼料金',
        ];
    }
}
