<?php

namespace App\Models\Driver;

use App\Models\Warehouse\RetractionRoutePlan;
use App\Models\UserManagement\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class RetractionJourneyPlan extends Model
{
    protected $table = 'driver.retraction_journey_plans';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    protected $fillable = [
        'id',
        'id_form',
        'id_province',
        'id_city',
        'id_district',
        'id_village',
        'assigner',
        'assign_to',
        'start_date',
        'end_date',
        'is_deleted',
        'created_at',
        'updated_at',
        'name',
        'created_by',
        'updated_by',
        'id_vehicle',
        'plan_status',
        'canceled_by',
        'canceled_at',
    ];

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
        return $this->belongsToMany(RetractionRoutePlan::class, 'driver.retraction_journey_has_route_plans', 'id_journey_plan', 'id_route_plan');
    }
}
