<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model {

    protected $table = 'public.provinces';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function cities(){
        return $this->hasMany(City::class);
    }
}