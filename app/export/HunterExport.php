<?php


namespace App\export;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HunterExport implements WithHeadings, WithColumnFormatting, FromCollection
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
            'id',
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
              h.id,
              h.id_unilever,
              CASE h.id_user_types
              WHEN '1'
                THEN 'Permanent'
              WHEN '2'
                THEN 'Freelance'
              ELSE 'Unknown'
              END    AS user_type,
              h.name,
              h.start_date :: DATE,
              h.end_date :: DATE,
              h.email,
              h.phone,
              h.address,
              p.name AS province,
              c.name AS city,
              d.name AS district,
              v.name AS village,
              h.latitude,
              h.longitude,
              (CASE h.id_user_types
              WHEN '1'
                THEN h.status_active = '1'
              ELSE now() :: DATE <= h.end_date :: DATE
              END)::VARCHAR(6)    AS active
            FROM hunter.hunter AS h
              LEFT JOIN public.provinces AS p ON h.id_province = p.id
              LEFT JOIN public.cities AS c ON h.id_city = c.id
              LEFT JOIN public.districts AS d ON h.id_district = d.id
              LEFT JOIN public.villages AS v ON h.id_village = v.id
            WHERE h.is_deleted = 1  
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
                    h.id ilike ? OR 
                    h.id_unilever ilike ? OR 
                    h.name ilike ? OR 
                    h.email ilike ? OR
                    h.phone ilike ? 
                )
            ";
        }
        return collect(DB::select(DB::raw($sql), $parameters));
    }
}