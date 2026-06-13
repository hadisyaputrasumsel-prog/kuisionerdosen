<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuisioner Terkirim</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f0ebf8; }
        .form-header { border-top: 10px solid #673ab7; }
    </style>
</head>
<body class="py-8 px-4 flex items-center justify-center min-h-screen">

    <div class="max-w-xl w-full bg-white rounded-lg shadow-md p-8 form-header text-center">
        <div class="mb-4 text-green-500">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-semibold mb-4 text-gray-800">Evaluasi Dosen & Mata Kuliah</h1>
        <p class="text-gray-600 mb-6">Tanggapan Anda telah direkam.</p>
        
        @if(session('success'))
            <p class="text-green-600 font-medium mb-6">{{ session('success') }}</p>
        @endif

        <a href="{{ route('form.index') }}" class="text-purple-600 hover:text-purple-800 font-medium underline transition">Kirim tanggapan lain</a>
    </div>

</body>
</html>
