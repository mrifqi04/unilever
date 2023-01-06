<?php
namespace App\Models\JuraganManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 *  Class Juragan
 * This is the model class for table "juragan.juragans"
 *
 * @property string $id primary key
 * @property string $id_unilever_owner
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property string $address
 * @property string $zip_code
 * @property float $latitude
 * @property float $longitude
 * @property integer $radius_default
 * @property integer $radius_threshold
 * @property string $id_country
 * @property string $id_province
 * @property string $id_city
 * @property string $id_district
 * @property string $id_village
 * @property boolean $is_active
 * @property \Carbon $created_at
 * @property string $created_by
 * @property \Carbon $updated_at
 * @property string $updated_by
 * @property integer $is_deleted
 */
class Juragan extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'juragan.juragans';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function login() {
        return $this->hasOne(\App\Models\JuraganManagement\Login::class, 'juragan_id');
    }

    public function province(){
        return $this->belongsTo(\App\Models\Province::class,'id_province');
    }

    public function city(){
        return $this->belongsTo(\App\Models\City::class,'id_city');
    }

    public function district(){
        return $this->belongsTo(\App\Models\District::class,'id_district');
    }

    public function village(){
        return $this->belongsTo(\App\Models\Village::class,'id_village');
    }

    public function creator(){
        return $this->belongsTo(\App\Models\UserManagement\User::class,'created_by');
    }

    public function updater(){
        return $this->belongsTo(\App\Models\UserManagement\User::class,'updated_by');
    }

    public function summaryMitra($startDate, $endDate){
        $data = DB::select(DB::raw("select
          c.id,c.name as city_name, count(o.id) as total
        from outlet.outlet as o
        left join public.cities as c on c.id=o.id_city
        left join outlet.map_outlet as mo on o.id=mo.id_outlet
        where o.is_deleted = 1 and mo.is_mitra = 1
          and to_timestamp(o.created_at)::date >= '".$startDate."'::date and to_timestamp(o.created_at)::date <= '".$endDate."'::date
        group by c.id,c.name
        order by c.name"));
        return $data;
    }

    public function summaryMitraMandiri($startDate, $endDate){
        $data = DB::select(DB::raw("select
          c.id,c.name as city_name, count(o.id) as total
        from outlet.outlet as o
        left join public.cities as c on c.id=o.id_city
        left join outlet.map_outlet as mo on o.id=mo.id_outlet
        where o.is_deleted = 1 and mo.is_mitra = 1 and mo.is_mandiri = 1
          and to_timestamp(o.created_at)::date >= '".$startDate."'::date and to_timestamp(o.created_at)::date <= '".$endDate."'::date
        group by c.id,c.name
        order by c.name"));
        return $data;
    }

    public function summaryOutletProgress($startDate, $endDate){
        $data = DB::select(DB::raw("select
          c.id, c.name as city_name, count(o.id) as total,
          ev.value
        from outlet.outlet_progress as op
        left join outlet.map_outlet as mo on mo.id = op.id_map_outlet
        left join outlet.outlet as o on o.id=mo.id_outlet
        left join public.cities as c on c.id=o.id_city
        left join public.environment as ev on ev.key = op.status_progress
        where o.is_deleted = 1 and mo.is_mitra = 2
          and op.status_active = 1
          and ( (op.status_progress = '1' and ev.value='Deal') or (op.status_progress in ('2', '3') and ev.value in ('Tunda', 'Approve')) )
          and to_timestamp(o.created_at)::date >= '".$startDate."'::date and to_timestamp(o.created_at)::date <= '".$endDate."'::date
        group by c.id,c.name, ev.value
        order by c.name"));
        return $data;
    }

    public function summary($startDate, $endDate){
        $data = DB::select(DB::raw("select
               j.id_city as city_id,
               c.name as city_name,
               count(distinct j.id) as total,
               sum(case when op.status_active = 1 and o.is_deleted = 1 and mo.is_mitra = 1 and mo.is_mandiri = 2 then 1 else 0 end) as total_outlet,
               sum(case when op.status_active = 1 and o.is_deleted = 1 and mo.is_mitra = 1 and mo.is_mandiri = 1 then 1 else 0 end) as total_outlet_mandiri,
               sum(case when op.status_active = 1 and o.is_deleted = 1 and mo.is_mitra = 2 and op.status_progress = '1' and ev.value='Deal' then 1 else 0 end) as total_outlet_deal,
               sum(case when op.status_active = 1 and o.is_deleted = 1 and mo.is_mitra = 2 and op.status_progress in ('2', '3') and ev.value = 'Approve' then 1 else 0 end) as total_outlet_approved,
               sum(case when op.status_active = 1 and o.is_deleted = 1 and mo.is_mitra = 2 and op.status_progress in ('2', '3') and ev.value = 'Tunda' then 1 else 0 end) as total_outlet_tunda
            from juragan.juragans as j
            left join public.cities as c on c.id=j.id_city
            left join outlet.outlet as o on j.id=o.id_juragan
            left join outlet.map_outlet as mo on o.id=mo.id_outlet
            left join outlet.outlet_progress as op on op.id_map_outlet = mo.id
            left join public.environment as ev on ev.key = op.status_progress and ev.value=op.section
            where j.is_deleted = 1
              and to_timestamp(o.created_at)::date >= '".$startDate."'::date and to_timestamp(o.created_at)::date <= '".$endDate."'::date
            group by 1,2
        "));
        return $data;
    }

    public function statusOutletSummary($startDate, $endDate, $id, $cityId){
        $juraganWhere = "";
        $cityWhere = "";
        if($id != ""){
            $juraganWhere = " and j.name ilike '%".$id."%' ";
        }
        if($cityId != ""){
            $cityWhere = " and j.id_city='".$cityId."' ";
        }

        $data = DB::select(DB::raw("select
                   j.id_city as city_id,
                   c.name as city_name,
                   j.id,
                   j.name,
                   sum(case when mo.is_mitra = 1 and mo.is_mandiri = 2 then 1 else 0 end) as total_outlet,
                   sum(case when mo.is_mitra = 1 and mo.is_mandiri = 1 then 1 else 0 end) as total_outlet_mandiri,
                   sum(case when mo.is_mitra = 2 and op.status_progress = '1' and ev.value='Deal' then 1 else 0 end) as total_outlet_deal,
                   sum(case when mo.is_mitra = 2 and op.status_progress in ('2', '3') and ev.value = 'Approve' then 1 else 0 end) as total_outlet_approved,
                   sum(case when mo.is_mitra = 2 and op.status_progress in ('2', '3') and ev.value = 'Tunda' then 1 else 0 end) as total_outlet_tunda
            from outlet.outlet_progress as op
            left join public.environment as ev on ev.key = op.status_progress and ev.value=op.section
            left join outlet.map_outlet as mo on mo.id=op.id_map_outlet
            left join outlet.outlet as o on o.id=mo.id_outlet
            left join juragan.juragans as j on j.id=o.id_juragan
            left join public.cities as c on c.id=j.id_city
            where op.status_active = 1 and o.is_deleted = 1 and j.is_deleted = 1
              ".$juraganWhere.$cityWhere."
              and to_timestamp(o.created_at)::date >= '".$startDate."'::date and to_timestamp(o.created_at)::date <= '".$endDate."'::date
            group by 1,2,3,4
        "));
        return $data;
    }
}