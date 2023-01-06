<?php

namespace App\Models\Driver;

use App\Models\Warehouse\RedeploymentRoutePlan;
use Illuminate\Database\Eloquent\Model;

class RedeploymentProgress extends Model
{
    protected $table = 'driver.redeployment_progress';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function routePlan()
    {
        return $this->hasMany(RedeploymentRoutePlan::class, 'id_delivery', 'id');
    }
}
