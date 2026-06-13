<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kuisioner Dosen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar-link { transition: 0.2s; border-left: 4px solid transparent; border-radius: 0 8px 8px 0; margin-right: 1rem; }
        .sidebar-link:hover { background-color: rgba(255,255,255,0.1); border-left-color: rgba(255,255,255,0.5); }
        .active-menu { background-color: rgba(255,255,255,0.2) !important; font-weight: bold; border-left: 4px solid #fff !important; border-radius: 0 8px 8px 0; margin-right: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .sidebar-bg {
            background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
        }
    </style>
</head>
<body>

<div class="d-flex" style="min-height: 100vh;">
    <!-- Sidebar -->
    <div class="sidebar-bg text-white py-3 d-flex flex-column" style="width: 280px; position: fixed; height: 100vh; overflow-y: auto; box-shadow: 4px 0 10px rgba(0,0,0,0.1); z-index: 1000;">
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
        </ul>
        <div class="mt-auto border-top border-secondary pt-3 text-center text-secondary small">
            &copy; 2026 Univ Sumatera Selatan
        </div>
    </div>

    <!-- Main Content -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <div class="flex-grow-1 bg-light p-4" style="margin-left: 280px; min-height: 100vh;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark mb-0">Dashboard & Statistik</h2>
            
            <!-- Filter -->
            <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex align-items-center bg-white p-2 rounded shadow-sm border">
                <label class="form-label fw-bold mb-0 me-3 text-nowrap">Periode:</label>
                <select name="periode" class="form-select border-0 bg-light me-2" onchange="this.form.submit()">
                    <option value="">Semua Periode</option>
                    @foreach($periodes as $p)
                        <option value="{{ $p }}" {{ request('periode') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
                @if(request('periode'))
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-danger" title="Reset Filter"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
        @endif

        <!-- Stats Row -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-primary text-white text-center py-4 rounded-3">
                    <h1 class="display-4 fw-bold">{{ $stats['total_responden'] }}</h1>
                    <span class="fs-5">Total Pengisian Kuisioner</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-success text-white text-center py-4 rounded-3">
                    <h1 class="display-4 fw-bold">{{ $stats['total_jadwal'] }}</h1>
                    <span class="fs-5">Total Jadwal Kelas</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-info text-white text-center py-4 rounded-3">
                    <h1 class="display-4 fw-bold">{{ $stats['total_dosen'] }}</h1>
                    <span class="fs-5">Total Dosen Aktif</span>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="card shadow-sm border-0 h-100 rounded-3">
                    <div class="card-header bg-white fw-bold py-3 border-bottom-0">Sebaran Jadwal Berdasarkan Prodi</div>
                    <div class="card-body" style="position: relative; height: 350px;">
                        <canvas id="prodiChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100 rounded-3">
                    <div class="card-header bg-white fw-bold py-3 border-bottom-0">Top 5 Dosen (Responden Terbanyak)</div>
                    <div class="card-body" style="position: relative; height: 350px;">
                        <canvas id="dosenChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Chart 1: Jadwal Per Prodi
                const prodiLabels = {!! json_encode($jadwalPerProdi->pluck('name')) !!};
                const prodiData = {!! json_encode($jadwalPerProdi->pluck('jadwals_count')) !!};
                
                new Chart(document.getElementById('prodiChart'), {
                    type: 'doughnut',
                    data: {
                        labels: prodiLabels,
                        datasets: [{
                            data: prodiData,
                            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#2e59d9'],
                            hoverOffset: 4
                        }]
                    },
                    options: { maintainAspectRatio: false }
                });

                // Chart 2: Top Dosen
                const dosenLabels = {!! json_encode($topDosen->map(function($j) { return $j->dosen->name ?? 'N/A'; })) !!};
                const dosenData = {!! json_encode($topDosen->pluck('evaluations_count')) !!};
                
                new Chart(document.getElementById('dosenChart'), {
                    type: 'bar',
                    data: {
                        labels: dosenLabels,
                        datasets: [{
                            label: 'Jumlah Responden',
                            data: dosenData,
                            backgroundColor: '#4e73df'
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true } }
                    }
                });
            });
        </script>

    </div>
</div>

</body>
</html>
