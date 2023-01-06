<?php

namespace App\Models\Scheduler;

use Illuminate\Database\Eloquent\Model;

class AuditorJob extends Model
{
    protected $table = 'jobs.auditor_job';
    public $guarded = ['id'];
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function Status()
    {
        return $this->belongsTo(Status::class);
    }
}
