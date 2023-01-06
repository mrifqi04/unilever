<?php


namespace App\export;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;


class JuraganValidasiToko implements
    WithHeadings,
    WithColumnFormatting,
    FromCollection,
    ShouldAutoSize,
    WithBatchInserts,
    WithChunkReading
{
    private $search;
    private $from_date;
    private $to_date;

    /**
     * HunterExport constructor.
     * @param string $search
     */
    public function __construct($from_date, $to_date)
    {
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            'TANGGAL',
            'JAM CHECK IN SURVEY',
            'ID HUNTER',
            'NAMA HUNTER',
            'ID JURAGAN',
            'JURAGAN',
            'ID PJP',
            'ID TOKO',
            'LATITUDE',
            'LONGITUDE',
            'NAMA TOKO',
            'NAMA PEMILIK TOKO',
            'ALAMAT',
            'NOTELP 1',
            'NOTELP 2',
            'PROVINSI',
            'KOTA',
            'KECAMATAN',
            'KELURAHAN',
            'JENIS TOKO',
            'JENIS TOKO LAINNYA',
            'STATUS TOKO',
            'TIPE JALAN',
            'PERIMETER 50-100 M',
            'PERIMETER LAINNYA',
            'SUDAH JUALAN ESKRIM?',
            'JIKA YA, APA SAJA?',
            'ADA KULKAS?',
            'JUMLAH PINTU KULKAS',
            'BERSEDIA MEMBELI CASH PAKET PERDANA?',
            'TERSEDIA TEMPAT UNTUK FREEZER?',
            'KAPASITAS LISTRIK (VA)',
            'FREKUENSI MATI LISTRIK DALAM 1 BULAN',
            'FOTO LUAR TOKO',
            'FOTO DALAM TOKO',
            'FOTO TEMPAT CABINET',
            'FOTO KTP',
            'HASIL VALIDASI JURAGAN',
            'JADWAL USUL PENGIRIMAN CABINET',
            'CATATAN',
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
                created_date,  
                checkin,  
                id_hunter,  
                name_hunter,  
                id_juragan,  
                name_juragan,  
                id_journey_plans,  
                id_outlet,  
                latitude,  
                longitude,  
                name_outlet,  
                owner,  
                address,  
                phone,  
                phone2,  
                province,  
                city,  
                district,  
                village,  
                id_outlet_type,  
                id_outlet_type_other,  
                id_ownership_status,  
                id_street_type,  
                area_radius,  
                area_radius_other,  
                selling,  
                selling_brands,  
                kulkas,  
                kulkas_type,  
                perdana,  
                freezer,  
                electricity_capacity,  
                blackout_intensity,  
                luartoko,  
                dalamtoko,  
                cabinet,  
                ktp,  
                status,  
                recommend_date,  
                note,  
                remark_message  
            FROM public.juragan_validasi_toko 
            where created_date >= '" . $this->from_date . "' and created_date <= '" . $this->to_date . "'";

        return collect(DB::select(DB::raw($sql)));
    }

    public function batchSize(): int
    {
        return 50;
    }
    
    public function chunkSize(): int
    {
        return 50;
    }
}
