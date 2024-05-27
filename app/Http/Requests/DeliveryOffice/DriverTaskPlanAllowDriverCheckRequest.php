<?php

namespace App\Http\Requests\DeliveryOffice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\Common\DriverTaskPlanAllowDriverRule;

class DriverTaskPlanAllowDriverCheckRequest extends FormRequest
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

        $driver_task_plan_id = $this->driver_task_plan_id;
        $driver_id = $this->driver_id;

        return [
            'driver_task_plan_id' => ['required', 'exists:driver_task_plans,id', new DriverTaskPlanAllowDriverRule($driver_task_plan_id, $driver_id)],
            'driver_id' => ['nullable', 'exists:drivers,id'],
        ];
    }

    /**
     * name を変更
     */
    public function attributes()
    {
        return [
            'driver_task_plan_id' => '稼働依頼プラン',
            'driver_id' => 'ドライバー',
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
        if (Route::is('*api.*')) {
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
