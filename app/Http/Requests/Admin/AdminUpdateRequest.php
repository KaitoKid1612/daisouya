<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AdminUpdateRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|email:strict,dns,spoof|max:255',
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'admin_permission_group_id' => 'required|exists:admin_permission_groups,id'
        ];
    }

    /**
     * name を変更(デフォルトname属性値)
     *
     */
    public function attributes()
    {
        return [
            'name' => '管理者名',
            'email' => 'メールアドレス',
            'password' => 'パスワード',
            'password_confirmation' => '確認用パスワード',
            'admin_permission_group_id' => 'アクセス権限グループ',
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
