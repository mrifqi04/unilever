<?php

namespace App\Models\WarehouseMapping;

use Illuminate\Database\Eloquent\Model;

class VehicleToWarehouseManagement extends Model
{
    protected $table = 'warehouse.vehicle_to_warehouses';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function getDateFormat() {
        return 'Y-m-d H:i:s.u';
    }

    protected $fillable = [
        'id', 
        'id_warehouse_management', 
        'id_vehicle_mappings',
        'juragan_total',
        'submitted_by',
        'updated_by'
    ];

    protected $casts = [
        'id_vehicle_mappings' => 'array'
    ];

}