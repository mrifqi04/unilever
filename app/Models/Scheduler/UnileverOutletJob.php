<?php

namespace App\Models\Scheduler;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class UnileverOutletJob
 * @package App\Models\Scheduler
 *
 * @property string $id
 * @property integer $type
 * @property string $file_path
 * @property string $file_name
 * @property string $error_file
 * @property integer $status_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $created_by
 */
class UnileverOutletJob extends Model
{
    protected $table = 'jobs.unilever_outlet_job';
    public $guarded = ['id'];
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function Status()
    {
        return $this->belongsTo(Status::class);
    }
}
