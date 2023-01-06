<?php

namespace App\Models\OutletManagement;

use Illuminate\Database\Eloquent\Model;

class OutletHasCabinet extends Model
{
    protected $primaryKey = 'outlet_id';
    protected $table = 'outlet.outlet_has_cabinets';
    protected $fillable = ['outlet_id', 'cabinet_id'];
    public $timestamps = false;
}
