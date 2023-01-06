<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class TransactionMandiriTimelines extends Model
{
    protected $table = 'transactions.transaction_mandiri_timelines';
    protected $dateFormat = 'Y-m-d H:i:s.u O';
    public $incrementing = false;
    protected $keyType = 'string';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function Status()
    {
        return $this->belongsTo(Status::class);
    }
}
