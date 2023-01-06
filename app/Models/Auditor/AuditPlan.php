<?php

namespace App\Models\Auditor;

use App\Models\City;
use App\Models\District;
use App\Models\JuraganManagement\Juragan;
use App\Models\OutletManagement\Outlet;
use App\Models\Province;
use App\Models\Village;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class AuditPlan
 * @package App\Models\Auditor
 *
 * @property string id
 * @property string id_journey_plan
 * @property string id_outlet
 * @property int is_deleted
 * @property int created_at
 * @property int updated_at
 * @property string created_by
 * @property string updated_by
 * @property Outlet outlet
 * @method static AuditPlan find(string $id)
 * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 */
class AuditPlan extends Model
{
    protected $table = "auditor.audit_plans";
    protected $primaryKey = "id";
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';
    protected $fillable = ['id', 'id_journey_plan', 'id_outlet', 'is_deleted', 'created_at', 'updated_at',
        'created_by', 'updated_by'];

    public $incrementing = false;
    public $timestamps = false;

    public function journeyPlan()
    {
        return $this->belongsTo(JourneyPlan::class, 'id_journey_plan');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }


}
