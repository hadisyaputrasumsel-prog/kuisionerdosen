<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Data Jadwal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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
        <div class="mt-auto pt-3 text-center text-white-50 small opacity-50">
            &copy; 2026 Univ Sumatera Selatan
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 bg-light p-4" style="margin-left: 280px; min-height: 100vh;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark mb-0">Data Dosen dan Mata Kuliah</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.laporan.cetak-tabel', ['periode' => request('periode'), 'prodi_id' => request('prodi_id')]) }}" target="_blank" class="btn btn-primary shadow-sm fw-bold">
                    <i class="bi bi-printer-fill me-2"></i> Cetak Daftar Evaluasi
                </a>
                <button class="btn btn-warning shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#periodeModal">
                    <i class="bi bi-gear-fill me-2"></i> Seting Periode Aktif
                </button>
            </div>
        </div>

        <!-- Filter -->
        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-body">
                <form action="{{ route('admin.jadwal') }}" method="GET" class="row align-items-center g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold mb-1">Filter Periode:</label>
                        <select name="periode" class="form-select form-select-lg" onchange="this.form.submit()">
                            <option value="">-- Semua Periode --</option>
                            @foreach($filterPeriodes as $p)
                                <option value="{{ $p }}" {{ request('periode') == $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold mb-1">Filter Program Studi:</label>
                        <select name="prodi_id" class="form-select form-select-lg" onchange="this.form.submit()">
                            <option value="">-- Semua Program Studi --</option>
                            @foreach($prodis as $prodi)
                                <option value="{{ $prodi->id }}" {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>{{ $prodi->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end" style="padding-top: 28px;">
                        <a href="{{ route('admin.jadwal') }}" class="btn btn-outline-secondary btn-lg w-100">Reset Filter</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Data Jadwal -->
        <div class="card shadow border-0 rounded-3 overflow-hidden">
            <div class="card-header card-header-custom font-weight-bold py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Daftar Dosen & Evaluasi</h5>
                <span class="badge bg-primary rounded-pill">{{ $jadwals->total() }} Total Data</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">#</th>
                                <th class="py-3">Dosen</th>
                                <th class="py-3">Mata Kuliah</th>
                                <th class="py-3">Program Studi</th>
                                <th class="py-3 text-center">Total Responden</th>
                                <th class="py-3">Periode</th>
                                <th class="py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $currentDosenId = null; @endphp
                            @forelse($jadwals as $index => $jadwal)
                                <tr>
                                    <td class="px-4 text-muted">{{ $jadwals->firstItem() + $index }}</td>
                                    @if($currentDosenId !== $jadwal->dosen_id)
                                        <td class="fw-semibold text-primary">{{ $jadwal->dosen->name ?? 'N/A' }}</td>
                                        @php $currentDosenId = $jadwal->dosen_id; @endphp
                                    @else
                                        <td class="text-center text-muted border-0"><i class="bi bi-arrow-return-right"></i></td>
                                    @endif
                                    <td>{{ $jadwal->mataKuliah->name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-secondary">{{ $jadwal->prodi->name ?? 'N/A' }}</span></td>
                                    <td class="text-center">
                                        <span class="badge {{ $jadwal->evaluations_count > 0 ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill px-3 py-2">
                                            {{ $jadwal->evaluations_count }} Mhs
                                        </span>
                                    </td>
                                    <td><span class="badge bg-info text-dark">{{ $jadwal->periode ?? 'Semester Aktif' }}</span></td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.jadwal.destroy', $jadwal->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini? Data kuisioner terkait jadwal ini juga akan terhapus.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Jadwal">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-folder-x fs-1 d-block mb-3"></i>
                                        Belum ada data jadwal pada periode ini.<br>
                                        Silakan lakukan sinkronisasi di menu <strong>Sinkron SIMAK</strong>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($jadwals->hasPages())
            <div class="card-footer bg-white py-3 border-0 d-flex justify-content-center">
                {{ $jadwals->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>

    </div>
</div>

<!-- Modal Setting Periode -->
<div class="modal fade" id="periodeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header card-header-custom text-white">
                <h5 class="modal-title fw-bold">Seting Periode Aktif</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="alert alert-info m-3 mb-0 border-0 shadow-sm text-sm">
                    <i class="bi bi-info-circle me-1"></i> Tentukan periode mana yang saat ini sedang aktif dan ditampilkan ke mahasiswa saat mengisi form kuisioner. Hanya jadwal pada periode aktif yang bisa dinilai.
                </div>
                <table class="table table-hover mb-0 mt-3 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">Nama Periode</th>
                            <th class="text-center">Status Mahasiswa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periodes as $p)
                        <tr>
                            <td class="px-4 fw-semibold">{{ $p->name }}</td>
                            <td class="text-center">
                                <form action="{{ route('admin.periode.toggle', $p->id) }}" method="POST">
                                    @csrf
                                    <div class="form-check form-switch d-inline-block" style="transform: scale(1.3);">
                                        <input class="form-check-input" type="checkbox" role="switch" onchange="this.form.submit()" {{ $p->is_active ? 'checked' : '' }}>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-4">Belum ada periode. Silakan Sinkron SIMAK terlebih dahulu.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
