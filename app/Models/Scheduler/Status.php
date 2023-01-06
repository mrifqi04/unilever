<?php

namespace App\Models\Scheduler;


use Illuminate\Database\Eloquent\Model;


/**
 *  Class JuraganJob
 * This is the model class for table "jobs.statuses"
 *
 * @property string $id
 * @property string $name
 * @property \Carbon $created_at
 */
class Status extends Model {
    protected $table = 'jobs.statuses';
    public $guarded = ['id'];
    protected $dateFormat = 'Y-m-d H:i:s.u O';
    public $timestamps = false;

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

}