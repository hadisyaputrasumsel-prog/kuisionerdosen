<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Laporan Kuisioner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <style>
        body { background-color: #f8f9fa; }
        .sidebar-link { transition: 0.2s; border-left: 4px solid transparent; border-radius: 0 8px 8px 0; margin-right: 1rem; }
        .sidebar-link:hover { background-color: rgba(255,255,255,0.1); border-left-color: rgba(255,255,255,0.5); }
        .active-menu { background-color: rgba(255,255,255,0.2) !important; font-weight: bold; border-left: 4px solid #fff !important; border-radius: 0 8px 8px 0; margin-right: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .sidebar-bg { background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%); }
        .card-header-custom { background: linear-gradient(90deg, #1e3c72 0%, #2a5298 100%); color: white; border: none; }
        
        @media print {
            .no-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; }
            .content-area { margin-left: 0 !important; padding: 0 !important; }
            .card { border: none !important; box-shadow: none !important; }
            .print-header { display: block !important; text-align: center; margin-bottom: 2rem; }
            .table { border-color: #000 !important; }
            .table th, .table td { border-color: #000 !important; padding: 8px !important; }
            .badge { border: 1px solid #000; color: #000 !important; background: transparent !important; }
        }
    </style>
</head>
<body>

<div class="d-flex" style="min-height: 100vh;">
    <!-- Sidebar -->
    <div class="sidebar-bg text-white py-3 d-flex flex-column no-print" style="width: 280px; position: fixed; height: 100vh; overflow-y: auto; box-shadow: 4px 0 10px rgba(0,0,0,0.1); z-index: 1000;">
        <div class="text-center mb-4 mt-2 px-3 pb-3">
            <h4 class="fw-bold mb-0 tracking-wider">ADMIN PANEL</h4>
            <small class="text-white-50">Sistem Kuisioner Dosen</small>
        </div>
        
        <div class="px-3 mb-2 text-white-50 small fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Menu Utama</div>
        <ul class="nav flex-column gap-1 mb-auto w-100">
            <li class="nav-item w-100">
                <a class="nav-link text-white {{ Route::is('admin.dashboard') ? 'active-menu' : 'sidebar-link' }} d-flex align-items-center py-3" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-grid-1x2-fill me-3 fs-5"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link text-white {{ Route::is('admin.sinkron') ? 'active-menu' : 'sidebar-link' }} d-flex align-items-center py-3" href="{{ route('admin.sinkron') }}">
                    <i class="bi bi-cloud-arrow-down-fill me-3 fs-5"></i> <span>Sinkron SIMAK</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link text-white {{ Route::is('admin.jadwal') ? 'active-menu' : 'sidebar-link' }} d-flex align-items-center py-3" href="{{ route('admin.jadwal') }}">
                    <i class="bi bi-people-fill me-3 fs-5"></i> <span>Data Dosen & Matkul</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link text-white {{ Route::is('admin.laporan') ? 'active-menu' : 'sidebar-link' }} d-flex align-items-center py-3" href="{{ route('admin.laporan') }}">
                    <i class="bi bi-journal-text me-3 fs-5"></i> <span>Laporan Hasil</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link text-white {{ Route::is('admin.questions.*') ? 'active-menu' : 'sidebar-link' }} d-flex align-items-center py-3" href="{{ route('admin.questions.index') }}">
                    <i class="bi bi-ui-checks me-3 fs-5"></i> <span>Materi Kuisioner</span>
                </a>
            </li>
            
            <div class="px-3 mt-4 mb-2 text-white-50 small fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Lainnya</div>
            <li class="nav-item w-100">
                <a class="nav-link text-white sidebar-link d-flex align-items-center py-3 text-warning" href="{{ route('form.index') }}" target="_blank">
                    <i class="bi bi-box-arrow-up-right me-3 fs-5"></i> <span>Lihat Form Mahasiswa</span>
                </a>
            </li>
            <li class="nav-item w-100 mt-2">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link text-white sidebar-link d-flex align-items-center w-100 text-start bg-transparent border-0 text-danger" style="opacity: 0.8; padding-top: 1rem; padding-bottom: 1rem;">
                        <i class="bi bi-box-arrow-left me-3 fs-5"></i> <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
        <div class="mt-auto pt-3 text-center text-white-50 small opacity-50">
            &copy; 2026 Univ Sumatera Selatan
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 bg-light p-4 content-area" style="margin-left: 280px; min-height: 100vh;">
        
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 no-print gap-2">
            <div>
                <h2 class="fw-bold text-dark mb-0">Laporan Hasil</h2>
                <small class="text-muted">Hasil evaluasi kepuasan mahasiswa terhadap dosen dalam pembelajaran</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.laporan.preview', ['periode' => request('periode'), 'prodi_id' => request('prodi_id')]) }}" target="_blank" class="btn btn-outline-primary fw-semibold shadow-sm">
                    <i class="bi bi-book me-1"></i> Buka Buku Cetak (PDF)
                </a>
            </div>
        </div>

        <!-- Global Filter Card -->
        <div class="card shadow-sm border-0 mb-4 rounded-3 no-print">
            <div class="card-body py-3">
                <form action="{{ route('admin.laporan') }}" method="GET" class="row align-items-center g-3" id="globalFilterForm">
                    <input type="hidden" name="tab" id="activeTabParam" value="{{ request('tab', 'preview') }}">
                    <div class="col-md-4">
                        <label class="form-label fw-bold mb-1 small text-uppercase text-secondary"><i class="bi bi-calendar-event me-1"></i>Filter Periode:</label>
                        <select name="periode" class="form-select form-select-lg" onchange="this.form.submit()">
                            <option value="">-- Semua Periode --</option>
                            @foreach($filterPeriodes as $p)
                                <option value="{{ $p }}" {{ request('periode') == $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold mb-1 small text-uppercase text-secondary"><i class="bi bi-building me-1"></i>Filter Program Studi:</label>
                        <select name="prodi_id" class="form-select form-select-lg" onchange="this.form.submit()">
                            <option value="">-- Semua Program Studi --</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}" {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>{{ $prodi->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end" style="padding-top: 28px;">
                        <a href="{{ route('admin.laporan') }}" class="btn btn-outline-secondary btn-lg w-100">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filter
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4 no-print" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ request('tab', 'preview') === 'preview' ? 'active' : '' }} fw-bold" id="preview-tab" data-bs-toggle="tab" data-bs-target="#preview" type="button" role="tab">
                    <i class="bi bi-table me-2"></i>Tabel Data
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ request('tab') === 'grafik' ? 'active' : '' }} fw-bold text-primary" id="grafik-tab" data-bs-toggle="tab" data-bs-target="#grafik" type="button" role="tab">
                    <i class="bi bi-bar-chart-fill me-2"></i>Preview Hasil Evaluasi (Grafik)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ request('tab') === 'book' ? 'active' : '' }} fw-bold text-secondary" id="book-tab" data-bs-toggle="tab" data-bs-target="#book" type="button" role="tab">
                    <i class="bi bi-gear-fill me-2"></i>Konfigurasi & Cetak Buku
                </button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- TAB 1: Tabel Data -->
            <div class="tab-pane fade {{ request('tab', 'preview') === 'preview' ? 'show active' : '' }}" id="preview" role="tabpanel">

                <div class="card shadow border-0" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-body p-0 p-md-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-3">#</th>
                                        <th class="py-3">Nama Dosen</th>
                                        <th class="py-3">Mata Kuliah</th>
                                        <th class="py-3">Program Studi</th>
                                        <th class="text-center py-3">Responden</th>
                                        <th class="text-center py-3">Nilai Rata-Rata</th>
                                        <th class="text-center py-3">Predikat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $currentDosenId = null; @endphp
                                    @forelse($jadwals as $index => $jadwal)
                                        <tr>
                                            <td class="px-4 text-muted">{{ $index + 1 }}</td>
                                            @if($currentDosenId !== $jadwal->dosen_id)
                                                <td class="fw-semibold text-primary">{{ $jadwal->dosen->name ?? 'N/A' }}</td>
                                                @php $currentDosenId = $jadwal->dosen_id; @endphp
                                            @else
                                                <td class="text-center text-muted border-0"><i class="bi bi-arrow-return-right"></i></td>
                                            @endif
                                            
                                            @php 
                                                $mkName = $jadwal->mataKuliah->name ?? 'N/A';
                                                if(!empty($jadwal->mataKuliah->code) && $jadwal->mataKuliah->code !== 'N/A') {
                                                    $mkName .= ' (' . $jadwal->mataKuliah->code . ')';
                                                }
                                            @endphp
                                            <td>{{ $mkName }}</td>
                                            
                                            <td><span class="badge bg-secondary bg-opacity-75 text-white fw-normal">{{ ucwords(strtolower($jadwal->prodi->name ?? 'N/A')) }}</span></td>
                                            <td class="text-center fw-bold">{{ $jadwal->evaluations->count() }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $jadwal->average_score >= 3.5 ? 'success' : ($jadwal->average_score >= 2.5 ? 'warning' : 'danger') }} fs-6">
                                                    {{ number_format($jadwal->average_score, 2) }}
                                                </span>
                                            </td>
                                            <td class="text-center fw-semibold text-{{ $jadwal->average_score >= 3.5 ? 'success' : ($jadwal->average_score >= 2.5 ? 'warning' : 'danger') }}">
                                                {{ $jadwal->predikat }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                                Belum ada data evaluasi dosen untuk filter yang dipilih.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Preview Hasil Evaluasi (Grafik) -->
            <div class="tab-pane fade {{ request('tab') === 'grafik' ? 'show active' : '' }}" id="grafik" role="tabpanel">
                @if($jadwals->isEmpty())
                    <div class="card shadow-sm border-0 rounded-3 p-5 text-center">
                        <i class="bi bi-pie-chart text-muted display-3 mb-3"></i>
                        <h4 class="fw-bold text-dark">Belum Ada Data Evaluasi</h4>
                        <p class="text-muted mb-3">Tidak ditemukan data evaluasi kuisioner untuk periode dan program studi yang dipilih.</p>
                        <a href="{{ route('admin.laporan') }}" class="btn btn-outline-primary mx-auto" style="width: fit-content;">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                        </a>
                    </div>
                @else
                    <!-- Action Bar -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm gap-2">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Visualisasi Grafik Hasil Evaluasi Dosen</h5>
                            <small class="text-muted">Periode: <strong>{{ $activePeriodeFormatted ?: 'Semua Periode' }}</strong> &bull; Tingkat: <strong>{{ $activeProdiName }}</strong></small>
                        </div>
                        <div class="d-flex gap-2">
                            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-printer me-1"></i> Cetak Tampilan
                            </button>
                            <a href="{{ route('admin.laporan.preview', ['periode' => request('periode'), 'prodi_id' => request('prodi_id')]) }}" target="_blank" class="btn btn-primary btn-sm">
                                <i class="bi bi-book-half me-1"></i> Buka Buku Cetak (PDF)
                            </a>
                        </div>
                    </div>

                    <!-- Summary Cards Row -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                                <div class="card-body d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 text-primary">
                                        <i class="bi bi-people-fill fs-3"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-semibold text-uppercase">Total Responden</div>
                                        <div class="fs-4 fw-bold text-dark">{{ number_format($totalResponden ?? 0) }}</div>
                                        <small class="text-muted">Respon Mahasiswa</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                                <div class="card-body d-flex align-items-center">
                                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3 text-success">
                                        <i class="bi bi-person-badge-fill fs-3"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-semibold text-uppercase">Dosen Dievaluasi</div>
                                        <div class="fs-4 fw-bold text-dark">{{ number_format($totalDosen ?? 0) }} Dosen</div>
                                        <small class="text-muted">Objek Evaluasi</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                                <div class="card-body d-flex align-items-center">
                                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3 text-warning">
                                        <i class="bi bi-journal-check fs-3"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-semibold text-uppercase">Kelas Perkuliahan</div>
                                        <div class="fs-4 fw-bold text-dark">{{ number_format($totalJadwal ?? ($jadwals ? $jadwals->count() : 0)) }} Kelas</div>
                                        <small class="text-muted">{{ number_format($totalMatkul ?? 0) }} Mata Kuliah</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                                <div class="card-body d-flex align-items-center">
                                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3 text-info">
                                        <i class="bi bi-award-fill fs-3"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-semibold text-uppercase">Rata-Rata Kinerja</div>
                                        <div class="fs-4 fw-bold text-dark">{{ number_format($avgScoreTotal ?? 0, 2) }}</div>
                                        <span class="badge bg-{{ ($overallPredikat ?? '') === 'Sangat Baik' ? 'success' : (($overallPredikat ?? '') === 'Baik' ? 'primary' : (($overallPredikat ?? '') === 'Sedang' ? 'warning text-dark' : 'danger')) }}">
                                            {{ $overallPredikat ?? 'Sangat Baik' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 1: Distribusi Predikat & Proporsi Responden -->
                    <div class="row g-4 mb-4">
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Distribusi Predikat Kinerja Dosen</h6>
                                        <small class="text-muted">Persentase capaian kategori predikat pengajaran</small>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div style="position: relative; height: 260px;">
                                        <canvas id="chartPredikatDosen"></canvas>
                                    </div>
                                    <div class="row text-center mt-3 g-2 pt-2 border-top">
                                        <div class="col">
                                            <small class="text-muted d-block">Sangat Baik</small>
                                            <span class="badge bg-success fs-6">{{ $chartPredikat['Sangat Baik'] ?? 0 }}</span>
                                        </div>
                                        <div class="col">
                                            <small class="text-muted d-block">Baik</small>
                                            <span class="badge bg-primary fs-6">{{ $chartPredikat['Baik'] ?? 0 }}</span>
                                        </div>
                                        <div class="col">
                                            <small class="text-muted d-block">Sedang</small>
                                            <span class="badge bg-warning text-dark fs-6">{{ $chartPredikat['Sedang'] ?? 0 }}</span>
                                        </div>
                                        <div class="col">
                                            <small class="text-muted d-block">Buruk</small>
                                            <span class="badge bg-danger fs-6">{{ $chartPredikat['Buruk'] ?? 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-diagram-3-fill text-success me-2"></i>{{ $pieTitle }}</h6>
                                        <small class="text-muted">Distribusi jumlah responden mahasiswa</small>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div style="position: relative; height: 290px;">
                                        <canvas id="chartProporsiResponden"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Capaian per Aspek & Top 10 Dosen -->
                    <div class="row g-4 mb-4">
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-header bg-white py-3 border-0">
                                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-bar-chart-steps text-info me-2"></i>Capaian Skor Rata-rata per Aspek Penilaian</h6>
                                    <small class="text-muted">Perbandingan skor pada 3 dimensi instrumen evaluasi</small>
                                </div>
                                <div class="card-body pt-0">
                                    <div style="position: relative; height: 260px;">
                                        <canvas id="chartAspekPenilaian"></canvas>
                                    </div>
                                    <div class="mt-3 p-3 bg-light rounded-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                            <span class="fw-semibold text-dark"><i class="bi bi-check2-circle text-primary me-2"></i>Proses Belajar Mengajar (PBM)</span>
                                            <span class="badge bg-primary fs-6">{{ number_format($avgScoreA ?? 0, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                            <span class="fw-semibold text-dark"><i class="bi bi-check2-circle text-success me-2"></i>Kapabilitas & Kompetensi Dosen (KKD)</span>
                                            <span class="badge bg-success fs-6">{{ number_format($avgScoreB ?? 0, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-semibold text-dark"><i class="bi bi-check2-circle text-purple me-2" style="color: #6f42c1;"></i>Ketersediaan Sarana Prasarana (KSP)</span>
                                            <span class="badge bg-secondary fs-6" style="background-color: #6f42c1 !important;">{{ number_format($avgScoreC ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-header bg-white py-3 border-0">
                                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-trophy-fill text-warning me-2"></i>Top 10 Dosen dengan Skor Evaluasi Tertinggi</h6>
                                    <small class="text-muted">Peringkat penilaian kinerja pengajaran dosen</small>
                                </div>
                                <div class="card-body pt-0">
                                    <div style="position: relative; height: 360px;">
                                        <canvas id="chartTopDosen"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Distribusi Frekuensi Jawaban per Butir Instrumen -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-white py-3 border-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-ui-checks-grid text-primary me-2"></i>Distribusi Frekuensi Jawaban Responden per Instrumen Kuisioner</h6>
                                <small class="text-muted">Frekuensi pemilihan opsi skala 1 (Sangat Kurang) hingga 5 (Sangat Baik) pada setiap butir pertanyaan</small>
                            </div>
                            <div>
                                <span class="badge bg-light text-dark border px-3 py-2"><i class="bi bi-info-circle me-1 text-primary"></i>Total {{ $questions->count() }} Butir Instrumen</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @foreach($groupedQuestions as $sectionName => $qs)
                                <div class="mb-4">
                                    <div class="p-2 px-3 mb-3 rounded-2 text-white fw-bold d-flex align-items-center justify-content-between" style="background: linear-gradient(90deg, #1e3c72 0%, #2a5298 100%);">
                                        <span><i class="bi bi-card-checklist me-2"></i>{{ $sectionName }}</span>
                                        <span class="badge bg-white text-dark">{{ $qs->count() }} Pertanyaan</span>
                                    </div>
                                    <div class="row g-3">
                                        @foreach($qs as $q)
                                            @php
                                                $t = $questionTallies[$q->id] ?? [1=>0,2=>0,3=>0,4=>0,5=>0];
                                                $tTotal = array_sum($t);
                                                $avgQ = $questionAvgScores[$q->id] ?? 0;
                                            @endphp
                                            <div class="col-lg-6 col-12">
                                                <div class="card border h-100 shadow-none rounded-3">
                                                    <div class="card-header bg-light py-2 border-bottom d-flex justify-content-between align-items-start gap-2">
                                                        <div class="fw-semibold small text-dark" style="line-height: 1.4;">
                                                            <span class="badge bg-primary me-1">Q{{ $q->order_num ?? $loop->iteration }}</span>
                                                            {{ $q->question_text }}
                                                        </div>
                                                        <span class="badge bg-{{ $avgQ >= 4.5 ? 'success' : ($avgQ >= 3.5 ? 'primary' : ($avgQ >= 2.5 ? 'warning text-dark' : 'danger')) }} text-nowrap">
                                                            Rata-rata: {{ number_format($avgQ, 2) }}
                                                        </span>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <div style="position: relative; height: 160px;">
                                                            <canvas id="instrumenChart_{{ $q->id }}"></canvas>
                                                        </div>
                                                        <div class="d-flex justify-content-between text-center small text-muted mt-2 pt-2 border-top" style="font-size: 0.78rem;">
                                                            <div><span class="text-danger fw-bold">1:</span> {{ $t[1] }}</div>
                                                            <div><span class="text-warning fw-bold">2:</span> {{ $t[2] }}</div>
                                                            <div><span class="text-secondary fw-bold">3:</span> {{ $t[3] }}</div>
                                                            <div><span class="text-primary fw-bold">4:</span> {{ $t[4] }}</div>
                                                            <div><span class="text-success fw-bold">5:</span> {{ $t[5] }}</div>
                                                            <div class="fw-bold text-dark">Total: {{ $tTotal }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- TAB 3: Konfigurasi Buku -->
            <div class="tab-pane fade {{ request('tab') === 'book' ? 'show active' : '' }}" id="book" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4 rounded-3">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-book me-2"></i> Konfigurasi Format Buku Cetak</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info border-0 shadow-sm mb-4">
                            <i class="bi bi-info-circle-fill me-2"></i> Anda sedang mengedit konfigurasi untuk <strong>{{ request('prodi_id') ? 'Tingkat Program Studi: ' . ucwords(strtolower($prodis->firstWhere('id', request('prodi_id'))->name ?? '')) : 'Tingkat Universitas' }}</strong>. Konfigurasi setiap tingkat disimpan secara terpisah.
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('admin.laporan.config') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="periode" value="{{ request('periode') }}">
                            <input type="hidden" name="prodi_id" value="{{ request('prodi_id') }}">

                            <div class="card mb-4 bg-light border rounded-3">
                                <div class="card-body p-3">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark"><i class="bi bi-file-earmark-richtext me-2 text-primary"></i>Pengaturan Cover Laporan</h6>
                                            <span class="badge bg-primary me-2">Periode Aktif: {{ $activePeriodeFormatted ?? request('periode') }}</span>
                                            <span class="badge bg-secondary">Tahun Laporan: {{ $coverYear ?? date('Y') }}</span>
                                        </div>
                                        @if(isset($config['cover']) && $config['cover'])
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Cover / Logo Terpasang</span>
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Tipe Tampilan Cover Buku</label>
                                        <div class="d-flex flex-wrap gap-4 p-3 bg-white rounded border">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipe_cover" id="tipeCoverStandard" value="standard" {{ ($config['tipe_cover'] ?? 'standard') === 'standard' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="tipeCoverStandard">
                                                    <strong class="text-dark">Cover Resmi Standar (Dinamis Otomatis)</strong>
                                                    <div class="text-muted small">Judul, Periode, Prodi, dan Tahun digenerate otomatis sesuai periode. Jika ada gambar, akan ditampilkan sebagai logo kampus di tengah.</div>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="tipe_cover" id="tipeCoverFull" value="full" {{ ($config['tipe_cover'] ?? '') === 'full' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="tipeCoverFull">
                                                    <strong class="text-dark">Cover Gambar Penuh (Full Page A4)</strong>
                                                    <div class="text-muted small">Gambar yang diupload akan menutupi 1 halaman penuh A4 sebagai cover depan.</div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">File Gambar Cover / Logo (Opsional, JPG/PNG)</label>
                                        @if(isset($config['cover']) && $config['cover'])
                                            <div class="mb-2 d-flex align-items-center gap-3 bg-white p-2 border rounded">
                                                <img src="{{ asset($config['cover']) }}" alt="Cover" class="img-thumbnail" style="max-height: 80px;">
                                                <div>
                                                    <small class="text-muted d-block">File aktif untuk periode ini:</small>
                                                    <code class="small">{{ $config['cover'] }}</code>
                                                </div>
                                            </div>
                                        @endif
                                        <input class="form-control" type="file" name="cover" accept="image/*">
                                        <small class="text-muted mt-1 d-block"><i class="bi bi-info-circle me-1"></i>Cover disimpan khusus untuk periode ini. Mengganti cover tidak akan merusak cover periode lainnya.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion mb-4" id="editorAccordion">
                                
                                <!-- Kata Pengantar -->
                                <div class="accordion-item mb-2 border rounded">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#colKataPengantar">
                                            KATA PENGANTAR
                                        </button>
                                    </h2>
                                    <div id="colKataPengantar" class="accordion-collapse collapse">
                                        <div class="accordion-body bg-light">
                                            <div class="alert alert-info py-2 px-3 small mb-2 border-0">
                                                <i class="bi bi-info-circle me-1"></i> Teks di bawah telah <strong>disinkronkan dinamis</strong> dengan periode aktif (<strong>{{ $activePeriodeFormatted ?? request('periode') }}</strong>). Anda juga bisa menggunakan kode <code>[PERIODE]</code>, <code>[TAHUN_AKADEMIK]</code>, <code>[SEMESTER]</code>, <code>[NAMA_PRODI]</code>, <code>[TAHUN]</code>.
                                            </div>
                                            <textarea name="kata_pengantar" class="form-control" rows="6" placeholder="Tuliskan kata pengantar di sini...">{{ $config['kata_pengantar'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lembar Pengesahan -->
                                <div class="accordion-item mb-2 border rounded">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#colLembarPengesahan">
                                            LEMBAR PENGESAHAN
                                        </button>
                                    </h2>
                                    <div id="colLembarPengesahan" class="accordion-collapse collapse">
                                        <div class="accordion-body bg-light">
                                            <textarea name="lembar_pengesahan" class="form-control" rows="4" placeholder="Tuliskan isi lembar pengesahan di sini (bisa gunakan editor TinyMCE)...">{{ $config['lembar_pengesahan'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- BAB I -->
                                <div class="accordion-item mb-2 border rounded">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#colBab1">
                                            BAB I: PENDAHULUAN
                                        </button>
                                    </h2>
                                    <div id="colBab1" class="accordion-collapse collapse">
                                        <div class="accordion-body bg-light">
                                            <textarea name="bab1" class="form-control" rows="5" placeholder="Latar belakang, tujuan, dll...">{{ $config['bab1'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- BAB II -->
                                <div class="accordion-item mb-2 border rounded">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#colBab2">
                                            BAB II: METODE EVALUASI
                                        </button>
                                    </h2>
                                    <div id="colBab2" class="accordion-collapse collapse">
                                        <div class="accordion-body bg-light">
                                            <textarea name="bab2" class="form-control" rows="5" placeholder="Metodologi pengumpulan data, responden, dll...">{{ $config['bab2'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- BAB III -->
                                <div class="accordion-item mb-2 border rounded">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#colBab3">
                                            BAB III: HASIL EVALUASI
                                        </button>
                                    </h2>
                                    <div id="colBab3" class="accordion-collapse collapse">
                                        <div class="accordion-body bg-light">
                                            <p class="text-muted small mb-2"><i class="bi bi-info-circle me-1"></i> Pengantar sebelum tabel data ditampilkan. Tabel Data Jadwal akan otomatis disisipkan di bawah teks ini.</p>
                                            <textarea name="bab3" class="form-control" rows="2" placeholder="Pengantar sebelum tabel data ditampilkan...">{{ $config['bab3'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- BAB IV -->
                                <div class="accordion-item mb-2 border rounded">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#colBab4">
                                            BAB IV: ANALISIS DAN PEMBAHASAN
                                        </button>
                                    </h2>
                                    <div id="colBab4" class="accordion-collapse collapse">
                                        <div class="accordion-body bg-light">
                                            <p class="text-muted small mb-2"><i class="bi bi-info-circle me-1"></i> Anda dapat menggunakan kode <strong>[ANALISIS_PER_INSTRUMEN]</strong> untuk otomatis menyisipkan narasi pembahasan dari setiap grafik evaluasi yang ada.</p>
                                            <textarea name="bab4" class="form-control" rows="4" placeholder="Analisis hasil, kekuatan, kelemahan... (Gunakan [ANALISIS_PER_INSTRUMEN] untuk auto-generate teks grafik)">{{ $config['bab4'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- BAB V -->
                                <div class="accordion-item mb-2 border rounded">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#colBab5">
                                            BAB V: KESIMPULAN DAN REKOMENDASI
                                        </button>
                                    </h2>
                                    <div id="colBab5" class="accordion-collapse collapse">
                                        <div class="accordion-body bg-light">
                                            <textarea name="bab5" class="form-control" rows="4" placeholder="Kesimpulan dan rekomendasi...">{{ $config['bab5'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- LAMPIRAN -->
                                <div class="accordion-item mb-2 border rounded">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#colLampiran">
                                            LAMPIRAN TAMBAHAN
                                        </button>
                                    </h2>
                                    <div id="colLampiran" class="accordion-collapse collapse">
                                        <div class="accordion-body bg-light">
                                            <p class="text-muted small mb-2"><i class="bi bi-info-circle me-1"></i> Instrumen Kuesioner, Grafik Hasil, dan Tabel Rekapitulasi akan dibuat <strong>secara otomatis</strong> oleh sistem dan disisipkan sebelum bagian lampiran ini.</p>
                                            <textarea name="lampiran" class="form-control" rows="4" placeholder="Catatan atau teks tambahan untuk lampiran jika diperlukan...">{{ $config['lampiran'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                                <i class="bi bi-eye-fill me-2"></i> Simpan & Lihat Preview Buku
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
    let chartsInitialized = false;

    function initEvaluationCharts() {
        if (chartsInitialized) return;
        chartsInitialized = true;

        // Chart 1: Predikat Dosen
        const ctxPred = document.getElementById('chartPredikatDosen');
        if (ctxPred) {
            new Chart(ctxPred, {
                type: 'doughnut',
                plugins: [ChartDataLabels],
                data: {
                    labels: ['Sangat Baik', 'Baik', 'Sedang', 'Buruk', 'Sangat Buruk'],
                    datasets: [{
                        data: [
                            {{ $chartPredikat['Sangat Baik'] ?? 0 }},
                            {{ $chartPredikat['Baik'] ?? 0 }},
                            {{ $chartPredikat['Sedang'] ?? 0 }},
                            {{ $chartPredikat['Buruk'] ?? 0 }},
                            {{ $chartPredikat['Sangat Buruk'] ?? 0 }}
                        ],
                        backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#fd7e14', '#dc3545'],
                        borderWidth: 2,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } },
                        datalabels: {
                            color: '#fff',
                            font: { weight: 'bold', size: 12 },
                            formatter: (value, ctx) => {
                                if (!value || value === 0) return '';
                                let sum = 0;
                                let dataArr = ctx.chart.data.datasets[0].data;
                                dataArr.forEach(d => { sum += d; });
                                return sum > 0 ? (value * 100 / sum).toFixed(1) + '%' : '';
                            }
                        }
                    }
                }
            });
        }

        // Chart 2: Proporsi Responden
        const ctxProp = document.getElementById('chartProporsiResponden');
        if (ctxProp) {
            new Chart(ctxProp, {
                type: 'pie',
                plugins: [ChartDataLabels],
                data: {
                    labels: {!! json_encode($pieLabels ?? []) !!},
                    datasets: [{
                        data: {!! json_encode($pieData ?? []) !!},
                        backgroundColor: [
                            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                            '#6f42c1', '#20c997', '#fd7e14', '#0dcaf0', '#6c757d', '#adb5bd'
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10, font: { size: 11 } } },
                        datalabels: {
                            color: '#fff',
                            font: { weight: 'bold', size: 11 },
                            formatter: (value, ctx) => {
                                let sum = 0;
                                let dataArr = ctx.chart.data.datasets[0].data;
                                dataArr.forEach(d => { sum += d; });
                                let pct = sum > 0 ? (value * 100 / sum) : 0;
                                return pct >= 4 ? pct.toFixed(1) + '%' : '';
                            }
                        }
                    }
                }
            });
        }

        // Chart 3: Aspek Penilaian
        const ctxAspek = document.getElementById('chartAspekPenilaian');
        if (ctxAspek) {
            new Chart(ctxAspek, {
                type: 'bar',
                plugins: [ChartDataLabels],
                data: {
                    labels: [
                        ['Proses Belajar', 'Mengajar (PBM)'],
                        ['Kapabilitas &', 'Kompetensi (KKD)'],
                        ['Sarana &', 'Prasarana (KSP)']
                    ],
                    datasets: [{
                        label: 'Skor Rata-Rata',
                        data: [{{ $avgScoreA ?? 0 }}, {{ $avgScoreB ?? 0 }}, {{ $avgScoreC ?? 0 }}],
                        backgroundColor: ['#0d6efd', '#198754', '#6f42c1'],
                        borderRadius: 6,
                        barPercentage: 0.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    },
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            offset: 4,
                            font: { weight: 'bold', size: 12 },
                            formatter: (val) => val
                        }
                    }
                }
            });
        }

        // Chart 4: Top Dosen
        const ctxTop = document.getElementById('chartTopDosen');
        if (ctxTop) {
            const topNames = {!! json_encode(array_column($topDosen ?? [], 'name')) !!};
            const topScores = {!! json_encode(array_column($topDosen ?? [], 'avg')) !!};
            new Chart(ctxTop, {
                type: 'bar',
                plugins: [ChartDataLabels],
                data: {
                    labels: topNames,
                    datasets: [{
                        label: 'Skor Kinerja',
                        data: topScores,
                        backgroundColor: '#198754',
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { beginAtZero: true }
                    },
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            anchor: 'end',
                            align: 'right',
                            offset: 4,
                            font: { weight: 'bold', size: 11 },
                            formatter: (val) => val
                        }
                    }
                }
            });
        }

        // Chart 5: Instrumen Kuisioner per Pertanyaan
        const qTallies = {!! json_encode($questionTallies ?? []) !!};
        Object.keys(qTallies).forEach(function(qId) {
            const ctxQ = document.getElementById('instrumenChart_' + qId);
            if (ctxQ) {
                const t = qTallies[qId];
                new Chart(ctxQ, {
                    type: 'bar',
                    plugins: [ChartDataLabels],
                    data: {
                        labels: ['1', '2', '3', '4', '5'],
                        datasets: [{
                            data: [t[1]||0, t[2]||0, t[3]||0, t[4]||0, t[5]||0],
                            backgroundColor: ['#dc3545', '#fd7e14', '#ffc107', '#0d6efd', '#198754'],
                            borderRadius: 4,
                            barPercentage: 0.65
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 } },
                            x: { ticks: { font: { size: 11, weight: 'bold' } } }
                        },
                        plugins: {
                            legend: { display: false },
                            datalabels: {
                                anchor: 'end',
                                align: 'top',
                                offset: 2,
                                font: { weight: 'bold', size: 10 },
                                formatter: (val) => val > 0 ? val : ''
                            }
                        }
                    }
                });
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Tab Event Listener
        const myTabEl = document.getElementById('myTab');
        if (myTabEl) {
            myTabEl.addEventListener('shown.bs.tab', function (event) {
                const target = event.target.getAttribute('data-bs-target');
                const tabName = target ? target.replace('#', '') : 'preview';
                const tabParam = document.getElementById('activeTabParam');
                if (tabParam) tabParam.value = tabName;
                
                if (tabName === 'grafik') {
                    initEvaluationCharts();
                }
            });
        }

        // Auto initialize if grafik is active tab
        const activeBtn = document.querySelector('#myTab .nav-link.active');
        if (activeBtn && activeBtn.getAttribute('data-bs-target') === '#grafik') {
            initEvaluationCharts();
        }

        tinymce.init({
            selector: 'textarea',
            height: 600,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks fontfamily fontsize | ' +
                     'bold italic underline strikethrough superscript subscript | ' +
                     'alignleft aligncenter alignright alignjustify | ' +
                     'bullist numlist outdent indent | ' +
                     'forecolor backcolor removeformat | ' +
                     'table image media link hr | ' +
                     'fullscreen preview code help',
            branding: false,
            promotion: false,
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
            }
        });
    });
</script>
</body>
</html>
