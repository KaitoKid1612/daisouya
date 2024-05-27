<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOfficeTaskReview extends Model
{
    use HasFactory;

    protected $guarded = [];
    /**
     * ドライバーと結合
     *
     * @return void
     */
    public function joinDriver()
    {
        return $this->hasOne(Driver::class, 'id', 'driver_id');
    }

    /**
     * 稼働依頼と結合
     *
     * @return void
     */
    public function joinTask()
    {
        return $this->hasOne(DriverTask::class, 'id', 'driver_task_id');
    }

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
     * ステータスと結合
     *
     * @return void
     */
    public function joinPublicStatus()
    {
        return $this->hasOne(DeliveryOfficeTaskReviewPublicStatus::class, 'id', 'review_public_status_id');
    }
}
