<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Billable;

/**
 * 配送営業所
 */
class DeliveryOffice extends Authenticatable # ModelからAuthenticatableに変更
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes; //ソフトディレート有効化
    use Billable; // 決済機能

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 結合 ユーザータイプ
     */
    public function joinUserType()
    {
        return $this->hasOne(UserType::class, 'id', 'user_type_id');
    }

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
        $addr1 = $this->joinAddr1->name ?? '';
        $addr2 = $this->addr2 ?? '';
        $addr3 = $this->addr3 ?? '';
        $addr4 = $this->addr4 ?? '';

        return "$addr1 $addr2 $addr3 $addr4";
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
     * 稼働依頼と結合
     *
     * @return void
     */
    public function joinTask()
    {
        return $this->hasMany(DriverTask::class, 'delivery_office_id', 'id');
    }

    /**
     * 依頼者(配送営業所)種類と結合
     *
     * @return void
     */
    public function joinDeliveryOfficeType()
    {
        return $this->hasOne(DeliveryOfficeType::class, 'id', 'delivery_office_type_id');
    }

    /**
     * 結合 請求に関するユーザの種類
     */
    public function joinChargeUserType()
    {
        return $this->hasOne(DeliveryOfficeChargeUserType::class, 'id', 'charge_user_type_id');
    }

    /**
     * アクセサ 会社名営業所名
     */
    public function getCompanyOfficeNameAttribute()
    {
        $company_name = $this->joinCompany->name ?? '';
        if(!$company_name) {
            $company_name = $this->delivery_company_name ?? '';
        }
        return $company_name . " " . $this->name;
    }

    /**
     * アクセサ フルネーム
     */
    public function getFullNameAttribute()
    {
        return $this->manager_name_sei . " " . $this->manager_name_mei;
    }

    /**
     * アクセサ フルネーム(ふりがな)
     */
    public function getFullNameKanaAttribute()
    {
        return $this->manager_name_sei_kana . " " . $this->manager_name_mei_kana;
    }

    /**
     *  ストライプと同期するname
     */
    public function stripeName()
    {
        return ($this->joinCompany->name ?? '') . ($this->delivery_company_name ?? '') . ($this->name ?? '') . " " . ($this->manager_name_sei ?? '') . " " . ($this->manager_name_mei ?? '');
    }
}
