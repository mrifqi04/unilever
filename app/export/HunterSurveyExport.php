<?php


namespace App\export;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Mockery\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

//WithDrawings,
class HunterSurveyExport implements WithHeadings, WithColumnFormatting, FromCollection, ShouldAutoSize, WithEvents
{
    use RegistersEventListeners;
    /**
     * @var Carbon $fromDate
     */
    private $fromDate;
    /**
     * @var Carbon $toDate
     */
    private $toDate;
    /**
     * @var Collection $collection
     */
    private $collection;

    /**
     * HunterSurveyExport constructor.
     * @param Carbon $fromDate
     * @param Carbon $toDate
     */
    public function __construct($fromDate, $toDate)
    {
        $this->collection = $this->getCollection($fromDate, $toDate);
    }

    /**
     * @inheritDoc
     */
    private function getDrawings()
    {
        $pictures = [];
        $row = 2;
        foreach ($this->collection as $item) {
            $picture_luartoko = (string)$item->survey_picture_luartoko;
            if (strlen($picture_luartoko) > 0) {
                if ($picture_luartoko[0] === '.') {
                    $picture_luartoko = config('app.image_base_dir') . '/' . substr($picture_luartoko, 1, strlen($picture_luartoko) - 1);
                }
                if (file_exists($picture_luartoko)) {
                    $drawing = new Drawing();
                    try {
                        $drawing->setName('Luar Toko');
                        $drawing->setDescription('Luar Toko');
                        $drawing->setPath($picture_luartoko);
                        $drawing->setHeight(50);
                        $drawing->setCoordinates('AM' . (string)$row);
                        $pictures[] = $drawing;
                    } catch (\Exception $exception) {
                    }
                }
            }
            $picture_dalamtoko = (string)$item->survey_picture_dalamtoko;
            if (strlen($picture_dalamtoko) > 0) {
                if ($picture_dalamtoko[0] === '.') {
                    $picture_dalamtoko = config('app.image_base_dir') . '/' . substr($picture_dalamtoko, 1, strlen($picture_luartoko) - 1);
                }
                if (file_exists($picture_dalamtoko)) {
                    try {
                        $drawing = new Drawing();
                        $drawing->setName('Dalam Toko');
                        $drawing->setDescription('Dalam Toko');
                        $drawing->setPath($picture_dalamtoko);
                        $drawing->setHeight(50);
                        $drawing->setCoordinates('AN' . (string)$row);
                        $pictures[] = $drawing;
                    } catch (\Exception $exception) {
                    }
                }
            }
            $picture_cabinet = (string)$item->survey_picture_cabinet;
            if (strlen($picture_cabinet) > 0) {
                if ($picture_cabinet[0] === '.') {
                    $picture_cabinet = config('app.image_base_dir') . '/' . substr($picture_cabinet, 1, strlen($picture_luartoko) - 1);
                }
                if (file_exists($picture_cabinet)) {
                    $drawing = new Drawing();
                    try {
                        $drawing->setName('Cabinet');
                        $drawing->setDescription('Cabinet');
                        $drawing->setPath($picture_cabinet);
                        $drawing->setHeight(50);
                        $drawing->setCoordinates('AO' . (string)$row);
                        $pictures[] = $drawing;
                    } catch (\Exception $exception) {
                    }
                }
            }
            $picture_ktp = (string)$item->survey_picture_ktp;
            if (strlen($picture_ktp) > 0) {
                if ($picture_ktp[0] === '.') {
                    $picture_ktp = config('app.image_base_dir') . '/' . substr($picture_ktp, 1, strlen($picture_luartoko) - 1);
                }
                if (file_exists($picture_ktp)) {
                    try {
                        $drawing = new Drawing();
                        $drawing->setName('Ktp');
                        $drawing->setDescription('Ktp');
                        $drawing->setPath($picture_ktp);
                        $drawing->setHeight(50);
                        $drawing->setCoordinates('AP' . (string)$row);
                        $pictures[] = $drawing;
                    } catch (\Exception $exception) {
                    }
                }
            }
            $picture_signature = (string)$item->survey_picture_signature;
            if (strlen($picture_signature) > 0) {
                if ($picture_signature[0] === '.') {
                    $picture_signature = config('app.image_base_dir') . '/' . substr($picture_signature, 1, strlen($picture_luartoko) - 1);
                }
                if (file_exists($picture_signature)) {
                    try {
                        $drawing = new Drawing();
                        $drawing->setName('signature');
                        $drawing->setDescription('signature');
                        $drawing->setPath($picture_signature);
                        $drawing->setHeight(50);
                        $drawing->setCoordinates('AU' . (string)$row);
                        $pictures[] = $drawing;
                    } catch (\Exception $exception) {
                    }
                }
            }
            $row++;
        }

        return $pictures;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->addDrawings($this->getDrawings());
            },
        ];
    }

    /**
     * @param Carbon $fromDate
     * @param Carbon $toDate
     * @return Collection
     */
    private function getCollection($fromDate, $toDate)
    {
        $sql = "
            SELECT
              ci.checkin :: DATE                                      AS survey_date,
              ci.checkin :: TIME                                      AS survey_time,
              jp.assign_to                                            AS hunter_id,
              h.name                                                  AS hunter_name,
              jp.id_juragan                                           AS juragan_id,
              j.name                                                  AS juragan_name,
              ''                                                      AS juragan_id_pjp,
              mo.id_outlet                                            AS outlet_id,
              o.latitude                                              AS outlet_latitude,
              o.longitude                                             AS outlet_longitude,
              o.name                                                  AS outlet_name,
              o.owner                                                 AS outlet_owner,
              o.address                                               AS outlet_address,
              o.phone                                                 AS outlet_phone1,
              o.phone2                                                AS outlet_phone2,
              p.name                                                  AS outlet_province,
              c.name                                                  AS outlet_city,
              d.name                                                  AS outlet_district,
              v.name                                                  AS outlet_village,
              a.answers -> 'base_outlet_survey' ->> 'pernah_survey'   AS survey_has_been,
              a.answers -> 'base_outlet_survey' ->> 'pemilik'         AS survey_owner,
              a.answers -> 'base_outlet_survey' ->> 'bersedia_survey' AS survey_want,
              a.answers -> 'base_outlet_survey' ->> 'menjadi_mitra'   AS survey_mitra,
              ''                                                      AS survey_prospektus,  
              a.answers ->> 'id_outlet_type'                          AS survey_outlet_type,
              ''                                                      AS survey_outlet_type_other,
              a.answers ->> 'id_ownership_status'                     AS survey_ownership,
              a.answers ->> 'id_street_type'                          AS survey_street_type,
              a.answers ->> 'area_radius'                             AS survey_area,
              ''                                                      AS survey_area_other,
              CASE a.answers -> 'selling' ->> 'selling'
              WHEN '1'
                THEN 'Ya'
              ELSE 'Tidak'
              END                                                     AS survey_selling,
              (
                SELECT string_agg(it ->> 'brandName', ',')
                FROM json_array_elements((a.answers -> 'selling' ->> 'name') :: JSON) AS it
              )                                                       AS survey_selling_brands,
              CASE a.answers -> 'kulkas' ->> 'exist'
              WHEN '1'
                THEN 'Ya'
              ELSE 'Tidak'
              END                                                     AS survey_refrigerator_exist,
              a.answers -> 'kulkas' ->> 'type'                        AS survey_refrigerator_type,
              CASE a.answers ->> 'perdana'
              WHEN '1'
                THEN 'Ya'
              ELSE 'Tidak'
              END                                                     AS survey_perdana,
              CASE a.answers ->> 'freezer'
              WHEN '1'
                THEN 'Ya'
              ELSE 'Tidak'
              END                                                     AS survey_freezer,
              a.answers ->> 'electricity_capacity'                    AS survey_electricity_capacity,
              a.answers ->> 'blackout_intensity'                      AS survey_blackout_intensity,
              (
                SELECT 
                    CASE
                        WHEN TRIM(ai.filename) <> '' THEN ai.basedir || ai.filename
                        ELSE ''
                    END
                FROM json_array_elements((a.answers ->> 'picture') :: JSON) AS it
                  LEFT JOIN hunter.answer_image AS ai ON it ->> 'id' = ai.id :: TEXT
                WHERE
                  it ->> 'name' = 'luartoko'
              )                                                       AS survey_picture_luartoko,
              (
                SELECT 
                    CASE
                        WHEN TRIM(ai.filename) <> '' THEN ai.basedir || ai.filename
                        ELSE ''
                    END
                FROM json_array_elements((a.answers ->> 'picture') :: JSON) AS it
                  LEFT JOIN hunter.answer_image AS ai ON it ->> 'id' = ai.id :: TEXT
                WHERE
                  it ->> 'name' = 'dalamtoko'
              )                                                       AS survey_picture_dalamtoko,
             (
                SELECT 
                    CASE
                        WHEN TRIM(ai.filename) <> '' THEN ai.basedir || ai.filename
                        ELSE ''
                    END
                FROM json_array_elements((a.answers ->> 'picture') :: JSON) AS it
                  LEFT JOIN hunter.answer_image AS ai ON it ->> 'id' = ai.id :: TEXT
                WHERE
                  it ->> 'name' = 'cabinet'
              )                                                       AS survey_picture_cabinet,
              (
                SELECT 
                    CASE
                        WHEN TRIM(ai.filename) <> '' THEN ai.basedir || ai.filename
                        ELSE ''
                    END
                FROM json_array_elements((a.answers ->> 'picture') :: JSON) AS it
                  LEFT JOIN hunter.answer_image AS ai ON it ->> 'id' = ai.id :: TEXT
                WHERE
                  it ->> 'name' = 'ktp'
              )                                                       AS survey_picture_ktp,
              ''                                                      AS survey_validasi_awal,  
              ''                                                      AS survey_jika_tunda,  
              ''                                                      AS survey_jika_tunda_dan_ya,  
              ''                                                      AS survey_catatan,  
              (
                SELECT 
                    CASE
                        WHEN TRIM(ai.filename) <> '' THEN ai.basedir || ai.filename
                        ELSE ''
                    END
                FROM json_array_elements((a.answers ->> 'picture') :: JSON) AS it
                  LEFT JOIN hunter.answer_image AS ai ON it ->> 'id' = ai.id :: TEXT
                WHERE
                  it ->> 'name' = 'signature'
              )                                                       AS survey_picture_signature,  
              ''                                                      AS survey_pdf  
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
            'fromDate' => $fromDate->format('Y-m-d'),
            'toDate' => $toDate->format('Y-m-d')
        ];
        return collect(DB::select(DB::raw($sql), $parameters));
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            'TANGGAL',
            'JAM CHECK IN SURVEY',
            'ID HUNTER',
            'NAMA HUNTER',
            'ID JURAGAN',
            'JURAGAN',
            'ID PJP',
            'ID TOKO',
            'LATITUDE',
            'LONGITUDE',
            'NAMA TOKO',
            'NAMA PEMILIK TOKO',
            'ALAMAT',
            'NOTELP1',
            'NOTELP2',
            'PROVINSI',
            'KOTA',
            'KECAMATAN',
            'KELURAHAN',
            'PERNAH DISURVEY TIM SERU?',
            'PEMILIK TOKO?',
            'BERSEDIA DISURVEY?',
            'TERTARIK MENJADI MITRA SERU?',
            'PROSPEKTUS?',
            'JENIS TOKO',
            'JENIS TOKO LAINNYa',
            'STATUS TOKO',
            'TIPE JALAN',
            'PERIMETER 50-100 M',
            'PERIMETER LAINNYa',
            'SUDAH JUALAN ESKRIM?',
            'JIKA YA, APA SAJA?',
            'ADA KULKAS?',
            'JUMLAH PINTU KULKAS',
            'BERSEDIA MEMBELI CASH PAKET PERDANA?',
            'TERSEDIA TEMPAT UNTUK FREEZER?',
            'KAPASITAS LISTRIK(VA)',
            'FREKUENSI MATI LISTRIK DALAM 1 BULAN',
            'FOTO LUAR TOKO',
            'FOTO DALAM TOKO',
            'FOTO TEMPAT CABINET',
            'FOTO KTP',
            'VALIDASI AWAL, MEMENUHI SYARAT?',
            'JIKA TUNDA, BERSEDIA DIHUBUNGI KEMBALI?',
            'JIKA TUNDA DAN YA, DIHUB KEMBALI TANGGAL?',
            'CATATAN LAIN-LAIN',
            'TANDA TANGAN TOKO',
            'PDF BUKTI SURVEY',
        ];
    }

    /**
     * @inheritDoc
     */
    public function columnFormats(): array
    {
        return [
            'N' => '#0',
            'O' => '#0',
        ];
    }

    /**
     * @inheritDoc
     */
    public function collection()
    {
        $result = $this->collection->map(function ($item, $key) {
            $clone = clone $item;
            $clone->survey_picture_luartoko = '';
            $clone->survey_picture_dalamtoko = '';
            $clone->survey_picture_cabinet = '';
            $clone->survey_picture_ktp = '';
            $clone->survey_picture_signature = '';
            return $clone;
        });
        return $result;
    }
}