@extends('layouts.app')

@section('content')

<div class="container p-6 mx-auto">

    {{-- Header Section --}}
    <div class="z-50 p-4 mb-4 bg-white rounded-sm shadow font-heading">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-purple-800 font-heading">Detail Data Guru</h2>
            <div class="flex space-x-1 font-sans text-xs text-gray-500">
                <a href="{{ route('admindashboard') }}" class="text-indigo-600 underline underline-offset-2 hover:text-indigo-700">Beranda</a>
                <span>/</span>
                <a href="{{ route('guru.index') }}" class="text-indigo-600 underline underline-offset-2 hover:text-indigo-700">Daftar Guru</a>
                <span>/</span>
                <span class="text-gray-400">Detail Guru</span>
            </div>
        </div>
    </div>

    {{-- Show Form --}}
    <div class="w-full p-6 mb-8 bg-white border border-gray-200 rounded-sm shadow-sm drop-shadow">
        <div class="flex items-center justify-between mb-8">
            <a href="{{ route('guru.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 transition-all duration-200 bg-white border border-gray-300 group rounded-xl hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
                <span class="text-sm">Kembali</span>
            </a>

            {{-- Optional: Add edit button on the right --}}
            {{-- <a href="#" class="inline-flex items-center px-4 py-2 font-sans text-sm font-medium text-purple-600 transition-colors duration-200 border border-purple-200 rounded-lg bg-purple-50 hover:bg-purple-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                Edit Profil
            </a> --}}
        </div>

        <!-- Profile Section -->
        <div class="mb-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-8 rounded-lg bg-gradient-to-r from-purple-50 to-indigo-50">
                <div class="flex flex-col items-center gap-6 sm:flex-row sm:items-start">
                    <!-- Profile Picture -->
                    <div class="flex-shrink-0">
                        @if ($guru->user->profile_picture)
                            <img src="src="{{ Storage::url($guru->user->profile_picture) }}"
                                alt="Foto Profil Admin"
                                class="object-cover w-32 h-32 border-4 border-white rounded-full shadow-lg">
                        @else
                            <div class="flex items-center justify-center w-32 h-32 text-4xl font-bold text-white border-4 border-white rounded-full shadow-lg bg-gradient-to-br from-purple-500 to-indigo-600">
                                {{ strtoupper(substr($guru->user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <!-- Basic Info -->
                    <div class="flex-1 text-center sm:text-left">
                        <h1 class="mb-2 text-2xl font-bold text-gray-900">{{ $guru->user->name }}</h1>
                        <p class="mb-1 text-gray-600">{{ $guru->user->email }}</p>
                        <div class="flex flex-wrap justify-center gap-2 mt-4 sm:justify-start">
                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-purple-700 bg-purple-100 rounded-full">
                                Guru
                            </span>
                            @if($guru->user->is_logged_in)
                                <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">
                                    <span class="w-2 h-2 mr-1 bg-green-400 rounded-full"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-full">
                                    <span class="w-2 h-2 mr-1 bg-gray-400 rounded-full"></span>
                                    Tidak Aktif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white border border-gray-200 rounded-sm shadow-sm">
            <div class="p-8">
                <!-- Personal Information Section -->
                <div class="mb-8">
                    <h2 class="pb-2 mb-6 text-lg font-semibold text-purple-800 border-b border-gray-200">Informasi Personal</h2>
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Nama Guru -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700">Nama Guru</label>
                            <div class="p-3 border rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-900">{{ $guru->nama_guru }}</p>
                            </div>
                        </div>

                        <!-- NIP -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700">NIP</label>
                            <div class="p-3 border rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-900">{{ $guru->nip }}</p>
                            </div>
                        </div>

                        <!-- No HP -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700">No HP</label>
                            <div class="p-3 border rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-900">{{ $guru->no_hp }}</p>
                            </div>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700">Jenis Kelamin</label>
                            <div class="p-3 border rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-900">{{ $guru->jenis_kelamin }}</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700">Email</label>
                            <div class="p-3 border rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-900">{{ $user->email }}</p>
                            </div>
                        </div>

                        <!-- Status Keaktifan -->
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Status Keaktifan -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">Status Keaktifan</label>
                                <div class="p-3 border rounded-lg bg-gray-50">
                                    <div class="flex items-center">
                                        @if($guru->status_keaktifan == 'Aktif')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <span class="w-2 h-2 mr-1 bg-green-400 rounded-full"></span>
                                                {{ $guru->status_keaktifan }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <span class="w-2 h-2 mr-1 bg-red-400 rounded-full"></span>
                                                {{ $guru->status_keaktifan }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Status Login -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">Status Login</label>
                                <div class="p-3 border rounded-lg bg-gray-50">
                                    <div class="flex items-center">
                                        @if($guru->user->is_logged_in)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <span class="w-2 h-2 mr-1 bg-green-400 rounded-full"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <span class="w-2 h-2 mr-1 bg-gray-400 rounded-full"></span>
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Alamat (Full Width) -->
                    <div class="mt-6 space-y-2">
                        <label class="text-sm font-medium text-gray-700">Alamat</label>
                        <div class="p-3 border rounded-lg bg-gray-50">
                            <p class="text-sm text-gray-900">{{ $guru->alamat }}</p>
                        </div>
                    </div>
                </div>

                <!-- System Information Section -->
                <div>
                    <h2 class="pb-2 mb-6 text-lg font-semibold text-purple-800 border-b border-gray-200">Informasi Sistem</h2>
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Terakhir Login -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700">Terakhir Login</label>
                            <div class="p-3 border rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-900">
                                    {{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Belum pernah login' }}
                                </p>
                            </div>
                        </div>

                        <!-- Dibuat Pada -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700">Dibuat Pada</label>
                            <div class="p-3 border rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-900">{{ $user->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        <!-- Terakhir Diperbarui -->
                        <div class="space-y-2 lg:col-span-2">
                            <label class="text-sm font-medium text-gray-700">Terakhir Diperbarui</label>
                            <div class="p-3 border rounded-lg bg-gray-50">
                                <p class="text-sm text-gray-900">{{ $user->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
