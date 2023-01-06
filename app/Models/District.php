<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model {

    protected $table = 'public.districts';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function villages(){
        return $this->hasMany(Village::class);
    }
}