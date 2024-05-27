<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryCompany extends Model
{
    use HasFactory;
    
    protected $guarded = [];


    public function joinOffice()
    {
        return $this->hasMany(DeliveryOffice::class, 'delivery_company_id', 'id');
    }
}
