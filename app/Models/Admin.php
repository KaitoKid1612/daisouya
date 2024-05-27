<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable # ModelからAuthenticatableに変更
{
    use HasApiTokens, HasFactory, Notifiable;

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
     * 結合 アクセス権限グループ
     */
    public function joinAdminPermissionGroup()
    {
        return $this->hasOne(AdminPermissionGroup::class, 'id', 'admin_permission_group_id');
    }

    /**
     * 結合 ユーザータイプ
     */
    public function joinUserType()
    {
        return $this->hasOne(UserType::class, 'id', 'user_type_id');
    }
}
