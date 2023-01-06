<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model {

    protected $table = 'public.villages';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function district(){
        return $this->belongsTo(District::class);
    }
}