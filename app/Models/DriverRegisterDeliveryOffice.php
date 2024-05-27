<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverRegisterDeliveryOffice extends Model
{
    use HasFactory;

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
     * ドライバーと結合
     *
     * @return void
     */
    public function joinDriver()
    {
        return $this->hasOne(Driver::class, 'id', 'driver_id');
    }
}
