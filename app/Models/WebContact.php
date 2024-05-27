<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebContact extends Model
{
    use HasFactory;
    
    protected $guarded = [];


    /**
     * お問い合わせタイプ 結合
     *
     * @return void
     */
    public function get_web_contact_type()
    {
        return $this->hasOne(WebContactType::class, 'id', 'web_contact_type_id');
    }

    /**
     * ステータス 結合
     *
     * @return void
     */
    public function get_web_contact_status()
    {
        return $this->hasOne(WebContactStatus::class, 'id', 'web_contact_status_id');
    }

    /**
     * ユーザータイプ 結合
     *
     * @return void
     */
    public function joinUserType()
    {
        return $this->hasOne(UserType::class, 'id', 'user_type_id');
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
}
