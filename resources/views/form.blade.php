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
            <h1 class="text-3xl font-semibold mb-2">Evaluasi Dosen & Mata Kuliah</h1>
            <p class="text-gray-600">Silakan isi kuisioner berikut untuk menilai kinerja dosen dalam proses pembelajaran. Masukan Anda sangat berarti bagi peningkatan kualitas pendidikan.</p>
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
                    $questions = [
                        ['id' => 'q1', 'title' => 'Kompetensi Pedagogik', 'desc' => 'Kemampuan dosen dalam merencanakan, melaksanakan, dan mengevaluasi pembelajaran.'],
                        ['id' => 'q2', 'title' => 'Kompetensi Profesional', 'desc' => 'Penguasaan materi pembelajaran secara luas dan mendalam oleh dosen.'],
                        ['id' => 'q3', 'title' => 'Kompetensi Kepribadian', 'desc' => 'Sikap dan perilaku dosen sebagai teladan yang baik bagi mahasiswa.'],
                        ['id' => 'q4', 'title' => 'Kompetensi Sosial', 'desc' => 'Kemampuan dosen berkomunikasi dan bergaul dengan mahasiswa secara efektif.']
                    ];
                @endphp

                @foreach($questions as $index => $q)
                <div class="bg-white rounded-lg shadow-md mb-6 p-6">
                    <label class="block text-lg font-medium text-gray-800 mb-2">{{ $index + 1 }}. {{ $q['title'] }} <span class="text-red-500">*</span></label>
                    <p class="text-sm text-gray-500 mb-4">{{ $q['desc'] }}</p>
                    
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-6">
                        <span class="text-sm text-gray-500 hidden sm:block">Sangat Kurang</span>
                        @for($i = 1; $i <= 5; $i++)
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="{{ $q['id'] }}" value="{{ $i }}" class="w-5 h-5 text-purple-600 focus:ring-purple-500 border-gray-300" required>
                                <span class="sm:hidden text-gray-700">{{ $i }}</span>
                            </label>
                        @endfor
                        <span class="text-sm text-gray-500 hidden sm:block">Sangat Baik</span>
                    </div>
                    <div class="flex justify-between sm:hidden mt-2 px-1 text-xs text-gray-500">
                        <span>Sangat Kurang (1)</span>
                        <span>Sangat Baik (5)</span>
                    </div>
                </div>
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
