<?php

namespace App\Http\Requests\DeliveryOffice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Route;
use App\Rules\DeliveryOffice\HasStripeCustomerIdRule;
use Illuminate\Support\Facades\Auth;

class PaymentConfigStorePaymentMethodRequest extends FormRequest
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
        $login_id = Auth::guard('delivery_offices')->id(); // ログインユーザーのID
        $login_user = auth('delivery_offices')->user(); // ログインユーザーの情報

        // API時のログインユーザーの情報
        if (Route::is('api.*')) {
            $login_user = Auth::user();
            $login_id = Auth::id();
        }

        $customer_id = $login_user->stripe_id;
        $rule_login_user = [];

        if ($customer_id) {
            $rule_login_user[] = new HasStripeCustomerIdRule();
        }
        return [
            'login_user' => $rule_login_user,
            'number' => ['required', 'digits_between:14,16'],
            'exp_month' => ['required', 'digits:2', 'between:01,12'],
            'exp_year' => ['required', 'digits:2', 'between:00,99'],
            'cvc' => ['required', 'digits:3'],
        ];
    }

    /**
     * name を変更
     */
    public function attributes()
    {
        return [
            'number' => 'クレジットカード番号',
            'exp_month' => '有効期限（月）',
            'exp_year' => '有効期限（年）',
            'cvc' => 'cvc値',
        ];
    }



    /** 
     * パスパラメータのidをバリデーションに含める 
     */
    public function validationData()
    {
        return array_merge($this->request->all(), [
            'login_user' => Auth::user()->stripe_id,
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
