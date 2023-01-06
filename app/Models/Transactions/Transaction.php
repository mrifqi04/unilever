<?php

namespace App\Models\Transactions;

use App\Models\JuraganManagement\Juragan;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions.transactions';
    protected $dateFormat = 'Y-m-d H:i:s.uO';
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

    public function RequestType()
    {
        return $this->belongsTo(RequestType::class);
    }

    public function Juragan()
    {
        return $this->belongsTo(Juragan::class);
    }

    public function Approval()
    {
        return $this->hasOne(TransactionApproval::class);
    }

    public function DetailMandiri()
    {
        return $this->hasOne(TransactionDetailMandiri::class, 'transaction_id', 'id');
    }

    public function MandiriTimelines()
    {
        return $this->hasMany(TransactionMandiriTimelines::class);
    }
}
