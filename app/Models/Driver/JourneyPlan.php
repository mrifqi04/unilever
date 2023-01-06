<?php

namespace App\Models\Driver;

use App\Models\Warehouse\RoutePlan;
use App\Models\UserManagement\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class JourneyPlan extends Model
{
    protected $table = 'driver.journey_plans';
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

//    public function routePlan()
//    {
//        return $this->belongsToMany(RoutePlan::class, 'driver.journey_has_route_plan', 'id_journey_plan', 'id');
//    }

    public function driver()
    {
        return $this->belongsTo(Drivers::class, 'assign_to', 'id');
    }
	
	public function user()
    {
        return $this->belongsTo(User::class, 'assigner', 'id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicles::class, 'id_vehicle', 'id');
    }

    public function province()
    {
        return $this->belongsTo(\App\Models\Province::class, 'id_province');
    }

    public function city()
    {
        return $this->belongsTo(\App\Models\City::class, 'id_city');
    }

    public function district()
    {
        return $this->belongsTo(\App\Models\District::class, 'id_district');
    }

    public function village()
    {
        return $this->belongsTo(\App\Models\Village::class, 'id_village');
    }

    public function journeyRoute()
    {
        return $this->belongsToMany(RoutePlan::class, 'driver.journey_has_route_plans', 'id_journey_plan', 'id_route_plan');
    }
}
