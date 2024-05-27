<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverSchedule extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    /**
     *  ドライバーと結合
     */
    public function joinDriver()
    {
        return $this->hasOne(Driver::class, 'id', 'driver_id');
    }
}
