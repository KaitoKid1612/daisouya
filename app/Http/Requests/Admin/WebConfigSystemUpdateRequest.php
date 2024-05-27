<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WebConfigSystemUpdateRequest extends FormRequest
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
            'email_notice' => 'required|string|email:strict,dns,spoof|max:255',
            'email_from' => 'required|string|email:strict,dns,spoof|max:255',
            'email_reply_to' => 'required|string|email:strict,dns,spoof|max:255',
            'email_no_reply' => 'required|string|email:strict,dns,spoof|max:255',
            'create_task_time_limit_from' => 'required|integer',
            'create_task_time_limit_to' => 'required|integer|gte:create_task_time_limit_from',
            'create_task_hour_limit' => 'required|integer|between:0,23',
            'task_time_out_later' => 'required|integer',
            'register_request_token_time_limit' => 'required|integer',
            'default_price' => 'required|integer|min:0|max:100000000',
            'default_emergency_price' => 'required|integer|min:0|max:100000000',
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'default_stripe_payment_fee_rate' => 'required|numeric|min:0|max:100',
            'soon_price_time_limit_from' => 'required|integer',
            'soon_price_time_limit_to' => 'required|integer|gt:soon_price_time_limit_from',
        ];
    }

    /**
     * name を変更
     */
    public function attributes()
    {
        return [
            'email_notice' => '通知を受け取るメールアドレス',
            'email_from' => '送信用メールアドレス(from)',
            'email_reply_to' => '返信受付メールアドレス(reply-to)',
            'email_no_reply' => '返信不可メールアドレス(no-reply)',
            'create_task_time_limit_from' => '稼働可能日の範囲 何日後から',
            'create_task_time_limit_to' => '稼働可能日の範囲 何日後まで',
            'create_task_hour_limit' => '稼働日として設定できる範囲 時間指定 何時まで',
            'task_time_out_later' => '稼稼働日の何日前に「新規」の稼働を「時間切れ」にするか',
            'register_request_token_time_limit' => '登録申請トークンの有効期限',
            'default_price' => '既定のシステム利用料金',
            'default_emergency_price' => '既定の緊急依頼料金',
            'default_tax_rate' => '既定の税率',
            'default_stripe_payment_fee_rate' => '既定のstripe決済手数料率',
            'soon_price_time_limit_from' => '緊急依頼の時間の範囲 稼働日0時0分の±何時から',
            'soon_price_time_limit_to' => '緊急依頼の時間の範囲。 稼働日0時0分の±何時まで',
        ];
    }
}
