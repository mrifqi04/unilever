<?php

namespace App\Models\OutletManagement;


use Illuminate\Database\Eloquent\Model;


/**
 *  Class RetractionRejectReason
 * This is the model class for table "outlet.retraction_reject_reasons"
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
class RetractionRejectReason extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'outlet.retraction_reject_reasons';
    public $guarded = ['id'];
    public $incrementing = true;
    protected $keyType = 'string';
    public $timestamps = false;

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }
}