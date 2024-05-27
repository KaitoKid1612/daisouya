<?php

namespace App\Http\Requests\Guest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Route;
use App\Rules\Common\KatakanaRule;

class WebContactCreateRequest extends FormRequest
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
            'user_type_id' => 'required|exists:user_types,id',
            'name_sei' => 'required|string|max:255',
            'name_mei' => 'required|string|max:255',
            'name_sei_kana' => ['required', 'string', 'max: 255', new KatakanaRule()],
            'name_mei_kana' => ['required', 'string', 'max: 255', new KatakanaRule()],
            'email' => 'required|email:strict,dns,spoof|max:255',
            'tel' => 'required|numeric|digits_between:10,11',
            'web_contact_type_id' => 'required|exists:web_contact_types,id',
            'title' => 'required|string|max:200',
            'text' => 'required|string|max:2000',
        ];
    }

    /**
     * name を変更(デフォルトname属性値)
     *
     */
    public function attributes()
    {
        return [
            'user_type_id' => 'ユーザータイプ',
            'name_sei' => '姓',
            'name_mei' => '名',
            'name_sei_kana' => '姓(カナ)',
            'name_mei_kana' => '名(カナ)',
            'email' => 'メールアドレス',
            'tel' => '電話番号',
            'web_contact_type_id' => 'お問い合わせタイプ',
            'title' => 'タイトル',
            'text' => '内容',
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
