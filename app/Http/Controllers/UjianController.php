<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class UjianController extends Controller
{
    public function index() {
        return view('upload');
    }

    private function formatTanggalIndo($date) {
        if (empty($date)) return '';
        if (is_numeric($date)) {
            try {
                $unix_date = ($date - 25569) * 86400;
                $date = date("Y-m-d", $unix_date);
            } catch (\Exception $e) { return $date; }
        }
        $bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        try {
            $ts = strtotime($date);
            return (!$ts) ? $date : date('d', $ts) . ' ' . $bulan[(int)date('m', $ts)] . ' ' . date('Y', $ts);
        } catch (\Exception $e) { return $date; }
    }

    private function clean($string) {
        return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$string));
    }

    public function preview(Request $request)
    {
        if (!$request->hasFile('file_excel')) return back();
        $file = $request->file('file_excel');
        $path = $file->storeAs('temp', 'data_ujian.xlsx');
        $import = Excel::toArray([], storage_path('app/'.$path), null, \Maatwebsite\Excel\Excel::XLSX, true);
        
        $headerJadwal = array_map(fn($v) => $this->clean($v), $import[0][0] ?? []);
        $headerPerwalian = array_map(fn($v) => $this->clean($v), $import[1][0] ?? []);

        $getIdx = function($headers, $keywords) {
            foreach ($headers as $index => $text) {
                foreach ($keywords as $key) {
                    if (str_contains($text, $key)) return $index;
                }
            }
            return null;
        };

        // Header Jadwal
        $cJ_Ruang = $getIdx($headerJadwal, ['ruang', 'rg']);
        $cJ_Hari = $getIdx($headerJadwal, ['hari']);
        $cJ_Tanggal = $getIdx($headerJadwal, ['tanggal', 'tgl']);
        $cJ_Jam = $getIdx($headerJadwal, ['waktu', 'jam', 'pukul']);
        $cJ_Kode = $getIdx($headerJadwal, ['kodemk', 'kodematkul', 'kdmk', 'idmk']);
        $cJ_Nama = $getIdx($headerJadwal, ['namamk', 'namamatkul', 'matakuliah']);
        $cJ_Kelas = $getIdx($headerJadwal, ['kelas', 'kls']);
        $cJ_Range = $getIdx($headerJadwal, ['peserta', 'range', 'npm']);
        $cJ_Prodi = $getIdx($headerJadwal, ['prodi', 'programstudi']);

        // Header Perwalian
        $cP_Npm = $getIdx($headerPerwalian, ['npm', 'noinduk']);
        $cP_Kode = $getIdx($headerPerwalian, ['kodemk', 'kodematkul', 'kdmk', 'idmk']);
        $cP_Kelas = $getIdx($headerPerwalian, ['kelas', 'kls']);
        $cP_Prodi = $getIdx($headerPerwalian, ['prodi', 'programstudi']);

        if (is_null($cJ_Ruang) || is_null($cP_Npm)) return "Kolom wajib tidak ditemukan.";

        $dataJadwalRaw = collect($import[0]);
        $dataPerwalian = collect($import[1])->skip(1);

        $daftarJadwal = $dataJadwalRaw->map(function($row, $index) use ($dataJadwalRaw, $dataPerwalian, $cJ_Ruang, $cJ_Hari, $cJ_Tanggal, $cJ_Jam, $cJ_Kode, $cJ_Nama, $cJ_Kelas, $cJ_Range, $cJ_Prodi, $cP_Npm, $cP_Kode, $cP_Kelas, $cP_Prodi) {
            
            if ($index == 0 || empty(trim((string)($row[$cJ_Nama] ?? ''))) || empty(trim((string)($row[$cJ_Kode] ?? '')))) {
                return null;
            }

            $kodeMKSpesifik = $this->clean($row[$cJ_Kode] ?? '');
            $prodiSpesifik = $this->clean($row[$cJ_Prodi] ?? '');
            $stringKelas = $this->clean($row[$cJ_Kelas] ?? '');
            $listRange = explode("\n", str_replace("\r", "", trim((string)($row[$cJ_Range] ?? ''))));

            $kriteriaBaris = [];
            foreach ($listRange as $key => $rangeRaw) {
                $rangeRaw = trim($rangeRaw);
                if (empty($rangeRaw)) continue;
                $kelasSatuHuruf = isset($stringKelas[$key]) ? $stringKelas[$key] : (isset($stringKelas[0]) ? $stringKelas[0] : '');

                $npmStart = 0; $npmEnd = 0;
                if (str_contains($rangeRaw, '-')) {
                    $parts = explode('-', $rangeRaw);
                    $s = preg_replace('/[^0-9]/', '', trim($parts[0]));
                    $e = preg_replace('/[^0-9]/', '', trim($parts[1]));
                    if (strlen($e) < strlen($s) && !empty($e)) $e = substr($s, 0, strlen($s)-strlen($e)) . $e;
                    $npmStart = (float)$s; $npmEnd = (float)$e;
                } else {
                    $s = preg_replace('/[^0-9]/', '', $rangeRaw);
                    $npmStart = (float)$s; $npmEnd = (float)$s;
                }
                $kriteriaBaris[] = ['kode' => $kodeMKSpesifik, 'prodi' => $prodiSpesifik, 'kls' => $kelasSatuHuruf, 's' => $npmStart, 'e' => $npmEnd];
            }

            $jumlahPeserta = 0;
            foreach ($dataPerwalian as $p) {
                $npmMhs = (float)preg_replace('/[^0-9]/', '', (string)($p[$cP_Npm] ?? ''));
                $kodeMhs = $this->clean($p[$cP_Kode] ?? '');
                $kelasMhs = $this->clean($p[$cP_Kelas] ?? '');
                $prodiMhs = !is_null($cP_Prodi) ? $this->clean($p[$cP_Prodi] ?? '') : '';

                foreach ($kriteriaBaris as $k) {
                    // Validasi: Kode MK DAN Prodi (Jika ada)
                    $matchMK = ($kodeMhs === $k['kode']);
                    
                    if (!is_null($cP_Prodi) && !empty($k['prodi'])) {
                        $matchMK = $matchMK && (str_contains($prodiMhs, $k['prodi']) || str_contains($k['prodi'], $prodiMhs));
                    }

                    if ($matchMK && $kelasMhs === $k['kls'] && ($npmMhs >= $k['s'] && $npmMhs <= $k['e'])) {
                        $jumlahPeserta++;
                        break; 
                    }
                }
            }

            return [
                'index' => $index,
                'hari' => trim((string)($row[$cJ_Hari] ?? '')),
                'tgl' => $this->formatTanggalIndo($row[$cJ_Tanggal] ?? ''),
                'jam' => trim((string)($row[$cJ_Jam] ?? '')),
                'ruang' => trim((string)($row[$cJ_Ruang] ?? '')),
                'kode_mk' => trim((string)($row[$cJ_Kode] ?? '')),
                'nama_mk' => trim((string)($row[$cJ_Nama] ?? '')),
                'kelas' => trim((string)($row[$cJ_Kelas] ?? '')),
                'prodi' => trim((string)($row[$cJ_Prodi] ?? '')),
                'jumlah_peserta' => $jumlahPeserta, 
            ];
        })->filter();

        return view('pilih_jadwal', compact('daftarJadwal'));
    }

    public function prosesCetak(Request $request)
    {
        $selectedRows = $request->input('pilihan_baris');
        $judulUser = $request->input('judul_header');

        if (empty($selectedRows)) return "Pilih minimal satu jadwal.";
        if (empty($judulUser)) return "Judul Dokumen Wajib Diisi!";

        ini_set('memory_limit', '512M');
        $path = 'temp/data_ujian.xlsx';
        $import = Excel::toArray([], storage_path('app/'.$path), null, \Maatwebsite\Excel\Excel::XLSX, true);
        
        $headerJadwal = array_map(fn($v) => $this->clean($v), $import[0][0] ?? []);
        $headerPerwalian = array_map(fn($v) => $this->clean($v), $import[1][0] ?? []);

        $getIdx = function($headers, $keywords) {
            foreach ($headers as $index => $text) {
                foreach ($keywords as $key) {
                    if (str_contains($text, $key)) return $index;
                }
            }
            return null;
        };

        $cJ_Ruang = $getIdx($headerJadwal, ['ruang', 'rg']);
        $cJ_Hari = $getIdx($headerJadwal, ['hari']);
        $cJ_Tanggal = $getIdx($headerJadwal, ['tanggal', 'tgl']);
        $cJ_Jam = $getIdx($headerJadwal, ['waktu', 'jam', 'pukul']);
        $cJ_Kode = $getIdx($headerJadwal, ['kodemk', 'kodematkul', 'kdmk', 'idmk']);
        $cJ_Nama = $getIdx($headerJadwal, ['namamk', 'namamatkul', 'matakuliah']);
        $cJ_Kelas = $getIdx($headerJadwal, ['kelas', 'kls']);
        $cJ_Range = $getIdx($headerJadwal, ['peserta', 'range', 'npm']);
        $cJ_Dosen = $getIdx($headerJadwal, ['dosen', 'pengajar', 'namadosen']);
        $cJ_Prodi = $getIdx($headerJadwal, ['prodi', 'programstudi']);

        $cP_Npm = $getIdx($headerPerwalian, ['npm', 'noinduk']);
        $cP_Nama = $getIdx($headerPerwalian, ['nama', 'mahasiswa']);
        $cP_Kode = $getIdx($headerPerwalian, ['kodemk', 'kodematkul', 'kdmk', 'idmk']);
        $cP_Kelas = $getIdx($headerPerwalian, ['kelas', 'kls']);
        $cP_Prodi = $getIdx($headerPerwalian, ['prodi', 'programstudi']);

        $dataJadwalRaw = collect($import[0]);
        $dataPerwalian = collect($import[1])->skip(1);
        $semuaHalaman = [];

        foreach ($selectedRows as $rowIndex) {
            $anchor = $dataJadwalRaw[$rowIndex];
            $kodeMKSpesifik = $this->clean($anchor[$cJ_Kode] ?? '');
            $prodiSpesifik = $this->clean($anchor[$cJ_Prodi] ?? '');
            $stringKelas = $this->clean($anchor[$cJ_Kelas] ?? '');
            $listRange = explode("\n", str_replace("\r", "", trim((string)($anchor[$cJ_Range] ?? ''))));

            $kriteriaCetak = [];
            foreach ($listRange as $key => $rangeRaw) {
                if (empty(trim($rangeRaw))) continue;
                $kelasSatuHuruf = isset($stringKelas[$key]) ? $stringKelas[$key] : (isset($stringKelas[0]) ? $stringKelas[0] : '');
                
                $npmStart = 0; $npmEnd = 0;
                if (str_contains($rangeRaw, '-')) {
                    $parts = explode('-', $rangeRaw);
                    $s = preg_replace('/[^0-9]/', '', trim($parts[0]));
                    $e = preg_replace('/[^0-9]/', '', trim($parts[1]));
                    if (strlen($e) < strlen($s) && !empty($e)) $e = substr($s, 0, strlen($s)-strlen($e)) . $e;
                    $npmStart = (float)$s; $npmEnd = (float)$e;
                } else {
                    $s = preg_replace('/[^0-9]/', '', $rangeRaw);
                    $npmStart = (float)$s; $npmEnd = (float)$s;
                }
                $kriteriaCetak[] = ['kode' => $kodeMKSpesifik, 'prodi' => $prodiSpesifik, 'kls' => $kelasSatuHuruf, 's' => $npmStart, 'e' => $npmEnd];
            }

            $arrayPeserta = [];
            foreach ($dataPerwalian as $p) {
                $rawNpm = trim((string)($p[$cP_Npm] ?? ''));
                if (empty($rawNpm)) continue;
                $npmMhs = (float)preg_replace('/[^0-9]/', '', $rawNpm);
                $kodeMhs = $this->clean($p[$cP_Kode] ?? ''); 
                $kelasMhs = $this->clean($p[$cP_Kelas] ?? '');
                $prodiMhs = !is_null($cP_Prodi) ? $this->clean($p[$cP_Prodi] ?? '') : '';

                foreach ($kriteriaCetak as $k) {
                    $matchMK = ($kodeMhs === $k['kode']);
                    
                    if (!is_null($cP_Prodi) && !empty($k['prodi'])) {
                        $matchMK = $matchMK && (str_contains($prodiMhs, $k['prodi']) || str_contains($k['prodi'], $prodiMhs));
                    }

                    if ($matchMK && $kelasMhs === $k['kls'] && ($npmMhs >= $k['s'] && $npmMhs <= $k['e'])) {
                        $arrayPeserta[] = [
                            'npm' => $rawNpm, 
                            'nama' => $p[$cP_Nama] ?? '', 
                            'kelas' => strtoupper((string)($p[$cP_Kelas] ?? ''))
                        ];
                        break; 
                    }
                }
            }

            $sortedPeserta = collect($arrayPeserta)->sort(function($a, $b) {
                if ($a['kelas'] === $b['kelas']) {
                    return (float)preg_replace('/[^0-9]/', '', $a['npm']) <=> (float)preg_replace('/[^0-9]/', '', $b['npm']);
                }
                return $a['kelas'] <=> $b['kelas'];
            });

            $semuaHalaman[] = [
                'peserta' => $sortedPeserta,
                'infoUjian' => [
                    'prodi' => $anchor[$cJ_Prodi] ?? 'PROGRAM STUDI',
                    'ruang' => trim((string)$anchor[$cJ_Ruang]),
                    'hari' => trim((string)$anchor[$cJ_Hari]),
                    'tanggal' => $this->formatTanggalIndo($anchor[$cJ_Tanggal]), 
                    'jam' => trim((string)$anchor[$cJ_Jam]),
                    'mata_kuliah' => trim((string)$anchor[$cJ_Nama]),
                    'kode_mk' => trim((string)$anchor[$cJ_Kode]),
                    'dosen' => trim((string)($anchor[$cJ_Dosen] ?? '-')),
                ]
            ];
        }

        return Pdf::loadView(($request->tipe == 'hadir' ? 'cetak_daftar_hadir' : 'cetak_pintu'), [
            'dataHalaman' => $semuaHalaman, 
            'judulHeader' => strtoupper($judulUser),
        ])->setPaper('a4', 'portrait')->stream('Daftar_Hadir.pdf');
    }
}