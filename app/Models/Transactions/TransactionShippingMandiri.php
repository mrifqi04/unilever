<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class TransactionShippingMandiri extends Model
{
    protected $table = 'transactions.transaction_shipping_mandiri';
    protected $dateFormat = 'Y-m-d H:i:s.u O';
    public $incrementing = false;
    protected $keyType = 'string';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function ShippingType()
    {
        return $this->belongsTo(ShippingType::class);
    }

    public function DetailMandiri()
    {
        return $this->belongsTo(TransactionDetailMandiri::class);
    }

    public function ShippingMandiriAnswer()
    {
        return $this->hasOne(TransactionShippingMandiriAnswer::class);
    }

}
