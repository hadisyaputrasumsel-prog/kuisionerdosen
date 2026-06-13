<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prodi;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\Jadwal;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'title' => 'A. PROSES BELAJAR MENGAJAR',
                'questions' => [
                    'Rencana materi dalam bentuk Rencana Pembelajaran Semester (RPS) dan tujuan mata kuliah dijelaskan saat awal semester perkuliahan',
                    'Dosen datang tepat waktu dan mengajar sesuai jadwal perkuliahan, kecuali berhalangan/ ditugaskan (Surat Tugas diinformasikan)',
                    'Diadakan tanya jawab, diskusi dan pembahasan latihan soal dalam proses pembelajaran',
                    'Manfaat soal latihan atau studi kasus dalam menambah pemahanan mata kuliah ini',
                    'Kesesuain evaluasi (tugas, kuis, UTS dan UAS) dengan materi yang diajarkan',
                    'Pembahasan (integrasi) hasil Penelitian dan/ atau hasil Pengabdian kepada Masyarakat yang berhubungan dengan mata kuliah',
                    'Sistematika menjelaskan kuliah (dosen menerangkan dengan baik kriteria penilaian secara rasional dan sesuai dengan aturan yang berlaku saat perjanjian pra kuliah)',
                    'Latihan soal terhadap setiap materi yang diberikan',
                    'Kesesuaian materi dan/ atau praktikum yang diberikan terhadap bahan kuliah'
                ]
            ],
            [
                'title' => 'B. KAPABILITAS (KOMPETENSI DOSEN)',
                'questions' => [
                    'Kemampuan dosen dalam menjelaskan materi perkuliahan',
                    'Penguasaan materi, wawasan, materi perkuliahan dan praktikum',
                    'Kemampuan dosen menjawab pertanyaan',
                    'Penggunaan media pembelajaran pendukung seperti video, quizizz, kahoot, mentimeter dll',
                    'Kemampuan dosen dalam memberikan motivasi/ membangkitkan minat belajar, menghidupkan suasana kelas dan mendorong mahasiswa untuk bersikap/ berperilaku serta berbudi pekerti luhur',
                    'Kemampuan dosen mendorong mahasiswa/i untuk melakukan riset dan berkarya sesuai dengan bidang keahliannya'
                ]
            ],
            [
                'title' => 'C. KETERSEDIAAN SARANA',
                'questions' => [
                    'Bahan ajar (handout/ filet ppt/ canva) tersedia dengan baik dan dibagikan melalui SIMAK atau LMS',
                    'Buku referensi (textbook) diinformasikan dan tersedia'
                ]
            ]
        ];

        $order = 1;
        foreach ($sections as $sec) {
            foreach ($sec['questions'] as $q) {
                \App\Models\Question::firstOrCreate([
                    'section' => $sec['title'],
                    'question_text' => $q,
                ], [
                    'is_active' => true,
                    'order_num' => $order++
                ]);
            }
        }

        $csvFile = base_path('semua_jadwal_ruangan.csv');
        
        if (!file_exists($csvFile)) {
            // Fallback ke dummy data jika CSV tidak ada
            $prodi1 = Prodi::create(['name' => 'Ilmu Komunikasi']);
            $prodi2 = Prodi::create(['name' => 'Manajemen']);
            $prodi3 = Prodi::create(['name' => 'Ilmu Komputer']);

            $dosen1 = Dosen::create(['name' => 'Dr. Hadi Syaputra, S.Kom., M.Kom.']);
            $dosen2 = Dosen::create(['name' => 'Intan Putri, S.I.Kom,. M.I.Kom']);
            $dosen3 = Dosen::create(['name' => 'Rikkie Dekas, S.E., M.M.']);

            $mk1 = MataKuliah::create(['name' => 'PENGOLAHAN CITRA', 'code' => 'IKM244321']);
            $mk2 = MataKuliah::create(['name' => 'KOMUNIKASI MASSA', 'code' => 'KOM101']);
            $mk3 = MataKuliah::create(['name' => 'PENGANGGARAN PERUSAHAAN', 'code' => 'MAN201']);

            Jadwal::create(['prodi_id' => $prodi3->id, 'dosen_id' => $dosen1->id, 'mata_kuliah_id' => $mk1->id]);
            Jadwal::create(['prodi_id' => $prodi1->id, 'dosen_id' => $dosen2->id, 'mata_kuliah_id' => $mk2->id]);
            Jadwal::create(['prodi_id' => $prodi2->id, 'dosen_id' => $dosen3->id, 'mata_kuliah_id' => $mk3->id]);
            return;
        }

        $file = fopen($csvFile, 'r');
        $headers = fgetcsv($file); // Skip header

        // Cache array agar query lebih efisien
        $prodis = [];
        $dosens = [];
        $matakuliahs = [];

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) < 3) continue;
            
            $dosenName = trim($row[0]);
            $mkRaw = trim($row[1]);
            $prodiName = trim($row[2]);

            // Skip data kosong
            if (empty($dosenName) || empty($mkRaw) || empty($prodiName)) continue;

            // Pisahkan nama MK dan SKS (contoh: "PENGOLAHAN CITRA - 3 SKS")
            $mkParts = explode(' - ', $mkRaw);
            $mkName = trim($mkParts[0]);

            // 1. Simpan/Ambil Prodi
            if (!isset($prodis[$prodiName])) {
                $prodi = Prodi::firstOrCreate(['name' => $prodiName]);
                $prodis[$prodiName] = $prodi->id;
            }

            // 2. Simpan/Ambil Dosen
            if (!isset($dosens[$dosenName])) {
                $dosen = Dosen::firstOrCreate(['name' => $dosenName]);
                $dosens[$dosenName] = $dosen->id;
            }

            // 3. Simpan/Ambil MataKuliah
            if (!isset($matakuliahs[$mkName])) {
                $mk = MataKuliah::firstOrCreate(['name' => $mkName]);
                $matakuliahs[$mkName] = $mk->id;
            }

            // 4. Simpan Jadwal (relasi ketiganya)
            Jadwal::firstOrCreate([
                'prodi_id' => $prodis[$prodiName],
                'dosen_id' => $dosens[$dosenName],
                'mata_kuliah_id' => $matakuliahs[$mkName]
            ]);
        }
        fclose($file);
    }
}
