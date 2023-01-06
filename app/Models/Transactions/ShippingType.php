<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class ShippingType extends Model
{
    protected $table = 'transaction.shipping_type';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function ShippingMandiri()
    {
        return $this->hasMany(TransactionShippingMandiri::class);
    }
}
