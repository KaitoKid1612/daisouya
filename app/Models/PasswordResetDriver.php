<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetDriver extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    public $incrementing = false;
    const UPDATED_AT = null; // updated_atは利用しない
}
