<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kuisioner Dosen</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f1f5f9; font-family: 'Inter', sans-serif; color: #334155; }
        
        /* Modern Sidebar */
        .sidebar-bg { background: linear-gradient(145deg, #0f172a 0%, #1e1b4b 100%); }
        .sidebar-link { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-left: 4px solid transparent; border-radius: 0 12px 12px 0; margin-right: 1rem; color: #94a3b8 !important; padding: 0.85rem 1.2rem; }
        .sidebar-link:hover { background-color: rgba(255,255,255,0.05); color: #f8fafc !important; }
        .active-menu { background-color: rgba(255,255,255,0.1) !important; font-weight: 600; border-left: 4px solid #38bdf8 !important; border-radius: 0 12px 12px 0; margin-right: 1rem; color: #ffffff !important; box-shadow: 0 4px 15px rgba(0,0,0,0.1); backdrop-filter: blur(10px); }
        
        /* Premium Cards */
        .premium-card { background: white; border-radius: 20px; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05); border: 1px solid rgba(255,255,255,0.8); transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .premium-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px -10px rgba(0,0,0,0.08); }
        
        /* Stat Cards */
        .stat-card-1 { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; }
        .stat-card-2 { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
        .stat-card-3 { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; }
        
        .stat-icon { background: rgba(255,255,255,0.2); width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; backdrop-filter: blur(5px); }
        .stat-bg-icon { position: absolute; right: -15%; top: -15%; font-size: 10rem; opacity: 0.1; transform: rotate(-15deg); }
        
        /* Clean Inputs */
        .premium-select { border-radius: 12px; border: 1px solid #e2e8f0; background-color: #f8fafc; padding: 0.6rem 1rem; font-weight: 500; color: #475569; transition: all 0.2s; }
        .premium-select:focus { border-color: #38bdf8; box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1); outline: none; }
    </style>
</head>
<body>

<div class="d-flex" style="min-height: 100vh;">
    <!-- Sidebar -->
    <div class="sidebar-bg text-white py-4 d-flex flex-column" style="width: 280px; position: fixed; height: 100vh; overflow-y: auto; box-shadow: 4px 0 20px rgba(0,0,0,0.15); z-index: 1000;">
        <div class="text-center mb-5 px-3">
            <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle mb-3 shadow" style="width: 60px; height: 60px;">
                <i class="bi bi-bar-chart-fill fs-2 text-primary"></i>
            </div>
            <h5 class="fw-bold mb-0 tracking-wider text-white">ADMIN PANEL</h5>
            <small class="text-info opacity-75 fw-medium">Sistem Kuisioner Dosen</small>
        </div>
        
        <div class="px-4 mb-2 text-secondary small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 1.5px;">Menu Utama</div>
        <ul class="nav flex-column gap-2 mb-auto w-100">
            <li class="nav-item w-100">
                <a class="nav-link text-white {{ Route::is('admin.dashboard') ? 'active-menu' : 'sidebar-link' }} d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-grid-1x2-fill me-3 fs-5"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link text-white {{ Route::is('admin.sinkron') ? 'active-menu' : 'sidebar-link' }} d-flex align-items-center" href="{{ route('admin.sinkron') }}">
                    <i class="bi bi-cloud-arrow-down-fill me-3 fs-5"></i> <span>Sinkron SIMAK</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link text-white {{ Route::is('admin.jadwal') ? 'active-menu' : 'sidebar-link' }} d-flex align-items-center" href="{{ route('admin.jadwal') }}">
                    <i class="bi bi-people-fill me-3 fs-5"></i> <span>Data Dosen & Matkul</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link text-white {{ Route::is('admin.laporan') ? 'active-menu' : 'sidebar-link' }} d-flex align-items-center" href="{{ route('admin.laporan') }}">
                    <i class="bi bi-journal-text me-3 fs-5"></i> <span>Laporan Hasil</span>
                </a>
            </li>
            <li class="nav-item w-100">
                <a class="nav-link text-white {{ Route::is('admin.questions.*') ? 'active-menu' : 'sidebar-link' }} d-flex align-items-center" href="{{ route('admin.questions.index') }}">
                    <i class="bi bi-ui-checks me-3 fs-5"></i> <span>Materi Kuisioner</span>
                </a>
            </li>
            
            <div class="px-4 mt-5 mb-2 text-secondary small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 1.5px;">Akses Eksternal</div>
            <li class="nav-item w-100">
                <a class="nav-link sidebar-link d-flex align-items-center text-info" href="{{ route('form.index') }}" target="_blank">
                    <i class="bi bi-box-arrow-up-right me-3 fs-5"></i> <span>Lihat Form Mahasiswa</span>
                </a>
            </li>
        </ul>
        <div class="mt-auto pt-4 pb-2 text-center text-secondary small fw-medium">
            &copy; {{ date('Y') }} Univ Sumatera Selatan
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4 p-md-5" style="margin-left: 280px; min-height: 100vh;">
        
        <!-- Header Section -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5">
            <div>
                <h2 class="fw-extrabold text-dark mb-1" style="font-weight: 800;">Dashboard & Statistik</h2>
                <p class="text-muted fw-medium mb-0">Ringkasan performa dan data evaluasi dosen.</p>
            </div>
            
            <!-- Filter -->
            <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex align-items-center bg-white p-2 rounded-pill shadow-sm border mt-3 mt-md-0" style="padding-left: 1.2rem !important; padding-right: 0.5rem !important;">
                <label class="form-label fw-bold mb-0 me-2 text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Periode</label>
                <select name="periode" class="premium-select border-0 bg-transparent me-1 fw-bold text-dark" onchange="this.form.submit()" style="min-width: 160px; box-shadow: none;">
                    <option value="">Semua Periode</option>
                    @foreach($periodes as $p)
                        <option value="{{ $p }}" {{ request('periode') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
                @if(request('periode'))
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-light rounded-circle d-flex align-items-center justify-content-center text-danger" style="width: 32px; height: 32px;" title="Reset Filter"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success premium-card border-0 text-success fw-medium mb-4"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger premium-card border-0 text-danger fw-medium mb-4"><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}</div>
        @endif

        <!-- Stats Row -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="premium-card stat-card-1 h-100 p-4 border-0 position-relative overflow-hidden">
                    <i class="bi bi-file-earmark-text-fill stat-bg-icon"></i>
                    <div class="d-flex justify-content-between align-items-center position-relative z-1 mb-4">
                        <div class="stat-icon text-white"><i class="bi bi-check2-all"></i></div>
                        <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold shadow-sm">Live Data</span>
                    </div>
                    <div class="position-relative z-1">
                        <h2 class="display-4 fw-bolder mb-0 text-white">{{ $stats['total_responden'] }}</h2>
                        <p class="mb-0 text-white-50 fw-semibold text-uppercase tracking-wider mt-1" style="font-size: 0.85rem; letter-spacing: 1px;">Total Pengisian Kuisioner</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="premium-card stat-card-2 h-100 p-4 border-0 position-relative overflow-hidden">
                    <i class="bi bi-calendar-event-fill stat-bg-icon"></i>
                    <div class="d-flex justify-content-between align-items-center position-relative z-1 mb-4">
                        <div class="stat-icon text-white"><i class="bi bi-journal-bookmark-fill"></i></div>
                    </div>
                    <div class="position-relative z-1">
                        <h2 class="display-4 fw-bolder mb-0 text-white">{{ $stats['total_jadwal'] }}</h2>
                        <p class="mb-0 text-white-50 fw-semibold text-uppercase tracking-wider mt-1" style="font-size: 0.85rem; letter-spacing: 1px;">Total Jadwal Kelas</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="premium-card stat-card-3 h-100 p-4 border-0 position-relative overflow-hidden">
                    <i class="bi bi-person-video3 stat-bg-icon"></i>
                    <div class="d-flex justify-content-between align-items-center position-relative z-1 mb-4">
                        <div class="stat-icon text-white"><i class="bi bi-people-fill"></i></div>
                    </div>
                    <div class="position-relative z-1">
                        <h2 class="display-4 fw-bolder mb-0 text-white">{{ $stats['total_dosen'] }}</h2>
                        <p class="mb-0 text-white-50 fw-semibold text-uppercase tracking-wider mt-1" style="font-size: 0.85rem; letter-spacing: 1px;">Total Dosen Aktif</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <div class="col-lg-5">
                <div class="premium-card h-100 p-4">
                    <h5 class="fw-bold text-dark mb-4">Sebaran Jadwal per Prodi</h5>
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="prodiChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="premium-card h-100 p-4">
                    <h5 class="fw-bold text-dark mb-4">Top 5 Dosen <span class="text-muted fw-normal fs-6">(Responden Terbanyak)</span></h5>
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="dosenChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Konfigurasi Font Default ChartJS
                Chart.defaults.font.family = "'Inter', sans-serif";
                Chart.defaults.color = '#64748b';
                
                // Chart 1: Jadwal Per Prodi
                const prodiLabels = {!! json_encode($jadwalPerProdi->pluck('name')) !!};
                const prodiData = {!! json_encode($jadwalPerProdi->pluck('jadwals_count')) !!};
                
                new Chart(document.getElementById('prodiChart'), {
                    type: 'doughnut',
                    data: {
                        labels: prodiLabels,
                        datasets: [{
                            data: prodiData,
                            backgroundColor: ['#6366f1', '#10b981', '#0ea5e9', '#f59e0b', '#ec4899', '#8b5cf6', '#14b8a6', '#f43f5e'],
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: { 
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true, boxWidth: 8 } }
                        }
                    }
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
                            backgroundColor: '#38bdf8',
                            borderRadius: 6,
                            barThickness: 24,
                            hoverBackgroundColor: '#0284c7'
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        scales: { 
                            y: { beginAtZero: true, grid: { borderDash: [4, 4], color: '#e2e8f0', drawBorder: false } },
                            x: { grid: { display: false } }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            });
        </script>

    </div>
</div>

</body>
</html>
