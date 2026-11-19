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

        // Mengambil semua jadwal yang sesuai dengan filter beserta jumlah evaluasinya
        $jadwals = (clone $query)->with('dosen')
            ->withCount('evaluations')
            ->get();

        // Mengelompokkan berdasarkan dosen dan menjumlahkan semua evaluasi dari setiap jadwal dosen tersebut
        $topDosen = $jadwals->groupBy('dosen_id')->map(function ($jadwalGroup) {
            $dosen = $jadwalGroup->first()->dosen;
            return (object)[
                'dosen' => $dosen,
                'evaluations_count' => $jadwalGroup->sum('evaluations_count')
            ];
        })
        ->sortByDesc('evaluations_count')
        ->take(5)
        ->values();

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

    public function saran($id)
    {
        $jadwal = \App\Models\Jadwal::with(['dosen', 'mataKuliah', 'prodi'])->findOrFail($id);
        $evaluations = \App\Models\Evaluation::where('jadwal_id', $id)
            ->whereNotNull('saran')
            ->where('saran', '!=', '')
            ->get();
            
        return view('admin.saran', compact('jadwal', 'evaluations'));
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

    /**
     * Otentikasi ke SIMAK dan ambil URL Jadwal Mengajar serta CookieJar sesi.
     *
     * @param string $username
     * @param string $password
     * @param string|null $rawBaseUrl
     * @return array{targetUrl: string, baseUrl: string, cookieJar: \GuzzleHttp\Cookie\CookieJar}
     * @throws \Exception
     */
    private function getSimakJadwalUrl(string $username, string $password, ?string $rawBaseUrl = null): array
    {
        $cookieJar = new \GuzzleHttp\Cookie\CookieJar();
        
        $baseUrl = $rawBaseUrl ?? env('SIMAK_BASE_URL', 'https://simak.uss.ac.id');
        $baseUrl = rtrim($baseUrl, '/');
        
        // 1. Login
        \Illuminate\Support\Facades\Http::withOptions(['verify' => false, 'cookies' => $cookieJar])
            ->withHeaders(['Host' => 'simak.uss.ac.id'])
            ->asForm()
            ->post($baseUrl . '/login/proses/', [
                'username' => $username,
                'password' => $password
            ]);

        // 2. Akses Dashboard
        $dashRes = \Illuminate\Support\Facades\Http::withOptions(['verify' => false, 'cookies' => $cookieJar])
            ->withHeaders(['Host' => 'simak.uss.ac.id'])
            ->get($baseUrl . '/apps/dashboard/dashboard/');
            
        $html = $dashRes->body();
        
        // Cari link jadwal
        if (preg_match('/href="([^"]+\/apps\/dosen\/jadwal\/[^"]+)"/', $html, $matches)) {
            $targetUrl = $matches[1];
            if (!str_starts_with($targetUrl, 'http')) {
                $targetUrl = $baseUrl . $targetUrl;
            }

            return [
                'targetUrl' => $targetUrl,
                'baseUrl'   => $baseUrl,
                'cookieJar' => $cookieJar
            ];
        }

        throw new \Exception('Tidak dapat menemukan menu Jadwal Mengajar di dashboard SIMAK. Pastikan username dan password benar.');
    }

    public function getPeriods(Request $request)
    {
        try {
            $session = $this->getSimakJadwalUrl(
                $request->simak_username,
                $request->simak_password,
                $request->simak_url
            );

            // Akses Jadwal
            $jadwalRes = \Illuminate\Support\Facades\Http::withOptions(['verify' => false, 'cookies' => $session['cookieJar']])
                ->withHeaders(['Host' => 'simak.uss.ac.id'])
                ->get($session['targetUrl']);
                
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
            $session = $this->getSimakJadwalUrl(
                $request->simak_username,
                $request->simak_password,
                $request->simak_url
            );

            $targetUrl = $session['targetUrl'];
            $baseUrl   = $session['baseUrl'];
            $cookieJar = $session['cookieJar'];

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

            // Bangun URL Jadwal Global Kampus (Jadwal Ruangan)
            $parts = explode('/', rtrim($targetUrl, '/'));
            $periode_str = $parts[count($parts) - 2] ?? '';
            
            $ta_id = '11';
            $tipe = 'REGULER';
            // Regex mendukung "ta-12~REGULER" atau sejenisnya
            if (preg_match('/ta-(\d+)~([A-Z]+)/', $periode_str, $matches)) {
                $ta_id = $matches[1];
                $tipe = $matches[2];
            }

            // ID dosen diambil dari URL Jadwal Mengajar akun yang login (mis. /apps/dosen/jadwal/6172/...)
            // SIMAK hanya mengizinkan Jadwal Ruangan dengan ID milik akun yang sedang login
            $dosenUnitId = preg_match('#/apps/dosen/jadwal/(\d+)/#', $targetUrl, $mUnit) ? $mUnit[1] : '5670';
            $globalUrl = "{$baseUrl}/apps/krs/gedung/ruang/daftar/{$dosenUnitId}/0~{$ta_id}~{$tipe}/";
            
            // Akses Jadwal Global
            $jadwalRes = \Illuminate\Support\Facades\Http::withOptions(['verify' => false, 'cookies' => $cookieJar])
                ->withHeaders(['Host' => 'simak.uss.ac.id'])
                ->get($globalUrl);
                
            $jadwalHtml = $jadwalRes->body();

            if (stripos($jadwalHtml, 'LOGIN SISTEM AKADEMIK') !== false) {
                return redirect()->back()->with('error', 'Gagal: SIMAK menolak akses ke halaman Jadwal Ruangan (diarahkan ke halaman login). Coba login ulang atau gunakan akun lain.');
            }

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

            if ($count === 0) {
                return redirect()->back()->with('warning', "Peringatan: Tidak ada data mata kuliah yang disinkronisasi (0 data ditemukan) dari URL: {$globalUrl}. Pastikan periode yang dipilih sudah memiliki jadwal di SIMAK.");
            }

            return redirect()->back()->with('success', "Data global kampus berhasil ditarik. Total mata kuliah baru disinkronisasi: $count");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal melakukan scraping via PHP: ' . $e->getMessage());
        }
    }

    /**
     * Parse periode string into structured components (academic year, semester, report year, label).
     */
    private function parsePeriodeComponents(?string $periode): array
    {
        $raw = $periode ?: '';
        
        $tahunAkademik = '';
        $coverYear = date('Y');
        
        if (preg_match('/(\d{4})\s*[\/\-]\s*(\d{4})/', $raw, $m)) {
            $tahunAkademik = "{$m[1]}/{$m[2]}";
            $coverYear = $m[2];
        } else {
            $tahunAkademik = date('Y') . '/' . (date('Y') + 1);
        }

        $semester = '';
        if (stripos($raw, 'genap') !== false) {
            $semester = 'Genap';
        } elseif (stripos($raw, 'ganjil') !== false) {
            $semester = 'Ganjil';
        }

        $periodeLabel = trim(preg_replace('/\s+/', ' ', str_ireplace('REGULER', '', $raw)));
        if (empty($periodeLabel)) {
            $periodeLabel = trim("{$tahunAkademik} {$semester}");
        }

        return [
            'tahunAkademik'          => $tahunAkademik,
            'semester'               => $semester,
            'coverYear'              => $coverYear,
            'periodeLabel'           => $periodeLabel,
            'activePeriodeFormatted' => trim("{$tahunAkademik} {$semester}"),
        ];
    }

    private function getConfigPath($prodiId, $periode = null): array
    {
        $prodiStr = $prodiId ? 'prodi_' . $prodiId : 'univ';
        $basePath = storage_path("app/report_config_{$prodiStr}.json");
        
        $periodePath = null;
        if (!empty($periode)) {
            $slug = \Illuminate\Support\Str::slug($periode, '_');
            $periodePath = storage_path("app/report_config_{$prodiStr}_{$slug}.json");
        }

        return [$basePath, $periodePath];
    }

    private function loadReportConfig($prodiId, $periode = null): array
    {
        [$basePath, $periodePath] = $this->getConfigPath($prodiId, $periode);

        if ($periodePath && file_exists($periodePath)) {
            $data = json_decode(file_get_contents($periodePath), true);
            if (is_array($data)) return $data;
        }

        if (file_exists($basePath)) {
            $data = json_decode(file_get_contents($basePath), true);
            if (is_array($data)) return $data;
        }

        return [];
    }

    private function formatDynamicReportText(string $text, array $pComp, string $prodiName, int $totalResponden = 0, int $totalDosen = 0, string $rataRataPredikat = 'Sangat Baik'): string
    {
        if (empty($text)) {
            return '';
        }

        $text = str_replace(
            ['[NAMA_PRODI]', '[PERIODE]', '[TAHUN_AKADEMIK]', '[SEMESTER]', '[TAHUN]', '[TOTAL_RESPONDEN]', '[TOTAL_DOSEN]', '[RATA_RATA_PREDIKAT]'],
            [$prodiName, $pComp['periodeLabel'], $pComp['tahunAkademik'], $pComp['semester'], $pComp['coverYear'], $totalResponden, $totalDosen, $rataRataPredikat],
            $text
        );

        $text = preg_replace(
            '/(Tahun\s+Akademik\s+)\d{4}\s*[\/\-]\s*\d{4}\s+(Genap|Ganjil)(\s+Reguler)?/i',
            '${1}' . $pComp['tahunAkademik'] . ' ' . $pComp['semester'] . '${3}',
            $text
        );
        $text = preg_replace(
            '/(Semester\s+)(Genap|Ganjil)(\s+Tahun\s+Akademik\s+)\d{4}\s*[\/\-]\s*\d{4}/i',
            '${1}' . $pComp['semester'] . '${3}' . $pComp['tahunAkademik'],
            $text
        );
        $text = preg_replace(
            '/(Palembang,\s*(?:&nbsp;|\s)*)\d{4}/i',
            'Palembang, ' . $pComp['coverYear'],
            $text
        );

        return $text;
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

        $pComp = $this->parsePeriodeComponents($request->periode);
        $tahunAkademik = $pComp['tahunAkademik'];
        $semester = $pComp['semester'];
        $coverYear = $pComp['coverYear'];
        $activePeriodeFormatted = $pComp['activePeriodeFormatted'];

        $defaultProdiName = $request->prodi_id ? ($prodis->firstWhere('id', $request->prodi_id)->name ?? 'Ilmu Komputer') : 'Universitas Sumatera Selatan';
        $activeProdiName = $defaultProdiName;

        $defaultTotalResponden = \App\Models\Evaluation::whereIn('jadwal_id', $jadwals->pluck('id'))->count();
        $defaultTotalDosen = $jadwals->unique('dosen_id')->count();

        $defaultBab3 = "<p>Survey Kepuasan Mahasiswa Terhadap Dosen dalam Pengajaran Program Studi [NAMA_PRODI] dilakukan pada bulan [PERIODE] dengan menyebarkan kuesioner secara online. Penyebaran kuesioner dilakukan melalui SIMAK dengan menggunakan google drive. Hasil survey diperoleh jumlah responden yang memberikan jawaban sebanyak [TOTAL_RESPONDEN] responden dalam hal ini mahasiswa Program Studi [NAMA_PRODI].</p><p><strong>Dosen Objek Survey</strong><br>Jumlah Dosen Prodi [NAMA_PRODI] yang disurvey sebanyak [TOTAL_DOSEN] orang. Kelompok Dosen digambarkan pada grafik berikut.</p><p>[PIE_CHART_DOSEN]</p><p><strong>Penilaian terhadap Kinerja Dosen dalam Proses Belajar Mengajar, Kapabilitas/ Kompetensi Dosen dan Ketersediaan Sarana Prasaran</strong><br>Hasil survey Kepuasan Mahasiswa terhadap Kapabilitas/ Kompetensi Dosen dan Ketersediaan Sarana Prasaran memperlihatkan bahwa secara umum Dosen Program Studi [NAMA_PRODI] melaksanakan proses pembelajaran [RATA_RATA_PREDIKAT].</p><p><strong>Tabulasi Penilaian Kepuasan Mahasiswa terhadap Dosen dalam Pembelajaran</strong></p>";

        $defaultKataPengantar = "<p>Puji syukur ke hadirat Tuhan Yang Maha Esa atas segala rahmat dan karunia-Nya sehingga <strong>Laporan Hasil Evaluasi Dosen dalam Mengajar Tahun Akademik [TAHUN_AKADEMIK] [SEMESTER] Program Studi [NAMA_PRODI] Universitas Sumatera Selatan</strong> ini dapat disusun dengan baik. Evaluasi dosen dalam mengajar merupakan salah satu instrumen penting dalam pelaksanaan Sistem Penjaminan Mutu Internal (SPMI) untuk mengukur dan meningkatkan kualitas proses pembelajaran di lingkungan perguruan tinggi. Melalui evaluasi ini, mahasiswa diberikan kesempatan untuk memberikan umpan balik secara objektif terhadap pelaksanaan kegiatan pembelajaran yang dilakukan oleh dosen selama satu semester.</p><p>Laporan ini disusun berdasarkan hasil pengisian kuesioner oleh mahasiswa pada <strong>Tahun Akademik [TAHUN_AKADEMIK] [SEMESTER] Reguler</strong> di <strong>Program Studi [NAMA_PRODI] Universitas Sumatera Selatan</strong>. Hasil evaluasi diharapkan dapat menjadi bahan refleksi dan masukan bagi dosen, program studi, fakultas, serta universitas dalam melakukan perbaikan dan peningkatan mutu pembelajaran secara berkelanjutan.</p><p>Kami menyampaikan apresiasi dan ucapan terima kasih kepada seluruh mahasiswa yang telah berpartisipasi dalam pengisian kuesioner, para dosen yang senantiasa berkomitmen meningkatkan kualitas pengajaran, serta seluruh pihak yang telah mendukung terselenggaranya kegiatan evaluasi ini. Semangat kolaborasi dan budaya mutu yang terus dibangun diharapkan mampu mendukung terciptanya proses pembelajaran yang efektif, inovatif, dan berorientasi pada kebutuhan mahasiswa.</p><p>Semoga laporan ini dapat memberikan manfaat sebagai bahan evaluasi, pengambilan keputusan, dan perencanaan strategi peningkatan kualitas pendidikan di Program Studi [NAMA_PRODI] Universitas Sumatera Selatan.</p><p>&nbsp;</p><p><strong>Palembang, [TAHUN]</strong></p><p><strong>Lembaga Penjamin Mutu dan Akreditasi<br>Universitas Sumatera Selatan</strong></p>";

        $config = $this->loadReportConfig($request->prodi_id, $request->periode);

        if (empty($config)) {
            $config = [
                'kata_pengantar' => $defaultKataPengantar,
                'daftar_isi' => '',
                'bab1' => '',
                'bab2' => '',
                'bab3' => $defaultBab3,
                'bab4' => '',
                'bab5' => '',
                'lampiran' => '',
                'cover' => '',
                'tipe_cover' => 'standard'
            ];
        }

        if (empty($config['bab3'])) {
            $config['bab3'] = $defaultBab3;
        }

        if (empty($config['kata_pengantar'])) {
            $config['kata_pengantar'] = $defaultKataPengantar;
        }

        // Terapkan penyesuaian dinamis pada kata pengantar untuk periode aktif
        $config['kata_pengantar'] = $this->formatDynamicReportText(
            $config['kata_pengantar'],
            $pComp,
            $activeProdiName,
            $defaultTotalResponden,
            $defaultTotalDosen
        );

        return view('admin.laporan', compact(
            'jadwals',
            'filterPeriodes',
            'prodis',
            'config',
            'tahunAkademik',
            'semester',
            'coverYear',
            'activePeriodeFormatted',
            'activeProdiName'
        ));
    }

    public function cetakTabel(Request $request)
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

        $jadwals = $query->get();

        $prodi = null;
        if ($request->filled('prodi_id')) {
            $prodi = \App\Models\Prodi::find($request->prodi_id);
        }

        return view('admin.laporan_cetak_tabel', compact('jadwals', 'request', 'prodi'));
    }

    public function saveLaporanConfig(Request $request)
    {
        $request->validate([
            'cover' => 'nullable|file|image|max:10240', // Maksimal 10MB
            'surat_tugas' => 'nullable|file|image|max:10240',
            'dokumentasi' => 'nullable|file|image|max:10240',
        ], [
            'cover.max' => 'Ukuran file Cover maksimal adalah 10MB.',
            'cover.uploaded' => 'Gagal mengupload cover. Pastikan ukuran file tidak terlalu besar (maks 10MB).',
            'surat_tugas.max' => 'Ukuran file Surat Tugas maksimal adalah 10MB.',
            'surat_tugas.uploaded' => 'Gagal mengupload Surat Tugas. Pastikan ukuran file tidak terlalu besar.',
            'dokumentasi.max' => 'Ukuran file Dokumentasi maksimal adalah 10MB.',
            'dokumentasi.uploaded' => 'Gagal mengupload Dokumentasi. Pastikan ukuran file tidak terlalu besar.',
            'image' => 'File yang diupload harus berupa gambar (JPG/PNG).',
        ]);

        [$basePath, $periodePath] = $this->getConfigPath($request->prodi_id, $request->periode);
        $config = $this->loadReportConfig($request->prodi_id, $request->periode);

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

        $config['tipe_cover'] = $request->tipe_cover ?? ($config['tipe_cover'] ?? 'standard');
        $config['kata_pengantar'] = $request->kata_pengantar ?? '';
        $config['lembar_pengesahan'] = $request->lembar_pengesahan ?? '';
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
            $savePath = $periodePath ?? $basePath;
            $result = @file_put_contents($savePath, json_encode($config));
            if ($result === false) {
                throw new \Exception("Gagal menulis ke file $savePath. Pastikan folder storage/app memiliki izin write (chmod 775/777).");
            }

            if (!file_exists($basePath)) {
                @file_put_contents($basePath, json_encode($config));
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

        $config = $this->loadReportConfig($request->prodi_id, $request->periode);
        $prodis = \App\Models\Prodi::orderBy('name')->get();

        $pComp = $this->parsePeriodeComponents($request->periode);
        $tahunAkademik = $pComp['tahunAkademik'];
        $semester = $pComp['semester'];
        $coverYear = $pComp['coverYear'];
        $activePeriodeFormatted = $pComp['activePeriodeFormatted'];

        $activeProdiName = $request->prodi_id 
            ? ($prodis->firstWhere('id', $request->prodi_id)->name ?? 'Ilmu Komputer') 
            : 'Universitas Sumatera Selatan';

        $activeTotalResponden = \App\Models\Evaluation::whereIn('jadwal_id', $jadwals->pluck('id'))->count();
        $activeTotalDosen = $jadwals->unique('dosen_id')->count();

        $defaultKataPengantar = "<p>Puji syukur ke hadirat Tuhan Yang Maha Esa atas segala rahmat dan karunia-Nya sehingga <strong>Laporan Hasil Evaluasi Dosen dalam Mengajar Tahun Akademik [TAHUN_AKADEMIK] [SEMESTER] Program Studi [NAMA_PRODI] Universitas Sumatera Selatan</strong> ini dapat disusun dengan baik. Evaluasi dosen dalam mengajar merupakan salah satu instrumen penting dalam pelaksanaan Sistem Penjaminan Mutu Internal (SPMI) untuk mengukur dan meningkatkan kualitas proses pembelajaran di lingkungan perguruan tinggi. Melalui evaluasi ini, mahasiswa diberikan kesempatan untuk memberikan umpan balik secara objektif terhadap pelaksanaan kegiatan pembelajaran yang dilakukan oleh dosen selama satu semester.</p><p>Laporan ini disusun berdasarkan hasil pengisian kuesioner oleh mahasiswa pada <strong>Tahun Akademik [TAHUN_AKADEMIK] [SEMESTER] Reguler</strong> di <strong>Program Studi [NAMA_PRODI] Universitas Sumatera Selatan</strong>. Hasil evaluasi diharapkan dapat menjadi bahan refleksi dan masukan bagi dosen, program studi, fakultas, serta universitas dalam melakukan perbaikan dan peningkatan mutu pembelajaran secara berkelanjutan.</p><p>Kami menyampaikan apresiasi dan ucapan terima kasih kepada seluruh mahasiswa yang telah berpartisipasi dalam pengisian kuesioner, para dosen yang senantiasa berkomitmen meningkatkan kualitas pengajaran, serta seluruh pihak yang telah mendukung terselenggaranya kegiatan evaluasi ini. Semangat kolaborasi dan budaya mutu yang terus dibangun diharapkan mampu mendukung terciptanya proses pembelajaran yang efektif, inovatif, dan berorientasi pada kebutuhan mahasiswa.</p><p>Semoga laporan ini dapat memberikan manfaat sebagai bahan evaluasi, pengambilan keputusan, dan perencanaan strategi peningkatan kualitas pendidikan di Program Studi [NAMA_PRODI] Universitas Sumatera Selatan.</p><p>&nbsp;</p><p><strong>Palembang, [TAHUN]</strong></p><p><strong>Lembaga Penjamin Mutu dan Akreditasi<br>Universitas Sumatera Selatan</strong></p>";

        $defaultBab3 = "<p>Survey Kepuasan Mahasiswa Terhadap Dosen dalam Pengajaran Program Studi [NAMA_PRODI] dilakukan pada bulan [PERIODE] dengan menyebarkan kuesioner secara online. Penyebaran kuesioner dilakukan melalui SIMAK dengan menggunakan google drive. Hasil survey diperoleh jumlah responden yang memberikan jawaban sebanyak [TOTAL_RESPONDEN] responden dalam hal ini mahasiswa Program Studi [NAMA_PRODI].</p><p><strong>Dosen Objek Survey</strong><br>Jumlah Dosen Prodi [NAMA_PRODI] yang disurvey sebanyak [TOTAL_DOSEN] orang. Kelompok Dosen digambarkan pada grafik berikut.</p><p>[PIE_CHART_DOSEN]</p><p><strong>Penilaian terhadap Kinerja Dosen dalam Proses Belajar Mengajar, Kapabilitas/ Kompetensi Dosen dan Ketersediaan Sarana Prasaran</strong><br>Hasil survey Kepuasan Mahasiswa terhadap Kapabilitas/ Kompetensi Dosen dan Ketersediaan Sarana Prasaran memperlihatkan bahwa secara umum Dosen Program Studi [NAMA_PRODI] melaksanakan proses pembelajaran [RATA_RATA_PREDIKAT].</p><p><strong>Tabulasi Penilaian Kepuasan Mahasiswa terhadap Dosen dalam Pembelajaran</strong></p><p>Adapun penjabaran secara terperinci mengenai frekuensi jawaban responden untuk setiap instrumen pertanyaan dapat dilihat pada grafik batang di <strong>Lampiran 2: Grafik Hasil Survei Instrumen</strong>.</p>";

        $defaultBab4 = "<p>Berdasarkan metodologi yang diuraikan pada Bab II dan hasil survei evaluasi pada Bab III, dapat dianalisis beberapa temuan penting mengenai kualitas pembelajaran di Program Studi [NAMA_PRODI] pada periode [PERIODE]:</p><ol><li><strong>Proses Belajar Mengajar (PBM):</strong> Rata-rata penilaian mahasiswa menunjukkan kategori <strong>[PREDIKAT_PBM]</strong> (Skor: [SKOR_PBM]). Hal ini mengindikasikan bahwa secara umum dosen telah merencanakan materi, menyampaikan materi, serta mendisiplinkan jadwal perkuliahan dengan sangat baik.</li><li><strong>Kapabilitas / Kompetensi Dosen (KKD):</strong> Rata-rata penilaian mahasiswa berada pada kategori <strong>[PREDIKAT_KKD]</strong> (Skor: [SKOR_KKD]). Kompetensi dosen dalam penguasaan materi, kejelasan penyampaian, dan interaksi diskusi dinilai memuaskan oleh mahasiswa.</li><li><strong>Ketersediaan Sarana Prasarana (KSP):</strong> Rata-rata penilaian terkait pemanfaatan fasilitas dan sarana prasarana berada pada kategori <strong>[PREDIKAT_KSP]</strong> (Skor: [SKOR_KSP]). Ketersediaan alat bantu mengajar dinilai mendukung kelancaran proses perkuliahan.</li></ol><p>Secara keseluruhan, kinerja pengajaran di lingkungan Program Studi [NAMA_PRODI] masuk dalam kategori <strong>[RATA_RATA_PREDIKAT]</strong>. Meskipun hasil survei menunjukkan angka yang positif, evaluasi berkelanjutan tetap diperlukan untuk mempertahankan dan meningkatkan standar mutu pendidikan.</p>";

        $defaultBab5 = "<p><strong>A. Kesimpulan</strong></p><p>Berdasarkan data dan analisis survei kepuasan mahasiswa terhadap kinerja pengajaran dosen di Program Studi [NAMA_PRODI] pada periode [PERIODE], dapat ditarik kesimpulan sebagai berikut:</p><ol><li>Secara garis besar, proses pembelajaran telah berjalan dengan <strong>[RATA_RATA_PREDIKAT]</strong>. Hal ini menunjukkan komitmen yang kuat dari tenaga pendidik dalam menyelenggarakan kegiatan akademik yang berkualitas.</li><li>Aspek <strong>Proses Belajar Mengajar (PBM)</strong> mendapatkan predikat [PREDIKAT_PBM], aspek <strong>Kapabilitas/Kompetensi Dosen (KKD)</strong> mendapatkan predikat [PREDIKAT_KKD], dan aspek <strong>Ketersediaan Sarana Prasarana (KSP)</strong> mendapatkan predikat [PREDIKAT_KSP].</li><li>Antusiasme dan partisipasi mahasiswa dalam memberikan umpan balik (sebanyak [TOTAL_RESPONDEN] respons untuk [TOTAL_DOSEN] dosen) mencerminkan kepedulian yang tinggi terhadap perbaikan mutu pendidikan di lingkungan kampus.</li></ol><p><strong>B. Rekomendasi</strong></p><p>Untuk mewujudkan lingkungan belajar yang lebih optimal di masa mendatang, diusulkan beberapa rekomendasi berikut:</p><ol><li><strong>Bagi Dosen:</strong> Diharapkan dapat mempertahankan metode pengajaran yang sudah dinilai baik oleh mahasiswa dan terus berinovasi dalam mengadopsi teknologi pembelajaran terkini guna meningkatkan interaktivitas.</li><li><strong>Bagi Program Studi:</strong> Disarankan untuk memfasilitasi pelatihan berkelanjutan terkait pedagogi dan penggunaan media ajar inovatif, terutama untuk indikator-indikator yang masih berpeluang untuk ditingkatkan.</li><li><strong>Bagi Institusi:</strong> Meninjau kembali dan memelihara sarana prasarana penunjang perkuliahan secara berkala agar mahasiswa dan dosen selalu merasa nyaman selama proses belajar mengajar berlangsung.</li></ol>";

        if (empty($config['kata_pengantar'])) {
            $config['kata_pengantar'] = $defaultKataPengantar;
        }
        if (empty($config['bab3'])) {
            $config['bab3'] = $defaultBab3;
        }
        if (empty($config['bab4'])) {
            $config['bab4'] = $defaultBab4;
        }
        if (empty($config['bab5'])) {
            $config['bab5'] = $defaultBab5;
        }

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

        $overallPredikat = 'Sangat Baik';
        if ($jadwals->isNotEmpty()) {
            $avgScore = $jadwals->avg('average_score');
            if ($avgScore >= 71.2) $overallPredikat = 'Sangat Baik';
            elseif ($avgScore >= 58.1) $overallPredikat = 'Baik';
            elseif ($avgScore >= 44.4) $overallPredikat = 'Sedang';
            elseif ($avgScore >= 30.7) $overallPredikat = 'Buruk';
            else $overallPredikat = 'Sangat Buruk';
        }

        $kataPengantarContent = $this->formatDynamicReportText(
            $config['kata_pengantar'],
            $pComp,
            $activeProdiName,
            $activeTotalResponden,
            $activeTotalDosen,
            $overallPredikat
        );

        $bab1Content = $this->formatDynamicReportText(
            $config['bab1'] ?? '',
            $pComp,
            $activeProdiName,
            $activeTotalResponden,
            $activeTotalDosen,
            $overallPredikat
        );

        $bab2Content = $this->formatDynamicReportText(
            $config['bab2'] ?? '',
            $pComp,
            $activeProdiName,
            $activeTotalResponden,
            $activeTotalDosen,
            $overallPredikat
        );

        $lembarPengesahanContent = $this->formatDynamicReportText(
            $config['lembar_pengesahan'] ?? '',
            $pComp,
            $activeProdiName,
            $activeTotalResponden,
            $activeTotalDosen,
            $overallPredikat
        );

        return view('admin.laporan_buku', compact(
            'jadwals',
            'config',
            'prodis',
            'questions',
            'chartData',
            'tahunAkademik',
            'semester',
            'coverYear',
            'activePeriodeFormatted',
            'activeProdiName',
            'kataPengantarContent',
            'bab1Content',
            'bab2Content',
            'lembarPengesahanContent'
        ));
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
