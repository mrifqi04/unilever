<?php
namespace App\Models;

use App\Models\Driver\DeliveryOrder;
use Illuminate\Database\Eloquent\Model;

class City extends Model {

    protected $table = 'public.cities';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function province(){
        return $this->belongsTo(Province::class);
    }

    public function districts(){
        return $this->hasMany(District::class);
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class, 'id_city', 'id');
    }
}