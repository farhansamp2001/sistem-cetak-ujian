<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Jadwal Ujian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <!-- CSS untuk Sorting Tabel -->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; color: #333; }
        .container { max-width: 1200px; margin-top: 20px; margin-bottom: 90px; }
        
        /* Box Input Judul Ramping */
        .input-judul-box { 
            background: #eef7ff; 
            border: 1.5px solid #3498db; 
            padding: 12px 15px; 
            border-radius: 8px; 
            margin-bottom: 15px; 
        }
        .input-judul-box label { font-size: 0.95rem; margin-bottom: 4px; color: #2c3e50; }
        .input-judul-box input { padding: 6px 12px; font-size: 0.95rem; height: auto; }

        /* Header Section Ramping */
        .header-section { 
            background: white; 
            padding: 12px 15px; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
            margin-bottom: 15px; 
        }
        .header-section h4 { font-size: 1.1rem; margin-bottom: 0; }
        .header-section p { font-size: 0.8rem; margin-bottom: 0; }

        /* Table Styling */
        .table-container { background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; padding: 10px; }
        .table thead { background-color: #2c3e50; color: white; }
        .table th { font-weight: 600; font-size: 0.8rem !important; text-transform: uppercase; padding: 12px; border: none; }
        .table td { vertical-align: middle; font-size: 0.85rem; padding: 8px 12px; }
        
        /* DataTable Style Custom */
        .datatable-top { display: none; }
        .datatable-info, .datatable-pagination { font-size: 0.8rem; }

        /* Sticky Footer */
        .sticky-footer { 
            position: fixed; 
            bottom: 0; 
            left: 0; 
            width: 100%; 
            background: rgba(255, 255, 255, 0.98); 
            padding: 12px 0; 
            box-shadow: 0 -2px 10px rgba(0,0,0,0.08); 
            z-index: 1000; 
            border-top: 1px solid #ddd;
        }
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .sticky-footer .btn-cetak {
            padding: 6px 18px;
            font-size: 0.85rem;
            border-radius: 6px;
            font-weight: 600;
        }

        /* Utilities */
        .form-check-input { width: 1.1em; height: 1.1em; cursor: pointer; }
        tr:hover { background-color: #f1f8ff; transition: 0.1s; }
        .search-box { max-width: 220px; height: 30px; font-size: 0.8rem; }
    </style>
</head>
<body>

<div class="container">
    <form action="{{ route('proses.cetak') }}" method="POST" target="_blank" id="formCetak">
        @csrf
        
        <div class="input-judul-box shadow-sm">
            <label class="fw-bold d-block">JUDUL DOKUMEN (Wajib Diisi):</label>
            <input type="text" name="judul_header" class="form-control shadow-sm" 
                   placeholder="Contoh: DAFTAR HADIR UTS atau DAFTAR HADIR UAS" 
                   required>
            <p class="text-muted small mt-1 mb-0" style="font-size: 0.75rem;">
                * Judul ini akan tercetak di bagian paling atas setiap halaman PDF.
            </p>
        </div>

        <div class="header-section d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="fw-bold">Daftar Jadwal Ditemukan</h4>
                <p class="text-muted small">Klik header tabel untuk mengurutkan (sort) data.</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <input type="text" id="searchInput" class="form-control search-box shadow-sm" placeholder="Cari data...">
                <button type="button" class="btn btn-outline-secondary btn-sm" style="font-size: 0.75rem; padding: 2px 8px;" onclick="toggleSelectAll()">Pilih Semua</button>
            </div>
        </div>

        <div class="table-container mb-5">
            <table class="table table-hover mb-0" id="jadwalTable">
                <thead>
                    <tr>
                        <th class="text-center" data-sortable="false">Pilih</th>
                        <th>Hari</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Ruangan</th>
                        <th>Kode MK</th>
                        <th>Nama MK</th>
                        <th class="text-center">Kelas</th>
                        <th>Prodi</th>
                        <th class="text-center">Peserta</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($daftarJadwal as $j)
                    <tr>
                        <td class="text-center">
                            <input class="form-check-input check-item" type="checkbox" name="pilihan_baris[]" value="{{ $j['index'] }}">
                        </td>
                        <td><span class="fw-semibold">{{ $j['hari'] }}</span></td>
                        <td class="text-nowrap">{{ $j['tgl'] }}</td>
                        <td class="text-nowrap"><span class="badge bg-light text-dark border">{{ $j['jam'] }}</span></td>
                        <td><strong class="text-primary">{{ $j['ruang'] }}</strong></td>
                        <td class="text-muted">{{ $j['kode_mk'] ?? '-' }}</td>
                        <td><div style="max-width: 220px;" class="text-truncate" title="{{ $j['nama_mk'] }}">{{ $j['nama_mk'] }}</div></td>
                        <td class="text-center"><span class="badge bg-secondary">{{ $j['kelas'] ?? '-' }}</span></td>
                        <td><small>{{ $j['prodi'] }}</small></td>
                        <td class="text-center">
                            <span class="badge rounded-pill bg-info text-dark">{{ $j['jumlah_peserta'] ?? '0' }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Sticky Footer dengan Tombol Terpisah -->
        <div class="sticky-footer">
            <div class="footer-content">
                <div>
                    <span class="fw-bold text-secondary small">
                        Terpilih: <span id="selectedCount" class="badge bg-primary fs-6">0</span> Jadwal
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" name="tipe" value="hadir" class="btn btn-primary btn-cetak shadow-sm">
                        📄 Cetak Daftar Hadir
                    </button>
                    <button type="submit" name="tipe" value="pintu" class="btn btn-success btn-cetak shadow-sm">
                        🚪 Cetak Label Pintu
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Script Sorting & Interaksi -->
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script>
    const dataTable = new simpleDatatables.DataTable("#jadwalTable", {
        searchable: false,
        fixedHeight: false,
        perPage: 100,
        paging: false
    });

    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#jadwalTable tbody tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
        });
    });

    document.getElementById('jadwalTable').addEventListener('change', function(e) {
        if (e.target.classList.contains('check-item')) {
            updateCount();
        }
    });

    function updateCount() {
        const checkedCount = document.querySelectorAll('.check-item:checked').length;
        document.getElementById('selectedCount').innerText = checkedCount;
    }

    let isAllSelected = false;
    function toggleSelectAll() {
        isAllSelected = !isAllSelected;
        const checkboxes = document.querySelectorAll('.check-item');
        checkboxes.forEach(cb => {
            if (cb.closest('tr').style.display !== 'none') {
                cb.checked = isAllSelected;
            }
        });
        updateCount();
    }

    document.getElementById('formCetak').addEventListener('submit', function(e) {
        const checkedCount = document.querySelectorAll('.check-item:checked').length;
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal satu jadwal untuk dicetak!');
        }
    });
</script>

</body>
</html>