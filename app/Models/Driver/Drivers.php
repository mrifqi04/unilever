<?php

namespace App\Models\Driver;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Drivers extends Model
{
    protected $table = 'driver.drivers';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public $timestamps = false;

    public function getCreatedAtAttribute($value)
    {
        $date = Carbon::createFromTimestamp($value);
        return $date;
    }

    public function getUpdatedAtAttribute($value)
    {
        $date = Carbon::createFromTimestamp($value);
        return $date;
    }

    public function journey()
    {
        return $this->hasOne(JourneyPlan::class, 'assign_to', 'id');
    }

    public function province(){
        return $this->belongsTo(\App\Models\Province::class,'id_province');
    }

    public function city(){
        return $this->belongsTo(\App\Models\City::class,'id_city');
    }

    public function district(){
        return $this->belongsTo(\App\Models\District::class,'id_district');
    }

    public function village(){
        return $this->belongsTo(\App\Models\Village::class,'id_village');
    }

    public function login(){
        return $this->hasOne(Login::class, 'driver_id', 'id');
    }
}
