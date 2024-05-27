<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterRequestDriver extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * 結合 ドライバープラン
     */
    public function joinDriverPlan()
    {
        return $this->hasOne(DriverPlan::class, 'id', 'driver_plan_id');
    }

    /**
     * 性別と結合
     *
     * @return void
     */
    public function joinGender()
    {
        return $this->hasOne(Gender::class, 'id', 'gender_id');
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

    /**
     * フルネームに加工
     */
    public function getFullNameAttribute()
    {
        return $this->name_sei . " " . $this->name_mei;
    }

    /**
     * フルネーム(カナ)に加工
     */
    public function getFullNameKanaAttribute()
    {
        return $this->name_sei_kana . " " . $this->name_mei_kana;
    }

    /**
     * 年齢
     */
    public function getAgeAttribute()
    {
        $birthday = new \DateTime($this->birthday);
        $now = new \DateTime();
        $interval = $now->diff($birthday);
        $age = $interval->y;

        return $age;
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
