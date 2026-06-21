<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saran & Masukan Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .card-header-custom {
            background: linear-gradient(90deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
        }
        .saran-box {
            background-color: white;
            border-left: 4px solid #2a5298;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body class="py-5">

<div class="container">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1">Saran & Masukan Mahasiswa</h2>
            <p class="text-muted mb-0">Daftar saran opsional yang diberikan oleh responden pada akhir form.</p>
        </div>
        <a href="javascript:history.back()" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card shadow border-0 rounded-3 overflow-hidden mb-4">
        <div class="card-header card-header-custom font-weight-bold py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i> Informasi Jadwal</h5>
        </div>
        <div class="card-body bg-white">
            <div class="row">
                <div class="col-md-3 text-muted fw-semibold">Dosen</div>
                <div class="col-md-9 fw-bold text-dark">: {{ $jadwal->dosen->name ?? '-' }}</div>
            </div>
            <div class="row mt-2">
                <div class="col-md-3 text-muted fw-semibold">Mata Kuliah</div>
                <div class="col-md-9">: {{ $jadwal->mataKuliah->name ?? '-' }}</div>
            </div>
            <div class="row mt-2">
                <div class="col-md-3 text-muted fw-semibold">Program Studi</div>
                <div class="col-md-9">: {{ $jadwal->prodi->name ?? '-' }}</div>
            </div>
            <div class="row mt-2">
                <div class="col-md-3 text-muted fw-semibold">Periode</div>
                <div class="col-md-9">: {{ $jadwal->periode ?? '-' }}</div>
            </div>
            <div class="row mt-2">
                <div class="col-md-3 text-muted fw-semibold">Total Saran Diterima</div>
                <div class="col-md-9">: <span class="badge bg-primary rounded-pill">{{ $evaluations->count() }} Saran</span></div>
            </div>
        </div>
    </div>

    @if($evaluations->count() > 0)
        <h5 class="fw-bold mb-3 mt-5">Daftar Saran:</h5>
        @foreach($evaluations as $index => $eval)
            <div class="saran-box">
                <div class="d-flex justify-content-between mb-2">
                    <span class="badge bg-light text-secondary border">Responden #{{ $index + 1 }}</span>
                    <small class="text-muted"><i class="bi bi-clock me-1"></i> {{ $eval->created_at->format('d M Y H:i') }}</small>
                </div>
                <p class="mb-0" style="font-size: 1.05rem; line-height: 1.6;">"{{ $eval->saran }}"</p>
            </div>
        @endforeach
    @else
        <div class="text-center py-5 mt-4 bg-white rounded-3 shadow-sm border">
            <i class="bi bi-chat-square-text text-muted" style="font-size: 4rem;"></i>
            <h4 class="mt-3 text-muted">Belum ada saran</h4>
            <p class="text-secondary">Tidak ada mahasiswa yang memberikan saran atau masukan untuk jadwal ini.</p>
        </div>
    @endif

</div>

</body>
</html>
