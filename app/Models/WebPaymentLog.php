<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebPaymentLog extends Model
{
    use HasFactory; 

    protected $guarded = [];

    /**
     * 結合 支払いステータス
     */
    public function joinPaymentLogStatus()
    {
        return $this->hasOne(WebPaymentLogStatus::class, 'id', 'web_payment_log_status_id');
    }

    /**
     * 結合 支払い事由
     */
    public function joinPaymentReason()
    {
        return $this->hasOne(WebPaymentReason::class, 'id', 'web_payment_reason_id');
    }

    /**
     * 結合 支払ったユーザのユーザタイプ
     */
    public function joinPayUserType()
    {
        return $this->hasOne(UserType::class, 'id', 'pay_user_type_id');
    }

    /**
     * 結合 受け取ったユーザのユーザタイプ
     */
    public function joinReceiveUserType()
    {
        return $this->hasOne(UserType::class, 'id', 'receive_user_type_id');
    }
}
