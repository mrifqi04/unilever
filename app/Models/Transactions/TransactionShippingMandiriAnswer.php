<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class TransactionShippingMandiriAnswer extends Model
{
    protected $table = 'transactions.transaction_shipping_mandiri_answer';
    protected $dateFormat = 'Y-m-d H:i:s.u O';
    public $incrementing = false;
    protected $keyType = 'string';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

}
