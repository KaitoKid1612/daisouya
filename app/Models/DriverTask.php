<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Prefecture;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverTask extends Model
{
    use HasFactory;
    use SoftDeletes; //ソフトディレート有効化

    protected $guarded = [];

    /**
     * 配送営業所と結合
     *
     * @return void
     */
    public function joinOffice()
    {
        return $this->hasOne(DeliveryOffice::class, 'id', 'delivery_office_id');
    }

    /**
     *  ドライバーと結合
     */
    public function joinDriver()
    {
        return $this->hasOne(Driver::class, 'id', 'driver_id');
    }

    /**
     * 稼働ステータスと結合
     */
    public function joinTaskStatus()
    {
        return $this->hasOne(DriverTaskStatus::class, 'id', 'driver_task_status_id');
    }

    /**
     * 結合 稼働依頼プラン
     */
    public function joinDriverTaskPlan()
    {
        return $this->hasOne(DriverTaskPlan::class, 'id', 'driver_task_plan_id');
    }

    /**
     * レビューと結合
     */
    public function joinDriverReview()
    {
        return $this->hasOne(DriverTaskReview::class, 'driver_task_id', 'id');
    }

    public function joinDeliveryOfficeReview()
    {
        return $this->hasOne(DeliveryOfficeTaskReview::class, 'driver_task_id', 'id');
    }

    /**
     * 支払いステータスと結合
     */
    public function joinTaskPaymentStatus()
    {
        return $this->hasOne(DriverTaskPaymentStatus::class, 'id', 'driver_task_payment_status_id');
    }

    /**
     * 返金ステータスと結合
     */
    public function joinTaskRefundStatus()
    {
        return $this->hasOne(DriverTaskRefundStatus::class, 'id', 'driver_task_refund_status_id');
    }

    /**
     * 稼働依頼プランと結合
     */
    public function joinDriverTaskPlans()
    {
        return $this->hasOne(DriverTaskPlan::class, 'id', 'driver_task_plan_id');
    }

    /**
     * アクセサ 
     * 稼働依頼をした営業所の都道府県取得
     */
    public function getPrefectureNameAttribute()
    {
        $prefecture =  Prefecture::select(['id', 'name'])
            ->where('id', $this->joinOffice->addr1_id)
            ->first();

        return $prefecture->name ?? '';
    }

    /**
     * 集荷先 都道府県と結合
     *
     * @return void
     */
    public function joinAddr1()
    {
        return $this->hasOne(Prefecture::class, 'id', 'task_addr1_id');
    }

    /**
     * 集荷先 フル郵便番号
     */
    public function getFullPostCodeAttribute()
    {
        return '〒' . $this->task_post_code1 . "-" . $this->task_post_code2;
    }

    /**
     * 集荷先 フル住所
     */
    public function getFullAddrAttribute()
    {
        $addr1 = $this->joinAddr1->name ?? '';
        $addr2 = $this->task_addr2 ?? '';
        $addr3 = $this->task_addr3 ?? '';
        $addr4 = $this->task_addr4 ?? '';
        $full_addr = "$addr1 $addr2 $addr3 $addr4";

        return $full_addr;
    }

    /**
     * アクセサ 
     * 稼働日を整形
     */
    public function getTaskDateYMDAttribute()
    {
        $datetime = new \DateTime($this->task_date);
        return $datetime->format('Y-m-d');
    }

    /**
     * 合計料金 システム利用料金 + 緊急依頼料金 + 税金 - 値引き額
     */
    public function getTotalPriceAttribute()
    {
        $total = ($this->system_price ?? 0) + ($this->busy_system_price ?? 0) + ($this->freight_cost ?? 0) + ($this->emergency_price ?? 0) + ($this->tax ?? 0) - ($this->discount ?? 0);
        return $total;
    }
}
