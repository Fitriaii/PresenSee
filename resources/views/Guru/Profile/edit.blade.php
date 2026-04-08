@extends('layouts.app')

@section('content')

<div class="container p-6 mx-auto">
    {{-- Header Section --}}
    <div class="z-50 p-4 mb-4 bg-white rounded-sm shadow font-heading">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-purple-800 font-heading">Edit Profil</h2>
            <div class="flex space-x-1 font-sans text-xs text-gray-500">
                <a href="{{ route('gurudashboard') }}" class="text-indigo-600 underline underline-offset-2 hover:text-indigo-700">Beranda</a>
                <span>/</span>
                <a href="{{ route('profileGuru.index') }}" class="text-indigo-600 underline underline-offset-2 hover:text-indigo-700">Profil</a>
                <span>/</span>
                <span class="text-gray-400">Edit Profil</span>
            </div>
        </div>
    </div>

    {{-- Input Form --}}
    <div class="p-6 mb-2 bg-white border border-gray-200 rounded-sm shadow-lg">

        <!-- Form -->
        <form id="guruForm" class="space-y-4" method="POST" action="{{ route('profileGuru.update', $guru) }}" enctype="multipart/form-data">
            @method('PATCH')
            @csrf
            <!-- Personal Information Section -->
            <div class="space-y-6">
                <h3 class="pb-2 text-lg font-semibold text-purple-800 border-b border-gray-200 font-heading">Informasi Personal</h3>

                <!-- Nama -->
                <div class="grid items-start grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:text-right">
                        <label for="nama_guru" class="block mb-1 text-sm font-semibold text-gray-700 font-heading">
                            Nama <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <div class="lg:col-span-2">
                        <input
                            type="text"
                            id="nama_guru"
                            name="nama_guru"
                            value="{{ old('nama_guru', isset($guru) ? $guru->nama_guru : '') }}"
                            required
                            placeholder="Masukkan nama lengkap"
                            class="w-full px-4 py-3 text-sm transition-colors duration-200 border border-gray-300 rounded-lg outline-none hover:border-purple-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 dark:bg-white dark:text-gray-900 dark:border-gray-300"
                        />
                    </div>
                </div>

                <!-- NIP -->
                <div class="grid items-start grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:text-right">
                        <label for="nip" class="block mb-1 text-sm font-semibold text-gray-700 font-heading">
                            NIP <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <div class="lg:col-span-2">
                        <input
                            type="text"
                            id="nip"
                            name="nip"
                            value="{{ old('nip', isset($guru) ? $guru->nip : '') }}"
                            required
                            placeholder="Masukkan NIP"
                            class="w-full px-4 py-3 text-sm transition-colors duration-200 border border-gray-300 rounded-lg outline-none hover:border-purple-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 dark:bg-white dark:text-gray-900 dark:border-gray-300"
                        />
                    </div>
                </div>

                <!-- Email -->
                <div class="grid items-start grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:text-right">
                        <label for="email" class="block mb-1 text-sm font-semibold text-gray-700 font-heading">
                            Email <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <div class="lg:col-span-2">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', isset($guru) ? $guru->user->email : '') }}"
                            required
                            placeholder="guru@example.com"
                            class="w-full px-4 py-3 text-sm transition-colors duration-200 border border-gray-300 rounded-lg outline-none hover:border-purple-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 dark:bg-white dark:text-gray-900 dark:border-gray-300"
                        />
                        <p class="mt-2 text-xs text-gray-500">Masukkan dengan format email yang valid</p>
                    </div>
                </div>


                <!-- Foto Profil -->
                <div class="grid items-start grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:text-right">
                        <label for="profile_picture" class="block mb-1 text-sm font-semibold text-gray-700 font-heading">
                            Foto Profil
                        </label>
                    </div>
                    <div class="lg:col-span-2">
                        <div class="flex items-center space-x-4">
                            <div class="flex-1">
                                <input
                                    type="file"
                                    id="profile_picture"
                                    name="profile_picture"
                                    accept="image/*"
                                    class="w-full px-4 py-3 text-sm transition-colors duration-200 border border-gray-300 rounded-lg outline-none dark:file:text-purple-400 hover:border-purple-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 dark:bg-white dark:text-gray-900 dark:border-gray-300"
                                />

                            </div>
                            <div class="flex-shrink-0">
                                <div class="relative flex items-center justify-center w-16 h-16 overflow-hidden bg-gray-100 border-2 border-gray-300 border-dashed rounded-lg">
                                  <!-- Jika ada preview gambar -->
                                  @if(isset($guru) && $guru->user->profile_picture)
                                    <img src="data:image/jpeg;base64,{{ $guru->user->profile_picture }}" alt="Preview Foto Profil" class="object-cover w-full h-full rounded-lg" />
                                  @else
                                    <!-- Icon Placeholder -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                  @endif
                                </div>

                                <!-- Teks file sebelumnya di bawah kotak preview -->
                                @if(old('profile_picture'))
                                  <p class="mt-1 text-xs text-blue-600 max-w-[80px] truncate">File sebelumnya: {{ old('profile_picture') }}</p>
                                @endif
                            </div>

                        </div>
                        <p class="mt-2 text-xs text-gray-500">Opsional, hanya mendukung format gambar (JPG, PNG, GIF)</p>
                    </div>
                </div>

                <!-- No Telepon -->
                <div class="grid items-start grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:text-right">
                        <label for="no_hp" class="block mb-1 text-sm font-semibold text-gray-700 font-heading">
                            No Telepon <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <div class="lg:col-span-2">
                        <input
                            type="text"
                            id="no_hp"
                            name="no_hp"
                            value="{{ old('no_hp', isset($guru) ? $guru->no_hp : '') }}"
                            required
                            placeholder="Masukkan nomor telepon"
                            class="w-full px-4 py-3 text-sm transition-colors duration-200 border border-gray-300 rounded-lg outline-none hover:border-purple-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 dark:bg-white dark:text-gray-900 dark:border-gray-300"
                        />
                    </div>
                </div>

                <!-- Alamat -->
                <div class="grid items-start grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:text-right">
                        <label for="alamat" class="block mb-1 text-sm font-semibold text-gray-700 font-heading">
                            Alamat <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <div class="lg:col-span-2">
                        <textarea
                            id="alamat"
                            name="alamat"
                            required
                            rows="3"
                            placeholder="Masukkan alamat lengkap"
                            class="w-full px-4 py-3 text-sm transition-colors duration-200 border border-gray-300 rounded-lg outline-none resize-none hover:border-purple-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 dark:bg-white dark:text-gray-900 dark:border-gray-300"
                        >{{ old('alamat', isset($guru) ? $guru->alamat : '') }}</textarea>

                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div class="grid items-start grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:text-right">
                        <label for="jenis_kelamin" class="block mb-1 text-sm font-semibold text-gray-700 font-heading">
                            Jenis Kelamin <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <div class="lg:col-span-2">
                        <select
                            id="jenis_kelamin"
                            name="jenis_kelamin"
                            required
                            class="w-full px-4 py-3 text-sm transition-colors duration-200 border border-gray-300 rounded-lg outline-none hover:border-purple-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 dark:bg-white dark:text-gray-900 dark:border-gray-300"
                        >
                            <option value="" disabled {{ old('jenis_kelamin', isset($guru) ? $guru->jenis_kelamin : '') == '' ? 'selected' : '' }}>Pilih jenis kelamin</option>
                            <option value="Laki-Laki" {{ old('jenis_kelamin', isset($guru) ? $guru->jenis_kelamin : '') == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin', isset($guru) ? $guru->jenis_kelamin : '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Status Keaktifan -->
                <div class="grid items-start grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:text-right">
                        <label for="status_keaktifan" class="block mb-1 text-sm font-semibold text-gray-700 font-heading">
                            Status Keaktifan <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <div class="lg:col-span-2">
                        <select
                            id="status_keaktifan"
                            name="status_keaktifan"
                            required
                            class="w-full px-4 py-3 text-sm transition-colors duration-200 border border-gray-300 rounded-lg outline-none hover:border-purple-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 dark:bg-white dark:text-gray-900 dark:border-gray-300"
                        >
                            <option value="" disabled {{ old('status_keaktifan', isset($guru) ? $guru->status_keaktifan : '') == '' ? 'selected' : '' }}>Pilih status keaktifan</option>
                            <option value="Aktif" {{ old('status_keaktifan', isset($guru) ? $guru->status_keaktifan : '') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Tidak Aktif" {{ old('status_keaktifan', isset($guru) ? $guru->status_keaktifan : '') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Account Information Section -->
            <div class="space-y-6">
                <h3 class="pb-2 text-lg font-semibold text-purple-800 border-b border-gray-200 font-heading">Keamanan Akun</h3>

                <!-- Password -->
                <div class="grid items-start grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:text-right">
                        <label for="password" class="block mb-1 text-sm font-semibold text-gray-700 font-heading">
                            Kata Sandi <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <div class="lg:col-span-2">
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                placeholder="Masukkan kata sandi"
                                class="w-full px-4 py-3 pr-12 text-sm transition-colors duration-200 border border-gray-300 rounded-lg outline-none hover:border-purple-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                            />
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 transition-colors hover:text-purple-600">
                                <svg id="eyeIcon1" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Minimal 8 karakter, gunakan kombinasi huruf dan angka</p>
                    </div>
                </div>

                <!-- Konfirmasi Password -->
                <div class="grid items-start grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:text-right">
                        <label for="password_confirmation" class="block mb-1 text-sm font-semibold text-gray-700 font-heading">
                            Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <div class="lg:col-span-2">
                        <div class="relative">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                placeholder="Masukkan ulang kata sandi"
                                class="w-full px-4 py-3 pr-12 text-sm transition-colors duration-200 border border-gray-300 rounded-lg outline-none hover:border-purple-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                            />
                            <button type="button" onclick="toggleConfirmPassword()" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 transition-colors hover:text-purple-600">
                                <svg id="eyeIcon2" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Kata sandi harus sama dengan yang di atas</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col justify-end gap-4 pt-8 border-t border-gray-200 sm:flex-row">
                <a href="{{ route('profileGuru.index') }}" type="button"
                    class="px-6 py-3 text-sm font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-3 text-sm font-semibold text-white transition-colors duration-200 bg-purple-600 rounded-lg shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-200 hover:shadow-md">
                    <span class="flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Perbarui Profil
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const eyeIcon = document.getElementById("eyeIcon1");

            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";

            // Ganti icon (opsional, di sini hanya ganti stroke)
            eyeIcon.innerHTML = isPassword
                ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.965 9.965 0 011.284-2.618m3.923-3.923A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.965 9.965 0 01-1.284 2.618M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3l18 18" />`
                : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
        }
        function toggleConfirmPassword() {
            const confirmPasswordInput = document.getElementById("password_confirmation");
            const eyeIcon = document.getElementById("eyeIcon2");

            const isConfirmPassword = confirmPasswordInput.type === "password";
            confirmPasswordInput.type = isConfirmPassword ? "text" : "password";

            // Ganti icon (opsional, di sini hanya ganti stroke)
            eyeIcon.innerHTML = isConfirmPassword
                ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.965 9.965 0 011.284-2.618m3.923-3.923A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.965 9.965 0 01-1.284 2.618M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3l18 18" />`
                : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
        }

        // Tampilkan alert sukses/error dari session
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: @json(session('success')),
            showConfirmButton: false,
            timer: 2000
        });
    @endif

    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: @json(session('error')),
            showConfirmButton: true
        });
    @endif
    </script>
@endsection
