<?php

namespace App\Models\Auditor;

use App\Models\City;
use App\Models\District;
use App\Models\JuraganManagement\Juragan;
use App\Models\Province;
use App\Models\Village;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class JourneyPlan
 * @package App\Models\Auditor
 *
 * @property string id
 * @property string name
 * @property string id_juragan
 * @property string id_form
 * @property string id_province
 * @property string id_city
 * @property string id_district
 * @property string id_village
 * @property string assigner
 * @property string assign_to
 * @property Carbon start_date
 * @property Carbon end_date
 * @property int created_at
 * @property string created_by
 * @property int updated_at
 * @property string updated_by
 * @property int is_deleted
 * @method static JourneyPlan find(string $id)
 * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 */
class JourneyPlan extends Model
{
    protected $table = "auditor.journey_plans";
    protected $primaryKey = "id";
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public $incrementing = false;
    public $timestamps = false;

    public function juragan()
    {
        return $this->belongsTo(Juragan::class, 'id_juragan');
    }

    public function form()
    {
        return $this->belongsTo(Form::class, 'id_form');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'id_province');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'id_city');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'id_district');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'id_village');
    }

    public function assignTo()
    {
        return $this->belongsTo(Auditor::class, 'assign_to');
    }

    public function auditPlans()
    {
        return $this->hasMany(AuditPlan::class, 'id_journey_plan');
    }
}
