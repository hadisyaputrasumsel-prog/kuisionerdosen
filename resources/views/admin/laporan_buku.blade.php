<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Laporan Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #525659;
            display: flex;
            justify-content: center;
            font-family: 'Times New Roman', Times, serif;
        }
        .book-container {
            width: 100%;
            max-width: 21cm; /* A4 width */
        }
        .page {
            background: white;
            width: 21cm;
            min-height: 29.7cm; /* A4 height */
            padding: 2.5cm;
            margin: 2cm auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            position: relative;
        }
        .controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .chapter-title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        .content-text {
            text-align: justify;
            line-height: 1.5;
            font-size: 12pt;
        }
        
        /* Formating for tables created from TinyMCE */
        .content-text table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .content-text th, .content-text td {
            border: 1px solid #000;
            padding: 8px;
        }
        
        /* Cover specific styling */
        .cover-page {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-align: center;
            padding-top: 4cm;
            padding-bottom: 4cm;
        }
        .cover-logo {
            max-width: 200px;
            margin: 2rem 0;
        }
        .cover-title {
            font-size: 20pt;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .cover-subtitle {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 30px;
        }
        
        /* Table styling for book */
        .book-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10pt;
        }
        .book-table th, .book-table td {
            border: 1px solid #000;
            padding: 6px;
        }
        .book-table th {
            text-align: center;
            background-color: #f2f2f2 !important;
            -webkit-print-color-adjust: exact;
        }

        @media print {
            body {
                background: white;
                display: block;
            }
            .controls { display: none; }
            .book-container { max-width: none; }
            .page {
                margin: 0;
                box-shadow: none;
                page-break-after: always;
            }
            /* Remove margins for printing to allow browser to handle A4 */
            @page {
                size: A4;
                margin: 0;
            }
        }
    </style>
</head>
<body>

<div class="controls">
    <button onclick="window.print()" class="btn btn-primary shadow-lg btn-lg mb-2 d-block w-100">
        <i class="bi bi-printer"></i> Cetak Laporan
    </button>
    <a href="{{ route('admin.laporan') }}" class="btn btn-light shadow-lg btn-lg d-block w-100">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="book-container">

    @if(!empty($config['cover']))
    <div class="page" style="padding: 0; margin: 2cm auto; height: 29.7cm; overflow: hidden;">
        <img src="{{ asset($config['cover']) }}" alt="Cover Full" style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
    @else
    <div class="page cover-page">
        <div>
            <div class="cover-title">LAPORAN HASIL SURVEY KEPUASAN MAHASISWA</div>
            <div class="cover-subtitle">TERHADAP DOSEN DALAM PENGAJARAN</div>
            
            <div style="font-size: 14pt; margin-top: 10px;">
                @if(request('periode'))
                    <div class="mb-2">Periode: {{ request('periode') }}</div>
                @endif
                
                @if(request('prodi_id'))
                    <div class="fw-bold">TINGKAT PROGRAM STUDI: {{ strtoupper($prodis->firstWhere('id', request('prodi_id'))->name ?? '') }}</div>
                @else
                    <div class="fw-bold">TINGKAT UNIVERSITAS</div>
                @endif
            </div>
        </div>

        @if(!empty($config['cover']))
            <img src="{{ asset($config['cover']) }}" alt="Logo" class="cover-logo" style="max-width: 300px; max-height: 300px; object-fit: contain;">
        @else
            <!-- Placeholder if no cover uploaded -->
            <div style="height: 200px; display: flex; align-items: center; justify-content: center; border: 1px dashed #ccc; width: 100%; max-width: 200px; margin: 2rem auto;">
                [Logo Kampus]
            </div>
        @endif

        <div style="font-size: 16pt; font-weight: bold;">
            UNIVERSITAS SUMATERA SELATAN<br>
            TAHUN {{ date('Y') }}
        </div>
    </div>
    @endif

    <!-- KATA PENGANTAR -->
    @if(!empty($config['kata_pengantar']))
    <div class="page">
        <div class="chapter-title">KATA PENGANTAR</div>
        <div class="content-text">{!! $config['kata_pengantar'] !!}</div>
    </div>
    @endif

    <!-- DAFTAR ISI -->
    <div class="page">
        <div class="chapter-title">DAFTAR ISI</div>
        <div class="content-text" id="auto-toc">
            <!-- Auto TOC injected via JS -->
        </div>
    </div>

    <!-- BAB I -->
    @if(!empty($config['bab1']))
    <div class="page">
        <div class="chapter-title">BAB I<br>PENDAHULUAN</div>
        <div class="content-text">{!! $config['bab1'] !!}</div>
    </div>
    @endif

    <!-- BAB II -->
    @if(!empty($config['bab2']))
    <div class="page">
        <div class="chapter-title">BAB II<br>METODE EVALUASI</div>
        <div class="content-text">{!! $config['bab2'] !!}</div>
    </div>
    @endif

    <!-- BAB III & TABLE -->
    <div class="page">
        <div class="chapter-title">BAB III<br>HASIL EVALUASI</div>
        @php
            $bab3Content = $config['bab3'] ?? '';
            $hasPieChart = str_contains($bab3Content, '[PIE_CHART_DOSEN]');
            $dosenLabels = [];
            $dosenData = [];
            
            if ($hasPieChart) {
                $dosenStats = [];
                foreach($jadwals as $j) {
                    $name = $j->dosen->name ?? 'Unknown';
                    if (!isset($dosenStats[$name])) {
                        $dosenStats[$name] = 0;
                    }
                    $dosenStats[$name] += $j->evaluations->count();
                }
                $dosenLabels = array_keys($dosenStats);
                $dosenData = array_values($dosenStats);

                $pieChartHtml = '<div style="width: 70%; margin: 30px auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);"><h4 style="text-align: center; margin-bottom: 20px; font-size: 14pt;">Proporsi Responden Berdasarkan Dosen</h4><canvas id="dosenPieChart"></canvas></div>';
                $bab3Content = str_replace('[PIE_CHART_DOSEN]', $pieChartHtml, $bab3Content);
            }
        @endphp
        @if(!empty($bab3Content))
            <div class="content-text mb-4">{!! $bab3Content !!}</div>
        @endif

        <table class="book-table" style="font-size: 8pt;">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 3%; vertical-align: middle;">No</th>
                    <th rowspan="2" style="width: 18%; vertical-align: middle;">Nama Dosen</th>
                    <th rowspan="2" style="width: 18%; vertical-align: middle;">Mata Kuliah</th>
                    <th rowspan="2" style="width: 11%; vertical-align: middle;">Program Studi</th>
                    <th rowspan="2" style="width: 4%; vertical-align: middle;">Resp.</th>
                    <th colspan="3" style="text-align: center;">Aspek Penilaian dan Hasil Penilaian</th>
                    <th rowspan="2" style="width: 8%; vertical-align: middle;">Nilai</th>
                    <th rowspan="2" style="width: 11%; vertical-align: middle;">Kategori</th>
                </tr>
                <tr>
                    <th style="width: 9%; vertical-align: middle;">PBM</th>
                    <th style="width: 9%; vertical-align: middle;">KKD</th>
                    <th style="width: 9%; vertical-align: middle;">KSP</th>
                </tr>
            </thead>
            <tbody>
                @php $currentDosenId = null; @endphp
                @forelse($jadwals as $index => $jadwal)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        @if($currentDosenId !== $jadwal->dosen_id)
                            <td><b>{{ $jadwal->dosen->name ?? 'N/A' }}</b></td>
                            @php $currentDosenId = $jadwal->dosen_id; @endphp
                        @else
                            <td style="text-align: center; color: #888; font-size: 10pt;">"</td>
                        @endif
                        
                        @php 
                            $mkName = $jadwal->mataKuliah->name ?? 'N/A';
                            if(!empty($jadwal->mataKuliah->code) && $jadwal->mataKuliah->code !== 'N/A') {
                                $mkName .= ' (' . $jadwal->mataKuliah->code . ')';
                            }
                        @endphp
                        <td>{{ $mkName }}</td>
                        <td style="text-align: center;">{{ $jadwal->prodi->name ?? 'N/A' }}</td>
                        <td style="text-align: center;">{{ $jadwal->evaluations->count() }}</td>
                        <td style="text-align: center;">
                            <span style="font-size: 9pt;">{{ number_format($jadwal->avg_A, 1) }}</span>
                            <span class="text-muted" style="font-size: 6.5pt; display: block; margin-top: 1px;">({{ $jadwal->pred_A }})</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-size: 9pt;">{{ number_format($jadwal->avg_B, 1) }}</span>
                            <span class="text-muted" style="font-size: 6.5pt; display: block; margin-top: 1px;">({{ $jadwal->pred_B }})</span>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-size: 9pt;">{{ number_format($jadwal->avg_C, 1) }}</span>
                            <span class="text-muted" style="font-size: 6.5pt; display: block; margin-top: 1px;">({{ $jadwal->pred_C }})</span>
                        </td>
                        <td style="text-align: center; font-weight: bold; font-size: 9pt;">{{ number_format($jadwal->average_score, 1) }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $jadwal->predikat }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">Belum ada data evaluasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div style="margin-top: 15px; font-size: 10pt; line-height: 1.5;">
            <strong>Keterangan :</strong><br>
            PBM = Proses Belajar Mengajar<br>
            KKD = Kapabilitas Kompetensi Dosen<br>
            KSP = Ketersediaan Sarana Prasarana
        </div>
    </div>

    <!-- BAB IV -->
    @if(!empty($config['bab4']))
    <div class="page">
        <div class="chapter-title">BAB IV<br>ANALISIS DAN PEMBAHASAN</div>
        <div class="content-text">{!! $config['bab4'] !!}</div>
    </div>
    @endif

    <!-- BAB V -->
    @if(!empty($config['bab5']))
    <div class="page">
        <div class="chapter-title">BAB V<br>KESIMPULAN DAN REKOMENDASI</div>
        <div class="content-text">{!! $config['bab5'] !!}</div>
    </div>
    @endif

    <!-- LAMPIRAN 1: INSTRUMEN -->
    <div class="page">
        <div class="chapter-title">LAMPIRAN 1<br>INSTRUMEN KUESIONER</div>
        <table class="book-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Bagian</th>
                    <th style="width: 70%;">Pertanyaan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($questions as $index => $q)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $q->section }}</td>
                    <td>{{ $q->question_text }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- LAMPIRAN 2: GRAFIK -->
    <div class="page">
        <div class="chapter-title">LAMPIRAN 2<br>GRAFIK HASIL SURVEI</div>
        <div style="width: 90%; margin: 2rem auto;">
            <canvas id="predikatChart"></canvas>
        </div>
    </div>

    <!-- LAMPIRAN 3: SURAT TUGAS -->
    @if(!empty($config['surat_tugas']))
    <div class="page">
        <div class="chapter-title">LAMPIRAN 3<br>SURAT TUGAS / KEPUTUSAN</div>
        <div style="text-align: center; margin-top: 2rem;">
            <img src="{{ asset($config['surat_tugas']) }}" style="max-width: 100%; max-height: 22cm; object-fit: contain;">
        </div>
    </div>
    @endif

    <!-- LAMPIRAN 4: DOKUMENTASI -->
    @if(!empty($config['dokumentasi']))
    <div class="page">
        <div class="chapter-title">LAMPIRAN 4<br>DOKUMENTASI PELAKSANAAN</div>
        <div style="text-align: center; margin-top: 2rem;">
            <img src="{{ asset($config['dokumentasi']) }}" style="max-width: 100%; max-height: 22cm; object-fit: contain;">
        </div>
    </div>
    @endif

    <!-- LAMPIRAN TAMBAHAN -->
    @if(!empty($config['lampiran']))
    <div class="page">
        <div class="chapter-title">LAMPIRAN TAMBAHAN</div>
        <div class="content-text">{!! $config['lampiran'] !!}</div>
    </div>
    @endif

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById('predikatChart').getContext('2d');
        var chartData = @json(array_values($chartData));
        var chartLabels = @json(array_keys($chartData));
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Jumlah Dosen per Predikat',
                    data: chartData,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(255, 99, 132, 0.6)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });

    });

    // Auto Generate TOC - Harus tunggu semua gambar/font selesai di-load
    window.addEventListener('load', function() {
        var autoToc = document.getElementById('auto-toc');
        if (autoToc) {
            function toRoman(num) {
                var lookup = {m:1000,cm:900,d:500,cd:400,c:100,xc:90,l:50,xl:40,x:10,ix:9,v:5,iv:4,i:1};
                var roman = '', i;
                for (i in lookup) {
                    while (num >= lookup[i]) {
                        roman += i;
                        num -= lookup[i];
                    }
                }
                return roman;
            }

            var tocHtml = '<div style="margin-top: 1rem;">';
            var dummy = document.createElement('div');
            dummy.style.height = '29.7cm'; // Ukuran A4
            document.body.appendChild(dummy);
            var a4Height = dummy.offsetHeight;
            document.body.removeChild(dummy);

            var pages = document.querySelectorAll('.page');
            var preambleNum = 1;
            var pageNum = 1;
            var isRoman = true;

            pages.forEach(function(page) {
                var titleEl = page.querySelector('.chapter-title');
                var printedPages = Math.ceil(page.offsetHeight / a4Height);
                if (printedPages < 1) printedPages = 1;

                if (titleEl) {
                    var titleText = titleEl.innerHTML.replace(/<br\s*[\/]?>/gi, ' - ');
                    var temp = document.createElement('div');
                    temp.innerHTML = titleText;
                    titleText = temp.textContent || temp.innerText || "";
                    
                    if (titleText.toUpperCase().includes('BAB I -')) {
                        isRoman = false;
                        pageNum = 1;
                    }

                    if (titleText.trim() !== '' && !titleText.toUpperCase().includes('DAFTAR ISI')) {
                        var displayNum = isRoman ? toRoman(preambleNum) : pageNum;
                        tocHtml += '<div style="display: flex; justify-content: space-between; margin-bottom: 10px;">' +
                                   '<span style="font-weight: bold;">' + titleText + '</span>' +
                                   '<span style="flex-grow: 1; border-bottom: 2px dotted #ccc; margin: 0 10px; position: relative; top: -6px;"></span>' +
                                   '<span>' + displayNum + '</span>' +
                                   '</div>';
                    }
                }

                if (isRoman) {
                    preambleNum += printedPages;
                } else {
                    pageNum += printedPages;
                }
            });

            tocHtml += '</div>';
            autoToc.innerHTML = tocHtml;
        }

        // Render Pie Chart if element exists
        var ctxDosen = document.getElementById('dosenPieChart');
        if (ctxDosen) {
            var dosenLabels = {!! json_encode($dosenLabels ?? []) !!};
            var dosenData = {!! json_encode($dosenData ?? []) !!};
            
            new Chart(ctxDosen, {
                type: 'pie',
                data: {
                    labels: dosenLabels,
                    datasets: [{
                        data: dosenData,
                        backgroundColor: [
                            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
                            '#858796', '#5a5c69', '#2e59d9', '#17a673', '#2c9faf'
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += context.parsed + ' responden';
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>

</body>
</html>
