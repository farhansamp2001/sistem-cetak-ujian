<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Data Ujian - FMIPA UNISBA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; color: #333; }
        .upload-container { max-width: 800px; margin: 50px auto; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .card-header { background: #2c3e50; color: white; border-radius: 15px 15px 0 0 !important; padding: 20px; }
        .btn-upload { background: #27ae60; color: white; padding: 12px 30px; font-weight: 600; border-radius: 8px; transition: 0.3s; }
        .btn-upload:hover { background: #219150; transform: translateY(-2px); color: white; }
        .instruction-section { font-size: 0.9rem; }
        .badge-sheet { background-color: #e67e22; color: white; }
    </style>
</head>
<body>

<div class="container upload-container">
    <div class="card">
        <div class="card-header text-center">
            <h4 class="mb-0">Sistem Cetak Daftar Hadir Ujian</h4>
            <p class="mb-0 mt-1 small">Fakultas MIPA Unisba</p>
        </div>
        <div class="card-body p-4">
            
            <div class="accordion mb-4" id="instructionAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInfo">
                            <i class="me-2">ℹ️</i> Baca Panduan Format Excel (Wajib)
                        </button>
                    </h2>
                    <div id="collapseInfo" class="accordion-collapse collapse" data-bs-parent="#instructionAccordion">
                        <div class="accordion-body instruction-section">
                            <p class="fw-bold"><span class="badge badge-sheet">Struktur Sheet:</span></p>
                            <ul>
                                <li><strong>Sheet 1:</strong> Berisi daftar <strong>Jadwal Ujian</strong>.</li>
                                <li><strong>Sheet 2:</strong> Berisi data <strong>Perwalian Mahasiswa</strong>.</li>
                            </ul>

                            <hr>

                            <p class="fw-bold text-success">Kolom Wajib di Sheet 1 (Jadwal):</p>
                            <p class="small text-muted">Hari, Tanggal, Jam, Ruang, Kode MK, Nama MK, Kelas, Prodi, Dosen, Peserta.</p>
                            
                            <p class="fw-bold text-success mt-3">Kolom Wajib di Sheet 2 (Perwalian):</p>
                            <p class="small text-muted">NPM, Nama Mahasiswa, Kode MK, Nama MK, Kelas, Prodi.</p>

                            <div class="alert alert-warning mt-2 mb-0" style="font-size: 0.85rem;">
                                <strong>Catatan Penting:</strong> 
                                <br>Sistem mencocokkan data berdasarkan <strong>Kode MK</strong> dan <strong>Prodi</strong>. Pastikan penulisan di kedua sheet sama persis agar tidak ada mahasiswa yang tertinggal.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('pilih.jadwal') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="file_excel" class="form-label fw-bold">Pilih File Excel (.xlsx)</label>
                    <input type="file" name="file_excel" id="file_excel" class="form-control form-control-lg" accept=".xlsx" required>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-upload shadow-sm w-100">
                        Proses & Lihat Jadwal
                    </button>
                    <p class="text-muted mt-3 small">Maksimal ukuran file: 10MB</p>
                </div>
            </form>

        </div>
    </div>
    
    <div class="text-center mt-4">
        <p class="text-muted small">&copy; 2026 FMIPA UNISBA</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>