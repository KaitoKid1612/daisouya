<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebConfigBase extends Model
{
    use HasFactory;

    public function joinAddr1()
    {
        return $this->hasOne(Prefecture::class, 'id', 'addr1_id');
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
}
