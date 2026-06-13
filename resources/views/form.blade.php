<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuisioner Evaluasi Dosen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f0ebf8; }
        .form-header { border-top: 10px solid #673ab7; }
    </style>
</head>
<body class="py-8 px-4">

    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md mb-6 p-6 form-header">
            <h1 class="text-3xl font-semibold mb-2">Evaluasi Dosen dalam Mengajar Tahun Akademik 2025/2026 Genap</h1>
            <div class="text-gray-600 leading-relaxed">
                <p>Kuisioner ini merupakan instrumen pengukuran kinerja dosen dalam melakukan Tri Dharma Pendidikan berupa pendidikan/ pengajaran.</p>
                <p>Isikan sesuai penilaian Anda terhadap setiap dosen dengan pilihan berikut :</p>
                <table class="mt-2">
                    <tbody>
                        <tr><td class="pr-4">SB</td><td class="pr-4">= Sangat Baik</td><td>= 5</td></tr>
                        <tr><td class="pr-4">B</td><td class="pr-4">= Baik</td><td>= 4</td></tr>
                        <tr><td class="pr-4">C</td><td class="pr-4">= Cukup</td><td>= 3</td></tr>
                        <tr><td class="pr-4">KB</td><td class="pr-4">= Kurang Baik</td><td>= 2</td></tr>
                        <tr><td class="pr-4">SKB</td><td class="pr-4">= Sangat Kurang Baik</td><td>= 1</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <form action="{{ route('form.submit') }}" method="POST" id="kuisionerForm">
            @csrf

            <!-- Section 1: Prodi -->
            <div class="bg-white rounded-lg shadow-md mb-6 p-6">
                <label for="prodi_id" class="block text-lg font-medium text-gray-800 mb-4">Pilih Program Studi Anda <span class="text-red-500">*</span></label>
                <select id="prodi_id" name="prodi_id" class="w-full border-gray-300 rounded-md shadow-sm p-3 border focus:border-purple-500 focus:ring focus:ring-purple-200 transition" required>
                    <option value="" disabled selected>-- Pilih Program Studi --</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}">{{ $prodi->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Section 2: Jadwal (Dosen & Mata Kuliah) -->
            <div id="jadwal_section" class="bg-white rounded-lg shadow-md mb-6 p-6 hidden">
                <label for="jadwal_id" class="block text-lg font-medium text-gray-800 mb-4">Pilih Dosen dan Mata Kuliah <span class="text-red-500">*</span></label>
                <select id="jadwal_id" name="jadwal_id" class="w-full border-gray-300 rounded-md shadow-sm p-3 border focus:border-purple-500 focus:ring focus:ring-purple-200 transition" required disabled>
                    <option value="" disabled selected>-- Pilih Dosen & Mata Kuliah --</option>
                </select>
            </div>

            <!-- Section 3: Kuisioner -->
            <div id="questions_section" class="hidden">
                @php
                    $sections = [
                        [
                            'title' => 'A. PROSES BELAJAR MENGAJAR',
                            'questions' => [
                                ['id' => 'q1', 'title' => 'Rencana materi dalam bentuk Rencana Pembelajaran Semester (RPS) dan tujuan mata kuliah dijelaskan saat awal semester perkuliahan'],
                                ['id' => 'q2', 'title' => 'Dosen datang tepat waktu dan mengajar sesuai jadwal perkuliahan, kecuali berhalangan/ ditugaskan (Surat Tugas diinformasikan)'],
                                ['id' => 'q3', 'title' => 'Diadakan tanya jawab, diskusi dan pembahasan latihan soal dalam proses pembelajaran'],
                                ['id' => 'q4', 'title' => 'Manfaat soal latihan atau studi kasus dalam menambah pemahanan mata kuliah ini'],
                                ['id' => 'q5', 'title' => 'Kesesuain evaluasi (tugas, kuis, UTS dan UAS) dengan materi yang diajarkan'],
                                ['id' => 'q6', 'title' => 'Pembahasan (integrasi) hasil Penelitian dan/ atau hasil Pengabdian kepada Masyarakat yang berhubungan dengan mata kuliah'],
                                ['id' => 'q7', 'title' => 'Sistematika menjelaskan kuliah (dosen menerangkan dengan baik kriteria penilaian secara rasional dan sesuai dengan aturan yang berlaku saat perjanjian pra kuliah)'],
                                ['id' => 'q8', 'title' => 'Latihan soal terhadap setiap materi yang diberikan'],
                                ['id' => 'q9', 'title' => 'Kesesuaian materi dan/ atau praktikum yang diberikan terhadap bahan kuliah']
                            ]
                        ],
                        [
                            'title' => 'B. KAPABILITAS (KOMPETENSI DOSEN)',
                            'questions' => [
                                ['id' => 'q10', 'title' => 'Kemampuan dosen dalam menjelaskan materi perkuliahan'],
                                ['id' => 'q11', 'title' => 'Penguasaan materi, wawasan, materi perkuliahan dan praktikum'],
                                ['id' => 'q12', 'title' => 'Kemampuan dosen menjawab pertanyaan'],
                                ['id' => 'q13', 'title' => 'Penggunaan media pembelajaran pendukung seperti video, quizizz, kahoot, mentimeter dll'],
                                ['id' => 'q14', 'title' => 'Kemampuan dosen dalam memberikan motivasi/ membangkitkan minat belajar, menghidupkan suasana kelas dan mendorong mahasiswa untuk bersikap/ berperilaku serta berbudi pekerti luhur'],
                                ['id' => 'q15', 'title' => 'Kemampuan dosen mendorong mahasiswa/i untuk melakukan riset dan berkarya sesuai dengan bidang keahliannya']
                            ]
                        ],
                        [
                            'title' => 'C. KETERSEDIAAN SARANA',
                            'questions' => [
                                ['id' => 'q16', 'title' => 'Bahan ajar (handout/ filet ppt/ canva) tersedia dengan baik dan dibagikan melalui SIMAK atau LMS'],
                                ['id' => 'q17', 'title' => 'Buku referensi (textbook) diinformasikan dan tersedia']
                            ]
                        ]
                    ];
                    $questionNumber = 1;
                @endphp

                @foreach($sections as $section)
                    <div class="bg-indigo-50 rounded-t-lg shadow-sm mb-4 p-5 border border-indigo-100 border-t-4 border-t-indigo-500 mt-8 first:mt-0">
                        <h2 class="text-xl font-semibold text-gray-800">{{ $section['title'] }}</h2>
                    </div>

                    @foreach($section['questions'] as $q)
                    <div class="bg-white rounded-lg shadow-sm mb-6 p-6 border border-gray-200">
                        <label class="block text-base font-medium text-gray-800 mb-6">{{ $questionNumber++ }}. {{ $q['title'] }} <span class="text-red-500">*</span></label>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center justify-center space-y-4 sm:space-y-0 sm:space-x-8">
                            <span class="text-sm text-gray-600 hidden sm:block w-32 text-right">Sangat Kurang Baik</span>
                            <div class="flex justify-between sm:justify-center w-full sm:w-auto space-x-0 sm:space-x-8 px-2 sm:px-0">
                                @for($i = 1; $i <= 5; $i++)
                                    <label class="flex flex-col items-center space-y-3 cursor-pointer">
                                        <span class="text-gray-500 text-sm font-medium">{{ $i }}</span>
                                        <input type="radio" name="{{ $q['id'] }}" value="{{ $i }}" class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-gray-300" required>
                                    </label>
                                @endfor
                            </div>
                            <span class="text-sm text-gray-600 hidden sm:block w-32 text-left">Sangat Baik</span>
                        </div>
                        <div class="flex justify-between sm:hidden mt-6 px-1 text-sm text-gray-600">
                            <span>Sangat Kurang Baik</span>
                            <span>Sangat Baik</span>
                        </div>
                    </div>
                    @endforeach
                @endforeach

                <div class="bg-white rounded-lg shadow-md mb-6 p-6">
                    <label for="saran" class="block text-lg font-medium text-gray-800 mb-4">Saran / Masukan untuk Dosen</label>
                    <textarea id="saran" name="saran" rows="4" class="w-full border-gray-300 rounded-md shadow-sm p-3 border focus:border-purple-500 focus:ring focus:ring-purple-200 transition" placeholder="Tuliskan saran Anda di sini..."></textarea>
                </div>

                <div class="flex justify-between items-center bg-white rounded-lg shadow-md p-6">
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-6 rounded-md shadow transition duration-200">
                        Kirim
                    </button>
                    <button type="reset" class="text-purple-600 font-medium hover:text-purple-800 transition">Kosongkan formulir</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('prodi_id').addEventListener('change', function() {
            let prodiId = this.value;
            let jadwalSection = document.getElementById('jadwal_section');
            let jadwalSelect = document.getElementById('jadwal_id');
            let questionsSection = document.getElementById('questions_section');

            // Reset
            jadwalSelect.innerHTML = '<option value="" disabled selected>Loading...</option>';
            jadwalSelect.disabled = true;
            jadwalSection.classList.remove('hidden');
            questionsSection.classList.add('hidden');

            fetch(`/get-jadwals/${prodiId}`)
                .then(response => response.json())
                .then(data => {
                    jadwalSelect.innerHTML = '<option value="" disabled selected>-- Pilih Dosen & Mata Kuliah --</option>';
                    if(data.length === 0) {
                        jadwalSelect.innerHTML += '<option value="" disabled>Belum ada jadwal untuk Prodi ini</option>';
                    } else {
                        data.forEach(item => {
                            jadwalSelect.innerHTML += `<option value="${item.id}">${item.dosen} - ${item.matakuliah}</option>`;
                        });
                        jadwalSelect.disabled = false;
                    }
                });
        });

        document.getElementById('jadwal_id').addEventListener('change', function() {
            if(this.value) {
                document.getElementById('questions_section').classList.remove('hidden');
            }
        });
    </script>
</body>
</html>
