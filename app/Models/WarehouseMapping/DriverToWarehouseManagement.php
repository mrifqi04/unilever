<?php

namespace App\Models\WarehouseMapping;

use Illuminate\Database\Eloquent\Model;

class DriverToWarehouseManagement extends Model
{
    protected $table = 'driver.driver_to_warehouses';
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
        'id_driver_mappings',
        'driver_total',
        'submitted_by',
        'updated_by'
    ];

    protected $casts = [
        'id_driver_mappings' => 'array'
    ];

}