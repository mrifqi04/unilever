<html>

<head>
    <meta http-equiv=Content-Type content="text/html; charset=windows-1252">
    <meta name=Generator content="Microsoft Word 15 (filtered)">
    <style>
        <!--
        /* Font Definitions */
        @font-face
        {font-family:"Cambria Math";
            panose-1:2 4 5 3 5 4 6 3 2 4;}
        @font-face
        {font-family:Calibri;
            panose-1:2 15 5 2 2 2 4 3 2 4;}
        /* Style Definitions */
        p.MsoNormal, li.MsoNormal, div.MsoNormal
        {margin-top:0in;
            margin-right:0in;
            margin-bottom:8.0pt;
            margin-left:0in;
            line-height:106%;
            font-size:11.0pt;
            font-family:"Calibri","sans-serif";}
        p
        {margin-right:0in;
            margin-left:0in;
            font-size:12.0pt;
            font-family:"Times New Roman","serif";}
        p.msopapdefault, li.msopapdefault, div.msopapdefault
        {mso-style-name:msopapdefault;
            margin-right:0in;
            margin-bottom:8.0pt;
            margin-left:0in;
            line-height:106%;
            font-size:12.0pt;
            font-family:"Times New Roman","serif";}
        .MsoChpDefault
        {font-size:10.0pt;}
        .MsoPapDefault
        {margin-bottom:8.0pt;
            line-height:106%;}
        @page WordSection1
        {size:595.35pt 841.95pt;
            margin:1.0in 1.0in 1.0in 1.0in;}
        div.WordSection1
        {page:WordSection1;}
        -->
    </style>

</head>

<body lang=EN-US>

<div class=WordSection1>

    <table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0
           style='border-collapse:collapse'>
        <tr style='height:80.5pt'>
            <td width=200 style='width:150.25pt;padding:0in 5.4pt 0in 5.4pt;height:80.5pt'>
                <p class=MsoNormal align=center style='margin-bottom:0in;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><img width=160 height=160 id="Picture 8"
                                             src="{{public_path()."/assets/images/seru.png"}}"></p>
            </td>
            <td width=200 style='width:150.3pt;padding:0in 5.4pt 0in 5.4pt;height:80.5pt'>
                <p class=MsoNormal align=center style='margin-bottom:0in;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span lang=EN-ID style='font-size:
  14.0pt'>FORM PERSETUJUAN </span></b></p>
                <p class=MsoNormal align=center style='text-align:center'><b><span
                                lang=EN-ID style='font-size:14.0pt;line-height:106%'>PENARIKAN CABINET</span></b></p>
                <p class=MsoNormal align=center style='margin-bottom:0in;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span lang=EN-ID style='font-size:
  14.0pt'>No : {{$data->id}}</span></b></p>
            </td>
            <td width=200 style='width:150.3pt;padding:0in 5.4pt 0in 5.4pt;height:80.5pt'>
                <p class=MsoNormal align=center style='margin-bottom:0in;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><img width=181 height=122 id="Picture 7"
                                             src="{{public_path()."/assets/images/unilever-logo.png"}}"></p>
            </td>
        </tr>
    </table>

    <p class=MsoNormal>&nbsp;</p>

    <p class=MsoNormal><span lang=EN-ID>{{date('l, j, F, Y', strtotime($data->created_at))}}</span></p>

    <p class=MsoNormal>&nbsp;</p>

    <p class=MsoNormal style='margin-bottom:6.0pt;line-height:150%'><span
                lang=EN-ID>Saya yang bertanda tangan dibawah ini, pemilik sekaligus penanggung
jawab toko dan </span></p>

    <p class=MsoNormal style='margin-bottom:6.0pt;line-height:150%'><span
                lang=EN-ID>Mitra Seru! sebagai berikut:</span></p>

    <p class=MsoNormal style='margin-bottom:6.0pt;line-height:150%'><span
                lang=EN-ID>Nama Toko&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <i>{{$data->DetailMandiri->outlet->name}}</i></span></p>

    <p class=MsoNormal style='margin-bottom:6.0pt;line-height:150%'><span
                lang=EN-ID>Kode Toko&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <i>{{$data->DetailMandiri->outlet->id_unilever}}</i></span></p>

    <p class=MsoNormal style='margin-bottom:6.0pt;line-height:150%'><span
                lang=EN-ID>Nama Pemilik&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : <i>{{$data->DetailMandiri->outlet->owner}}</i></span></p>

    <p class=MsoNormal style='margin-bottom:6.0pt;line-height:150%'><span
                lang=EN-ID>Alamat&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
: <i>{{$data->DetailMandiri->outlet->address}}</i></span></p>

    <p class=MsoNormal style='margin-bottom:6.0pt;line-height:150%'><span
                lang=EN-ID>Nama Juragan&nbsp;&nbsp;&nbsp; : <i>{{$data->Juragan->name}}</i></span></p>

    <p class=MsoNormal style='margin-bottom:6.0pt;line-height:150%'><span
                lang=EN-ID>Kode Juragan&nbsp;&nbsp;&nbsp;&nbsp; : <i>{{$data->Juragan->id_unilever_owner}}</i></span></p>

    <p class=MsoNormal style='margin-bottom:6.0pt;text-align:justify;line-height:
150%'><span lang=EN-ID>Pada hari ini telah dikunjungi oleh pihak Seru! dan
menyatakan atas kesadaran sendiri dan tanpa paksaan dari pihak manapun, telah
sepakat dan menyatakan setuju untuk mengakhiri kerjasama dengan pihak Seru! dan
bersedia mengembalikan seluruh asset yang dipinjamkan kepada kami sebagai mitra
Seru!.</span></p>

    <p class=MsoNormal style='margin-bottom:6.0pt;text-align:justify;line-height:
150%'><span lang=EN-ID>Adapun teknis pengembalian dan waktu diserahkan
sepenuhnya kepada pihak Seru!.</span></p>

    <p class=MsoNormal style='margin-bottom:6.0pt;text-align:justify;line-height:
150%'><span lang=EN-ID>Kami juga bersedia menyelesaikan semua kewajiban yang
masih belum terselesaikan dengan pihak Seru! selambat-lambatnya dalam waktu 7
hari sejak pernyataan ini dibuat. </span></p>

    <p class=MsoNormal style='margin-left:1.0in;text-indent:-1.0in'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </p>

    <p class=MsoNormal style='margin-left:1.0in;text-indent:-1.0in'>&nbsp;</p>

    <p class=MsoNormal style='margin-left:1.0in;text-indent:-1.0in'>&nbsp;</p>

    <p class=MsoNormal style='margin-left:1.0in;text-indent:-1.0in'>&nbsp;</p>

    <p class=MsoNormal style='margin-left:1.0in;text-indent:-1.0in'>&nbsp;</p>

    <p class=MsoNormal style='margin-left:1.0in;text-indent:-1.0in'>&nbsp;</p>

    <table class=MsoTableGrid border=0 cellspacing=0 cellpadding=0
           style='border-collapse:collapse;border:none'>
        <tr>
            <td width=200 valign=top style='width:150.25pt;padding:0in 5.4pt 0in 5.4pt'>
                <p class=MsoNormal align=center style='margin-bottom:0in;margin-bottom:.0001pt;
  text-align:center;line-height:normal'>Mitra</p>
            </td>
            <td width=200 valign=top style='width:150.3pt;padding:0in 5.4pt 0in 5.4pt'>
                <p class=MsoNormal align=center style='margin-bottom:0in;margin-bottom:.0001pt;
  text-align:center;line-height:normal'>Juragan</p>
            </td>
            <td width=200 valign=top style='width:150.3pt;padding:0in 5.4pt 0in 5.4pt'>
                <p class=MsoNormal align=center style='margin-bottom:0in;margin-bottom:.0001pt;
  text-align:center;line-height:normal'>Unilever</p>
            </td>
        </tr>
        <tr style='height:27.75pt'>
            <td width=200 style='width:150.25pt;padding:0in 5.4pt 0in 5.4pt;height:27.75pt'>
                <p class=MsoNormal align=center style='margin-bottom:0in;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><img width=122 height=122
                                             src={{$ttd}}></p>
            </td>
            <td width=200 valign=top style='width:150.3pt;padding:0in 5.4pt 0in 5.4pt;
  height:27.75pt'>
                <p class=MsoNormal style='margin-bottom:0in;margin-bottom:.0001pt;line-height:
  normal'>&nbsp;</p>
            </td>
            <td width=200 style='width:150.3pt;padding:0in 5.4pt 0in 5.4pt;height:27.75pt'>
                <p class=MsoNormal align=center style='margin-bottom:0in;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><b><span style='font-size:12.0pt'>Approved
  by System</span></b></p>
            </td>
        </tr>
        <tr>
            <td width=200 valign=top style='width:150.25pt;padding:0in 5.4pt 0in 5.4pt'>
                <p class=MsoNormal align=center style='margin-bottom:0in;margin-bottom:.0001pt;
  text-align:center;line-height:normal'>(                                     )</p>
            </td>
            <td width=200 valign=top style='width:150.3pt;padding:0in 5.4pt 0in 5.4pt'>
                <p class=MsoNormal align=center style='margin-bottom:0in;margin-bottom:.0001pt;
  text-align:center;line-height:normal'>(                                     )</p>
            </td>
            <td width=200 valign=top style='width:150.3pt;padding:0in 5.4pt 0in 5.4pt'>
                <p class=MsoNormal style='margin-bottom:0in;margin-bottom:.0001pt;line-height:
  normal'>&nbsp;</p>
            </td>
        </tr>
        <tr>
            <td width=200 valign=top style='width:150.25pt;padding:0in 5.4pt 0in 5.4pt'>
                <p class=MsoNormal align=center style='margin-bottom:0in;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span lang=EN-ID style='font-size:9.0pt'>Nama
  Pemilik Toko</span></p>
            </td>
            <td width=200 valign=top style='width:150.3pt;padding:0in 5.4pt 0in 5.4pt'>
                <p class=MsoNormal align=center style='margin-bottom:0in;margin-bottom:.0001pt;
  text-align:center;line-height:normal'><span lang=EN-ID style='font-size:9.0pt'>Nama
  Juragan</span></p>
            </td>
            <td width=200 valign=top style='width:150.3pt;padding:0in 5.4pt 0in 5.4pt'>
                <p class=MsoNormal style='margin-bottom:0in;margin-bottom:.0001pt;line-height:
  normal'>&nbsp;</p>
            </td>
        </tr>
    </table>

    <p class=MsoNormal style='margin-left:1.0in;text-indent:-1.0in'>&nbsp;</p>

</div>

</body>

</html>
