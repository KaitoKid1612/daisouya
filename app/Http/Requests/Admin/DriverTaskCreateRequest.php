<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Rules\Common\DriverTaskPlanAllowDriverRule;
use App\Rules\Admin\DriverTaskDiscountExceedMaxRule;

class DriverTaskCreateRequest extends FormRequest
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
        $rule_delivery_company_id = ''; // 会社IDのルール
        $rule_delivery_company_name = ''; // 会社名 のルール
        if ($this->task_delivery_company_id === 'None') {
            $rule_delivery_company_id = ['required', Rule::in(['None'])];
            $rule_delivery_company_name = 'required|max: 255';
        } elseif ($this->task_delivery_company_id) {
            $rule_delivery_company_id = 'required|exists:delivery_companies,id';
            $rule_delivery_company_name = 'nullable';
        } else {
            $rule_delivery_company_id = 'required|exists:delivery_companies,id';
            $rule_delivery_company_name = 'required';
        }

        /* ドライバーが指定されているときは、ステータスは１ でなければならない。ドライバーが指定されていないときは、ステータスは１以外 でなければならない */
        $driver_id = $this->driver_id;
        $driver_task_status_id = $this->driver_task_status_id;

        $driver_id_rule = '';
        $driver_task_status_id_rule = '';

        if ($driver_task_status_id >= 2) {
            $driver_id_rule = ['required', 'exists:drivers,id'];
        }

        if ($driver_id) {
            $driver_task_status_id_rule = ['required', 'exists:driver_task_statuses,id', Rule::notIn(['1'])];
        } else {
            $driver_task_status_id_rule = ['required', 'exists:driver_task_statuses,id', Rule::in(['1'])];
        }

        // 料金取得
        $system_price = $this->system_price ?? 0;
        $busy_system_price = $this->busy_system_price ?? 0;
        $freight_cost = $this->freight_cost ?? 0;
        $emergency_price = $this->emergency_price ?? 0;
        $discount = $this->discount ?? 0;

        return [
            'task_date' => 'required|date_format:Y-m-d',
            'driver_id' => $driver_id_rule,
            'delivery_office_id' => 'required|exists:delivery_offices,id',
            'driver_task_status_id' => $driver_task_status_id_rule,
            'driver_task_plan_id' => ['required', 'exists:driver_task_plans,id', new DriverTaskPlanAllowDriverRule($this->driver_task_plan_id, $this->driver_id)],
            'rough_quantity' => 'required|integer|min:0|max:100000',
            'delivery_route' => 'required|String|max:1000',
            'task_memo' => 'nullable|String|max:1000',
            'task_delivery_company_id' => $rule_delivery_company_id,
            'task_delivery_company_name' => $rule_delivery_company_name,
            'task_delivery_office_name' => 'required|max: 255',
            'task_email' => 'nullable|email:strict,dns,spoof|max:255',
            'task_tel' => 'nullable|numeric|digits_between:10,11',
            'task_post_code1' => 'required|numeric|digits:3',
            'task_post_code2' => 'required|numeric|digits:4',
            'task_addr1_id' => 'required|exists:prefectures,id',
            'task_addr2' => 'required|string|max: 255',
            'task_addr3' => 'required|string|max: 255',
            'task_addr4' => 'nullable|string|max: 255',
            'system_price' => 'required|integer',
            'busy_system_price' => 'integer',
            'freight_cost' => 'required|integer|min:0|max:1000000',
            'emergency_price' => 'required|integer',
            'discount' => ['nullable', 'integer', new DriverTaskDiscountExceedMaxRule($system_price, $busy_system_price, $freight_cost, $emergency_price, $discount)],
            'tax_rate' => 'required|numeric',
            'payment_fee_rate' => 'required|numeric',
            'payment_method_id' => 'required|string|max: 255',
        ];
    }

    /**
     * name を変更
     */
    public function attributes()
    {
        return [
            'task_date' => '稼働日',
            'driver_id' => 'ドライバー',
            'delivery_office_id' => '営業所',
            'driver_task_status_id' => '稼働ステータス',
            'driver_task_plan_id' => '稼働依頼プラン',
            'rough_quantity' => '物量',
            'delivery_route' => '配送ルート',
            'task_memo' => 'メモ',
            'task_delivery_company_id' => '配送会社名',
            'task_delivery_company_name' => '配送会社名',
            'task_delivery_office_name' => '営業所名',
            'task_email' => 'メールアドレス',
            'task_tel' => '電話番号',
            'task_post_code1' => '郵便番号1',
            'task_post_code2' => '郵便番号2',
            'task_addr1_id' => '都道府県',
            'task_addr2' => '市区町村',
            'task_addr3' => '丁目 番地 号',
            'task_addr4' => '建物名部屋番号',
            'system_price' => 'システム利用料金',
            'busy_system_price' => 'システム利用料金(繁忙期)',
            'freight_cost' => 'ドライバー運賃',
            'emergency_price' => '緊急依頼料金',
            'discount' => '値引き額',
            'tax_rate' => '消費税率',
            'payment_fee_rate' => '決済手数料率',
            'payment_method_id' => 'stripe_payment_method_id',
        ];
    }
}
