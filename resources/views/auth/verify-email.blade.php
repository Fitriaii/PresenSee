<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - PresenSee</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex items-center justify-center min-h-screen font-sans bg-gradient-to-br from-orange-300 to-purple-300">

    <!-- Card -->
    <div class="w-full max-w-md p-8 mx-4 bg-white border border-gray-100 shadow-xl rounded-2xl">

        <!-- Header -->
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-gray-800">
                <span class="text-purple-600">PresenSee</span>
            </h1>
            <h2 class="mt-1 text-xl font-bold text-orange-600 font-heading">
            SMP Islamiyah Widodaren
            </h2>
        </div>

        <!-- Icon -->
        <div class="flex justify-center mb-4">
            <div class="flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full">
                <svg data-slot="icon" fill="none" class="w-8 h-8 text-purple-600" stroke-width="1.8" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"></path>
                </svg>
            </div>
        </div>

        <!-- Title -->
        <h2 class="text-lg font-semibold text-center text-gray-800">
            Verifikasi Email Diperlukan
        </h2>

        <!-- Description -->
        <p class="mt-2 text-sm text-center text-gray-600">
            Untuk mengaktifkan akun, silakan buka email yang telah dikirim dan klik tautan verifikasi.
        </p>

        <p class="mt-1 text-xs text-center text-gray-400">
            Jika email tidak ditemukan, silakan periksa folder spam.
        </p>

        <!-- Success Alert -->
        @if (session('status') == 'verification-link-sent')
            <div class="p-3 mt-4 text-sm text-green-700 bg-green-100 border border-green-200 rounded-lg text-center">
                Link verifikasi berhasil dikirim ulang.
            </div>
        @endif

        <!-- Button -->
        <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
            @csrf
            <button
                type="submit"
                class="w-full py-3 text-sm font-semibold text-white transition rounded-xl bg-gradient-to-r from-orange-400 to-purple-500 hover:from-orange-500 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-300"
            >
                Kirim Ulang Email
            </button>
        </form>

        <!-- Footer Actions -->
        <div class="flex items-center justify-between mt-5 text-sm">

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-gray-500 hover:text-gray-700">
                    Keluar
                </button>
            </form>
        </div>

    </div>

</body>
</html>
