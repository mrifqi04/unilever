<?php

namespace App\Models\OutletManagement;


use App\Models\Warehouse\Cabinets;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


/**
 *  Class Juragan
 * This is the model class for table "outlet.outlet"
 *
 * @property string $id primary key
 * @property string $id_juragan
 * @property string $id_unilever
 * @property string $name
 * @property string $owner
 * @property string $phone
 * @property string $phone2
 * @property string $address
 * @property string $descriptions
 * @property float $latitude
 * @property float $longitude
 * @property string $location
 * @property string $id_country
 * @property string $id_province
 * @property string $id_city
 * @property string $id_district
 * @property string $id_village
 * @property integer $id_outlet_type
 * @property integer $id_ownership_status
 * @property integer $id_street_type
 * @property integer $status_active
 * @property integer $created_at
 * @property string $created_by
 * @property integer $updated_at
 * @property string $updated_by
 * @property integer $is_deleted
 */
class Outlet extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'outlet.outlet';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    /**
     * Get formatted created_at.
     *
     * @return string
     */
    public function getCreatedAttribute(){
        return Carbon::createFromTimestamp($this->created_at);
    }

    /**
     * Get formatted updated_at.
     *
     * @return string
     */
    public function getUpdatedAttribute(){
        return Carbon::createFromTimestamp($this->updated_at);
    }

    public function juragan(){
        return $this->belongsTo(\App\Models\JuraganManagement\Juragan::class,'id_juragan');
    }

    public function mapOutlet(){
        return $this->hasOne(MapOutlet::class,'id_outlet');
    }

    public function province(){
        return $this->belongsTo(\App\Models\Province::class,'id_province');
    }

    public function city(){
        return $this->belongsTo(\App\Models\City::class,'id_city');
    }

    public function district(){
        return $this->belongsTo(\App\Models\District::class,'id_district');
    }

    public function village(){
        return $this->belongsTo(\App\Models\Village::class,'id_village');
    }

    public function StatusType(){
        return $this->belongsTo(OutletStatusType::class,'id_outlet_type');
    }

    public function OwnershipStatus(){
        return $this->belongsTo(OwnershipStatus::class,'id_ownership_status');
    }

    public function StreetType(){
        return $this->belongsTo(StreetType::class,'id_street_type');
    }

    public function Cabinets(){
        return $this->belongsToMany(Cabinets::class, 'outlet.outlet_has_cabinets', 'outlet_id', 'cabinet_id');
    }
}