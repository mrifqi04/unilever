<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'transactions.status';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }
}
