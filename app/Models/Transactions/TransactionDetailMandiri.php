<?php

namespace App\Models\Transactions;

use App\Models\OutletManagement\Outlet;
use App\Models\Warehouse\Cabinets;
use Illuminate\Database\Eloquent\Model;

class TransactionDetailMandiri extends Model
{
    protected $table = 'transactions.transaction_detail_mandiri';
    protected $dateFormat = 'Y-m-d H:i:s.uO';
    public $incrementing = false;
    protected $keyType = 'string';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function transction(){
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function ShippingMandiri()
    {
        return $this->hasMany(TransactionShippingMandiri::class);
    }

    public function Outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id');
    }

    public function DestOutlet()
    {
        return $this->belongsTo(Outlet::class, 'destination_outlet_id', 'id');
    }

    public function Cabinet()
    {
        return $this->belongsTo(Cabinets::class);
    }
}
