<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Logistik Keluar</title>

    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css"> --}}
    <style>
        @page {
            size: A4;
            font-size: 10pt,
        }
        h1 {
            font-weight: bold;
            font-size: 20pt;
            text-align: center;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        .table th {
            padding: 3px 3px;
            border: 1px solid #000000;
        }
        .table td {
            padding: 3px 3px;
            border: 1px solid #000000;
        }
        
        
    </style>
</head>

<body>
    {{-- <section class="padding-10mm"> --}}
        <h1>Logistik Keluar</h1>
        <p style="text-align: right"></h3>
        <table width="100%" class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th width="15%" style="text-align: left;">Jenis Kebutuhan</th>
                    <th width="20%" style="text-align: left;">Keterangan</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Penerima</th>
                    <th>Satuan</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @php
                $no = 1;
                @endphp
                @foreach ($logistik_keluar as $keluar)
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td style="">{{ $keluar->jenis_kebutuhan }}</td>
                    <td style="">{{ $keluar->keterangan }}</td>
                    <td style="text-align: center;">{{ $keluar->jumlah }}</td>
                    <td style="text-align: center;">{{ $keluar->status }}</td>
                    <td style="text-align: center;">{{ $keluar->posko_penerima->nama }}</td>
                    <td style="text-align: center;">{{ $keluar->satuan }}</td>
                    <td style="text-align: center;">{{ date('d M Y', strtotime($keluar->tanggal)) }}</td>
                </tr>
                    
                @endforeach
            </tbody>
        </table>
    {{-- </section> --}}

</body>

</html>