<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kelola Kuisioner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <style>
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
            <h2 class="fw-bold text-dark mb-0">Kelola Materi Kuisioner</h2>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Pertanyaan
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @foreach($groupedQuestions as $section => $questionsInSection)
            <div class="card shadow-sm border-0 mb-4 rounded-3 overflow-hidden">
                <div class="card-header card-header-custom fw-bold py-3 d-flex justify-content-between align-items-center">
                    <span>{{ $section }}</span>
                    <span class="badge bg-light text-dark rounded-pill">{{ count($questionsInSection) }} Pertanyaan</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="70%">Pertanyaan</th>
                                    <th width="10%" class="text-center">Status</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questionsInSection as $q)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $q->order_num }}</td>
                                        <td>{{ $q->question_text }}</td>
                                        <td class="text-center">
                                            @if($q->is_active)
                                                <span class="badge bg-success rounded-pill px-3">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill px-3">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $q->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('admin.questions.destroy', $q->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pertanyaan ini?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal{{ $q->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <form class="modal-content" action="{{ route('admin.questions.update', $q->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Edit Pertanyaan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Kelompok (Section)</label>
                                                        <input type="text" name="section" class="form-control" value="{{ $q->section }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Isi Pertanyaan</label>
                                                        <textarea name="question_text" class="form-control" rows="3" required>{{ $q->question_text }}</textarea>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Urutan (No)</label>
                                                            <input type="number" name="order_num" class="form-control" value="{{ $q->order_num }}" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Status Aktif</label>
                                                            <div class="form-check form-switch mt-2">
                                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $q->is_active ? 'checked' : '' }} style="transform: scale(1.5); margin-left: -1em;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer flex-nowrap p-0">
                                                    <button type="submit" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 m-0 rounded-0 border-end">Simpan</button>
                                                    <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 m-0 rounded-0 text-secondary" data-bs-dismiss="modal">Batal</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        @if($groupedQuestions->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-folder-x fs-1 d-block mb-3"></i>
                Belum ada pertanyaan. Silakan tambahkan pertanyaan pertama Anda.
            </div>
        @endif

    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('admin.questions.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Pertanyaan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Kelompok (Section)</label>
                    <input type="text" name="section" class="form-control" placeholder="Contoh: A. PROSES BELAJAR MENGAJAR" required>
                    <small class="text-muted">Gunakan nama yang sama untuk mengelompokkan pertanyaan.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Isi Pertanyaan</label>
                    <textarea name="question_text" class="form-control" rows="3" required></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Urutan (No)</label>
                        <input type="number" name="order_num" class="form-control" value="1" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status Aktif</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked style="transform: scale(1.5); margin-left: -1em;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer flex-nowrap p-0">
                <button type="submit" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 m-0 rounded-0 border-end fw-bold">Tambah</button>
                <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 m-0 rounded-0 text-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
