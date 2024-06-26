<?php

namespace App\Http\Requests\DeliveryOffice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Route;
use App\Rules\DeliveryOffice\HasPickupAddrRule;

class DeliveryPickupAddrUpdateRequest extends FormRequest
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
            'pickup_id' => ['required', new HasPickupAddrRule()],
            'delivery_company_id' => $rule_delivery_company_id,
            'delivery_company_name' => $rule_delivery_company_name,
            'delivery_office_name' => 'required|max: 255',
            'email' => 'nullable|email:strict,dns,spoof|max:255',
            'tel' => 'nullable|numeric|digits_between:10,11',
            'post_code1' => 'required|numeric|digits:3',
            'post_code2' => 'required|numeric|digits:4',
            'addr1_id' => 'required|exists:prefectures,id',
            'addr2' => 'required|string|max: 255',
            'addr3' => 'required|string|max: 255',
            'addr4' => 'nullable|string|max: 255',
        ];
    }

    /**
     * name を変更
     */
    public function attributes()
    {
        return [
            'delivery_company_id' => '配送会社名',
            'delivery_company_name' => '配送会社名',
            'delivery_office_name' => '営業所名',
            'email' => 'メールアドレス',
            'tel' => '電話番号',
            'post_code1' => '郵便番号1',
            'post_code2' => '郵便番号2',
            'addr1_id' => '都道府県',
            'addr2' => '市区町村',
            'addr3' => '丁目 番地 号',
            'addr4' => '建物名部屋番号',
        ];
    }

    /** 
     * パスパラメータのidをバリデーションに含める 
     */
    public function validationData()
    {
        return array_merge($this->request->all(), [
            'pickup_id' => $this->route('pickup_id'),
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
