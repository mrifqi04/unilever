<?php


namespace App\export;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HunterExportSurvey implements WithHeadings, WithColumnFormatting, FromCollection
{
    private $fromDate;

    private $toDate;

    /**
     * HunterExportSurvey constructor.
     * @param Carbon $fromDate
     * @param Carbon $toDate
     */
    public function __construct($fromDate, $toDate)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            'TANGGAL',
            'id unilever',
            'user type',
            'name',
            'start date',
            'end date',
            'email',
            'phone',
            'address',
            'province',
            'city',
            'district',
            'village',
            'latitude',
            'longitude',
            'active',
        ];
    }

    /**
     * @inheritDoc
     */
    public function columnFormats(): array
    {
        return [
            'H' => '#0',
        ];
    }

    /**
     * @inheritDoc
     */
    public function collection()
    {
        $sql = "
            SELECT
              ci.checkin :: DATE                                      AS survey_date,
              ci.checkin :: TIME                                      AS survey_time,
              jp.assign_to                                            AS hunter_id,
              h.name                                                  AS hunter_name,
              jp.id_juragan                                           AS juragan_id,
              j.name                                                  AS juragan_name,
              mo.id_outlet                                            AS outlet_id,
              o.name                                                  AS outlet_name,
              o.owner                                                 AS outlet_owner,
              o.address                                               AS outlet_address,
              o.phone                                                 AS outlet_phone1,
              o.phone2                                                AS outlet_phone2,
              o.latitude                                              AS outlet_latitude,
              o.longitude                                             AS outlet_longitude,
              p.name                                                  AS outlet_province,
              c.name                                                  AS outlet_city,
              d.name                                                  AS outlet_district,
              v.name                                                  AS outlet_village,
              a.answers -> 'base_outlet_survey' ->> 'pernah_survey'   AS survey_has_been,
              a.answers -> 'base_outlet_survey' ->> 'pemilik'         AS survey_owner,
              a.answers -> 'base_outlet_survey' ->> 'bersedia_survey' AS survey_want,
              a.answers -> 'base_outlet_survey' ->> 'menjadi_mitra'   AS survey_mitra,
              a.answers ->> 'id_outlet_type'                          AS survey_outlet_type,
              a.answers ->> 'id_ownership_status'                     AS survey_ownership,
              a.answers ->> 'id_street_type'                          AS survey_street_type,
              a.answers ->> 'area_radius'                             AS survey_area,
              a.answers -> 'selling' ->> 'selling'                    AS survey_selling,
              (
                SELECT string_agg(it ->> 'brandName', ',')
                FROM json_array_elements((a.answers -> 'selling' ->> 'name') :: JSON) AS it
              )                                                       AS survey_selling_brands,
              a.answers -> 'kulkas' ->> 'exist'                       AS survey_refrigerator_exist,
              a.answers -> 'kulkas' ->> 'type'                        AS survey_refrigerator_type,
              a.answers ->> 'perdana'                                 AS survey_perdana,
              a.answers ->> 'freezer'                                 AS survey_freezer,
              a.answers ->> 'electricity_capacity'                    AS survey_electricity_capacity,
              a.answers ->> 'blackout_intensity'                      AS survey_blackout_intensity,
              (
                SELECT ai.basedir || ai.filename
                FROM json_array_elements((a.answers ->> 'picture') :: JSON) AS it
                  LEFT JOIN hunter.answer_image AS ai ON it ->> 'id' = ai.id :: TEXT
                WHERE
                  it ->> 'name' = 'luartoko'
              )                                                       AS survey_picture_luartoko,
              (
                SELECT ai.basedir || ai.filename
                FROM json_array_elements((a.answers ->> 'picture') :: JSON) AS it
                  LEFT JOIN hunter.answer_image AS ai ON it ->> 'id' = ai.id :: TEXT
                WHERE
                  it ->> 'name' = 'dalamtoko'
              )                                                       AS survey_picture_dalamtoko,
              (
                SELECT ai.basedir || ai.filename
                FROM json_array_elements((a.answers ->> 'picture') :: JSON) AS it
                  LEFT JOIN hunter.answer_image AS ai ON it ->> 'id' = ai.id :: TEXT
                WHERE
                  it ->> 'name' = 'ktp'
              )                                                       AS survey_picture_ktp,
              (
                SELECT ai.basedir || ai.filename
                FROM json_array_elements((a.answers ->> 'picture') :: JSON) AS it
                  LEFT JOIN hunter.answer_image AS ai ON it ->> 'id' = ai.id :: TEXT
                WHERE
                  it ->> 'name' = 'signature'
              )                                                       AS survey_picture_signature
            FROM hunter.journey_plans AS jp
              LEFT JOIN hunter.hunter AS h ON jp.assign_to = h.id
              LEFT JOIN juragan.juragans AS j ON jp.id_juragan = j.id
              LEFT JOIN hunter.survey_outlet AS so ON jp.id = so.id_journey_plans
              LEFT JOIN outlet.map_outlet AS mo ON so.id_map_outlet = mo.id
              LEFT JOIN outlet.outlet AS o ON mo.id_outlet = o.id
              LEFT JOIN public.provinces AS p ON o.id_province = p.id
              LEFT JOIN public.cities AS c ON o.id_city = c.id
              LEFT JOIN public.districts AS d ON o.id_district = d.id
              LEFT JOIN public.villages AS v ON o.id_village = v.id
              LEFT JOIN hunter.checkins AS ci ON jp.id = ci.id_journey_plans AND jp.assign_to = ci.id_hunter AND o.id = ci.id_outlet
              LEFT JOIN hunter.answers AS a ON so.id = a.id_survey_outlet
            WHERE
              ci.is_deleted = 1 AND
              jp.start_date::DATE BETWEEN :fromDate AND :toDate
            ORDER BY
              ci.id_hunter, ci.checkin ASC
        ";
        $parameters = [
            'fromDate' => $this->fromDate->format('Y-m-d'),
            'toDate' => $this->toDate->format('Y-m-d')
        ];
        return collect(DB::select(DB::raw($sql), $parameters));
    }
}