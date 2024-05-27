<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverTaskPlanAllowDriver extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * 結合 稼働依頼プラン
     */
    public function joinDriverTaskPlan()
    {
        return $this->hasOne(DriverTaskPlan::class, 'id', 'driver_task_plan_id');
    }

    /**
     * 結合 ドライバープラン
     */
    public function joinDriverkPlan()
    {
        return $this->hasOne(DriverPlan::class, 'id', 'driver_plan_id');
    }
}
