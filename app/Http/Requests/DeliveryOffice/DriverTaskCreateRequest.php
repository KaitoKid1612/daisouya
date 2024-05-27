<?php

namespace App\Http\Requests\DeliveryOffice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Rules\DeliveryOffice\DriverEntryStatusWaitingRule;
use App\Rules\Common\DriverTaskPlanAllowDriverRule;
use App\Rules\DeliveryOffice\HasPickupAddrRule;

use App\Models\WebBusySeason;
use App\Libs\Price\DriverTaskPriceSupport;

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
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_user = $this->user();
            $login_id = Auth::id();
        }

        // 無料ユーザーのときはバリデーションしない
        $rule_payment_method_id = $login_user->charge_user_type_id === 2 ? '' : 'required|string|min:1|max: 255';

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

        $driver_id = $this->driver_id ?? null;

        // logger($driver_id);

        /* 繁忙期料金適用対象か */
        $rule_system_price = [];
        $rule_busy_system_price = [];
        $task_date = $this->task_date; // 稼働日
        $driver_task_plan_id = $this->driver_task_plan_id; // 稼働依頼プラン
        $driver_task_price_support = new DriverTaskPriceSupport();

        $result_check_busy_task_price = null;
        if ($driver_task_plan_id && $task_date) {
            $result_check_busy_task_price = $driver_task_price_support->checkBusyTaskPrice($driver_task_plan_id, $task_date);
        }
        if ($result_check_busy_task_price) {
            // 繁忙期が適用対象の場合
            $rule_system_price = ['nullable', 'integer'];
            $rule_busy_system_price = ['required', 'integer'];
        }else {
            $rule_system_price = ['required', 'integer'];
            $rule_busy_system_price = ['nullable', 'integer'];


        }


        $data = [
            'task_date' => 'required|date_format:Y-m-d',
            'driver_id' => ['nullable', 'exists:drivers,id', new DriverEntryStatusWaitingRule($driver_id)],
            'driver_task_plan_id' => ['required', 'exists:driver_task_plans,id', new DriverTaskPlanAllowDriverRule($this->driver_task_plan_id, $this->driver_id)],
            'rough_quantity' => 'required|integer|min:0|max:100000',
            'delivery_route' => 'required|string|max:1000',
            'task_memo' => 'nullable|string|max:1000',
            'payment_method_id' => $rule_payment_method_id,
            'system_price' => $rule_system_price,
            'busy_system_price' => $rule_busy_system_price,
            'freight_cost' => 'required|integer|min:0|max:1000000',
            'emergency_price' => 'required|integer',
            'tax' => 'required|integer',
            'total_price' => 'required|integer',
        ];

        // 集荷先を保存する場合
        if ($this->pickup_addr_id === 'is_new') {
            $add_data = [
                'task_delivery_company_id' => $rule_delivery_company_id,
                'task_delivery_company_name' => $rule_delivery_company_name,
                'task_delivery_office_name' => 'required|max: 255',
                'task_email' => 'required|email:strict,dns,spoof|max:255',
                'task_tel' => 'required|numeric|digits_between:10,11',
                'task_post_code1' => 'required|numeric|digits:3',
                'task_post_code2' => 'required|numeric|digits:4',
                'task_addr1_id' => 'required|exists:prefectures,id',
                'task_addr2' => 'required|string|max: 255',
                'task_addr3' => 'required|string|max: 255',
                'task_addr4' => 'nullable|string|max: 255',
            ];
            array_merge($data, $add_data);
        } else {
            $add_data = [
                'pickup_addr_id' => [
                    new HasPickupAddrRule(),
                    'exists:delivery_pickup_addrs,id',
                ],
            ];
            array_merge($data, $add_data);
        }

        return $data;
    }

    /**
     * name を変更
     */
    public function attributes()
    {
        return [
            'task_date' => '稼働日',
            'driver_id' => 'ドライバー',
            'driver_task_plan_id' => '稼働依頼プラン',
            'rough_quantity' => '物量',
            'delivery_route' => '配送コース',
            'task_memo' => '稼働メモ',
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
            'pickup_addr_id' => '登録済み集荷先',
            'pickup_addr_new' => '新しい集荷先',
            'payment_method_id' => '支払い方法',
            'system_price' => 'システム利用料金',
            'busy_system_price' => 'システム利用料金(繁忙期)',
            'emergency_price' => '緊急依頼料金',
            'tax' => '消費税',
            'total_price' => '総計',
            'freight_cost' => 'ドライバー運賃',
        ];
    }

    /**
     * 独自バリデーションのエラーメッセージを生成する
     * APIの場合は、Unicode文字列をエスケープしないようにする。
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        if (Route::is('api.*')) {
            throw new HttpResponseException(
                response()->json([
                    'message' => '入力内容にエラーがあります。',
                    'errors' => $validator->errors(),
                ], 422, [], JSON_UNESCAPED_UNICODE)
            );
        } else {
            parent::failedValidation($validator);
        }
    }
}
