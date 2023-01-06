<?php

namespace App\Models\Driver;

use App\Models\Warehouse\RoutePlan;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    protected $table = 'driver.delivery_orders';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';
    public $timestamps = false;
    protected $fillable = ['id', 'adr', 'id_province', 'created_date', 'created_at', 'id_city', 'is_deleted', 'created_by'];

    public function routePlan()
    {
        return $this->hasMany(RoutePlan::class, 'id_delivery_order', 'id');
    }

    public function province()
    {
        return $this->belongsTo(\App\Models\Province::class, 'id_province');
    }

    public function city()
    {
        return $this->belongsTo(\App\Models\City::class, 'id_city', 'id');
    }

    public function district()
    {
        return $this->belongsTo(\App\Models\District::class, 'id_district');
    }

    public function village()
    {
        return $this->belongsTo(\App\Models\Village::class, 'id_village');
    }
}
