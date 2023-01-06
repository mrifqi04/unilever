<?php

namespace App\Models\Scheduler;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs.job';
    public $guarded = ['id'];
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function Status(){
        return $this->belongsTo(Status::class);
    }
}
