<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuisioner Evaluasi Dosen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
        }
        .fade-in {
            animation: fadeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Hide scrollbar for select elements but keep functionality */
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
        }
    </style>
</head>
<body class="py-10 px-4 min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 text-gray-800 relative overflow-x-hidden">

    <!-- Decorative Background Elements -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-purple-300/30 blur-[100px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-indigo-300/30 blur-[100px]"></div>
    </div>

    <div class="max-w-3xl mx-auto">
        
        <!-- University Branding -->
        <div class="text-center mb-8 fade-in" style="animation-delay: 0.1s;">
            <img src="{{ asset('uss-logo.png') }}" alt="Logo Universitas Sumatera Selatan" class="h-24 md:h-28 mx-auto mb-4 object-contain drop-shadow-md" onerror="this.style.display='none'">
            <h2 class="text-xl md:text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-teal-700 to-indigo-800 tracking-widest uppercase">Universitas Sumatera Selatan</h2>
        </div>

        <!-- Header -->
        <div class="glass-card rounded-2xl mb-8 p-8 md:p-10 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-teal-500 via-indigo-500 to-purple-500"></div>
            
            <div class="relative z-10">
                <h1 class="text-3xl md:text-4xl font-extrabold mb-4 tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-teal-700 to-indigo-700">
                    Evaluasi Dosen dalam Mengajar <br class="hidden md:block"/>Tahun Akademik {{ $periodeLabel }}
                </h1>
                <div class="text-gray-600 leading-relaxed space-y-4">
                    <p class="text-lg">Kuisioner ini merupakan instrumen pengukuran kinerja dosen dalam melakukan Tri Dharma Pendidikan berupa pendidikan/ pengajaran.</p>
                    <div class="bg-indigo-50/50 rounded-xl p-5 border border-indigo-100/50">
                        <p class="font-medium text-indigo-900 mb-3">Panduan Penilaian:</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-2 gap-x-4 text-sm font-medium">
                            <div class="flex items-center space-x-2"><span class="w-6 h-6 rounded bg-indigo-600 text-white flex items-center justify-center">5</span><span>Sangat Baik (SB)</span></div>
                            <div class="flex items-center space-x-2"><span class="w-6 h-6 rounded bg-indigo-500 text-white flex items-center justify-center">4</span><span>Baik (B)</span></div>
                            <div class="flex items-center space-x-2"><span class="w-6 h-6 rounded bg-indigo-400 text-white flex items-center justify-center">3</span><span>Cukup (C)</span></div>
                            <div class="flex items-center space-x-2"><span class="w-6 h-6 rounded bg-indigo-300 text-white flex items-center justify-center">2</span><span>Kurang Baik (KB)</span></div>
                            <div class="flex items-center space-x-2 md:col-span-2"><span class="w-6 h-6 rounded bg-indigo-200 text-indigo-800 flex items-center justify-center">1</span><span>Sangat Kurang Baik (SKB)</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('form.submit') }}" method="POST" id="kuisionerForm" class="space-y-8">
            @csrf

            <!-- Section 1: Prodi -->
            <div class="glass-card rounded-2xl p-8 transition-all duration-300 hover:shadow-lg focus-within:ring-2 focus-within:ring-indigo-500/20">
                <label for="prodi_id" class="block text-lg font-semibold text-gray-800 mb-3">Pilih Program Studi Anda <span class="text-pink-500">*</span></label>
                <div class="relative">
                    <select id="prodi_id" name="prodi_id" class="w-full bg-white/50 border border-gray-200 text-gray-700 rounded-xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-medium text-base shadow-sm hover:bg-white" required>
                        <option value="" disabled selected>-- Pilih Program Studi --</option>
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}">{{ $prodi->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Section 2: Jadwal (Dosen & Mata Kuliah) -->
            <div id="jadwal_section" class="glass-card rounded-2xl p-8 hidden transition-all duration-300 hover:shadow-lg focus-within:ring-2 focus-within:ring-indigo-500/20">
                <label for="jadwal_id" class="block text-lg font-semibold text-gray-800 mb-3">Pilih Dosen dan Mata Kuliah <span class="text-pink-500">*</span></label>
                <div class="relative">
                    <select id="jadwal_id" name="jadwal_id" class="w-full bg-gray-50 border border-gray-200 text-gray-500 rounded-xl px-5 py-4 focus:outline-none transition-all font-medium text-base shadow-sm cursor-not-allowed" required disabled>
                        <option value="" disabled selected>-- Pilih Dosen & Mata Kuliah --</option>
                    </select>
                </div>
            </div>

            <!-- Section 3: Kuisioner -->
            <div id="questions_section" class="hidden space-y-8">
                @php
                    $questionNumber = 1;
                @endphp

                @foreach($groupedQuestions as $sectionName => $questions)
                    <div class="pt-4">
                        <div class="inline-block px-4 py-2 rounded-lg bg-indigo-100 text-indigo-800 font-bold tracking-wider text-sm mb-6 shadow-sm border border-indigo-200">
                            {{ $sectionName }}
                        </div>

                        <div class="space-y-6">
                            @foreach($questions as $q)
                            <div class="glass-card rounded-2xl p-6 sm:p-8 hover:shadow-xl transition-all duration-300 border-l-4 border-l-transparent hover:border-l-indigo-500">
                                <label class="block text-lg font-medium text-gray-800 mb-8 leading-relaxed"><span class="font-bold text-indigo-600 mr-2">{{ $questionNumber++ }}.</span> {{ $q->question_text }} <span class="text-pink-500">*</span></label>
                                
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between sm:justify-center w-full max-w-2xl mx-auto px-2">
                                    <span class="text-sm font-semibold text-gray-400 uppercase tracking-widest hidden sm:block w-40 whitespace-nowrap text-right pr-6">Sangat Kurang</span>
                                    
                                    <div class="flex justify-between sm:justify-center w-full sm:w-auto space-x-0 sm:space-x-6">
                                        @for($i = 1; $i <= 5; $i++)
                                            <label class="relative flex flex-col items-center cursor-pointer group">
                                                <input type="radio" name="q_{{ $q->id }}" value="{{ $i }}" class="peer sr-only" required>
                                                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center border-2 border-gray-200 bg-white text-gray-500 text-lg font-bold transition-all duration-200 ease-in-out group-hover:border-indigo-300 group-hover:bg-indigo-50 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-indigo-500/40 peer-focus-visible:ring-4 peer-focus-visible:ring-indigo-200 scale-100 peer-checked:scale-110">
                                                    {{ $i }}
                                                </div>
                                            </label>
                                        @endfor
                                    </div>
                                    
                                    <span class="text-sm font-semibold text-gray-400 uppercase tracking-widest hidden sm:block w-40 whitespace-nowrap text-left pl-6">Sangat Baik</span>
                                </div>
                                
                                <div class="flex justify-between sm:hidden mt-6 px-1 text-xs font-semibold text-gray-400 uppercase tracking-widest">
                                    <span>Sangat Kurang</span>
                                    <span>Sangat Baik</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="glass-card rounded-2xl p-8 hover:shadow-lg transition-all duration-300 focus-within:ring-2 focus-within:ring-indigo-500/20 mt-8">
                    <label for="saran" class="block text-lg font-semibold text-gray-800 mb-4">Saran / Masukan untuk Dosen (Opsional)</label>
                    <textarea id="saran" name="saran" rows="4" class="w-full bg-white/50 border border-gray-200 text-gray-700 rounded-xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-medium text-base shadow-sm hover:bg-white resize-y" placeholder="Tuliskan saran yang membangun di sini..."></textarea>
                </div>

                <div class="glass-card rounded-2xl p-6 sm:p-8 flex flex-col sm:flex-row justify-between items-center gap-4 mt-8">
                    <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold text-lg py-4 px-10 rounded-xl shadow-lg shadow-indigo-500/30 transform transition-all duration-200 hover:scale-[1.02] active:scale-95 focus:outline-none focus:ring-4 focus:ring-indigo-500/50">
                        Kirim Penilaian
                    </button>
                    <button type="reset" class="w-full sm:w-auto text-gray-500 font-semibold hover:text-gray-800 px-4 py-2 transition-colors">
                        Kosongkan formulir
                    </button>
                </div>
            </div>
        </form>
        
        <div class="text-center mt-12 mb-4 text-sm text-gray-400 font-medium">
            &copy; {{ date('Y') }} Sistem Evaluasi Dosen. All rights reserved.
        </div>
    </div>

    <script>
        document.getElementById('prodi_id').addEventListener('change', function() {
            let prodiId = this.value;
            let jadwalSection = document.getElementById('jadwal_section');
            let jadwalSelect = document.getElementById('jadwal_id');
            let questionsSection = document.getElementById('questions_section');

            // Show animation
            jadwalSection.classList.remove('hidden');
            jadwalSection.classList.add('fade-in');
            
            // Reset
            jadwalSelect.innerHTML = '<option value="" disabled selected>Memuat data...</option>';
            jadwalSelect.disabled = true;
            jadwalSelect.classList.add('bg-gray-50', 'text-gray-500', 'cursor-not-allowed');
            jadwalSelect.classList.remove('bg-white/50', 'text-gray-700', 'hover:bg-white', 'focus:ring-2');
            
            questionsSection.classList.add('hidden');
            questionsSection.classList.remove('fade-in');

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
                        
                        // Style for active state
                        jadwalSelect.classList.remove('bg-gray-50', 'text-gray-500', 'cursor-not-allowed');
                        jadwalSelect.classList.add('bg-white/50', 'text-gray-700', 'hover:bg-white', 'focus:ring-2');
                    }
                });
        });

        document.getElementById('jadwal_id').addEventListener('change', function() {
            if(this.value) {
                let questionsSection = document.getElementById('questions_section');
                questionsSection.classList.remove('hidden');
                
                // Add fade-in animation to all sections with a slight delay stagger
                const sections = questionsSection.children;
                for(let i=0; i<sections.length; i++) {
                    sections[i].style.opacity = '0';
                    setTimeout(() => {
                        sections[i].classList.add('fade-in');
                    }, i * 150);
                }
                
                // Scroll to questions smoothly
                setTimeout(() => {
                    questionsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            }
        });
    </script>
</body>
</html>
