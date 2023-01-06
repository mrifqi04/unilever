<?php

namespace App\Models\OutletManagement;

use Illuminate\Database\Eloquent\Model;

class OutletProgress extends Model
{
    protected $table = 'outlet.outlet_progress';
    public $guarded = ['id'];
    protected $fillable = ['id', 'username', 'password', 'driver_id', 'created_by', 'is_deleted'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u';
    public $timestamps = false;

    public function outletActivity(){
        return $this->belongsTo(OutletActivity::class,'id_outlet_activity');
    }

    public function mapOutlet(){
        return $this->belongsTo(MapOutlet::class,'id_map_outlet');
    }
}
