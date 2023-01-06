<?php

namespace App\Models\Auditor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Form
 * @package App\Models\Auditor
 * @property string name
 * @property string list_questions
 * @property int created_at
 * @property int updated_at
 * @property int created_by
 * @property string created_by_name
 * @property string updated_by
 * @property string updated_by_name
 * @property string id
 * @property int is_deleted
 * @property Carbon active_date
 * @property Carbon expired_date
 * @method static Form find(string $id)
 */
class Form extends Model
{
    protected $table = "auditor.forms";
    protected $primaryKey = "id";
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public $incrementing = false;
}
