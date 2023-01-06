<table>
    <thead>
    <tr>
        <th>Kota</th>
        <th>Juragan</th>
        <th>ID Juragan</th>
        <th>Jumlah Toko Baru</th>
        <th>Divalidasi</th>
        <th>Toko Approved</th>
        <th>Toko Baru Mandiri</th>
        <th>Toko Tunda</th>
        <th>Request Tarik</th>
        <th>Request Tukar Guling</th>
    </tr>
    </thead>
    <tbody>
    @foreach($datas as $data)
        <tr>
            <td>{{ $data->city_name }}</td>
            <td>{{ $data->name }}</td>
            <td>{{ $data->id }}</td>
            <td>{{ $data->total_outlet }}</td>
            <td>{{ $data->total_outlet_deal }}</td>
            <td>{{ $data->total_outlet_approved }}</td>
            <td>{{ $data->total_outlet_mandiri }}</td>
            <td>{{ $data->total_outlet_tunda }}</td>
            <td>-</td>
            <td>-</td>
        </tr>
    @endforeach
    </tbody>
</table>
