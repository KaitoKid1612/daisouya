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
use App\Rules\Driver\PasswordMatchRule;

class DriverUpdateRequest extends FormRequest
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
        $login_id = Auth::guard('delivery_offices')->id();

        // APIの時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_id = Auth::id();
            $login_user = Auth::user();
        }

        $type = $this->type;

        $result = [];
        if ($type === 'email') {
            $result = [
                'email' => ['required', 'email:strict,dns,spoof', 'max:255', Rule::unique('drivers')->ignore($login_id, 'id')],
            ];
        } elseif ($type === 'password') {
            $result = [
                'current_password' => ['required', new PasswordMatchRule()],
                'password' => ['required', 'confirmed', Password::defaults()],
            ];
        } elseif ($type === 'icon') {
            $result = [
                'icon_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            ];
        } elseif ($type === 'user') {
            $result = [
                'name_sei' => 'required|string|max:255',
                'name_mei' => 'required|string|max:255',
                'name_sei_kana' => ['required', 'string', 'max: 255', new KatakanaRule()],
                'name_mei_kana' => ['required', 'string', 'max: 255', new KatakanaRule()],
                'gender_id' => 'required|exists:genders,id',
                'birthday' => 'required|date_format:Y-m-d',
                'post_code1' => 'required|numeric|digits:3',
                'post_code2' => 'required|numeric|digits:4',
                'addr1_id' => 'required|exists:prefectures,id',
                'addr2' => 'required|string|max:255',
                'addr3' => 'required|string|max:255',
                'addr4' => 'nullable|required|string|max:255',
                'tel' => 'required|numeric|digits_between:10,11',
                'career' => 'required|string|max:1000',
                'introduction' => 'required|string|max:1000',
            ];
        } elseif ($type === 'delete') {
            $result = [
                'password' => ['required', new PasswordMatchRule()],
                'password_confirmation' => ['required', 'same:password', Password::defaults()],
            ];
        }

        return $result;
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
            'current_password' => '現在のパスワード',
            'password' => 'パスワード',
            'password_confirmation' => '確認用パスワード',
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
            'icon_img' => 'アイコン画像',
        ];
    }

    /**
     * メッセージ内容変更
     */
    public function messages()
    {
        return [
            'password.confirmed' => 'パスワードと確認用パスワードが一致していません',
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
