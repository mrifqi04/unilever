<?php


namespace App\export;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;


class AuditorExport implements WithHeadings, WithColumnFormatting, FromCollection
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
              a.id,
              a.id_unilever,
              CASE a.id_user_types
              WHEN '1'
                THEN 'Permanent'
              WHEN '2'
                THEN 'Freelance'
              ELSE 'Unknown'
              END    AS user_type,
              a.name,
              a.start_date :: DATE,
              a.end_date :: DATE,
              a.email,
              a.phone,
              a.address,
              p.name AS province,
              c.name AS city,
              d.name AS district,
              v.name AS village,
              a.latitude,
              a.longitude,
              (CASE a.id_user_types
              WHEN '1'
                THEN a.status_active = '1'
              ELSE now() :: DATE <= a.end_date :: DATE
              END)::VARCHAR(6)    AS active
            FROM auditor.auditors AS a
              LEFT JOIN public.provinces AS p ON a.id_province = p.id
              LEFT JOIN public.cities AS c ON a.id_city = c.id
              LEFT JOIN public.districts AS d ON a.id_district = d.id
              LEFT JOIN public.villages AS v ON a.id_village = v.id
            WHERE a.is_deleted = 1  
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
                    a.id ilike ? OR 
                    a.id_unilever ilike ? OR 
                    a.name ilike ? OR 
                    a.email ilike ? OR
                    a.phone ilike ? 
                )
            ";
        }
        return collect(DB::select(DB::raw($sql), $parameters));
    }
}