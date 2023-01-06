<?php


namespace App\export;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CabinetExport implements WithHeadings, WithColumnFormatting, FromCollection
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
            'qr code',
            'serial number',
            'brand',
            'model',
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
                c.qrcode,
                c.serialnumber,
                c.brand,
                c.model_type
            FROM warehouse.cabinets AS c
            WHERE c.deleted_at IS NULL  
        ";
        $parameters = [];
        if ($this->search !== '') {
            $parameters[] = "%" . $this->search . "%";
            $parameters[] = "%" . $this->search . "%";
            $parameters[] = "%" . $this->search . "%";
            $parameters[] = "%" . $this->search . "%";
            $sql .= "
                AND (
                    c.qrcode ilike ? OR 
                    c.serialnumber ilike ? OR 
                    c.brand ilike ? OR 
                    c.model_type ilike ?  
                )
            ";
        }
        return collect(DB::select(DB::raw($sql), $parameters));
    }
}