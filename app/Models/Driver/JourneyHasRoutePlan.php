<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Model;

class JourneyHasRoutePlan extends Model
{
    public $timestamps = false;
    
    protected $table = 'driver.journey_has_route_plans';

    protected $primaryKey = 'id_journey_plan';

    protected $fillable = ['id_journey_plan', 'id_route_plan'];
}
