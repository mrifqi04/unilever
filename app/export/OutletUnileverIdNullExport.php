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
use Session;

class OutletUnileverIdNullExport implements WithHeadings, WithMapping, FromCollection
{
    private $collection = [];

    /**
     * OutletUnileverIdNullExport constructor.
     */
    public function __construct()
    {
        $sql = '
          SELECT 
            o.id_city AS city_id,  
            d.reference_id AS district_reference_id,   
            v.reference_id AS village_reference_id, 
            j.id_unilever_owner AS juragan_unilever_id, 
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
              LEFT JOIN outlet.map_outlet AS mo ON op.id_map_outlet = mo.id 
              WHERE 
                 mo.id_outlet = o.id 
              LIMIT 1
            ) AS juragan_approve_date     
          FROM v_requests req
          left join outlet.outlet AS o on req.outlet_id = o.id
          LEFT JOIN public.districts AS d ON o.id_district = d.id   
          LEFT JOIN public.villages AS v ON o.id_village = v.id  
          LEFT JOIN juragan.juragans AS j ON req.juragan_id = j.id   
          WHERE 
            o.id_unilever IS NULL';

        $filter_data = Session::get('filter_data');

        $idProvince = $filter_data['id_province'];
        $idCity = $filter_data['id_city'];
        $idDistrict = $filter_data['id_district'];
        $idVillage = $filter_data['id_village'];
        $idJuragan = $filter_data['id_juragan'];
        $idOutlet = $filter_data['id_outlet'];
        $idProgress = $filter_data['id_progress'];
        if ($idProvince != '') {
            $sql .= " AND id_province = '$idProvince' ";
        }
        if ($idCity != '') {
            $sql .= " AND id_city = '$idCity' ";
        }
        if ($idJuragan != '') {
            $sql .= " AND juragan_id = '$idJuragan' ";
        }
        if ($idOutlet != '') {
            $sql .= " AND outlet_id = '$idOutlet' ";
        }
        if ($idProgress != '') {
            $sql .= " AND status_progress = '$idProgress' ";
        }
        if ($idDistrict != '') {
            $sql .= " AND id_district = '$idDistrict' ";
        }
        if ($idVillage != '') {
            $sql .= " AND id_village = '$idVillage' ";
        }

        $this->collection = collect(DB::select(DB::raw($sql)));
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'COMPANY',
            'TOWN',
            'LOCALITY',
            'SLOCALITY',
            'POP',
            'DISTRIBUTOR',
            'POPNO',
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
            'RANK',
            'AMOUNT_LIMIT',
            'DAYS_LIMIT',
            'FREEZER',
            'COOL_CAB',
            'ACTIVE',
            'IDENTIFY_BY',
            'IDENTIFY_ON',
            'USER_ENTRY',
            'DATE_ENTRY',
            'POPTYPE',
            'AREATYPE',
            'DISTRIBUTION_TYPE',
            'Sub_Distributor',
            'USER_MODIFY',
            'DATE_MODIFY',
            'SHOPPER_TYPE',
            'TOWN_BILL_TO',
            'LOCALITY_BILL_TO',
            'SLOCALITY_BILL_TO',
            'POP_BILL_TO',
            'AIR_CONDITIONER',
            'CHEQUE_REALIZED',
            'COUNTERSALE_YN',
            'DISTRICT',
            'LOCALITY_CORPORATE',
            'NIC_NO',
            'OWNER_NAME',
            'POP_CORPORATE',
            'PREV_POP_CODE',
            'PREV_TOWN_CODE',
            'REFRIGERATOR',
            'SHORT_NAME',
            'SLOCALITY_CORPORATE',
            'TOWN_CORPORATE',
            'CHEQUE_AUTO_REALIZED',
            'isChanged',
            'TAX_EXCEPTION',
            'HOLDING_CAPACITY',
            'SELLING_CAPACITY',
            'LONGITUDE',
            'LATITUDE',
            'GEO_BOUNDRY',
            'ASSET_SCHEME',
            'POP_IMAGE',
            'POP_CODE',
            'CONVERSION_STATUS',
            'CSD_STATUS',
            'GPS_COORDINATES',
            'ACTIVE_REMARKS',
            'PERFECT_STORE_LEVEL',
            'PERFECT_STORE_DATE',
            'POP_BANK',
            'DOWNLOAD',
            'UPDATED_DATE',
            'WF_SERIAL',
            'OLD_WF_SERIAL',
            'UPLOADED',
            'WF_STATUS',
            'DATE_APPROVE',
            'USER_APPROVE',
            'POP_BARCODE',
            'KEY_CUSTOMER',
            'AUTO_TAX_INVOICE',
            'ROW_VER',
            'IROW_VER',
            'LEGACY_CODE',
            'FIN_SUBLEDGER',
            'DISTRIBUTOR_DISTRICT',
            'ALT_NAME',
            'ALT_SHOP_NO',
            'ALT_MARKET_NAME',
            'ALT_STREET',
            'DELIVERY_ADDRESS',
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
            '06',                                   // COMPANY
            $row->city_id,                          // TOWN
            $row->district_reference_id,            // LOCALITY
            $row->village_reference_id,             // SLOCALITY
            $row->juragan_unilever_id,              // POP: unilever id juragan
            '',                                     // DISTRIBUTOR
            'NULL',                                 // POPNO
            $row->outlet_name,                      // NAME
            'NULL',                                 // ACCOUNT_TYPE
            '',                                     // SHOP_NO
            $row->outlet_id,                        // MARKET_NAME
            $row->outlet_address,                   // STREET
            '',                                     // POLICE_STATION
            '',                                     // TEHSIL
            '',                                     // POST_CODE
            $row->outlet_phone,                     // PHONE_NO
            '',                                     // FAX_NO
            '',                                     // EMAIL
            '1.00',                                 // COMPANY_TURNOVER
            '',                                     // TOTAL_TURNOVER
            '',                                     // SUB_ELEMENT
            '',                                     // COMPANY_RANK
            '',                                     // RANK
            '',                                     // AMOUNT_LIMIT
            '',                                     // DAYS_LIMIT
            '',                                     // FREEZER
            '',                                     // COOL_CAB
            $row->outlet_is_active === 1 ? 1 : 0,   // ACTIVE
            '',                                     // IDENTIFY_BY
            $row->juragan_approve_date,             // IDENTIFY_ON
            '',                                     // USER_ENTRY
            '',                                     // DATE_ENTRY
            '',                                     // POPTYPE
            '',                                     // AREATYPE
            'NULL',                                 // DISTRIBUTION_TYPE
            '',                                     // Sub_Distributor
            '',                                     // USER_MODIFY
            '',                                     // DATE_MODIFY
            '',                                     // SHOPPER_TYPE
            '',                                     // TOWN_BILL_TO
            '',                                     // LOCALITY_BILL_TO
            '',                                     // SLOCALITY_BILL_TO
            '',                                     // POP_BILL_TO
            '',                                     // AIR_CONDITIONER
            '',                                     // CHEQUE_REALIZED
            '',                                     // COUNTERSALE_YN
            '',                                     // DISTRICT
            '',                                     // LOCALITY_CORPORATE
            '',                                     // NIC_NO
            '',                                     // OWNER_NAME
            '',                                     // POP_CORPORATE
            '',                                     // PREV_POP_CODE
            '',                                     // PREV_TOWN_CODE
            '',                                     // REFRIGERATOR
            $row->outlet_name,                      // SHORT_NAME
            '',                                     // SLOCALITY_CORPORATE
            '',                                     // TOWN_CORPORATE
            '',                                     // CHEQUE_AUTO_REALIZED
            '',                                     // isChanged
            '',                                     // TAX_EXCEPTION
            '',                                     // HOLDING_CAPACITY
            '',                                     // SELLING_CAPACITY
            $row->outlet_longitude,                 // LONGITUDE
            $row->outlet_latitude,                  // LATITUDE
            '',                                     // GEO_BOUNDRY
            '',                                     // ASSET_SCHEME
            '',                                     // POP_IMAGE
            '',                                     // POP_CODE
            '',                                     // CONVERSION_STATUS
            '',                                     // CSD_STATUS
            '',                                     // GPS_COORDINATES
            '',                                     // ACTIVE_REMARKS
            '',                                     // PERFECT_STORE_LEVEL
            '',                                     // PERFECT_STORE_DATE
            '',                                     // POP_BANK
            '',                                     // DOWNLOAD
            '',                                     // UPDATED_DATE
            'NULL',                                 // WF_SERIAL
            'NULL',                                 // OLD_WF_SERIAL
            'NULL',                                 // UPLOADED
            'NULL',                                 // WF_STATUS
            '',                                     // DATE_APPROVE
            'NULL',                                 // USER_APPROVE
            '',                                     // POP_BARCODE
            '',                                     // KEY_CUSTOMER
            '',                                     // AUTO_TAX_INVOICE
            '',                                     // ROW_VER
            '',                                     // IROW_VER
            'NULL',                                 // LEGACY_CODE
            'NULL',                                 // FIN_SUBLEDGER
            '',                                     // DISTRIBUTOR_DISTRICT
            'NULL',                                 // ALT_NAME
            'NULL',                                 // ALT_SHOP_NO
            '',                                     // ALT_MARKET_NAME
            'NULL',                                 // ALT_STREET
            'NULL',                                 // DELIVERY_ADDRESS
        ];
    }
}