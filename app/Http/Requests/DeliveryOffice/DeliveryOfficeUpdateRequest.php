<?php

namespace App\Http\Requests\DeliveryOffice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Rules\Common\KatakanaRule;
use App\Rules\DeliveryOffice\PasswordMatchRule;

class DeliveryOfficeUpdateRequest extends FormRequest
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

        $result = []; // バリデーションの内容；

        $type = $this->type;
        $login_id = Auth::guard('delivery_offices')->id();

        if ($type === 'delete') {
            $result = [
                'password' => ['required', new PasswordMatchRule()],
                'password_confirmation' => ['required', 'same:password', Password::defaults()],
            ];
        } elseif ($type === 'email') {
            $result = [
                'email' => ['required', 'email:strict,dns,spoof', 'max:255', Rule::unique('delivery_offices')->ignore($login_id, 'id')],
            ];
        } elseif ($type === 'password') {
            $result = [
                'current_password' => ['required', new PasswordMatchRule()],
                'password' => ['required', 'confirmed', Password::defaults()],
            ];
        } else {
            // delivery_company_id が 所属なし(None)の場合、入力されている場合、それ以外(null)の場合。
            $rule_delivery_company_id = ''; // 会社IDのルール
            $rule_delivery_company_name = ''; // 会社名 のルール
            if ($this->delivery_company_id === 'None') {
                $rule_delivery_company_id = ['required', Rule::in(['None'])];
                $rule_delivery_company_name = 'required|max: 255';
            } elseif ($this->delivery_company_id) {
                $rule_delivery_company_id = 'required|exists:delivery_companies,id';
                $rule_delivery_company_name = 'nullable';
            } else {
                $rule_delivery_company_id = 'required|exists:delivery_companies,id';
                $rule_delivery_company_name = 'required';
            }

            $result = [
                'name' => 'required|string|max:255',
                'manager_name_sei' => 'required|string|max:255',
                'manager_name_mei' => 'required|string|max:255',
                'manager_name_sei_kana' => ['required', 'string', 'max: 255', new KatakanaRule()],
                'manager_name_mei_kana' => ['required', 'string', 'max: 255', new KatakanaRule()],
                "delivery_company_id" => $rule_delivery_company_id,
                'delivery_company_name' => $rule_delivery_company_name,
                'post_code1' => 'required|numeric|digits:3',
                'post_code2' => 'required|numeric|digits:4',
                'addr1_id' => 'required|integer|exists:prefectures,id',
                'addr2' => 'required|string|max:255',
                'addr3' => 'required|string|max:255',
                'addr4' => 'nullable|string|max:255',
                'manager_tel' => 'required|numeric|digits_between:10,11',
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
            'name' => '営業所名',
            'manager_name_sei' => '担当者 姓',
            'manager_name_mei' => '担当者 名',
            'manager_name_sei_kana' => '担当者 姓(カナ)',
            'manager_name_mei_kana' => '担当者 名(カナ)',
            'email' => 'メールアドレス',
            'current_password' => '現在のパスワード',
            'password' => 'パスワード',
            'delivery_company_id' => '配送会社',
            "delivery_company_name" => '会社名',
            'post_code1' => '郵便番号1',
            'post_code2' => '郵便番号2',
            'addr1_id' => '住所(都道府県)',
            'addr2' => '住所(市区町村)',
            'addr3' => '住所(丁目 番地 号)',
            'addr4' => '住所(建物名 部屋番号)',
            'manager_tel ' => '担当者 電話番号',
        ];
    }
    /**
     * メッセージ内容変更
     */
    public function messages()
    {
        return [
            'password.confirmed' => 'パスワードと確認用パスワードが一致していません',
            'password_confirmation.required' => 'パスワードは必ず指定してください。',
            'password_confirmation.same' => 'パスワードと確認用パスワードが一致していません',
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
