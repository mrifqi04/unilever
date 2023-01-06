<?php


namespace App\export;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;


class OutletTukarMandiri implements WithHeadings, WithColumnFormatting, FromCollection, ShouldAutoSize
{
    private $search;
    private $from_date;
    private $to_date;

    /**
     * HunterExport constructor.
     * @param string $search
     */
    public function __construct($search, $from_date, $to_date)
    {
        $this->search = trim((string) $search);
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            'ID REQUEST',
            'TANGGAL REQUEST',
            'ID JURAGAN',
            'JURAGAN',
            'PROVINSI',
            'KOTA',
            'KECAMATAN',
            'KELURAHAN',
            'ID TOKO ASAL',
            'NAMA TOKO ASAL',
            'NAMA PEMILIK TOKO ASAL',
            'ALAMAT ASAL',
            'NOTELP 1 TOKO ASAL',
            'NOTELP 2 TOKO ASAL',
            'LATITUDE ASAL',
            'LONGITUDE ASAL',
            'TIPE CABINET 1',
            'QR CODE CABINET 1',
            'SERIAL NO CABINET 1',
            'KODE CABINET 2',
            'TIPE CABINET 2',
            'QC CODE CABINET 2',
            'ALASAN PENARIKAN',
            'ALASAN LAINNYA',
            'KETERANGAN LAIN-LAIN',
            'ID TOKO TUJUAN',
            'NAMA TOKO TUJUAN',
            'NAMA PEMILIK TOKO TUJUAN',
            'ALAMAT TOKO TUJUAN',
            'NOTELP 1 TOKO TUJUAN',
            'NOTELP 2 TOKO TUJUAN',
            'LATITUDE TOKO TUJUAN',
            'LONGITUDE TOKO TUJUAN',
            'KETERANGAN LAIN-LAIN TOKO TUJUAN',
            'TANGGAL TARIK',
            'NOMOR DRIVER TARIK',
            'NOMOR MOBIL TARIK',
            '#CABINET TARIK',
            '#HIGHLIGHTER CABINET TARIK',
            '#KERANJANG TARIK',
            '#BUKU PANDUAN TARIK',
            '#KUNCI TARIK',
            '#SCRAPPER TARIK',
            'FOTO LUAR CABINET TARIK',
            'FOTO DALAM CABINET TARIK',
            'FOTO BAP TARIK',
            'TANDA TANGAN TOKO TARIK',
            'KETERANGAN LAIN-LAIN',
            'TANGGAL KIRIM',
            'NOMOR DRIVER KIRIM',
            'NOMOR MOBIL KIRIM',
            '#CABINET KIRIM',
            '#HIGHLIGHTER KIRIM',
            '# KERANJANG KIRIM',
            '#BUKU PANDUAN KIRIM',
            '#KUNCI KIRIM',
            '#SCRAPPER KIRIM',
            'BARCODE KIRIM',
            'KTP KIRIM',
            'POSM KIRIM',
            'DUS & STYROFOAM KIRIM',
            'INSTRUKSI KIRIM',
            'LAIN-LAIN KIRIM',
            'FOTO LUAR CABINET KIRIM',
            'FOTO DALAM CABINET KIRIM',
            'FOTO KTP',
            'FOTO KTP & PEMILIK',
            'FOTO ADR',
            'TANDA TANGAN TOKO PENERIMA',
            'KETERANGAN LAIN-LAIN KIRIM',
            'STATUS REQUEST',
            'BUKTI REQUEST (FORM A1)',
            'BAP',
            'SURAT JALAN',
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
        SELECT  
                t.id as request_id, 
                t.created_at as tanggal_request, 
                t.juragan_id as juragan_id, 
                j.name as juragan_name, 
                pr.name as juragan_province, 
                ct.name as juragan_city, 
                dt.name as juragan_district, 
                vl.name as juragan_village, 
                tdm.destination_outlet_id as outlet_id_asal, 
                o_orig.name as outlet_name_asal, 
                o_orig.owner as outlet_owner_asal, 
                o_orig.address as outlet_address_asal, 
                o_orig.phone as outlet_phone_asal, 
                o_orig.phone2 as outlet_phone2_asal, 
                o_orig.latitude as outlet_latitude_asal, 
                o_orig.longitude as outlet_longitude_asal, 
                cab.model_type as tipe_cabinet_1, 
                cab.qrcode as qr_cabinet_1, 
                cab.serialnumber as code_cabinet_1, 
        '' as tipe_cabinet_1, 
        '' as qr_cabinet_1, 
        '' as code_cabinet_1, 
                tdm.reason as alasan_penarikan, 
        '' as alasan_lainnya, 
        '' as keterangan_lainlain, 
                tdm.destination_outlet_id as outlet_id_tujuan, 
                o_dest.name as outlet_name_tujuan, 
                o_dest.owner as outlet_owner_tujuan, 
                o_dest.address as outlet_address_tujuan, 
                o_dest.phone as outlet_phone_tujuan, 
                o_dest.phone2 as outlet_phone2_tujuan, 
                o_dest.latitude as outlet_latitude_tujuan, 
                o_dest.longitude as outlet_longitude_tujuan, 
        '' as keterangan_lainlain_tujuan, 
                tsm_tarik.shipping_date as tanggal_tarik, 
                tsm_tarik.driver_no as no_driver_tarik, 
                tsm_tarik.vahicle_plate_no as vehicle_plate_tarik, 
                tsma_tarik.answer->>'unit_cabinet_value' as cabinet_tarik, 
                tsma_tarik.answer->>'unit_highlighter_value' as highlighter_tarik, 
                tsma_tarik.answer->>'unit_keranjang_value' as keranjang_tarik, 
                tsma_tarik.answer->>'unit_panduan_value' as buku_panduan_tarik, 
                tsma_tarik.answer->>'unit_kunci_value' as kunci_tarik, 
                tsma_tarik.answer->>'unit_scrapper_value' as scarapper_tarik, 
                ( 
                    SELECT  
                        CASE  
                            WHEN TRIM(ai.filename) <> '' THEN ai.basedir || ai.filename  
                            ELSE ''  
                        END  
                    FROM json_array_elements((tsma_tarik.images)::JSON) AS it  
                    LEFT JOIN hunter.answer_image AS ai ON it->>'id' = ai.id::text  
                    WHERE it->>'name' = 'luarkabinet'  
                ) AS foto_luar_cabinet_tarik, 
                (  
                    SELECT  
                        CASE  
                            WHEN TRIM(ai.filename) <> '' THEN ai.basedir || ai.filename  
                            ELSE ''  
                        END  
                    FROM json_array_elements((tsma_tarik.images)::JSON) AS it  
                    LEFT JOIN hunter.answer_image AS ai ON it->>'id' = ai.id::text  
                    WHERE it->>'name' = 'dalamkabinet'  
                ) AS foto_dalam_cabinet_tarik,  
                (  
                    SELECT  
                        CASE  
                            WHEN TRIM(ai.filename) <> '' THEN ai.basedir || ai.filename  
                            ELSE ''  
                        END  
                    FROM json_array_elements((tsma_tarik.images)::JSON) AS it  
                    LEFT JOIN hunter.answer_image AS ai ON it->>'id' = ai.id::text  
                    WHERE it->>'name' = 'bap'  
                ) AS foto_bap_tarik,  
                (  
                    SELECT  
                        CASE  
                            WHEN TRIM(ai.filename) <> '' THEN ai.basedir || ai.filename  
                            ELSE ''  
                        END  
                    FROM json_array_elements((tsma_tarik.images)::JSON) AS it  
                    LEFT JOIN hunter.answer_image AS ai ON it->>'id' = ai.id::text  
                    WHERE it->>'name' = 'signature'  
                ) AS foto_signature_tarik,  
        '' as keterangan_lainlain_opomaneh,  
                tsm_kirim.shipping_date as tanggal_kirim,  
                tsm_kirim.driver_no as no_driver_kirim,  
                tsm_kirim.vahicle_plate_no as vehicle_plate_kirim,  
                tsma_kirim.answer->>'unit_cabinet_value' as cabinet_kirim,  
                tsma_kirim.answer->>'unit_highlighter_value' as highlighter_kirim,  
                tsma_kirim.answer->>'unit_keranjang_value' as keranjang_kirim,  
                tsma_kirim.answer->>'unit_panduan_value' as buku_panduan_kirim,  
                tsma_kirim.answer->>'unit_kunci_value' as kunci_kirim,  
                tsma_kirim.answer->>'unit_scrapper_value' as scarapper_kirim,  
        '' as barcode_kirim_opomaneh,  
        '' as ktp_kirim_opomaneh,  
        '' as posm_kirim_opomaneh,  
        '' as dus_kirim_opomaneh,  
        '' as instruksi_kirim_opomaneh,  
        '' as lainlain_kirim_opomaneh,  
                (  
                    SELECT  
                        CASE  
                            WHEN TRIM(ai.filename) <> '' THEN ai.basedir || ai.filename  
                            ELSE ''  
                        END  
                    FROM json_array_elements((tsma_kirim.images)::JSON) AS it  
                    LEFT JOIN hunter.answer_image AS ai ON it->>'id' = ai.id::text  
                    WHERE it->>'name' = 'luarkabinet'  
                ) AS foto_luar_cabinet_kirim,  
                (  
                    SELECT  
                        CASE  
                            WHEN TRIM(ai.filename) <> '' THEN ai.basedir || ai.filename  
                            ELSE ''  
                        END  
                    FROM json_array_elements((tsma_kirim.images)::JSON) AS it  
                    LEFT JOIN hunter.answer_image AS ai ON it->>'id' = ai.id::text  
                    WHERE it->>'name' = 'dalamkabinet'  
                ) AS foto_dalam_cabinet_kirim,  
                (  
                    SELECT  
                        CASE  
                            WHEN TRIM(ai.filename) <> '' THEN ai.basedir || ai.filename  
                            ELSE ''  
                        END  
                    FROM json_array_elements((tsma_kirim.images)::JSON) AS it  
                    LEFT JOIN hunter.answer_image AS ai ON it->>'id' = ai.id::text  
                    WHERE it->>'name' = 'ktp'  
                ) AS foto_ktp_kirim,  
                (  
                    SELECT  
                        CASE  
                            WHEN TRIM(ai.filename) <> '' THEN ai.basedir || ai.filename  
                            ELSE ''  
                        END  
                    FROM json_array_elements((tsma_kirim.images)::JSON) AS it  
                    LEFT JOIN hunter.answer_image AS ai ON it->>'id' = ai.id::text  
                    WHERE it->>'name' = 'ktpdanpemilik'  
                ) AS foto_ktppemilik_kirim,  
                (  
                    SELECT  
                        CASE  
                            WHEN TRIM(ai.filename) <> '' THEN ai.basedir || ai.filename  
                            ELSE ''  
                        END  
                    FROM json_array_elements((tsma_kirim.images)::JSON) AS it  
                    LEFT JOIN hunter.answer_image AS ai ON it->>'id' = ai.id::text  
                    WHERE it->>'name' = 'adr'  
                ) AS foto_adr_kirim,  
                (  
                    SELECT  
                        CASE  
                            WHEN TRIM(ai.filename) <> '' THEN ai.basedir || ai.filename  
                            ELSE ''  
                        END  
                    FROM json_array_elements((tsma_kirim.images)::JSON) AS it  
                    LEFT JOIN hunter.answer_image AS ai ON it->>'id' = ai.id::text  
                    WHERE it->>'name' = 'signature'  
                ) AS foto_signature_kirim,  
                tsma_kirim.notes as keterangan_lain_kirim,  
                stat.name as status_request,  
                t_app.unilever_approval_notes,  
        '' as forma1_opomaneh,  
        '' as bap_opomaneh,  
        '' as adr_opomaneh  
            FROM transactions.transactions as t  
            left join transactions.status as stat on stat.id=t.status_id  
            left join transactions.transaction_approval as t_app on t.id=t_app.transaction_id  
            left join juragan.juragans as j on j.id=t.juragan_id  
            left join public.provinces as pr on pr.id=j.id_province  
            left join public.cities as ct on ct.id=j.id_city  
            left join public.districts as dt on dt.id=j.id_district  
            left join public.villages as vl on vl.id=j.id_village  
            left join transactions.transaction_detail_mandiri as tdm on t.id=tdm.transaction_id  
            left join outlet.outlet as o_orig on o_orig.id=tdm.outlet_id  
            left join warehouse.cabinets as cab on cab.id=tdm.cabinet_id  
            left join outlet.outlet as o_dest on o_dest.id=tdm.destination_outlet_id  
            left join transactions.transaction_shipping_mandiri as tsm_tarik on tsm_tarik.transaction_detail_mandiri_id=tdm.id and tsm_tarik.shipping_type_id=1  
            left join transactions.transaction_shipping_mandiri_answer as tsma_tarik on tsma_tarik.transaction_shipping_mandiri_id=tsm_tarik.id  
            left join transactions.transaction_shipping_mandiri as tsm_kirim on tsm_kirim.transaction_detail_mandiri_id=tdm.id and tsm_kirim.shipping_type_id=2  
            left join transactions.transaction_shipping_mandiri_answer as tsma_kirim on tsma_kirim.transaction_shipping_mandiri_id=tsm_kirim.id  
            where t.request_type_id = 2 
            AND t.created_at >= '". $this->from_date . "' and t.created_at <= '". $this->to_date . "'";

            // AND t.created_at::date >= %s AND t.created_at::date <= %s 

        
        return collect(DB::select(DB::raw($sql)));
    }
}
