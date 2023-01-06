<?php

namespace App\Models\Driver;

use App\Models\Warehouse\RetractionRoutePlan;
use Illuminate\Database\Eloquent\Model;

class RetractionProgress extends Model
{
    protected $table = 'driver.retraction_progress';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function routePlan()
    {
        return $this->hasMany(RetractionRoutePlan::class, 'id_delivery', 'id');
    }
}
