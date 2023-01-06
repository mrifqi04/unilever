<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Model;

class ApprovalStatus extends Model
{
    protected $table = 'transaction.approval_status';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function TransactionApproval(){
        return $this->hasMany(TransactionApproval::class);
    }
}
