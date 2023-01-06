<?php

namespace App\Models\Scheduler;


use Illuminate\Database\Eloquent\Model;


/**
 *  Class JuraganJob
 * This is the model class for table "jobs.juragan_job"
 *
 * @property string $id
 * @property integer $type
 * @property string $file_path
 * @property string $file_name
 * @property string $file_name_origin
 * @property string $error_file
 * @property string $error_description
 * @property integer $status_id
 * @property \Carbon $created_at
 * @property \Carbon $updated_at
 * @property string $created_by
 */
class JuraganJob extends Model {
    protected $table = 'jobs.juragan_job';
    public $guarded = ['id'];
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function Status(){
        return $this->belongsTo(Status::class);
    }
}