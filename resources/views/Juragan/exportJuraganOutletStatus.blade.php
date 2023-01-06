<table>
    <thead>
    <tr>
        <th>Kota</th>
        <th>Juragan</th>
        <th>ID Juragan</th>
        <th>ID Toko</th>
        <th>Nama Toko</th>
        <th>Pemilik Toko</th>
        <th>Alamat</th>
        <th>No Telp</th>
        <th>Status</th>
        <th>Tgl Request</th>
        <th>Tgl Jadwal Kirim</th>
        <th colspan="3" class="text-center">Remark</th>
    </tr>
    </thead>
    <tbody>
    @foreach($datas as $data)
        <tr>
            <td>{{$data->city}}</td>
            <td>{{$data->juragan}}</td>
            <td>{{$data->juragan_id}}</td>
            <td>{{$data->outlet_id}}</td>
            <td>{{$data->outlet_name}}</td>
            <td>{{$data->owner}}</td>
            <td>{{$data->address}}</td>
            <td>{{$data->phone}}</td>
            <td>{{$data->section." - ".$data->value}}</td>
            <td>{{$data->recommend_date}}</td>
            <td>{{$data->send_date}}</td>
            <td class="text-center">
                @if($data->is_mandiri == 1)
                    Toko Mandiri
                @else
                    -
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
