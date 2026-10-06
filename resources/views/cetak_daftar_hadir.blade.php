<!DOCTYPE html>
<html>
<head>
    <title>Cetak Daftar Hadir</title>
    <style>
        /* Pengaturan Margin dan Posisi Header */
        @page { 
            margin: 4.5cm 1.2cm 0cm 1.2cm; /* Margin atas diperbesar untuk ruang header fixed */
        }
        
        body { 
            font-family: "Times New Roman", Times, serif; 
            font-size: 9pt; 
            line-height: 1.15; 
            color: #000;
        }

        /* HEADER FIXED (Akan muncul di setiap halaman otomatis) */
        .header-fixed {
            position: fixed;
            top: -4cm; /* Posisi di dalam margin atas */
            left: 0;
            right: 0;
            height: 3.5cm;
            text-align: center;
        }

        .page-break { page-break-after: always; }
        .page-break:last-child { page-break-after: never; }

        .judul-dokumen {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin-top: -50px;
            margin-bottom: 8px;
            text-transform: uppercase;
            text-decoration: underline;
        }
        
        .info-ujian-wrapper { width: 100%; margin-bottom: 5px; font-weight: bold; }
        .table-info { width: 100%; border-collapse: collapse; table-layout: fixed; font-weight: bold; }
        .table-info td { vertical-align: top; padding: 1px 0; }
        
        .label { width: 110px; white-space: nowrap; font-weight: bold; }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 3px 4px; }
        table.data-table th { background-color: #f2f2f2; text-align: center; }

        .area-ttd-wrapper {
            float: right;
            width: 300px;
            margin-top: 15px;
            page-break-inside: avoid; 
        }

        table.table-ttd-pengawas { width: 100%; border-collapse: collapse; border: 1.5px solid #000; }
        table.table-ttd-pengawas th, table.table-ttd-pengawas td { border: 1px solid #000; padding: 6px; }

        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

<div class="header-fixed">
    <img src="{{ public_path('assets/img/header-unisba.png') }}" style="width: 100%;">
</div>

@foreach($dataHalaman as $data)
    <div class="page-break">
        <div class="judul-dokumen">
            {{ $judulHeader }}
        </div>

        <div class="info-ujian-wrapper">
            <table class="table-info">
                <tr>
                    <td>
                        <table style="width: 100%;">
                            <tr>
                                <td class="label">Program Studi</td>
                                <td style="width: 10px;">:</td>
                                <td>{{ $data['infoUjian']['prodi'] }}</td>
                            </tr>
                            <tr>
                                <td class="label">Kode Mata Kuliah</td>
                                <td>:</td>
                                <td>{{ $data['infoUjian']['kode_mk'] }}</td>
                            </tr>
                            <tr>
                                <td class="label">Mata Kuliah</td>
                                <td>:</td>
                                <td>{{ $data['infoUjian']['mata_kuliah'] }}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-left: 25px;">
                        <table style="width: 100%;">
                            <tr>
                                <td class="label">Hari / Tanggal</td>
                                <td style="width: 10px;">:</td>
                                <td>{{ $data['infoUjian']['hari'] }}, {{ $data['infoUjian']['tanggal'] }}</td>
                            </tr>
                            <tr>
                                <td class="label">Waktu</td>
                                <td>:</td>
                                <td>{{ $data['infoUjian']['jam'] }} WIB</td>
                            </tr>
                            <tr>
                                <td class="label">Ruang</td>
                                <td>:</td>
                                <td>{{ $data['infoUjian']['ruang'] }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%">NO</th>
                    <th style="width: 15%">NPM</th>
                    <th style="width: 45%">NAMA MAHASISWA</th>
                    <th style="width: 8%">KELAS</th>
                    <th style="width: 27%">TANDA TANGAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['peserta'] as $m)
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td style="text-align: center">{{ $m['npm'] }}</td>
                    <td>{{ strtoupper($m['nama']) }}</td>
                    <td style="text-align: center">{{ $m['kelas'] }}</td>
                    <td style="height: 20px;"></td> </tr>
                @endforeach
            </tbody>
        </table>

        <div class="clearfix">
            <div class="area-ttd-wrapper">
                <div style="margin-bottom: 5px;">Bandung, .....................................................</div>
                <table class="table-ttd-pengawas">
                    <tr><th style="width: 50%;">PENGAWAS</th><th style="width: 50%;">TANDA TANGAN</th></tr>
                    <tr><td style="height: 20px;"></td><td></td></tr>
                    <tr><td style="height: 20px;"></td><td></td></tr>
                </table>
            </div>
        </div>
    </div>
@endforeach

</body>
</html>