<?php

namespace App\Http\Requests\DeliveryOffice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Route;

class DriverTaskCalcPriceRequest extends FormRequest
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
        $rules = [
            'task_date' => 'required|date_format:Y-m-d',
            'freight_cost' => 'required|integer|min:0|max:1000000',
            'driver_task_plan_id' => ['required', 'exists:driver_task_plans,id'],
        ];

        if ($this->input('driver_task_plan_id') == 2) {
            $rules['system_price'] = 'required|integer';
        } else {
            $rules['system_price'] = 'nullable';
        }

        return $rules;
    }

    /**
     * name を変更
     */
    public function attributes()
    {
        return [
            'task_date' => '稼働日',
            'freight_cost' => 'ドライバー運賃',
            'driver_task_plan_id' => '稼働依頼プランID',
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
