<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Driver\HasDriverRegisterDeliveryOfficeMemoRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class DriverRegisterDeliveryOfficeMemoDestroyRequest extends FormRequest
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
            'register_office_memo_id' => ['required', new HasDriverRegisterDeliveryOfficeMemoRule],
        ];
    }

    /** 
     * パスパラメータのidをバリデーションに含める 
     */
    public function validationData()
    {
        return array_merge($this->request->all(), [
            'register_office_memo_id' => $this->route('register_office_memo_id'),
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
