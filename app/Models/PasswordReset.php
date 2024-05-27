<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    public $incrementing = false;
    protected $primaryKey = 'email';
    const UPDATED_AT = null; // updated_atは利用しない

}
