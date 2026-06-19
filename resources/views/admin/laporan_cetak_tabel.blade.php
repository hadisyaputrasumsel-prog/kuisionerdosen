<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Hasil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 14px; background: #fff; color: #000; padding: 20px; }
        .table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .table th { background-color: #f8f9fa !important; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .header-title { font-size: 18px; font-weight: bold; text-align: center; margin-bottom: 5px; text-transform: uppercase; }
        .header-subtitle { font-size: 16px; text-align: center; margin-bottom: 20px; }
        @media print {
            .no-print { display: none !important; }
            @page { margin: 2cm; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" class="btn btn-primary">Cetak Sekarang</button>
        <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
    </div>

    <div class="header-title">DAFTAR EVALUASI DOSEN OLEH MAHASISWA</div>
    <div class="header-subtitle">
        @if($prodi)
            Program Studi: {{ $prodi->name }}<br>
        @else
            Seluruh Program Studi<br>
        @endif
        Periode: {{ $request->periode ?? 'Semua Periode' }}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 25%">Nama Dosen</th>
                <th style="width: 35%">Mata Kuliah</th>
                <th style="width: 25%">Program Studi</th>
                <th style="width: 10%">Responden</th>
            </tr>
        </thead>
        <tbody>
            @php $currentDosenId = null; @endphp
            @forelse($jadwals as $index => $jadwal)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    
                    @if($currentDosenId !== $jadwal->dosen_id)
                        <td><strong>{{ $jadwal->dosen->name ?? 'N/A' }}</strong></td>
                        @php $currentDosenId = $jadwal->dosen_id; @endphp
                    @else
                        <td class="text-center">"</td>
                    @endif
                    
                    @php 
                        $mkName = $jadwal->mataKuliah->name ?? 'N/A';
                        if(!empty($jadwal->mataKuliah->code) && $jadwal->mataKuliah->code !== 'N/A') {
                            $mkName .= ' (' . $jadwal->mataKuliah->code . ')';
                        }
                    @endphp
                    <td>{{ $mkName }}</td>
                    <td>{{ $jadwal->prodi->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $jadwal->evaluations->count() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data evaluasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
