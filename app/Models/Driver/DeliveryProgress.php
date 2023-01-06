<?php

namespace App\Models\Driver;

use App\Models\Warehouse\RoutePlan;
use Illuminate\Database\Eloquent\Model;

class DeliveryProgress extends Model
{
    protected $table = 'driver.delivery_progress';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function routePlan()
    {
        return $this->hasMany(RoutePlan::class, 'id_delivery', 'id');
    }
}
