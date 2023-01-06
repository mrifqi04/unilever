<?php

namespace App\Models\Transactions;

use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Model;

class TransactionApproval extends Model
{
    protected $table = 'transactions.transaction_approval';
    protected $dateFormat = 'Y-m-d H:i:s.uO';
    public $incrementing = false;
    protected $keyType = 'string';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function Transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function ApprovalStatus()
    {
        return $this->belongsTo(ApprovalStatus::class);
    }

    public function UliUser()
    {
        return $this->belongsTo(User::class, 'unilever_user_id', 'id');
    }

    public function AsmUser()
    {
        return $this->belongsTo(User::class, 'asm_user_id', 'id');
    }
}
