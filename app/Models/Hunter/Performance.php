<?php

namespace App\Models\Hunter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Performance extends Model
{
    public static function getSummaries(?Carbon $from_date, ?Carbon $to_date)
    {
        $days = max($from_date->diffInDays($to_date), 1);
        $sql = "
            SELECT
              jp.id_city                                                                        AS city_id,
              c.name                                                                            AS city_name,
              (SELECT COUNT(hunter.hunter.*)
               FROM hunter.hunter
               WHERE
                 hunter.hunter.id_city = jp.id_city AND
                 hunter.hunter.end_date > now())                                                AS hunter_active,
              COUNT(so.*)                                                                       AS outlet_visit,
              SUM(CASE WHEN so.status = 1 THEN 1
                  ELSE 0 END
              )                                                                                 AS outlet_deal,
              SUM(CASE WHEN so.status = 2 THEN 1
                  ELSE 0
                  END
              )                                                                                 AS outlet_tunda,
              SUM(CASE WHEN so.status = 4 THEN 1
                  ELSE 0
                  END
              )                                                                                 AS outlet_no_deal,
              (SUM(CASE WHEN so.status = 1 THEN 1
                   ELSE 0
                   END
               ) :: FLOAT / count(so.*) :: FLOAT) * 100                                         AS coversion_rate,
              SUM(CASE WHEN so.status = 1 THEN 1
                  ELSE 0
                  END
              ) :: FLOAT / (SELECT GREATEST(COUNT(hunter.hunter.*),1)
                            FROM hunter.hunter
                            WHERE
                              hunter.hunter.id_city = jp.id_city AND
                              hunter.hunter.end_date > now()) :: FLOAT                          AS ec_rate,
              (COUNT(so.*) :: FLOAT / (SELECT GREATEST(COUNT(hunter.hunter.*),1)
                                       FROM hunter.hunter
                                       WHERE
                                         hunter.hunter.id_city = jp.id_city AND
                                         hunter.hunter.end_date > now()) :: FLOAT) :: FLOAT / :days AS avg_visit
            FROM hunter.survey_outlet AS so
              INNER JOIN hunter.journey_plans AS jp ON so.id_journey_plans = jp.id
              INNER JOIN public.cities AS c ON jp.id_city = c.id
            WHERE
              so.id_journey_plans <> '0' AND 
              so.created_date::date BETWEEN :from_date AND :to_date
            GROUP BY
              jp.id_city,
              c.name          
        ";
        $result = collect(DB::select(DB::raw($sql), [
            'days' => $days,
            'from_date' => $from_date->format('Y-m-d'),
            'to_date' => $to_date->format('Y-m-d'),
        ]));
        return $result;
    }

    public static function getDailys(?Carbon $from_date, ?Carbon $to_date)
    {
        $sql = "
            SELECT
              so.created_date :: DATE AS date,
              SUM(CASE WHEN so.status = 1
                THEN 1
                  ELSE 0 END
              ) AS outlet_deal,
              SUM(CASE WHEN so.status = 2
                THEN 1
                  ELSE 0
                  END
              ) AS outlet_tunda,
              SUM(CASE WHEN so.status = 4
                THEN 1
                  ELSE 0
                  END
              ) AS outlet_no_deal
            FROM hunter.survey_outlet AS so
            WHERE
              so.id_journey_plans <> '0'
              AND so.created_date :: DATE BETWEEN :from_date AND :to_date
            GROUP BY
              so.created_date :: DATE       
        ";
        $result = collect(DB::select(DB::raw($sql), [
            'from_date' => $from_date->format('Y-m-d'),
            'to_date' => $to_date->format('Y-m-d'),
        ]));
        return $result;
    }

    public static function getPerformance(?string $juragan_name, ?string $hunter_id, ?string $hunter_name, ?Carbon $from_date, ?Carbon $to_date)
    {
        $select = "
            SELECT
              jp.id_city                                                                        AS city_id,
              c.name                                                                            AS city_name,
              jp.id_juragan                                                                     AS juragan_id,
              j.name                                                                            AS juragan_name,
              jp.assign_to                                                                      AS hunter_id,
              h.name                                                                            AS hunter_name,
              CASE
              WHEN h.end_date > now()
                THEN 1
              ELSE 0
              END                                                                               AS hunter_active,
              (SELECT max(checkout)
               FROM hunter.checkins
               WHERE
                 hunter.checkins.id_journey_plans = so.id_journey_plans AND
                 hunter.checkins.id_hunter = jp.assign_to)                                      AS last_checkout,
              COUNT(so.*)                                                                       AS outlet_visit,
              SUM(CASE WHEN so.status = 1
                THEN 1
                  ELSE 0 END
              )                                                                                 AS outlet_deal,
              SUM(CASE WHEN so.status = 2
                THEN 1
                  ELSE 0
                  END
              )                                                                                 AS outlet_tunda,
              SUM(CASE WHEN so.status = 4
                THEN 1
                  ELSE 0
                  END
              )                                                                                 AS outlet_no_deal,
              SUM(CASE WHEN so.status = 1
                THEN 1
                  ELSE 0
                  END
              ) :: FLOAT / (SELECT GREATEST(COUNT(hunter.hunter.*), 1)
                            FROM hunter.hunter
                            WHERE
                              hunter.hunter.id_city = jp.id_city AND
                              hunter.hunter.end_date > now()) :: FLOAT                          AS ec_rate,
              (COUNT(so.*) :: FLOAT / (SELECT GREATEST(COUNT(hunter.hunter.*), 1)
                                       FROM hunter.hunter
                                       WHERE
                                         hunter.hunter.id_city = jp.id_city AND
                                         hunter.hunter.end_date > now()) :: FLOAT) :: FLOAT / 2 AS avg_visit
            FROM hunter.survey_outlet AS so
              INNER JOIN hunter.journey_plans AS jp ON so.id_journey_plans = jp.id
              INNER JOIN public.cities AS c ON jp.id_city = c.id
              INNER JOIN juragan.juragans AS j ON jp.id_juragan = j.id
              INNER JOIN hunter.hunter AS h ON jp.assign_to = h.id
        ";
        $group_by = "
            so.id_journey_plans,
            jp.id_city,
            c.name,
            jp.id_juragan,
            j.name,
            jp.assign_to,
            h.name,
            h.end_date
        ";
        $conditions = ["so.id_journey_plans <> '0' "];
        $parameters = [];
        if ($juragan_name != '') {
            $conditions[] = 'j.name ilike :juragan_name ';
            $parameters['juragan_name'] = "%{$juragan_name}%";
        }
        if ($hunter_id != '') {
            $conditions[] = 'jp.assign_to ilike :hunter_id ';
            $parameters['hunter_id'] = "%{$hunter_id}%";
        }
        if ($hunter_name != '') {
            $conditions[] = 'h.name ilike :hunter_name ';
            $parameters['hunter_name'] = "%{$hunter_name}%";
        }
        if (!is_null($from_date) && !is_null($to_date)) {
            $conditions[] = 'so.created_date :: DATE BETWEEN :from_date AND :to_date ';
            $parameters['from_date'] = $from_date;
            $parameters['to_date'] = $to_date;
        }
        $sql = $select;
        if (count($conditions) > 0) {
            $sql = $select . ' WHERE ' . join(' AND ', $conditions) . " GROUP BY " . $group_by;
        }
        $result = collect(DB::select(DB::raw($sql), $parameters));
        return $result;
    }


}
