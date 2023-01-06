<?php

namespace App\Models\Warehouse;

use App\Models\Driver\DeliveryOrder;
use App\Models\Driver\RedeploymentProgress;
use App\Models\Driver\RedeploymentJourneyPlan;
use App\Models\Driver\VehicleDriver;
use App\Models\OutletManagement\Outlet;
use Illuminate\Database\Eloquent\Model;

class RedeploymentRoutePlan extends Model
{
    protected $table = 'driver.redeployment_route_plans';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';
    public $timestamps = false;

    public function deliveryProgress()
    {
        return $this->hasMany(RedeploymentProgress::class, 'id_route_plan');
    }

    public function deliveryOrders()
    {
        return $this->belongsTo(DeliveryOrder::class, 'id_delivery_order', 'id');
    }

    public function cabinet()
    {
        return $this->belongsTo(Cabinets::class, 'id_cabinet', 'id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id');
    }

    public function journeyRoute()
    {
        return $this->belongsToMany(RedeploymentJourneyPlan::class, 'driver.redeployment_journey_has_route_plans', 'id_route_plan', 'id_journey_plan');
    }
}
