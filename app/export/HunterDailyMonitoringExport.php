<?php

namespace App\export;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HunterDailyMonitoringExport implements FromCollection, WithHeadings
{

    /**
     * @var $collection \Illuminate\Support\Collection
     */
    private $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->collection;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'city id',
            'city name',
            'juragan id',
            'juragan name',
            'hunter id',
            'hunter name',
            'outlet name',
            'check in',
            'check out',
            'duration',
            'latitude',
            'longitude',
            'status id',
            'status name',
        ];
    }
}