<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Periode;
use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\MataKuliah;
use App\Models\Jadwal;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->has('periode')) {
            $activePeriode = \App\Models\Periode::where('is_active', true)->first()->name ?? null;
            if ($activePeriode) {
                $request->merge(['periode' => $activePeriode]);
            }
        }

        $periodes = Jadwal::select('periode')->distinct()->pluck('periode')->filter();
        
        $query = Jadwal::query();
        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }

        $stats = [
            'total_jadwal' => (clone $query)->count(),
            'total_dosen' => (clone $query)->distinct('dosen_id')->count('dosen_id'),
            'total_responden' => \App\Models\Evaluation::whereIn('jadwal_id', (clone $query)->pluck('id'))->count(),
        ];

        // Jadwal per Prodi (filtered by period)
        $jadwalPerProdi = Prodi::withCount(['jadwals' => function($q) use ($request) {
            if ($request->filled('periode')) $q->where('periode', $request->periode);
        }])->get();

        $topDosen = (clone $query)->with('dosen')
            ->withCount('evaluations')
            ->orderByDesc('evaluations_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'jadwalPerProdi', 'topDosen', 'periodes'));
    }

    public function sinkron()
    {
        return view('admin.sinkron');
    }

    public function jadwal(Request $request)
    {
        if (!$request->has('periode')) {
            $activePeriode = \App\Models\Periode::where('is_active', true)->first()->name ?? null;
            if ($activePeriode) {
                $request->merge(['periode' => $activePeriode]);
            }
        }

        $query = \App\Models\Jadwal::with(['dosen', 'mataKuliah', 'prodi'])
            ->join('dosens', 'jadwals.dosen_id', '=', 'dosens.id')
            ->select('jadwals.*')
            ->withCount('evaluations')
            ->orderBy('dosens.name', 'asc');

        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }
        
        if ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }

        $jadwals = $query->paginate(20);
        $filterPeriodes = \App\Models\Jadwal::select('periode')->whereNotNull('periode')->distinct()->pluck('periode')->filter();
        $prodis = \App\Models\Prodi::orderBy('name')->get();
        $periodes = \App\Models\Periode::all();

        return view('admin.jadwal', compact('jadwals', 'filterPeriodes', 'prodis', 'periodes'));
    }

    public function destroyJadwal($id)
    {
        $jadwal = \App\Models\Jadwal::findOrFail($id);
        $jadwal->delete();
        return redirect()->back()->with('success', 'Jadwal berhasil dihapus.');
    }

    public function togglePeriode(Request $request, $id)
    {
        $periode = \App\Models\Periode::findOrFail($id);
        
        if (!$periode->is_active) {
            // Jika diaktifkan, matikan semua periode lain (karena hanya boleh 1 yang aktif)
            \App\Models\Periode::where('id', '!=', $id)->update(['is_active' => false]);
            $periode->is_active = true;
        } else {
            // Jika dimatikan
            $periode->is_active = false;
        }
        
        $periode->save();
        return redirect()->back()->with('success', 'Status periode berhasil diubah. Sekarang hanya 1 periode yang aktif.');
    }
    
    public function storePeriode(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:periodes,name']);
        Periode::create(['name' => $request->name, 'is_active' => false]);
        return redirect()->back()->with('success', 'Periode berhasil ditambahkan.');
    }

    public function getPeriods(Request $request)
    {
        try {
            $cookieJar = new \GuzzleHttp\Cookie\CookieJar();
            
            $baseUrl = $request->simak_url ?? env('SIMAK_BASE_URL', 'https://simak.uss.ac.id');
            $baseUrl = rtrim($baseUrl, '/');
            
            // 1. Login
            $loginRes = \Illuminate\Support\Facades\Http::withOptions(['verify' => false, 'cookies' => $cookieJar])
                ->withHeaders(['Host' => 'simak.uss.ac.id'])
                ->asForm()
                ->post($baseUrl . '/login/proses/', [
                    'username' => $request->simak_username,
                    'password' => $request->simak_password
                ]);

            // 2. Akses Dashboard
            $dashRes = \Illuminate\Support\Facades\Http::withOptions(['verify' => false, 'cookies' => $cookieJar])
                ->withHeaders(['Host' => 'simak.uss.ac.id'])
                ->get($baseUrl . '/apps/dashboard/dashboard/');
                
            $html = $dashRes->body();
            
            // Cari link jadwal
            $targetUrl = null;
            if (preg_match('/href="([^"]+\/apps\/dosen\/jadwal\/[^"]+)"/', $html, $matches)) {
                $targetUrl = $matches[1];
                if (!str_starts_with($targetUrl, 'http')) {
                    $targetUrl = $baseUrl . $targetUrl;
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Tidak dapat menemukan menu Jadwal. Pastikan login benar.']);
            }

            // 3. Akses Jadwal
            $jadwalRes = \Illuminate\Support\Facades\Http::withOptions(['verify' => false, 'cookies' => $cookieJar])
                ->withHeaders(['Host' => 'simak.uss.ac.id'])
                ->get($targetUrl);
                
            $jadwalHtml = $jadwalRes->body();
            
            $dom = new \DOMDocument();
            @$dom->loadHTML($jadwalHtml);
            $xpath = new \DOMXPath($dom);

            $periodeNodes = $xpath->query("//select[@id='ta_id']//option");
            $periods = [];
            foreach ($periodeNodes as $node) {
                $periods[] = [
                    'value' => $node->getAttribute('value'),
                    'label' => trim($node->textContent)
                ];
            }

            return response()->json(['success' => true, 'periods' => $periods]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function scrapeData(Request $request)
    {
        $request->validate([
            'simak_username' => 'required|string',
            'simak_password' => 'required|string'
        ]);

        try {
            $cookieJar = new \GuzzleHttp\Cookie\CookieJar();
            
            $baseUrl = $request->simak_url ?? env('SIMAK_BASE_URL', 'https://simak.uss.ac.id');
            $baseUrl = rtrim($baseUrl, '/');
            
            // 1. Login
            $loginRes = \Illuminate\Support\Facades\Http::withOptions(['verify' => false, 'cookies' => $cookieJar])
                ->withHeaders(['Host' => 'simak.uss.ac.id'])
                ->asForm()
                ->post($baseUrl . '/login/proses/', [
                    'username' => $request->simak_username,
                    'password' => $request->simak_password
                ]);

            // 2. Akses Dashboard
            $dashRes = \Illuminate\Support\Facades\Http::withOptions(['verify' => false, 'cookies' => $cookieJar])
                ->withHeaders(['Host' => 'simak.uss.ac.id'])
                ->get($baseUrl . '/apps/dashboard/dashboard/');
                
            $html = $dashRes->body();
            
            // Cari link jadwal
            $targetUrl = null;
            if (preg_match('/href="([^"]+\/apps\/dosen\/jadwal\/[^"]+)"/', $html, $matches)) {
                $targetUrl = $matches[1];
                if (!str_starts_with($targetUrl, 'http')) {
                    $targetUrl = $baseUrl . $targetUrl;
                }
                
                // Jika user memilih periode spesifik
                if ($request->filled('simak_ta')) {
                    $ta_val = $request->simak_ta;
                    // pastikan formatnya ada prefix "ta-"
                    if (is_numeric($ta_val) || !str_starts_with($ta_val, 'ta-')) {
                        $ta_val = 'ta-' . str_replace('ta-', '', $ta_val);
                    }
                    
                    $periode_string = $ta_val;
                    if ($request->filled('simak_tipe')) {
                        $periode_string .= '~' . $request->simak_tipe;
                    }
                    
                    $parts = explode('/', rtrim($targetUrl, '/'));
                    if (count($parts) >= 2) {
                        $parts[count($parts) - 2] = $periode_string;
                        $targetUrl = implode('/', $parts) . '/';
                    }
                }
            } else {
                return redirect()->back()->with('error', 'Gagal melakukan scraping: Tidak dapat menemukan menu Jadwal Mengajar di dashboard SIMAK. Pastikan username dan password benar.');
            }

            // 3. Bangun URL Jadwal Global Kampus (Jadwal Ruangan)
            $parts = explode('/', rtrim($targetUrl, '/'));
            $periode_str = $parts[count($parts) - 2] ?? '';
            
            $ta_id = '11';
            $tipe = 'REGULER';
            // Regex mendukung "ta-12~REGULER" atau sejenisnya
            if (preg_match('/ta-(\d+)~([A-Z]+)/', $periode_str, $matches)) {
                $ta_id = $matches[1];
                $tipe = $matches[2];
            }

            // Gunakan unit ID 5670 untuk USS Global Ruang Daftar
            $globalUrl = "{$baseUrl}/apps/krs/gedung/ruang/daftar/5670/0~{$ta_id}~{$tipe}/";
            
            // 4. Akses Jadwal Global
            $jadwalRes = \Illuminate\Support\Facades\Http::withOptions(['verify' => false, 'cookies' => $cookieJar])
                ->withHeaders(['Host' => 'simak.uss.ac.id'])
                ->get($globalUrl);
                
            $jadwalHtml = $jadwalRes->body();
            
            $dom = new \DOMDocument();
            @$dom->loadHTML($jadwalHtml);
            $xpath = new \DOMXPath($dom);

            $count = 0;
            $popovers = $xpath->query("//a[@data-popup='popover' and @data-content]");
            
            // Hindari duplikasi karena popover bisa saja ganda jika sks dipisah di ruangan/jam beda
            $inserted = []; 
            
            foreach ($popovers as $node) {
                $content = $node->getAttribute('data-content');
                
                // Extract PRODI
                preg_match('/PRODI : <span[^>]*>([^<]+)<\/span>/i', $content, $mProdi);
                $program_studi = trim($mProdi[1] ?? '');
                
                // Extract MATAKULIAH (buang text " - X SKS" di belakangnya)
                preg_match('/MATAKULIAH : <span[^>]*>([^<]+?)(\s*-\s*\d+\s*SKS)?<\/span>/i', $content, $mMk);
                $matakuliah = trim($mMk[1] ?? '');
                
                // Extract DOSEN (bisa multiple, pisahkan dengan koma di SIMAK, kita ambil bersih)
                preg_match('/DOSEN : <span[^>]*>([^<]+)<\/span>/i', $content, $mDosen);
                $dosen = trim(trim($mDosen[1] ?? ''), ', ');

                if ($program_studi && $matakuliah && $dosen) {
                    $key = $program_studi . '_' . $matakuliah . '_' . $dosen;
                    if (!in_array($key, $inserted)) {
                        $prodiModel = Prodi::firstOrCreate(['name' => $program_studi]);
                        $dosenModel = Dosen::firstOrCreate(['name' => $dosen]);
                        $mkModel = MataKuliah::firstOrCreate(['name' => $matakuliah], ['code' => 'N/A']); 
                        
                        $periode_val = $request->input('periode_label');
                        if (empty(trim($periode_val)) || trim($periode_val) === 'REGULER') {
                            $periode_val = '2025/2026 Ganjil Reguler';
                        }
                        
                        \App\Models\Periode::firstOrCreate(['name' => $periode_val], ['is_active' => false]);

                        Jadwal::firstOrCreate([
                            'dosen_id' => $dosenModel->id,
                            'mata_kuliah_id' => $mkModel->id,
                            'prodi_id' => $prodiModel->id,
                            'periode' => $periode_val,
                        ]);
                        
                        $inserted[] = $key;
                        $count++;
                    }
                }
            }

            return redirect()->back()->with('success', "Data global kampus berhasil ditarik. Total mata kuliah baru disinkronisasi: $count");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal melakukan scraping via PHP: ' . $e->getMessage());
        }
    }

    public function laporan(Request $request)
    {
        if (!$request->has('periode')) {
            $activePeriode = \App\Models\Periode::where('is_active', true)->first()->name ?? null;
            if ($activePeriode) {
                $request->merge(['periode' => $activePeriode]);
            }
        }

        $query = \App\Models\Jadwal::with(['dosen', 'mataKuliah', 'prodi', 'evaluations'])
            ->join('dosens', 'jadwals.dosen_id', '=', 'dosens.id')
            ->select('jadwals.*')
            ->has('evaluations')
            ->orderBy('dosens.name', 'asc');

        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }
        if ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }

        $jadwals = $query->get();

        $this->calculateJadwalScores($jadwals);

        $filterPeriodes = \App\Models\Jadwal::select('periode')->whereNotNull('periode')->distinct()->pluck('periode')->filter();
        $prodis = \App\Models\Prodi::orderBy('name')->get();

        $prodiStr = $request->prodi_id ? 'prodi_' . $request->prodi_id : 'univ';
        $configPath = storage_path("app/report_config_{$prodiStr}.json");
        $config = file_exists($configPath) ? json_decode(file_get_contents($configPath), true) : [
            'kata_pengantar' => '',
            'daftar_isi' => '',
            'bab1' => '',
            'bab2' => '',
            'bab3' => 'Hasil Evaluasi Dosen dalam Pengajaran pada semester ini disajikan pada tabel di bawah ini. Tabel ini memuat data dosen, mata kuliah, program studi, jumlah responden, nilai rata-rata, dan predikat berdasarkan hasil kuisioner yang diisi oleh mahasiswa.',
            'bab4' => '',
            'bab5' => '',
            'lampiran' => '',
            'cover' => ''
        ];

        return view('admin.laporan', compact('jadwals', 'filterPeriodes', 'prodis', 'config'));
    }

    public function saveLaporanConfig(Request $request)
    {
        $request->validate([
            'cover' => 'nullable|file|image|max:10240', // Maksimal 10MB
            'surat_tugas' => 'nullable|file|image|max:10240',
            'dokumentasi' => 'nullable|file|image|max:10240',
        ], [
            'cover.max' => 'Ukuran file Cover maksimal adalah 10MB.',
            'surat_tugas.max' => 'Ukuran file Surat Tugas maksimal adalah 10MB.',
            'dokumentasi.max' => 'Ukuran file Dokumentasi maksimal adalah 10MB.',
            'image' => 'File yang diupload harus berupa gambar (JPG/PNG).',
        ]);

        $prodiStr = $request->prodi_id ? 'prodi_' . $request->prodi_id : 'univ';
        $configPath = storage_path("app/report_config_{$prodiStr}.json");
        $config = file_exists($configPath) ? json_decode(file_get_contents($configPath), true) : [];

        try {
            // Pastikan folder uploads tersedia
            if (!file_exists(public_path('uploads'))) {
                $dirCreated = @mkdir(public_path('uploads'), 0777, true);
                if (!$dirCreated) {
                    throw new \Exception("Gagal membuat direktori public/uploads. Periksa permission folder public.");
                }
            }

            if ($request->hasFile('cover')) {
                $file = $request->file('cover');
                $filename = 'cover_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $filename);
                $config['cover'] = 'uploads/' . $filename;
            } elseif ($request->has('cover_error') || (isset($_FILES['cover']) && $_FILES['cover']['error'] !== 0 && $_FILES['cover']['error'] !== 4)) {
                return redirect()->back()->withErrors(['cover' => 'Upload Cover gagal. Pastikan ukuran gambar tidak terlalu besar (batas server PHP: ' . ini_get('upload_max_filesize') . ').'])->withInput();
            }

            if ($request->hasFile('surat_tugas')) {
                $file = $request->file('surat_tugas');
                $filename = 'surat_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $filename);
                $config['surat_tugas'] = 'uploads/' . $filename;
            }

            if ($request->hasFile('dokumentasi')) {
                $file = $request->file('dokumentasi');
                $filename = 'dok_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $filename);
                $config['dokumentasi'] = 'uploads/' . $filename;
            }
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['upload' => 'Error Upload: ' . $e->getMessage()])->withInput();
        }

        $config['kata_pengantar'] = $request->kata_pengantar ?? '';
        $config['bab1'] = $request->bab1 ?? '';
        $config['bab2'] = $request->bab2 ?? '';
        $config['bab3'] = $request->bab3 ?? '';
        $config['bab4'] = $request->bab4 ?? '';
        $config['bab5'] = $request->bab5 ?? '';
        $config['lampiran'] = $request->lampiran ?? '';

        try {
            if (!is_array($config)) {
                $config = [];
            }
            $result = @file_put_contents($configPath, json_encode($config));
            if ($result === false) {
                throw new \Exception("Gagal menulis ke file $configPath. Pastikan folder storage/app memiliki izin write (chmod 775/777).");
            }
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['sistem' => $e->getMessage()])->withInput();
        }

        return redirect()->route('admin.laporan.preview', [
            'periode' => $request->periode,
            'prodi_id' => $request->prodi_id
        ]);
    }

    public function previewLaporan(Request $request)
    {
        $query = \App\Models\Jadwal::with(['dosen', 'mataKuliah', 'prodi', 'evaluations'])
            ->join('dosens', 'jadwals.dosen_id', '=', 'dosens.id')
            ->select('jadwals.*')
            ->has('evaluations')
            ->orderBy('dosens.name', 'asc');

        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }
        if ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }

        $jadwals = $query->get();

        foreach ($jadwals as $jadwal) {
            $totalScore = 0;
            $totalQuestions = 0;
            foreach ($jadwal->evaluations as $eval) {
                $answers = is_string($eval->answers) ? json_decode($eval->answers, true) : $eval->answers;
                if (is_array($answers)) {
                    $totalScore += array_sum($answers);
                    $totalQuestions += count($answers);
                }
            }
            $jadwal->average_score = $totalQuestions > 0 ? round($totalScore / $totalQuestions, 2) : 0;
            if ($jadwal->average_score >= 4.5) $jadwal->predikat = 'Sangat Baik';
            elseif ($jadwal->average_score >= 3.5) $jadwal->predikat = 'Baik';
            elseif ($jadwal->average_score >= 2.5) $jadwal->predikat = 'Cukup';
            elseif ($jadwal->average_score >= 1.5) $jadwal->predikat = 'Kurang';
            else $jadwal->predikat = 'Sangat Kurang';
        }

        $prodiStr = $request->prodi_id ? 'prodi_' . $request->prodi_id : 'univ';
        $configPath = storage_path("app/report_config_{$prodiStr}.json");
        $config = file_exists($configPath) ? json_decode(file_get_contents($configPath), true) : [];
        $prodis = \App\Models\Prodi::orderBy('name')->get();

        $questions = \App\Models\Question::where('is_active', true)->orderBy('section')->orderBy('order_num')->get();

        $this->calculateJadwalScores($jadwals);

        $chartData = [
            'Sangat Baik' => 0,
            'Baik' => 0,
            'Sedang' => 0,
            'Buruk' => 0,
            'Sangat Buruk' => 0,
        ];
        foreach ($jadwals as $jadwal) {
            if (isset($chartData[$jadwal->predikat])) {
                $chartData[$jadwal->predikat]++;
            }
        }

        return view('admin.laporan_buku', compact('jadwals', 'config', 'prodis', 'questions', 'chartData'));
    }

    private function calculateJadwalScores($jadwals)
    {
        // Cache questions mapping
        $questions = \App\Models\Question::all();
        $qSectionMap = [];
        foreach ($questions as $q) {
            $key = 'q_' . $q->id;
            if (strpos($q->section, 'A. PROSES') !== false) {
                $qSectionMap[$key] = 'A';
            } elseif (strpos($q->section, 'B. KAPABILITAS') !== false) {
                $qSectionMap[$key] = 'B';
            } elseif (strpos($q->section, 'C. KETERSEDIAAN') !== false) {
                $qSectionMap[$key] = 'C';
            }
        }

        foreach ($jadwals as $jadwal) {
            $totalRespondents = $jadwal->evaluations->count();
            
            $scoreA = 0;
            $scoreB = 0;
            $scoreC = 0;
            
            foreach ($jadwal->evaluations as $eval) {
                $answers = is_string($eval->answers) ? json_decode($eval->answers, true) : $eval->answers;
                if (is_array($answers)) {
                    foreach ($answers as $qId => $score) {
                        if (isset($qSectionMap[$qId])) {
                            if ($qSectionMap[$qId] === 'A') $scoreA += $score;
                            elseif ($qSectionMap[$qId] === 'B') $scoreB += $score;
                            elseif ($qSectionMap[$qId] === 'C') $scoreC += $score;
                        }
                    }
                }
            }
            
            $jadwal->avg_A = $totalRespondents > 0 ? round($scoreA / $totalRespondents, 2) : 0;
            $jadwal->avg_B = $totalRespondents > 0 ? round($scoreB / $totalRespondents, 2) : 0;
            $jadwal->avg_C = $totalRespondents > 0 ? round($scoreC / $totalRespondents, 2) : 0;
            $jadwal->average_score = $jadwal->avg_A + $jadwal->avg_B + $jadwal->avg_C;

            // Total Seluruh Indikator
            $x = $jadwal->average_score;
            if ($x >= 71.2) $jadwal->predikat = 'Sangat Baik';
            elseif ($x >= 58.1) $jadwal->predikat = 'Baik';
            elseif ($x >= 44.4) $jadwal->predikat = 'Sedang';
            elseif ($x >= 30.7) $jadwal->predikat = 'Buruk';
            else $jadwal->predikat = 'Sangat Buruk';

            // Proses Belajar Mengajar
            $xA = $jadwal->avg_A;
            if ($xA >= 38.2) $jadwal->pred_A = 'Sangat Baik';
            elseif ($xA >= 30.9) $jadwal->pred_A = 'Baik';
            elseif ($xA >= 23.6) $jadwal->pred_A = 'Sedang';
            elseif ($xA >= 16.3) $jadwal->pred_A = 'Buruk';
            else $jadwal->pred_A = 'Sangat Buruk';

            // Kapabilitas/Kompetensi Dosen
            $xB = $jadwal->avg_B;
            if ($xB >= 25.6) $jadwal->pred_B = 'Sangat Baik';
            elseif ($xB >= 20.7) $jadwal->pred_B = 'Baik';
            elseif ($xB >= 15.8) $jadwal->pred_B = 'Sedang';
            elseif ($xB >= 10.9) $jadwal->pred_B = 'Buruk';
            else $jadwal->pred_B = 'Sangat Buruk';

            // Sarana Prasarana
            $xC = $jadwal->avg_C;
            if ($xC >= 8.8) $jadwal->pred_C = 'Sangat Baik';
            elseif ($xC >= 7.1) $jadwal->pred_C = 'Baik';
            elseif ($xC >= 5.4) $jadwal->pred_C = 'Sedang';
            elseif ($xC >= 3.7) $jadwal->pred_C = 'Buruk';
            else $jadwal->pred_C = 'Sangat Buruk';
        }
    }
}
