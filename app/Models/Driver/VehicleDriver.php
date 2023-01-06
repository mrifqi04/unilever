<?php

namespace App\Models\Driver;

use App\Models\Warehouse\RoutePlan;
use Illuminate\Database\Eloquent\Model;

class VehicleDriver extends Model
{
    protected $table = 'driver.vehicle_has_driver';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function routePlan()
    {
        return $this->hasMany(RoutePlan::class, 'id_vehicle_has_driver', 'id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicles::class, 'id_vehicle');
    }

    public function driver()
    {
        return $this->belongsTo(Drivers::class, 'id_driver');
    }
}
