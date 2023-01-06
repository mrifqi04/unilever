<?php
namespace App\Models\Auditor;


use App\Models\City;
use App\Models\District;
use App\Models\Province;
use App\Models\UserType;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Auditor
 * @package App\Models\Auditor
 *
 * @property string email
 * @property string name
 * @property string id_user_types
 * @property Carbon start_date
 * @property Carbon end_date
 * @property int created_at
 * @property int updated_at
 * @property int created_by
 * @property string created_by_name
 * @property string updated_by
 * @property string updated_by_name
 * @property string user_type_name
 * @property int status_active
 * @property string phone
 * @property int is_deleted
 * @property string id_unilever
 * @property string address
 * @property string longitude
 * @property string latitude
 * @property string id
 * @property string id_province
 * @property string id_city
 * @property string id_district
 * @property string id_village
 * @method static Auditor find(string $id)
 */
class Auditor extends Model {
    protected $table = "auditor.auditors";
    protected $primaryKey = "id";
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public $incrementing = false;
    public $timestamps = false;

    public function getCreatedAtAttribute($value)
    {
        if ($value) {
            $date = Carbon::createFromTimestamp($value);
            return $date;
        }
        
        return $value;
    }

    public function getUpdatedAtAttribute($value)
    {
        if ($value) {
            $date = Carbon::createFromTimestamp($value);
            return $date;
        }
        return $value;
    }

    public function userType()
    {
        return $this->belongsTo(UserType::class, 'id_user_types');
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

    public function login() {
        return $this->hasOne(Login::class, 'id_auditor');
    }
}