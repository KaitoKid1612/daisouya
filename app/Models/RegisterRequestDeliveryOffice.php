<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterRequestDeliveryOffice extends Model
{
    use HasFactory;
    
    protected $guarded = [];


    /**
     * 配送会社と結合
     *
     * @return void
     */
    public function joinCompany()
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

    public function getFullNameAttribute()
    {
        return $this->manager_name_sei . " " . $this->manager_name_mei;
    }

    public function getFullNameKanaAttribute()
    {
        return $this->manager_name_sei_kana . " " . $this->manager_name_mei_kana;
    }

    /**
     * 登録申請ステータス 結合
     *
     * @return void
     */
    public function get_register_request_status()
    {
        return $this->hasOne(RegisterRequestStatus::class, 'id', 'register_request_status_id');
    }
}
