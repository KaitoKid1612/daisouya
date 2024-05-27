<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverRegisterDeliveryOfficeMemo extends Model
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
     * 配送会社と結合
     *
     * @return void
     */
    public function joinDeliveryCompany()
    {
        return $this->hasOne(DeliveryCompany::class, 'id', 'delivery_company_id');
    }

    /**
     * フル郵便番号
     */
    public function getFullPostCodeAttribute()
    {
        return '〒' . $this->post_code1 . "-" . $this->post_code2;
    }

    /**
     * フル住所
     */
    public function getFullAddrAttribute()
    {
        return $this->joinAddr1->name . " " . $this->addr2 . " " . $this->addr3 . " " . $this->addr4;
    }

    /**
     * 都道府県と結合
     *
     * @return void
     */
    public function joinAddr1()
    {
        return $this->hasOne(Prefecture::class, 'id', 'addr1_id');
    }
}
