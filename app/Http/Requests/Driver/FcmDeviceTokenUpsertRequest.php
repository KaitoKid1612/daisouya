<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Route;
use App\Rules\Driver\UniqueFcmDeviceTokenRule;

class FcmDeviceTokenUpsertRequest extends FormRequest
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
        $device_name = $this->device_name ?? '';
        $fcm_token = $this->fcm_token ?? '';
        
        return [
            'device_name' => ['required', 'string', 'max:255'],
            'fcm_token' => ['required', 'string', 'max:255',  new UniqueFcmDeviceTokenRule($device_name, $fcm_token)],
        ];
    }

    /**
     * name を変更
     */
    public function attributes()
    {
        return [
            'device_name' => 'デバイス名',
            'fcm_token' => 'FCMデバイストークン',
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
