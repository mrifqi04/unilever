<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    protected $table = 'driver.logins';
    public $guarded = ['id'];
    protected $fillable = ['id', 'username', 'password', 'driver_id', 'created_by', 'is_deleted'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function driver()
    {
        return $this->belongsTo(Drivers::class, 'driver_id', 'id');
    }
}
