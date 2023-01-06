<?php

namespace App\Models\OutletManagement;

use Illuminate\Database\Eloquent\Model;

class OutletActivity extends Model
{
    protected $table = 'outlet.outlet_activity';
    public $guarded = ['id'];

    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';
    public $timestamps = false;

    public function mapOutlet(){
        return $this->belongsTo(MapOutlet::class,'id_map_outlet');
    }

    public function outletProgress(){
        return $this->hasMany(OutletProgress::class,'id_outlet_activity');
    }
}
