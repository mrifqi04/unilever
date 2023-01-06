<?php
/**
 * Created by PhpStorm.
 * User: muhammad.nafianto
 * Date: 3/11/2020
 * Time: 11:15 AM
 */

namespace App\export;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OutletCSDPExport implements WithHeadings, WithMapping, FromCollection
{
    private $collection = [];
    /**
     * @var \Carbon\Carbon
     */
    private $exportDate;

    /**
     * OutletUnileverIdNullExport constructor.
     * @param string $juraganId
     * @param \Carbon\Carbon $fromDate
     * @param \Carbon\Carbon $toDate
     * @param \Carbon\Carbon $exportDate
     */
    public function __construct($juraganId, $fromDate, $toDate, $exportDate)
    {
        $this->exportDate = $exportDate;
        $sql = '
          SELECT 
            o.id_city AS city_id,  
            d.reference_id AS district_reference_id,   
            v.reference_id AS village_reference_id, 
            j.id_unilever_owner AS juragan_unilever_id, 
            o.owner AS outlet_owner,
            o.name AS outlet_name, 
            o.id AS outlet_id,  
            o.address AS outlet_address, 
            o.phone AS outlet_phone, 
            o.status_active AS outlet_is_active,
            o.longitude AS outlet_longitude, 
            o.latitude AS outlet_latitude,    
            (
              SELECT 
                op.created_date
              FROM outlet.outlet_progress AS op
              WHERE 
                 op.id_map_outlet = mo.id
              LIMIT 1
            ) AS juragan_approve_date,
            o.csdp,
            to_timestamp(o.created_at)::DATE as submit_date
          FROM outlet.outlet AS o 
          LEFT JOIN outlet.map_outlet AS mo ON o.id = mo.id_outlet
          LEFT JOIN public.districts AS d ON o.id_district = d.id   
          LEFT JOIN public.villages AS v ON o.id_village = v.id   
          LEFT JOIN juragan.juragans AS j ON o.id_juragan = j.id   
          WHERE 1=1 '; 
         //   mo.is_mitra = 1 ';
        $params = [];
        if ($juraganId != '') {
            $params[] = $juraganId;
            $sql .= ' AND j.id = ?';
        }
        if (!is_null($fromDate) && !is_null($toDate)) {
            $sql .= ' AND to_timestamp(o.created_at) BETWEEN ? AND ?';
            $params[] = $fromDate->format('Y-m-d');
            $params[] = $toDate->format('Y-m-d');
        }
        $this->collection = collect(DB::select(DB::raw($sql), $params));
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'TOWN',
            'LOCALITY',
            'SLOCALITY',
            'NAME',
            'ACCOUNT_TYPE',
            'SHOP_NO',
            'MARKET_NAME',
            'STREET',
            'POLICE_STATION',
            'TEHSIL',
            'POST_CODE',
            'PHONE_NO',
            'FAX_NO',
            'EMAIL',
            'COMPANY_TURNOVER',
            'TOTAL_TURNOVER',
            'SUB_ELEMENT',
            'COMPANY_RANK',
            'POPTYPE',
            'AREATYPE',
            'SUB_DISTRIBUTOR',
            'COUNTERSALE_YN',
            'NIC_NO',
            'OWNER_NAME',
            'PREV_POP_CODE',
            'SHORT_NAME',
            'CHEQUE_AUTO_REALIZED',
            'TAX_EXCEPTION',
            'LONGITUDE',
            'LATITUDE',
            'ASSETSCHEMEFLAG',
            'TAX_ID',
            'SLAB',
            'TAX_NO',
            'TAX_NAME',
            'TAX_ADDRESS',
            'BANK',
            'ACCOUNTNO',
            'ACCOUNTTITLE',
            'BRANCH',
            'SELL_CATEGORY',
            'CREDIT_ALLOWED',
            'AMOUNT_LIMIT',
            'DAYS_LIMIT',
            'CREDITMODE',
            'CREDIT_ACTION',
            'BRANDED_CODE',
            'UNBRANDED_CODE',
            'RANK',
            'SHOPPER_TYPE',
            'IDENTIFY_ON',
            'IDENTIFY_BY',
            'DISTRIBUTOR_DISTRICT',
            'CSDP',
            'SUBMIT_DATE',
        ];
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->collection;
    }

    /**
     * @param \stdClass $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->city_id,                          // TOWN
            $row->district_reference_id,            // LOCALITY
            $row->village_reference_id,             // SLOCALITY
            $row->outlet_name,                      // NAME
            '00',                                   // ACCOUNT_TYPE
            '',                                     // SHOP_NO
            $row->outlet_id,                        // MARKET_NAME
            $row->outlet_address,                   // STREET
            '',                                     // POLICE_STATION
            '',                                     // TEHSIL
            '',                                     // POST_CODE
            $row->outlet_phone,                     // PHONE_NO
            '',                                     // FAX_NO
            '',                                     // EMAIL
            '1',                                    // COMPANY_TURNOVER
            '1',                                    // TOTAL_TURNOVER
            'C10026',                               // SUB_ELEMENT
            '1',                                    // COMPANY_RANK
            '00',                                   // POPTYPE
            '02',                                   // AREATYPE
            'N',                                    // SUB_DISTRIBUTOR
            'N',                                    // COUNTERSALE_YN
            '',                                     // NIC_NO
            $row->outlet_owner,                     // OWNER_NAME
            '',                                     // PREV_POP_CODE
            $row->outlet_owner,                     // SHORT_NAME
            'N',                                    // CHEQUE_AUTO_REALIZED
            'N',                                    // TAX_EXCEPTION
            $row->outlet_longitude,                 // LONGITUDE
            $row->outlet_latitude,                  // LATITUDE
            '',                                     // ASSETSCHEMEFLAG
            '01',                                   // TAX_ID
            '01',                                   // SLAB
            '',                                     // TAX_NO
            '',                                     // TAX_NAME
            '',                                     // TAX_ADDRESS
            '',                                     // BANK
            '',                                     // ACCOUNTNO
            '',                                     // ACCOUNTTITLE
            '',                                     // BRANCH
            '',                                     // SELL_CATEGORY
            '',                                     // CREDIT_ALLOWED
            '',                                     // AMOUNT_LIMIT
            '',                                     // DAYS_LIMIT
            '',                                     // CREDITMODE
            '',                                     // CREDIT_ACTION
            '',                                     // BRANDED_CODE
            '',                                     // UNBRANDED_CODE
            '',                                     // RANK
            '',                                     // SHOPPER_TYPE
            $this->exportDate->format('d-m-Y'),    // IDENTIFY_ON
            '',                                     // IDENTIFY_BY
            '',                                     // DISTRIBUTOR_DISTRICT
            $row->csdp,                                     // CSDP
            date('d-m-Y', strtotime($row->submit_date)),                                     // SUBMIT DATE
        ];
    }
}