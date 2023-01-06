<?php


namespace App\export;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\DefaultValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class OutletExport extends DefaultValueBinder implements
  WithHeadings,
  WithColumnFormatting,
  FromCollection,
  ShouldAutoSize,
  WithCustomValueBinder,
  WithEvents
{
  /**
   * @var Collection $collection
   */
  private $collection;

  /**
   * HunterExport constructor.
   * @param string $search
   */
  public function __construct($search)
  {
    $this->collection = $this->getCollection($search);
  }

  /**
   * @param string $search
   * @return Collection
   */
  private function getCollection(string $search)
  {
    $sql = "
      SELECT 
      a.id             AS seru_id, 
      a.id_unilever    AS unilever_id, 
      a.id_juragan     AS juragan_id, 
      j.name           AS juragan, 
      a.name           AS nama_toko, 
      a.owner          AS nama_pemilik_toko, 
      a.phone          AS notelp1, 
      a.phone2         AS notelp2, 
      p.name           AS provinsi, 
      c.name           AS kota, 
      d.name           AS kecamatan, 
      v.name           AS kelurahan, 
      a.address        AS alamat, 
      a.latitude       AS latitude, 
      a.longitude      AS longitude, 
      ( 
      SELECT CASE 
      WHEN trim(ai.filename) <> '' 
      THEN ai.basedir || ai.filename 
      ELSE '' 
      END 
      FROM json_array_elements((ans.answers ->> 'picture') :: JSON) AS it 
      LEFT JOIN hunter.answer_image AS ai ON it ->> 'id' = ai.id :: TEXT 
      WHERE 
      it ->> 'name' = 'luartoko' 
      )                AS foto_luar_outlet, 
      ( 
      SELECT CASE 
      WHEN trim(ai.filename) <> '' 
      THEN ai.basedir || ai.filename 
      ELSE '' 
      END 
      FROM json_array_elements((ans.answers ->> 'picture') :: JSON) AS it 
      LEFT JOIN hunter.answer_image AS ai ON it ->> 'id' = ai.id :: TEXT 
      WHERE 
      it ->> 'name' = 'dalamtoko' 
      )                AS foto_dalam_outlet, 
      ( 
      SELECT CASE 
      WHEN trim(ai.filename) <> '' 
      THEN ai.basedir || ai.filename 
      ELSE '' 
      END 
      FROM json_array_elements((ans.answers ->> 'picture') :: JSON) AS it 
      LEFT JOIN hunter.answer_image AS ai ON it ->> 'id' = ai.id :: TEXT 
      WHERE 
      it ->> 'name' = 'ktp' 
      )                AS foto_ktp_pemilik, 
      'ADR' || dr.adr  AS no_adr, 
      'ADR' || dr.adr  AS adr, 
      ''               AS aktif_mitra_sejak, 
      CASE so.status 
      WHEN 1 
      THEN 'deal' 
      WHEN 2 
      THEN 'tunda' 
      WHEN 3 
      THEN 'approve juragan' 
      WHEN 4 
      THEN 'batal' 
      WHEN 5 
      THEN 'approve uli' 
      WHEN 6 
      THEN 'terkirim' 
      ELSE '' 
      END              AS status, 
      cab.brand        AS brand_cabinet, 
      cab.model_type   AS tipe_cabinet, 
      cab.serialnumber AS sn, 
      cab.qrcode       AS qrcode, 
      ''               AS perubahan_data_terakhir, 
      a.descriptions   AS keterangan, 
      u.name           AS user, 
      to_date(cast(to_timestamp(a.created_at) as TEXT),'YYYY-MM-DD')           AS submit_date 
      FROM outlet.outlet AS a 
      LEFT JOIN juragan.juragans AS j ON a.id_juragan = j.id 
      LEFT JOIN outlet.outlet_status_types AS ot ON a.id_outlet_type = ot.id 
      LEFT JOIN outlet.ownership_status AS os ON a.id_ownership_status = os.id 
      LEFT JOIN outlet.street_types AS st ON a.id_street_type = st.id 
      LEFT JOIN public.provinces AS p ON a.id_province = p.id 
      LEFT JOIN public.cities AS c ON a.id_city = c.id 
      LEFT JOIN public.districts AS d ON a.id_district = d.id 
      LEFT JOIN public.villages AS v ON a.id_village = v.id 
      LEFT JOIN outlet.outlet_has_cabinets AS ohc ON a.id = ohc.outlet_id 
      LEFT JOIN warehouse.cabinets AS cab ON ohc.cabinet_id = cab.id 
      LEFT JOIN outlet.map_outlet AS mo ON a.id = mo.id 
      LEFT JOIN hunter.survey_outlet AS so ON mo.id = so.id_map_outlet 
      LEFT JOIN hunter.answers AS ans ON so.id = ans.id_survey_outlet 
      LEFT JOIN driver.route_plans AS rp ON a.id = rp.id_outlet 
      LEFT JOIN driver.delivery_orders AS dr ON rp.id_delivery_order = dr.id 
      LEFT JOIN public.users AS u ON a.created_by = u.id 
      WHERE 
      a.is_deleted = 1 
        ";
    $parameters = [];
    if ($search !== '') {
      $parameters[] = "%" . $search . "%";
      $parameters[] = "%" . $search . "%";
      $parameters[] = "%" . $search . "%";
      $parameters[] = "%" . $search . "%";
      $parameters[] = "%" . $search . "%";
      $sql .= "
                AND (
                    a.id ilike ? OR
                    a.id_unilever ilike ? OR
                    a.owner ilike ? OR
                    a.phone ilike ? OR
                    j.name ilike ?
                )
            ";
    }
    return collect(DB::select(DB::raw($sql), $parameters));
  }

  /**
   * @inheritDoc
   */
  public function headings(): array
  {
    return [
      'SERU ID',
      'UNILEVER ID',
      'JURAGAN',
      'JURAGAN ID',
      'NAMA TOKO',
      'NAMA PEMILIK TOKO',
      'NOTELP 1',
      'NOTELP 2',
      'PROVINSI',
      'KOTA',
      'KECAMATAN',
      'KELURAHAN',
      'ALAMAT',
      'LATITUDE',
      'LONGITUDE',
      'FOTO LUAR OUTLET',
      'FOTO DALAM OUTLET',
      'FOTO KTP PEMILIK',
      'NO ADR',
      'ADR',
      'AKTIF MITRA SEJAK',
      'STATUS',
      'BRAND CABINET',
      'TIPE CABINET',
      'S/N',
      'QR CODE',
      'PERUBAHAN DATA TERAKHIR',
      'KETERANGAN',
      'USER',
    ];
  }

  /**
   * @inheritDoc
   */
  public function columnFormats(): array
  {
    return [
      'A' => '#0',
      'B' => '#0',
      'C' => '#0',
      'G' => '#0',
      'H' => '#0',
    ];
  }

  /**
   * @inheritDoc
   */
  public function bindValue(Cell $cell, $value)
  {
    if (in_array(strtoupper($cell->getColumn()), ['A', 'B', 'C', 'G', 'H'])) {
      $cell->setValueExplicit($value, DataType::TYPE_STRING);
      return true;
    }
    return parent::bindValue($cell, $value);
  }

  /**
   * @inheritDoc
   */
  public function collection()
  {
    $result = $this->collection->map(function ($item, $key) {
      $clone = clone $item;
      $clone->foto_luar_outlet = '';
      $clone->foto_dalam_outlet = '';
      $clone->foto_ktp_pemilik = '';
      return $clone;
    });
    return $result;
  }

  /**
   * @inheritDoc
   */
  private function getDrawings()
  {
    $pictures = [];
    $row = 2;
    foreach ($this->collection as $item) {
      $foto_luar_outlet = (string) $item->foto_luar_outlet;
      if (strlen($foto_luar_outlet) > 0) {
        if ($foto_luar_outlet[0] === '.') {
          $foto_luar_outlet = config('app.image_base_dir') . '/' . substr($foto_luar_outlet, 1, strlen($foto_luar_outlet) - 1);
        }
        if (file_exists($foto_luar_outlet)) {
          $drawing = new Drawing();
          try {
            $drawing->setName('Luar Toko');
            $drawing->setDescription('Luar Toko');
            $drawing->setPath($foto_luar_outlet);
            $drawing->setHeight(50);
            $drawing->setCoordinates('P' . (string) $row);
            $pictures[] = $drawing;
          } catch (\Exception $exception) { }
        }
      }
      $foto_dalam_outlet = (string) $item->foto_dalam_outlet;
      if (strlen($foto_dalam_outlet) > 0) {
        if ($foto_dalam_outlet[0] === '.') {
          $foto_dalam_outlet = config('app.image_base_dir') . '/' . substr($foto_dalam_outlet, 1, strlen($foto_luar_outlet) - 1);
        }
        if (file_exists($foto_dalam_outlet)) {
          try {
            $drawing = new Drawing();
            $drawing->setName('Dalam Toko');
            $drawing->setDescription('Dalam Toko');
            $drawing->setPath($foto_dalam_outlet);
            $drawing->setHeight(50);
            $drawing->setCoordinates('Q' . (string) $row);
            $pictures[] = $drawing;
          } catch (\Exception $exception) { }
        }
      }
      $foto_ktp_pemilik = (string) $item->foto_ktp_pemilik;
      if (strlen($foto_ktp_pemilik) > 0) {
        if ($foto_ktp_pemilik[0] === '.') {
          $foto_ktp_pemilik = config('app.image_base_dir') . '/' . substr($foto_ktp_pemilik, 1, strlen($foto_luar_outlet) - 1);
        }
        if (file_exists($foto_ktp_pemilik)) {
          try {
            $drawing = new Drawing();
            $drawing->setName('Ktp');
            $drawing->setDescription('Ktp');
            $drawing->setPath($foto_ktp_pemilik);
            $drawing->setHeight(50);
            $drawing->setCoordinates('R' . (string) $row);
            $pictures[] = $drawing;
          } catch (\Exception $exception) { }
        }
      }
      $row++;
    }

    return $pictures;
  }

  /**
   * @inheritDoc
   */
  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $event->sheet->addDrawings($this->getDrawings());
      },
    ];
  }
}
