{{--@foreach($data as $dt)--}}
<style>
    /*table {*/
        /*border-style : solid;*/
        /*font-size: 5px;*/
        /*border-width: 1px;*/
    /*}*/
    /*table thead {*/
        /*border-bottom-width: 1px;*/
        /*border-bottom-style : solid;*/
    /*}*/

    /*table tbody tr td{*/
        /*border-right-style: solid;*/
        /*border-right-width: 1px;*/
    /*}*/

    table {
        font-size: 5px;
        border-collapse: collapse;
    }

    table, td, th {
        border: 1px solid black;
    }
</style>

Tanggal : {{\Carbon\Carbon::parse($header->start_date)->format('Y-m-d')}}<br/>
No. Mobil : {{$header->vehicle->license_number}}<br/>
ID Driver : {{$header->driver->id_unilever}}<br/>
Nama Driver : {{$header->driver->name}}<br/>
No. Telp Driver : {{$header->driver->phone}}<br/>
<br/>
<table>
    <thead>
    <tr>
        <td>ADR</td>
        <td>ID Juragan</td>
        <td>Juragan</td>
        <td>ID Outlet</td>
        <td>Nama Outlet</td>
        <td>Nama Pemilik</td>
        <td>Nomor HP</td>
        <td>Alamat</td>
        <td>Item Kirim</td>
        <td>QR Code</td>
        <td>Qty.</td>
        <td>Tipe Request</td>
        <td>Pasang Highlighter</td>
        <td>Lepas Sterofoam</td>
        <td>Bracket Highlighter</td>
        <td>Highlighter</td>
    </tr>
    </thead>
    <tbody>
    @foreach($data[0]->journeyRoute as $dt)
        <tr>
            <td>ADR00{{$dt->deliveryOrders->adr}}</td>
            <td>{{$dt->outlet->id_juragan}}</td>
            <td>{{$dt->outlet->juragan->name}}</td>
            <td>{{$dt->outlet->id}}</td>
            <td>{{$dt->outlet->name}}</td>
            <td>{{$dt->outlet->owner}}</td>
            <td>{{$dt->outlet->phone}}</td>
            <td>{{$dt->outlet->address}}</td>
            <td>{{$dt->cabinet->serialnumber}}</td>
            <td>{{$dt->cabinet->qrcode}}</td>
            <td>1</td>
            <td>Baru</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
        </tr>
    @endforeach
    </tbody>
</table>
