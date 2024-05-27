<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Rules\Common\KatakanaRule;

class RegisterRequestCreateRequest extends FormRequest
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
        if ($this->isMethod('POST')) {
            return [
                'name_sei' => 'required|string|max: 255',
                'name_mei' => 'required|string|max: 255',
                'name_sei_kana' => ['required', 'string', 'max: 255', new KatakanaRule()],
                'name_mei_kana' => ['required', 'string', 'max: 255', new KatakanaRule()],
                // 'email' => 'required|email:strict,dns,spoof|unique:drivers,email',
                'email' => ['required', 'email:strict,dns,spoof', Rule::unique('drivers')->whereNull('deleted_at'),],
                'gender_id' => 'required|exists:genders,id',
                'birthday' => 'required|date_format:Y-m-d',
                'post_code1' => 'required|numeric|digits:3',
                'post_code2' => 'required|numeric|digits:4',
                'addr1_id' => 'required|exists:prefectures,id',
                'addr2' => 'required|string|max:255',
                'addr3' => 'required|string|max:255',
                'addr4' => 'nullable|string|max:255',
                'tel' => 'required|numeric|digits_between:10,11',
                'career' => 'required|string|max:1000',
                'introduction' => 'required|string|min:30|max:1000',
                'message' => 'nullable|string|max:1000',
                'terms_service' => 'required|accepted',
                'avatar' => 'required|mimes:jpeg,png,jpg',
                'bank' => 'required|mimes:jpeg,png,jpg',
                'driving_license_front' => 'required|mimes:jpeg,png,jpg',
                'driving_license_back' => 'required|mimes:jpeg,png,jpg',
                'auto_insurance' => 'required|mimes:jpeg,png,jpg',
                'voluntary_insurance' => 'required|mimes:jpeg,png,jpg',
                'inspection_certificate' => 'required|mimes:jpeg,png,jpg',
                'license_plate_front' => 'required|mimes:jpeg,png,jpg',
                'license_plate_back' => 'required|mimes:jpeg,png,jpg',
            ];
        } else {
            return [];
        }
    }

    /**
         * Custom message error
         *
         * @return array<string, string>
         */
        public function messages(): array
        {
            return [
                'introduction.min' => '30文字以上で入力してください。',
            ];
        }

    /**
     * name を変更(デフォルトname属性値)
     *
     */
    public function attributes()
    {
        return [
            'name_sei' => '姓',
            'name_mei' => '名',
            'name_sei_kana' => '姓(カナ)',
            'name_mei_kana' => '名(カナ)',
            'email' => 'メールアドレス',
            'password' => 'パスワード',
            'gender_id' => '性別',
            'birthday' => '誕生日',
            'post_code1' => '郵便番号1',
            'post_code2' => '郵便番号2',
            'addr1_id' => '住所(都道府県)',
            'addr2' => '住所(市区町村)',
            'addr3' => '住所(丁目 番地 号)',
            'addr4' => '住所(建物名 部屋番号)',
            'tel' => '電話番号',
            'career' => '経歴',
            'introduction' => '自己紹介',
            'message' => 'その他メッセージ',
            'terms_service' => '利用規約',
            'avatar' => '顔写真',
            'bank' => '支払い先の口座情報',
            'driving_license_front' => '運転免許証の表',
            'driving_license_back' => '運転免許証の裏',
            'auto_insurance' => '自賠責保険',
            'voluntary_insurance' => '任意保険',
            'inspection_certificate' => '車検証',
            'license_plate_front' => 'ナンバープレートを含めた自動車の画像(前方)',
            'license_plate_back' => 'ナンバープレートを含めた自動車の画像(後方)',
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