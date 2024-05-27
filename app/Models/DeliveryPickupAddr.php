<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryPickupAddr extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    /**
     * 営業所と結合
     *
     * @return void
     */
    public function joinOffice()
    {
        return $this->hasOne(DeliveryOffice::class, 'id', 'delivery_office_id');
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
