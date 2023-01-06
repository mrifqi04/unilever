<?php

namespace App\Models\OutletManagement;


use Illuminate\Database\Eloquent\Model;


/**
 *  Class StreetType
 * This is the model class for table "outlet.street_types"
 *
 * @property integer $id primary key
 * @property string $name
 * @property integer $status_active
 * @property integer $created_at
 * @property string $created_by
 * @property integer $updated_at
 * @property string $updated_by
 * @property integer $is_deleted
 */
class StreetType extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'outlet.street_types';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function outlets(){
        return $this->hasMany(Outlet::class,'id_street_type');
    }

}