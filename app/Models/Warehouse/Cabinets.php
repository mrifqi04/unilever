<?php

namespace App\Models\Warehouse;

use App\Models\OutletManagement\Outlet;
use Illuminate\Database\Eloquent\Model;

class Cabinets extends Model
{
    protected $table = 'warehouse.cabinets';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';
    public $timestamps = false;

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet.outlet_has_cabinets', 'cabinet_id');
    }

    public function routePlan()
    {
        return $this->hasOne(RoutePlan::class, 'id_cabinet', 'id');
    }

}
