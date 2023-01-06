<?php

namespace App\Models\OutletManagement;


use Illuminate\Database\Eloquent\Model;


/**
 *  Class MapOutlet
 * This is the model class for table "outlet.map_outlet"
 *
 * @property string $id primary key
 * @property string $id_outlet
 * @property integer $is_mitra
 * @property \Carbon $created_date
 * @property string $created_by
 * @property \Carbon $updated_date
 * @property string $updated_by
 * @property integer $is_deleted
 */
class MapOutlet extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'outlet.map_outlet';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    public function outletProgress()
    {
        return $this->hasMany(OutletProgress::class, 'id_map_outlet');
    }

    public function outletRetractionProgress()
    {
        return $this->hasMany(OutletRetractionProgress::class, 'id_map_outlet');
    }
}