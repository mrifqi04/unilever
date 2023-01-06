<?php

namespace App\Models\Hunter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DailyMonitoring extends Model
{
    public static function getMonitoring($hunter_id, $hunter_name, $date)
    {
        $select = "
          SELECT 
          o.id_city    AS city_id, 
          c.name       AS city_name, 
          o.id_juragan AS juragan_id, 
          j.name       AS juragan_name, 
          jp.assign_to AS hunter_id, 
          h.name       AS hunter_name, 
          o.name       AS outlet_name, 
          ci.checkin, 
          ci.checkout, 
          CASE 
          WHEN NOT checkin IS NULL AND NOT checkout ISNULL 
            THEN 
              (DATE_PART('day', checkout :: TIMESTAMP - checkin :: TIMESTAMP) * 24 + 
               DATE_PART('hour', checkout :: TIMESTAMP - checkin :: TIMESTAMP)) * 60 + 
              DATE_PART('minute', checkout :: TIMESTAMP - checkin :: TIMESTAMP) 
          ELSE 0 
          END             duration, 
          o.latitude, 
          o.longitude, 
          so.status    AS status_id, 
          CASE 
          WHEN so.status = 1 
            THEN 'Deal' 
          WHEN so.status = 2 
            THEN 'Tunda' 
          WHEN so.status = 3 
            THEN 'Approve Juragan' 
          WHEN so.status = 4 
            THEN 'Batal' 
          ELSE 
            'Unknown' 
          END          AS status_name 
        FROM hunter.survey_outlet AS so 
          LEFT JOIN outlet.map_outlet mo ON so.id_map_outlet = mo.id 
          LEFT JOIN outlet.outlet o ON mo.id_outlet = o.id 
          LEFT JOIN public.cities AS c ON o.id_city = c.id 
          LEFT JOIN juragan.juragans AS j ON o.id_juragan = j.id 
          LEFT JOIN hunter.journey_plans AS jp ON so.id_journey_plans = jp.id  
          LEFT JOIN hunter.hunter AS h ON jp.assign_to = h.id 
          LEFT JOIN hunter.checkins AS ci ON so.id_journey_plans = ci.id_journey_plans AND mo.id_outlet = ci.id_outlet ";
        $conditions = ["so.id_journey_plans <> '0' "];
        $parameters = [];
        if ($hunter_id != '') {
            $conditions[] = 'jp.assign_to = :hunter_id ';
            $parameters['hunter_id'] = $hunter_id;
        }
        if ($hunter_name != '') {
            $conditions[] = 'lower(h.name) = :hunter_name ';
            $parameters['hunter_name'] = strtolower($hunter_name);
        }
        if ($date != '') {
            $conditions[] = 'so.created_date::date = :date ';
            $parameters['date'] = $date;
        }
        $sql = $select;
        if (count($conditions) > 0) {
            $sql = $select . ' WHERE ' . join(' AND ', $conditions);
        }
        $result = collect(DB::select(DB::raw($sql), $parameters));
        return $result;
    }
}
