<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Laporan Kuisioner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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
        
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h2 class="fw-bold text-dark mb-0">Laporan Hasil</h2>
        </div>

        <ul class="nav nav-tabs mb-4 no-print" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold" id="preview-tab" data-bs-toggle="tab" data-bs-target="#preview" type="button" role="tab">Tabel Data</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-primary" id="book-tab" data-bs-toggle="tab" data-bs-target="#book" type="button" role="tab">Konfigurasi & Cetak Buku</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- TAB 1: Tabel Data -->
            <div class="tab-pane fade show active" id="preview" role="tabpanel">
                <!-- Filter -->
                <div class="card shadow-sm border-0 mb-4 rounded-3 no-print">
                    <div class="card-body">
                        <form action="{{ route('admin.laporan') }}" method="GET" class="row align-items-center g-3">
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
                                <a href="{{ route('admin.laporan') }}" class="btn btn-outline-secondary btn-lg w-100">Reset Filter</a>
                            </div>
                        </form>
                    </div>
                </div>

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

            <!-- TAB 2: Konfigurasi Buku -->
            <div class="tab-pane fade" id="book" role="tabpanel">
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

                            <div class="mb-4">
                                <label class="form-label fw-bold">Upload Cover Laporan (Opsional, JPG/PNG)</label>
                                @if(isset($config['cover']) && $config['cover'])
                                    <div class="mb-2">
                                        <img src="{{ asset($config['cover']) }}" alt="Cover" class="img-thumbnail" style="max-height: 150px;">
                                    </div>
                                @endif
                                <input class="form-control" type="file" name="cover" accept="image/*">
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
                                            <textarea name="kata_pengantar" class="form-control" rows="4" placeholder="Tuliskan kata pengantar di sini...">{{ $config['kata_pengantar'] ?? '' }}</textarea>
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
                                            <textarea name="bab4" class="form-control" rows="4" placeholder="Analisis hasil, kekuatan, kelemahan...">{{ $config['bab4'] ?? '' }}</textarea>
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
    document.addEventListener("DOMContentLoaded", function() {
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
