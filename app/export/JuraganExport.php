<?php


namespace App\export;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;


class JuraganExport implements WithHeadings, WithColumnFormatting, FromCollection, ShouldAutoSize
{
    private $search;

    /**
     * HunterExport constructor.
     * @param string $search
     */
    public function __construct($search)
    {
        $this->search = trim((string)$search);
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            'ID SERU',
            'ID UNILEVER',
            'NAMA',
            'EMAIL',
            'NOTELP',
            'PROVINSI',
            'KOTA',
            'KECAMATAN',
            'KELURAHAN',
            'ALAMAT',
            'KODE POS',
            'LATITUDE',
            'LONGITUDE',
            'RADIUS KERJA (M)',
            'TRESHOLD RADIUS (M)',
            'TANGGAL DIBUAT',
            'DIBUAT OLEH',
            'TANGGAL UPDATE',
            'DIUPDATE OLEH',
        ];
    }

    /**
     * @inheritDoc
     */
    public function columnFormats(): array
    {
        return [
            'E' => '#0',
        ];
    }

    /**
     * @inheritDoc
     */
    public function collection()
    {
        $sql = "
           select
              j.id                as id_seru,
              j.id_unilever_owner as id_unilever,
              j.name              as nama,
              j.email             as email,
              j.phone             as notelp,
              p.name              as provinsi,
              c.name              as kota,
              d.name              as kecamatan,
              v.name              as kelurahan,
              j.address           as alamat,
              j.zip_code          as kode_pos,
              j.latitude          as latitude,
              j.longitude         as longitude,
              j.radius_default    as radius_kerja,
              j.radius_threshold  as treshold_radius,
              j.created_at        as tanggal_dibuat,
              uc.name             as dibuat_oleh,
              j.updated_at        as tanggal_update,
              uu.name             as diupdate_oleh
            from juragan.juragans as j
              left join public.provinces as p on j.id_province = p.id
              left join public.cities as c on j.id_city = c.id
              left join public.districts as d on j.id_district = d.id
              left join public.villages as v on j.id_village = v.id
              left join public.users as uc on j.created_by = uc.id
              left join public.users as uu on j.updated_by = uu.id
            where j.is_deleted = 1 
        ";
        $parameters = [];
        if ($this->search !== '') {
            $parameters[] = "%" . $this->search . "%";
            $parameters[] = "%" . $this->search . "%";
            $parameters[] = "%" . $this->search . "%";
            $parameters[] = "%" . $this->search . "%";
            $parameters[] = "%" . $this->search . "%";
            $sql .= "
                AND (
                    j.id ilike ? OR 
                    j.id_unilever_owner ilike ? OR 
                    j.name ilike ? OR 
                    j.email ilike ? OR
                    j.phone ilike ? 
                )
            ";
        }
        return collect(DB::select(DB::raw($sql), $parameters));
    }
}