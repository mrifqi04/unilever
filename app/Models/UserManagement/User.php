<?php

namespace App\Models\UserManagement;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Modules\ACL\Traits\HasRoles;


/**
 *  Class User
 * This is the model class for table "public.users"
 *
 * @property string $id
 * @property string $username
 * @property string $password
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property \Carbon $created_at
 * @property string $created_by
 */
class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    protected $table = 'public.users';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
