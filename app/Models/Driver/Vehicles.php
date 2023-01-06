<?php

namespace App\Models\Driver;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Vehicles extends Model
{
    protected $table = 'driver.vehicles';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.uO';

    public function getCreatedAtAttribute($value)
    {
        $date = Carbon::parse($value)->format('Y-m-d H:i:s');
        return $date;
    }

    public function getUpdatedAtAttribute($value)
    {
        $date = Carbon::parse($value)->format('Y-m-d H:i:s');
        return $date;
    }

    public function journeyPlan()
    {
        return $this->hasOne(JourneyPlan::class, 'id_vehicle', 'id');
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
}
