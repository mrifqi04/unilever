<?php

namespace App\Models\WarehouseMapping;

use Illuminate\Database\Eloquent\Model;

class JuraganToWarehouseManagement extends Model
{
    protected $table = 'juragan.juragan_to_warehouses';
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
        'id_juragan_mappings',
        'juragan_total',
        'submitted_by',
        'updated_by'
    ];

    protected $casts = [
        'id_juragan_mappings' => 'array'
    ];

}