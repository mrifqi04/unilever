<?php


namespace App\export;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VehicleExport implements WithHeadings, WithColumnFormatting, FromCollection, ShouldAutoSize
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
            'NOPOL MOBIL',
            'MERK',
            'TIPE MOBIL',
            'TAHUN PEMBUATAN',
            'NO STNK',
            'PROPINSI',
            'KOTA',
            'KECAMATAN',
            'KELURAHAN',
            'TANGGAL MULAI',
            'TANGGAL BERAKHIR',
            'TANGGAL DIBUAT',
        ];
    }

    /**
     * @inheritDoc
     */
    public function columnFormats(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function collection()
    {
        $sql = "
            SELECT
              ''               AS ID_SERU,
              ''               AS ID_UNILEVER,
              h.license_number AS NOPOL_MOBIL,
              ''               AS MERK,
              ''               AS TIPE_MOBIL,
              ''               AS TAHUN_PEMBUATAN,
              ''               AS NO_STNK,
              p.name           AS PROPINSI,
              c.name           AS KOTA,
              d.name           AS KECAMATAN,
              v.name           AS KELURAHAN,
              ''               AS TANGGAL_MULAI,
              ''               AS TANGGAL_BERAKHIR,
              ''               AS TANGGAL_DIBUAT
            FROM driver.vehicles AS h
              LEFT JOIN public.provinces AS p ON h.id_province = p.id
              LEFT JOIN public.cities AS c ON h.id_city = c.id
              LEFT JOIN public.districts AS d ON h.id_district = d.id
              LEFT JOIN public.villages AS v ON h.id_village = v.id
            WHERE h.is_deleted = 1";
        $parameters = [];
        if ($this->search !== '') {
            $parameters[] = $this->search;
            $sql .= " AND h.license_number = ?";
        }
        
        return collect(DB::select(DB::raw($sql), $parameters));
    }
}