<?php

namespace App\Http\Requests\DeliveryOffice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Route;
use App\Rules\DeliveryOffice\HasDriverTaskRule;

class DriverTaskUpdateRequest extends FormRequest
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
        $type = $this->type ?? '';
        $type_list = ['complete', 'cancel', 'failure', 'payment_method'];
        $result = [
            'task_id' => [new HasDriverTaskRule()],
            'type' => ["required", Rule::in($type_list)],
        ];
        if ($type === 'payment_method') {
            $result = [
                'task_id' => [new HasDriverTaskRule()],
                'type' => ["required", Rule::in($type_list)],
                'payment_method_id' => 'required|string|min:1|max: 255',
            ];
        }
        return $result;
    }

    /**
     * name を変更
     */
    public function attributes()
    {
        return [
            'task_date' => '稼働日',
            'driver_id' => 'ドライバー',
            'driver_task_status_id' => '稼働ステータス',
            'rough_quantity' => '物量',
            'task_memo' => '稼働メモ',
            'payment_method_id' => '支払い方法',
        ];
    }

    /** 
     * パスパラメータのidをバリデーションに含める 
     */
    public function validationData()
    {
        return array_merge($this->request->all(), [
            'task_id' => $this->route('task_id'),
        ]);
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
