<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use App\Rules\Common\KatakanaRule;

class DeliveryOfficeCreateRequest extends FormRequest
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
     * Get the validation rules that apply to the reqgit branchuest.
     *
     * @return array
     */
    public function rules()
    {
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
        return [
            'name' => 'required|max: 255',
            'manager_name_sei' => 'required|max: 255',
            'manager_name_mei' => 'required|max: 255',
            'manager_name_sei_kana' => ['required', 'string', 'max: 255', new KatakanaRule()],
            'manager_name_mei_kana' => ['required', 'string', 'max: 255', new KatakanaRule()],
            'email' => 'required|email:strict,dns,spoof|max:255|unique:delivery_offices',
            'password' => ['required', 'confirmed', Password::defaults()],
            "delivery_company_id" => $rule_delivery_company_id,
            'delivery_company_name' => $rule_delivery_company_name,
            'post_code1' => 'required|numeric|digits:3',
            'post_code2' => 'required|numeric|digits:4',
            'addr1_id' => 'required|exists:prefectures,id',
            'addr2' => 'required|string|max: 255',
            'addr3' => 'required|string|max: 255',
            'addr4' => 'nullable|string|max: 255',
            'manager_tel' => 'required|numeric|digits_between:10,11',
            'charge_user_type_id' => 'required|exists:delivery_office_charge_user_types,id',
        ];
    }

    /**
     * name を変更(デフォルトname属性値)
     *
     */
    public function attributes()
    {
        return [
            'name' => '名前',
            'manager_name_sei' => '姓',
            'manager_name_mei' => '名',
            'manager_name_sei_kana' => '姓(カナ)',
            'manager_name_mei_kana' => '名(カナ)',
            'email' => 'メールアドレス',
            'password' => 'パスワード',
            "delivery_company_id" => '運送会社',
            "delivery_company_name" => '会社名',
            'post_code1' => '郵便番号1',
            'post_code2' => '郵便番号2',
            'addr1_id' => '住所(都道府県)',
            'addr2' => '住所(市区町村)',
            'addr3' => '住所(丁目 番地 号)',
            'addr4' => '住所(建物名 部屋番号)',
            'manager_tel' => '電話番号',
            'charge_user_type_id' => '請求に関するユーザーの種類'
        ];
    }

    /**
     * メッセージ内容変更
     */
    public function messages()
    {
        return [
            'password.confirmed' => 'パスワードと確認用パスワードが一致していません',
        ];
    }
}
