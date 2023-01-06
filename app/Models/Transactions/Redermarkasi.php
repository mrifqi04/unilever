<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class Redermarkasi extends Model
{
    protected $table = 'transactions.redermarkasi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $dates = [
        'approved_at',
        'created_at',
        'updated_at'
    ];

    public function getDateFormat() {
        return 'Y-m-d H:i:s.u';
    }

    protected $fillable = [
        'id', 
        'id_juragan_asal', 
        'id_juragan_tujuan',
        'id_submitted_outlets',
        'submitted_by',
        'latest_status',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'id_submitted_outlets' => 'array'
    ];

    public function juraganAsal(){
        return $this->hasOne(\App\Models\JuraganManagement\Juragan::class,'id_juragan_asal');
    }

    public function juraganTujuan(){
        return $this->hasOne(\App\Models\JuraganManagement\Juragan::class,'id_juragan_tujuan');
    }

}