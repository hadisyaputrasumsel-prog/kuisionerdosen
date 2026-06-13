<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuisioner Terkirim - Universitas Sumatera Selatan</title>
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
            animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .bounce-in {
            animation: bounceIn 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { opacity: 1; transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="py-10 px-4 flex items-center justify-center min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 relative overflow-hidden text-gray-800">

    <!-- Decorative Background Elements -->
    <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-teal-300/20 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-indigo-300/20 blur-[120px] pointer-events-none"></div>

    <div class="max-w-xl w-full z-10 flex flex-col items-center">
        
        <!-- University Branding -->
        <div class="text-center mb-8 fade-in" style="animation-delay: 0.1s; opacity: 0;">
            <img src="{{ asset('uss-logo.png') }}" alt="Logo Universitas Sumatera Selatan" class="h-20 md:h-24 mx-auto mb-3 object-contain drop-shadow-md" onerror="this.style.display='none'">
            <h2 class="text-lg md:text-xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-teal-700 to-indigo-800 tracking-widest uppercase">Universitas Sumatera Selatan</h2>
        </div>

        <div class="glass-card rounded-3xl w-full shadow-2xl p-10 md:p-14 text-center relative overflow-hidden fade-in" style="animation-delay: 0.3s; opacity: 0;">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-teal-400 via-indigo-500 to-purple-500"></div>
            
            <div class="mb-8 text-teal-500 bounce-in" style="animation-delay: 0.5s; opacity: 0;">
                <div class="w-24 h-24 mx-auto bg-teal-50 rounded-full flex items-center justify-center border-4 border-teal-100 shadow-inner">
                    <svg class="w-12 h-12 text-teal-600 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            
            <h1 class="text-3xl md:text-4xl font-extrabold mb-4 tracking-tight text-gray-800">
                Terima Kasih!
            </h1>
            
            <p class="text-lg text-gray-600 mb-2 font-medium">Tanggapan evaluasi Anda telah berhasil direkam.</p>
            <p class="text-gray-500 mb-8 text-sm">Kontribusi Anda sangat berharga bagi peningkatan kualitas pembelajaran kami.</p>
            
            @if(session('success'))
                <div class="bg-teal-50 border border-teal-200 text-teal-700 rounded-xl py-3 px-4 mb-8 font-medium shadow-sm inline-block">
                    {{ session('success') }}
                </div>
            @endif

            <a href="{{ route('form.index') }}" class="inline-flex items-center justify-center space-x-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-indigo-500/30 transform transition-all duration-200 hover:scale-105 active:scale-95 focus:outline-none focus:ring-4 focus:ring-indigo-500/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Kirim Tanggapan Lain</span>
            </a>
        </div>
        
        <div class="text-center mt-10 text-sm text-gray-400 font-medium fade-in" style="animation-delay: 0.7s; opacity: 0;">
            &copy; {{ date('Y') }} Sistem Evaluasi Dosen. All rights reserved.
        </div>
    </div>

</body>
</html>
