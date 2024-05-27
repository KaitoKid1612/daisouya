<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebNoticeLog extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    const UPDATED_AT = null; // updated_atは利用しない

    /**
     * 結合 ログレベル
     */
    public function joinLogLevel()
    {
        return $this->hasOne(WebLogLevel::class, 'id', 'web_log_level_id');
    }

    /**
     * 結合 通知種類
     */
    public function joinNoticeType()
    {
        return $this->hasOne(WebNoticeType::class, 'id', 'web_notice_type_id');
    }

    /**
     * 稼働
     */
    public function joinTask()
    {
        return $this->hasOne(DriverTask::class, 'id', 'task_id');
    }
}
