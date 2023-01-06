<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class RequestType extends Model
{
    protected $table = 'transactions.request_type';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function Status(){
        return $this->belongsTo(Status::class);
    }
}
