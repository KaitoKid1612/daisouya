<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\DeliveryOffice;

class Driver extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes; //ソフトディレート有効化

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
     * 結合 ドライバープラン
     */
    public function joinDriverPlan()
    {
        return $this->hasOne(DriverPlan::class, 'id', 'driver_plan_id');
    }

    /**
     * 結合 申請状況
     */
    public function joinDriverEntryStatusId()
    {
        return $this->hasOne(DriverEntryStatus::class, 'id', 'driver_entry_status_id');
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
     * レビューと結合
     *
     * @return void
     */
    public function joinDriverReview()
    {
        return $this->hasMany(DriverTaskReview::class, 'driver_id');
    }

    /**
     * 稼働依頼と結合
     *
     * @return void
     */
    public function joinTask()
    {
        return $this->hasMany(DriverTask::class, 'driver_id');
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
     * 登録済み営業所と結合
     *
     * @return void
     */
    public function joinRegisterOffice()
    {
        return $this->hasMany(DriverRegisterDeliveryOffice::class, 'driver_id');
    }

    /**
     * 登録済み営業所メモと結合
     *
     * @return void
     */
    public function joinRegisterOfficeMemo()
    {
        return $this->hasMany(DriverRegisterDeliveryOfficeMemo::class, 'driver_id');
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
     * フル郵便番号
     */
    public function getFullPostCodeAttribute()
    {
        return '〒' . $this->post_code1 . "-" . $this->post_code2;
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
     * 登録済み営業所の名前一覧取得
     * 
     * @return String
     */
    public function getRegisterOfficeNameAttribute()
    {
        $data = "";
        foreach ($this->joinRegisterOffice as $office) {
            $delivery_office = DeliveryOffice::select('name')->where('id', $office->id)->first();
            // logger($delivery_office->toArray()['name']);
            $delivery_office_name = $delivery_office->name ?? '';
            $data .= "{$delivery_office_name},";
        }
        return rtrim($data, ",");
    }
}
