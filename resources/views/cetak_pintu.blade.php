<!DOCTYPE html>
<html>
<head>
    <title>Cetak Label Pintu</title>
    <style>
        /* Pengaturan Kertas Portrait A4 */
        @page { 
            size: a4 portrait; 
            margin: 0.5cm; 
        }
        
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0;
            color: #000;
        }

        /* Pembungkus utama per halaman */
        .page-wrapper {
            width: 100%;
            clear: both;
            position: relative;
        }

        /* Container Kolom (Kiri & Kanan) */
        .column-box {
            width: 47%; /* Dipersempit sedikit agar tidak mepet garis */
            display: inline-block;
            vertical-align: top;
            box-sizing: border-box;
            page-break-inside: avoid; 
        }

        /* Jarak untuk kolom kiri */
        .column-left {
            margin-right: 5%; 
        }

        .header-pintu {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .logo-unisba {
            width: 100%;
            height: auto;
        }

        .info-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 9pt;
            font-weight: bold;
        }

        .info-table td {
            padding: 1px 0;
            vertical-align: top;
        }

        .label-col { width: 100px; }
        .dots-col { width: 12px; text-align: center; }

        .student-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt; 
        }

        .student-table th, .student-table td {
            border: 1px solid #000;
            padding: 2px 3px;
            text-align: center;
        }

        .text-left { text-align: left !important; }

        .page-break { 
            page-break-after: always; 
        }
    </style>
</head>
<body>

@foreach(collect($dataHalaman)->chunk(2) as $chunk)
    <div class="page-wrapper @if(!$loop->last) page-break @endif">
        
        @foreach($chunk as $index => $data)
            <div class="column-box {{ $index % 2 == 0 ? 'column-left' : '' }}">
                <div class="header-pintu">
                    <img src="{{ public_path('assets/img/header-unisba.png') }}" class="logo-unisba">
                </div>

                <table class="info-table">
                    <tr>
                        <td class="label-col">Program Studi</td>
                        <td>:</td>
                        <td>{{ $data['infoUjian']['prodi'] }}</td>
                    </tr>
                    <tr>
                        <td>Kode MK</td>
                        <td>:</td>
                        <td>{{ $data['infoUjian']['kode_mk'] }}</td>
                    </tr>
                    <tr>
                        <td>Mata Kuliah</td>
                        <td>:</td>
                        <td>{{ $data['infoUjian']['mata_kuliah'] }}</td>
                    </tr>
                    <tr>
                        <td>Hari / Tanggal</td>
                        <td>:</td>
                        <td>{{ $data['infoUjian']['hari'] }}, {{ $data['infoUjian']['tanggal'] }}</td>
                    </tr>
                    <tr>
                        <td>Waktu</td>
                        <td>:</td>
                        <td>{{ $data['infoUjian']['jam'] }} WIB</td>
                    </tr>
                    <tr>
                        <td>Ruang</td>
                        <td>:</td>
                        <td>{{ strtoupper($data['infoUjian']['ruang']) }}</td>
                    </tr>
                </table>

                <table class="student-table">
                    <thead>
                        <tr>
                            <th style="width: 20px;">No</th>
                            <th style="width: 80px;">NPM</th>
                            <th>Nama</th>
                            <th style="width: 25px;">Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['peserta'] as $m)
                         <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $m['npm'] }}</td>
                            <td class="text-left">{{ strtoupper($m['nama']) }}</td>
                            <td>{{ $m['kelas'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
@endforeach

</body>
</html>