<!DOCTYPE html>
<html>
<head>
    <title>Etiket Pasien</title>
    <style>
        body{
            font-family: Arial;
            width: 300px;
            margin: auto;
        }
        .etiket{
            border:1px dashed #000;
            padding:15px;
            text-align:center;
        }
        h3{
            margin:5px 0;
        }
    </style>
</head>
<body onload="window.print()">

<div class="etiket">
    <h3>{{ config('app.name') }}</h3>
    <hr>

    <b>{{ $registrasi->pasien->nama }}</b><br>
    No RM: {{ $registrasi->pasien->no_rm }}<br>
    No Rawat: {{ $registrasi->no_rawat }}<br>
    Poli: {{ $registrasi->nama_poli }}<br>
     DPJP: {{ $registrasi->nama_dokter }}<br>
    Tanggal: {{ date('d-m-Y') }}

    <br><br>

   <img src="data:image/png;base64,{{ $qr }}" width="120">
</div>

</body>
</html>