<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;

class WarehouseManagement extends Model
{
    protected $table = 'warehouse.warehouse_managements';
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
        'warehouse_name', 
        'warehouse_description',
        'id_warehouse_admins'
    ];

    protected $casts = [
        'id_warehouse_admins' => 'array'
    ];

    public function juragans()
    {
        return $this->hasMany('App\Models\WarehouseMapping\JuraganToWarehouseManagement', 'id_warehouse_management')->where('is_deleted', 1);
    }

    /**
     * Get the warehouse's drivers
     */
    public function drivers()
    {
        return $this->hasMany('App\Models\WarehouseMapping\DriverToWarehouseManagement', 'id_warehouse_management')->where('is_deleted', 1);
    }

    /**
     * Get the warehouse's vehicles
     */
    public function vehicles()
    {
        return $this->hasMany('App\Models\WarehouseMapping\VehicleToWarehouseManagement', 'id_warehouse_management')->where('is_deleted', 1);
    }
}