<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sinkronisasi SIMAK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script>
        (function() {
            try {
                if (localStorage.getItem('sidebar_collapsed') === 'true' && window.innerWidth >= 992) {
                    document.documentElement.classList.add('sidebar-is-collapsed');
                }
            } catch(e) {}
        })();
    </script>
    <style>
        body { background-color: #f8f9fa; }
        .sidebar-link { transition: 0.2s; border-left: 4px solid transparent; border-radius: 0 8px 8px 0; margin-right: 1rem; }
        .sidebar-link:hover { background-color: rgba(255,255,255,0.1); border-left-color: rgba(255,255,255,0.5); }
        .active-menu { background-color: rgba(255,255,255,0.2) !important; font-weight: bold; border-left: 4px solid #fff !important; border-radius: 0 8px 8px 0; margin-right: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .sidebar-bg {
            background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
        }
        .card-header-custom {
            background: linear-gradient(90deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
        }

        /* Sidebar show/hide & transitions */
        #adminSidebar {
            width: 280px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            z-index: 1050;
            left: 0;
            top: 0;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #mainContent {
            margin-left: 280px;
            min-height: 100vh;
            width: calc(100% - 280px);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Collapsed State */
        body.sidebar-collapsed #adminSidebar {
            transform: translateX(-280px) !important;
        }
        body.sidebar-collapsed #mainContent {
            margin-left: 0 !important;
            width: 100% !important;
        }
        
        /* Pre-render anti-flicker */
        html.sidebar-is-collapsed #adminSidebar {
            transform: translateX(-280px) !important;
            transition: none !important;
        }
        html.sidebar-is-collapsed #mainContent {
            margin-left: 0 !important;
            width: 100% !important;
            transition: none !important;
        }

        /* Mobile overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(2px);
            z-index: 1040;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        @media (max-width: 991.98px) {
            #adminSidebar {
                transform: translateX(-280px);
            }
            #adminSidebar.mobile-show {
                transform: translateX(0) !important;
            }
            #mainContent {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        @media print {
            .no-print { display: none !important; }
            #adminSidebar, .sidebar-overlay { display: none !important; }
            #mainContent { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
        }
    </style>
</head>
<body>

<!-- Mobile Sidebar Backdrop Overlay -->
<div id="sidebarOverlay" class="sidebar-overlay no-print"></div>

<div class="d-flex" style="min-height: 100vh;">
    <!-- Sidebar -->
    <div id="adminSidebar" class="sidebar-bg text-white py-3 d-flex flex-column no-print">
        <div class="d-flex align-items-center justify-content-between px-3 mb-4 mt-2 pb-2 border-bottom border-white-50 border-opacity-10">
            <div>
                <h4 class="fw-bold mb-0 tracking-wider">ADMIN PANEL</h4>
                <small class="text-white-50">Sistem Kuisioner Dosen</small>
            </div>
            <button id="sidebarCollapseBtn" class="btn btn-sm text-white-50 p-1 border-0" type="button" title="Sembunyikan Menu">
                <i class="bi bi-chevron-left fs-5"></i>
            </button>
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
    <div id="mainContent" class="flex-grow-1 bg-light p-4 content-area">
        <div class="d-flex align-items-center gap-3 mb-4">
            <button id="sidebarToggle" class="btn btn-white bg-white border shadow-sm text-secondary d-inline-flex align-items-center justify-content-center" style="width: 42px; height: 42px; border-radius: 10px;" type="button" title="Sembunyikan/Tampilkan Menu">
                <i class="bi bi-list fs-4"></i>
            </button>
            <div>
                <h2 class="fw-bold text-dark mb-0">Sinkronisasi Data Dosen dan Mata Kuliah</h2>
                <small class="text-muted">Tarik data jadwal dan kelas dari SIMAK</small>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning shadow-sm">{{ session('warning') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
        @endif

        <div class="row">
            <div class="col-md-10 mb-4 mx-auto">
                <div class="card shadow border-0" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header card-header-custom font-weight-bold py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-cloud-arrow-down me-2"></i> Tarik Data dari SIMAK</h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted mb-4 text-center">
                            Sistem akan mengambil data dosen mata kuliah dari halaman <strong>JADWAL RUANGAN</strong> SIMAK (Seluruh Dosen & Program Studi). 
                            Silakan masukkan kredensial SIMAK Anda di bawah ini.
                        </p>
                        
                        <div class="alert alert-warning border-0 shadow-sm" role="alert">
                            <strong>⚠️ Perhatian:</strong><br>
                            Data kredensial Anda hanya digunakan sesaat untuk login oleh server dan tidak akan disimpan ke database.
                        </div>

                        <form action="{{ route('admin.scrape') }}" method="POST" class="mt-4">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Username SIMAK</label>
                                    <input type="text" id="simak_username" name="simak_username" class="form-control form-control-lg" required placeholder="Masukkan username">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Password SIMAK</label>
                                    <input type="password" id="simak_password" name="simak_password" class="form-control form-control-lg" required placeholder="Masukkan password">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">URL Target SIMAK (Advanced)</label>
                                <input type="text" id="simak_url" name="simak_url" class="form-control" value="{{ env('SIMAK_BASE_URL', 'https://simak.uss.ac.id') }}" placeholder="https://simak.uss.ac.id">
                                <small class="text-muted">Biarkan default kecuali Anda perlu *bypass* IP publik dari dalam server (misal: isi dengan `http://192.168.x.x` atau IP LAN server SIMAK).</small>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Pilih Periode Sinkronisasi</label>
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <input type="hidden" name="periode_label" id="periode_label" value="">
                                    <select name="simak_ta" id="simak_ta" class="form-select form-select-lg flex-grow-1" onchange="document.getElementById('periode_label').value = this.options[this.selectedIndex].text + ' ' + document.getElementById('simak_tipe').value">
                                        <option value="">-- Ambil daftar Periode dari SIMAK --</option>
                                    </select>
                                    <select name="simak_tipe" id="simak_tipe" class="form-select form-select-lg" style="width: auto; min-width: 150px;" onchange="document.getElementById('periode_label').value = document.getElementById('simak_ta').options[document.getElementById('simak_ta').selectedIndex].text + ' ' + this.value">
                                        <option value="REGULER">REGULER</option>
                                        <option value="PENDEK">PENDEK</option>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary btn-lg" id="btn-fetch-periods">
                                        <i class="bi bi-search"></i> Ambil Pilihan
                                    </button>
                                </div>
                                <small class="text-muted" id="period-status">Silakan ketik username & password lalu klik "Ambil Pilihan".</small>
                            </div>
                            
                            <hr class="my-4">
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm fw-bold" id="btn-submit-scrape" onclick="return confirm('Proses sinkronisasi akan memakan waktu. Lanjutkan?')" disabled>
                                Tarik Seluruh Data Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.getElementById('btn-fetch-periods').addEventListener('click', function() {
    let username = document.getElementById('simak_username').value;
    let password = document.getElementById('simak_password').value;
    
    if(!username || !password) {
        alert("Silakan masukkan Username dan Password terlebih dahulu!");
        return;
    }
    
    let btn = this;
    let statusText = document.getElementById('period-status');
    let select = document.getElementById('simak_ta');
    let submitBtn = document.getElementById('btn-submit-scrape');
    
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Mencari...';
    statusText.innerHTML = 'Sedang masuk ke SIMAK dan mencari daftar semester...';
    
    let formData = new FormData();
    formData.append('simak_username', username);
    formData.append('simak_password', password);
    formData.append('simak_url', document.getElementById('simak_url').value);
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch('{{ route("admin.scrape.periods") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-search"></i> Ambil Pilihan';
        
        if(data.success) {
            select.innerHTML = '';
            
            data.periods.forEach((p, index) => {
                let opt = document.createElement('option');
                opt.value = p.value;
                opt.text = p.label;
                if(index === 0) opt.selected = true;
                select.appendChild(opt);
            });
            // Update hidden label
            document.getElementById('periode_label').value = select.options[0].text + ' ' + document.getElementById('simak_tipe').value;
            
            statusText.innerHTML = '<span class="text-success fw-bold">Berhasil mengambil daftar Periode! Silakan pilih lalu Tarik Data.</span>';
            submitBtn.disabled = false;
        } else {
            statusText.innerHTML = '<span class="text-danger">' + data.message + '</span>';
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-search"></i> Ambil Pilihan';
        statusText.innerHTML = '<span class="text-danger">Terjadi kesalahan jaringan. Coba lagi.</span>';
    });
});

// Sidebar Toggle & Collapse Logic
document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const toggleBtn = document.getElementById('sidebarToggle');
    const collapseBtn = document.getElementById('sidebarCollapseBtn');
    const overlay = document.getElementById('sidebarOverlay');
    const sidebar = document.getElementById('adminSidebar');

    if (window.innerWidth >= 992) {
        if (localStorage.getItem('sidebar_collapsed') === 'true') {
            body.classList.add('sidebar-collapsed');
        } else {
            body.classList.remove('sidebar-collapsed');
        }
    }
    document.documentElement.classList.remove('sidebar-is-collapsed');

    function toggleSidebar() {
        if (window.innerWidth >= 992) {
            body.classList.toggle('sidebar-collapsed');
            const isCollapsed = body.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebar_collapsed', isCollapsed ? 'true' : 'false');
        } else {
            if (sidebar) {
                const isShown = sidebar.classList.toggle('mobile-show');
                if (overlay) {
                    overlay.classList.toggle('show', isShown);
                }
            }
        }
    }

    function closeSidebar() {
        if (window.innerWidth >= 992) {
            body.classList.add('sidebar-collapsed');
            localStorage.setItem('sidebar_collapsed', 'true');
        } else {
            if (sidebar) sidebar.classList.remove('mobile-show');
            if (overlay) overlay.classList.remove('show');
        }
    }

    if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
    if (collapseBtn) collapseBtn.addEventListener('click', closeSidebar);
    if (overlay) {
        overlay.addEventListener('click', function() {
            if (sidebar) sidebar.classList.remove('mobile-show');
            overlay.classList.remove('show');
        });
    }
});
</script>
</body>
</html>
