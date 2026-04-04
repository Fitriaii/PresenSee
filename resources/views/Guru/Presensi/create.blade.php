@extends('layouts.app')

@section('content')
<div class="container p-6 mx-auto">

    {{-- Header Section --}}
    <div class="z-50 p-4 mb-4 bg-white rounded-sm shadow font-heading">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-purple-800 font-heading">Tambah Data Presensi</h2>
            <div class="flex space-x-1 font-sans text-xs text-gray-500">
                <a href="{{ route('gurudashboard') }}" class="text-indigo-600 underline underline-offset-2 hover:text-indigo-700">Beranda</a>
                <span>/</span>
                <a href="{{ route('presensi.index') }}" class="text-indigo-600 underline underline-offset-2 hover:text-indigo-700">Presensi</a>
                <span>/</span>
                <span class="text-gray-400">Tambah Presensi</span>
            </div>
        </div>
    </div>

    <div class="z-50 p-6 mb-6 bg-white rounded-sm shadow-lg font-heading">

        @if ($showWarning)
            <div class="p-4 mb-6 border shadow-lg bg-gradient-to-r from-amber-50 to-orange-50 border-amber-200 rounded-xl">
                <div class="flex items-start gap-3">
                    <!-- Warning Icon -->
                    <div class="flex items-center justify-center flex-shrink-0 w-10 h-10 rounded-full bg-amber-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>

                    <!-- Warning Content -->
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <h4 class="text-sm font-semibold text-amber-800 lg:text-base">Perhatian</h4>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-amber-200 text-amber-800">
                                Di Luar Jam Pelajaran
                            </span>
                        </div>
                        <p class="text-sm leading-relaxed text-amber-700">
                            Anda membuka presensi di luar jam pelajaran
                            <span class="font-semibold text-amber-800">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</span>.
                        </p>

                        <!-- Additional Info -->
                        <div class="p-3 mt-3 bg-opacity-50 rounded-lg bg-amber-100">
                            <div class="flex items-start gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xs text-amber-700">
                                    Presensi tetap dapat dilakukan, namun pastikan Anda memiliki izin untuk mengakses di luar jam pelajaran.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Time Badge -->
                    <div class="flex-shrink-0 hidden sm:block">
                        <div class="text-center">
                            <div class="text-xs font-medium text-amber-600">Jadwal Pelajaran</div>
                            <div class="text-sm font-bold text-amber-800">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Detail Jadwal Section --}}

        <div class="mb-6">
            <h2 class="mb-6 text-lg font-bold text-purple-800 font-heading">Detail Jadwal</h2>

            <div class="grid grid-cols-1 gap-6">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-between ml-6 w-60">
                        <label class="text-sm font-semibold text-black font-heading">Mata Pelajaran</label>
                        <span class="ml-1 text-sm font-semibold text-black">:</span>
                    </div>
                    <div class="w-full">
                        <p class="text-sm text-gray-700">{{ $jadwal->mapel->nama_mapel }}</p>
                    </div>
                </div>
                <hr class="border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-between ml-6 w-60">
                        <label class="text-sm font-semibold text-black font-heading">Kelas</label>
                        <span class="ml-1 text-sm font-semibold text-black">:</span>
                    </div>
                    <div class="w-full">
                        <p class="text-sm text-gray-700">{{ $jadwal->kelas->nama_kelas }}</p>
                    </div>
                </div>
                <hr class="border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-between ml-6 w-60">
                        <label class="text-sm font-semibold text-black font-heading">Hari</label>
                        <span class="ml-1 text-sm font-semibold text-black">:</span>
                    </div>
                    <div class="w-full">
                        <p class="text-sm text-gray-700">{{ $jadwal->hari }}</p>
                    </div>
                </div>
                <hr class="border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-between ml-6 w-60">
                        <label class="text-sm font-semibold text-black font-heading">Jam</label>
                        <span class="ml-1 text-sm font-semibold text-black">:</span>
                    </div>
                    <div class="w-full">
                        <p class="text-sm text-gray-700">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="mb-6 text-lg font-bold text-purple-800 font-heading">Form Presensi</h2>

            <div class="mb-6 space-x-8" x-data="{ tab: 'kamera' }">

                <div class="flex flex-wrap mb-4 border-b border-gray-200">
                    <button
                        type="button"
                        @click="tab = 'kamera'"
                        :class="tab === 'kamera' ? 'text-purple-600 border-purple-600' : 'text-gray-600 border-transparent '"
                        class="flex items-center gap-2 px-6 py-2 text-sm font-medium border-b-2 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134-.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                        </svg>
                        Presensi Kamera
                    </button>
                    <button
                        type="button"
                        @click="stopCamera(); tab = 'manual'"
                        :class="tab === 'manual' ? 'text-purple-600 border-purple-600' : 'text-gray-600 border-transparent '"
                        class="flex items-center gap-2 px-6 py-2 text-sm font-medium border-b-2 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
                        </svg>
                        Presensi Manual
                    </button>
                </div>

                <div class="flex items-center gap-4 mb-4">
                    <label class="w-48 text-sm font-medium text-black">Waktu Presensi <span class="ml-1 font-semibold">:</span></label>
                    <input
                        type="datetime-local"
                        name="waktu_presensi"
                        id="waktu_presensi"
                        class="px-4 py-2 text-sm text-black border border-gray-400 rounded outline-none w-60 bg-inherit hover:border-purple-600 focus:border-purple-600"
                        required
                        :readonly="tab === 'kamera'"
                    >
                    @error('waktu_presensi')
                        <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                <div x-show="tab === 'kamera'">
                    <!-- Main Content Container -->
                    <div class="grid grid-cols-1 gap-4 mb-6 xl:grid-cols-4 lg:gap-6 lg:mb-8">
                        <!-- Camera Section -->
                        <div class="xl:col-span-3">
                            <div class="p-4 bg-white shadow-md rounded-xl lg:p-6">
                                <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
                                    <h3 class="text-base font-semibold text-gray-800 lg:text-lg">Kamera Presensi</h3>
                                    <div id="modeStatus" class="px-3 py-1 text-xs font-medium text-blue-800 bg-blue-100 border border-blue-200 rounded-full w-fit">
                                        Mode Otomatis Aktif
                                    </div>
                                </div>

                                <div id="videoWrapper" class="relative overflow-hidden bg-gray-900 rounded-lg shadow-inner aspect-video video-container"
                                     data-jadwal-id="{{ $jadwal->id }}"
                                     data-kelas="{{ $jadwal->kelas->id }}"
                                     data-total-siswa="{{ $totalSiswa }}">

                                    <!-- Status Indicators -->
                                    <div id="statusIndicator" class="absolute z-10 w-3 h-3 bg-red-500 rounded-full top-2 right-2"></div>
                                    <div id="recognitionStats" class="absolute z-10 hidden px-3 py-2 text-xs text-white bg-black rounded-md top-2 left-2 bg-opacity-70">
                                        <div class="flex items-center gap-3">
                                            <span>FPS: <span id="fpsCounter" class="font-mono">0</span></span>
                                            <span>Deteksi: <span id="detectionCount" class="font-mono">0</span></span>
                                        </div>
                                    </div>

                                    <!-- Video Element -->
                                    <video id="video" autoplay muted class="object-cover w-full h-full"></video>

                                    <!-- Scanning Line -->
                                    <div id="scanningLine" class="hidden absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-green-500 to-transparent animate-pulse"></div>

                                    <!-- Camera Overlay -->
                                    <div id="cameraOverlay" class="absolute inset-0 flex flex-col items-center justify-center text-white transition-all duration-500 bg-gradient-to-br from-gray-800 to-gray-900">
                                        <div class="px-4 text-center">
                                            <div class="w-16 h-16 p-3 mx-auto mb-4 bg-gray-700 rounded-full lg:w-20 lg:h-20 lg:mb-6 lg:p-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-full h-full text-gray-400 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h2l2-3h10l2 3h2a2 2 0 012 2v9a2 2 0 01-2 2H3a2 2 0 01-2-2V9a2 2 0 012-2z"/>
                                                    <circle cx="12" cy="13" r="4"/>
                                                </svg>
                                            </div>
                                            <h4 class="mb-2 text-lg font-medium text-gray-200 lg:text-xl">Kamera Belum Aktif</h4>
                                            <p class="text-xs text-gray-400 lg:text-sm">Klik tombol "Nyalakan Kamera" untuk memulai</p>
                                        </div>
                                    </div>

                                    <!-- Hidden Canvas -->
                                    <canvas id="canvas" class="hidden"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance History Sidebar -->
                        <div class="xl:col-span-1">
                            <div class="h-full p-4 bg-white shadow-md rounded-xl lg:p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-base font-semibold text-gray-800 lg:text-lg">Riwayat Presensi</h3>
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                </div>

                                <div class="mb-4 text-sm text-gray-600">
                                    <p>Sesi saat ini</p>
                                </div>

                                <div id="attendanceHistory" class="space-y-3 overflow-y-auto max-h-80 lg:max-h-96">
                                    <!-- Attendance cards will be added via JavaScript -->
                                    <div class="py-6 text-center text-gray-500 lg:py-8">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3 text-gray-300 lg:w-12 lg:h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-sm">Belum ada presensi</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Control Buttons -->
                    <div class="p-4 mb-6 bg-white shadow-md rounded-xl lg:p-6">
                        <h3 class="mb-4 text-base font-semibold text-gray-800 lg:text-lg">Kontrol Kamera</h3>
                        <div class="flex flex-col justify-center gap-3 sm:flex-row sm:flex-wrap lg:gap-4">
                            <button type="button" class="px-4 lg:px-6 py-2 lg:py-3 text-sm font-semibold text-white rounded-lg shadow-lg bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5" id="start-recognition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 lg:w-5 lg:h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                                Nyalakan Kamera
                            </button>

                            <button type="button" class="px-4 lg:px-6 py-2 lg:py-3 text-sm font-semibold text-white rounded-lg shadow-lg bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5" id="detectFace" disabled>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 lg:w-5 lg:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Presensi Sekarang
                            </button>

                            <button type="button" class="px-4 lg:px-6 py-2 lg:py-3 text-sm font-semibold text-white rounded-lg shadow-lg bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5" id="stop-recognition" disabled>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 lg:w-5 lg:h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M12 18.75H4.5a2.25 2.25 0 0 1-2.25-2.25V9m12.841 9.091L16.5 19.5m-1.409-1.409c.407-.407.659-.97.659-1.591v-9a2.25 2.25 0 0 0-2.25-2.25h-9c-.621 0-1.184.252-1.591.659m12.182 12.182L2.909 5.909M1.5 4.5l1.409 1.409" />
                                </svg>
                                Matikan Kamera
                            </button>
                        </div>
                    </div>

                    <!-- Information Panel -->
                    <div class="p-4 border border-blue-100 shadow-md bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl lg:p-6">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start">
                            <div class="flex items-center justify-center flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full lg:w-12 lg:h-12">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600 lg:w-6 lg:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="mb-2 text-base font-semibold text-blue-900 lg:text-lg">Presensi Otomatis Menggunakan Kamera</h4>
                                <p class="mb-4 text-sm leading-relaxed text-blue-800 lg:text-base">
                                    Sistem akan secara otomatis mendeteksi wajah setiap beberapa detik. Jika wajah siswa dikenali dan sesuai jadwal, maka presensi akan tercatat secara langsung tanpa perlu tindakan tambahan.
                                </p>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 lg:gap-4">
                                    <div class="flex items-center gap-2 text-xs text-blue-700 lg:text-sm">
                                        <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full"></div>
                                        <span>Deteksi wajah berkala otomatis</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-blue-700 lg:text-sm">
                                        <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full"></div>
                                        <span>Proses presensi cepat dan efisien</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-blue-700 lg:text-sm">
                                        <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full"></div>
                                        <span>Langsung tercatat jika sesuai</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form x-show="tab === 'manual'" action="{{ route('presensi.manual.simpan') }}" method="POST" class="space-y-4">
                    @csrf

                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                    {{-- Waktu presensi untuk manual diisi dari JS atau biarkan user pilih --}}
                    {{-- <input type="hidden" name="waktu_presensi" value="{{ now() }}"> --}}

                    <div class="mt-2 overflow-auto rounded-sm shadow-sm">
                        <table class="min-w-full bg-white rounded shadow-md">
                            <thead>
                                <tr class="text-sm font-semibold text-gray-700 bg-gray-100 font-heading">
                                    <th class="px-6 py-3 text-xs text-left text-gray-500 uppercase">No</th>
                                    <th class="px-6 py-3 text-xs text-left text-gray-500 uppercase">NIS</th>
                                    <th class="px-6 py-3 text-xs text-left text-gray-500 uppercase">Nama Siswa</th>
                                    <th class="px-6 py-3 text-xs text-center text-gray-500 uppercase">Hadir</th>
                                    <th class="px-6 py-3 text-xs text-center text-gray-500 uppercase">Sakit</th>
                                    <th class="px-6 py-3 text-xs text-center text-gray-500 uppercase">Izin</th>
                                    <th class="px-6 py-3 text-xs text-center text-gray-500 uppercase">Alpha</th>
                                    <th class="px-6 py-3 text-xs text-left text-gray-500 uppercase">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ( $siswa as $index => $item )
                                @php
                                    $status = old("presensi.$index.status", $item->presensi_data->status ?? null);
                                    $catatan = old("presensi.$index.catatan", $item->presensi_data->catatan ?? '');
                                @endphp
                                <tr class="text-xs text-gray-700 border-t hover:bg-gray-50">
                                    <td class="px-6 py-2 text-sm text-gray-900 whitespace-nowrap">
                                        {{ $loop->iteration }}
                                        <input type="hidden" name="presensi[{{ $index }}][siswa_kelas_id]" value="{{ $item->id }}">
                                    </td>
                                    <td class="px-6 py-2 text-sm text-gray-900">{{ $item->siswa->nis }}</td>
                                    <td class="px-6 py-2 text-sm text-gray-900">{{ $item->siswa->nama_siswa }}</td>

                                    {{-- Hadir --}}
                                    <td class="px-6 py-2 text-sm text-center text-gray-900">
                                        <input type="radio" name="presensi[{{ $index }}][status]" value="Hadir"
                                            {{ $status === 'Hadir' ? 'checked' : '' }} required>
                                    </td>

                                    {{-- Sakit --}}
                                    <td class="px-6 py-2 text-sm text-center text-gray-900">
                                        <input type="radio" name="presensi[{{ $index }}][status]" value="Sakit"
                                            {{ $status === 'Sakit' ? 'checked' : '' }}>
                                    </td>

                                    {{-- Izin --}}
                                    <td class="px-6 py-2 text-sm text-center text-gray-900">
                                        <input type="radio" name="presensi[{{ $index }}][status]" value="Izin"
                                            {{ $status === 'Izin' ? 'checked' : '' }}>
                                    </td>

                                    {{-- Alpha --}}
                                    <td class="px-6 py-2 text-sm text-center text-gray-900">
                                        <input type="radio" name="presensi[{{ $index }}][status]" value="Alpha"
                                            {{ $status === 'Alpha' ? 'checked' : '' }}>
                                    </td>

                                    {{-- Keterangan --}}
                                    @php
                                        $catatan_otomatis = ['Wajah tidak dikenali', 'Salah kelas', 'Error saat mencatat presensi'];
                                        $isAutoNote = in_array($catatan, $catatan_otomatis);
                                    @endphp

                                    <td class="px-6 py-2 text-xs text-gray-600">
                                        <input type="text"
                                            name="presensi[{{ $index }}][catatan]"
                                            class="w-full px-2 py-1 border rounded {{ $isAutoNote ? 'bg-gray-200 text-gray-500' : '' }}"
                                            placeholder="Opsional"
                                            value="{{ $catatan }}"
                                            {{ $isAutoNote ? 'readonly' : '' }}>
                                    </td>

                                </tr>
                                @empty
                                <tr class="text-sm text-gray-500">
                                    <td colspan="8" class="px-4 py-3 text-center">Tidak ada data siswa ditemukan untuk kelas ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-col justify-end gap-4 pt-8 border-t border-gray-200 sm:flex-row">
                        <a href="{{ route('presensi.index') }}" type="button"
                            class="px-6 py-3 text-sm font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-3 text-sm font-semibold text-white transition-colors duration-200 bg-purple-600 rounded-lg shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-200 hover:shadow-md">
                            <span class="flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Presensi
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .face-frame {
        position: absolute;
        border: 3px solid #10b981;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
    }

    .face-frame.success { border: 3px solid green; }
    .face-frame.not_allowed { border: 3px solid orange; }
    .face-frame.not_recognized { border: 3px solid red; }

    .confidence-badge {
        position: absolute;
        background: rgba(16, 185, 129, 0.9);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        backdrop-filter: blur(4px);
    }

    .video-container {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .scanning-line {
        position: absolute;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, transparent, #10b981, transparent);
        animation: scan 2s linear infinite;
    }

    @keyframes scan {
        0% { top: 0; opacity: 1; }
        50% { opacity: 0.5; }
        100% { top: 100%; opacity: 1; }
    }

    .status-indicator {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #ef4444; /* Merah untuk tidak aktif */
        animation: pulse 2s infinite;
    }

    .status-indicator.active {
        background: #10b981; /* Hijau untuk aktif */
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .btn-primary {
        background: linear-gradient(135deg, #10b981, #059669);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        transition: all 0.3s ease;
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
    }

    .recognition-stats {
        position: absolute;
        top: 12px;
        left: 12px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 12px;
        backdrop-filter: blur(4px);
    }
</style>

<script>
    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');
    const startBtn = document.getElementById('start-recognition');
    const detectBtn = document.getElementById('detectFace');
    const stopBtn = document.getElementById('stop-recognition');
    const videoWrapper = document.getElementById('videoWrapper');
    const jadwalId = videoWrapper.dataset.jadwalId;
    const storageKey = `attendanceHistory_${jadwalId}`
    const selectedKelas = videoWrapper.dataset.kelas;
    const totalSiswa = parseInt(videoWrapper.dataset.totalSiswa || 0) ;
    const statusIndicator = document.getElementById('statusIndicator');
    const scanningLine = document.getElementById('scanningLine');
    const recognitionStats = document.getElementById('recognitionStats');
    const fpsCounter = document.getElementById('fpsCounter');
    const detectionCount = document.getElementById('detectionCount');
    const waktuPresensiInput = document.getElementById('waktu_presensi');


    const FLASK_API_URL = 'http://127.0.0.1:5050/recognize';
    const soundSuccess = new Audio('/sounds/success.mp3');
    const soundAlready = new Audio('/sounds/already.mp3');
    const soundFail = new Audio('/sounds/fail.mp3');
    const soundWarning = new Audio('/sounds/warning.mp3');

    let mediaStream = null;
    let detectionInterval = null;
    let frameCount = 0;
    let lastFrameTime = Date.now();
    let totalDetections = 0;
    let isProcessing = false;
    let isCooldown = false;
    let siswaPresensiSet = new Set();

    window.addEventListener('DOMContentLoaded', () => {
        const today = new Date().toDateString(); // Format: "Wed Jul 10 2025"
        const data = JSON.parse(localStorage.getItem(storageKey) || '[]');

        // Bersihkan data jika tanggal tidak sesuai
        if (data.length && data[0].tanggal !== today) {
            localStorage.removeItem(storageKey);
            return;
        }

        if (data.length > 0) removeEmptyMessage();

        data.forEach(item => {
            siswaPresensiSet.add(item.nis);
            const card = document.createElement('div');
            card.setAttribute('data-nis', item.nis);
            card.className = 'bg-white rounded-md shadow p-3 text-xs text-gray-700 flex items-center gap-2';
            card.innerHTML = `
                <div class="px-2 py-1 font-semibold text-green-600 bg-green-100 rounded">✔</div>
                <div>
                    <div class="font-semibold">${item.nama}</div>
                    <div class="text-gray-500">${item.waktu}</div>
                </div>
            `;
            document.getElementById('attendanceHistory').appendChild(card);
        });
    });


    // 2. Fungsi update list
    function updateAttendanceList(siswaNis, namaSiswa) {
        const container = document.getElementById('attendanceHistory');
        if (!container || siswaPresensiSet.has(siswaNis)) return;

        removeEmptyMessage();

        siswaPresensiSet.add(siswaNis);
        const waktu = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const tanggal = new Date().toDateString();

        const card = document.createElement('div');
        card.setAttribute('data-nis', siswaNis);
        card.className = 'bg-white rounded-md shadow p-3 text-xs text-gray-700 flex items-center gap-2';
        card.innerHTML = `
            <div class="px-2 py-1 font-semibold text-green-600 bg-green-100 rounded">✔</div>
            <div>
                <div class="font-semibold">${namaSiswa}</div>
                <div class="text-gray-500">${waktu}</div>
            </div>
        `;
        container.prepend(card);

        // Simpan ke localStorage
        const existing = JSON.parse(localStorage.getItem(storageKey) || '[]');
        existing.unshift({ nis: siswaNis, nama: namaSiswa, waktu, tanggal });
        localStorage.setItem(storageKey, JSON.stringify(existing));

        if (siswaPresensiSet.size >= totalSiswa) {
            stopCamera();
            Swal.fire('Presensi Selesai', 'Semua siswa telah melakukan presensi.', 'success');
        }
    }


    function removeEmptyMessage() {
        const emptyMessage = document.querySelector('#attendanceHistory .text-center');
        if (emptyMessage) emptyMessage.remove();
    }

    // (Opsional) Bersihkan localStorage saat pindah halaman/jadwal
    function clearAttendanceHistoryForThisJadwal() {
        localStorage.removeItem(storageKey);
    }

    video.addEventListener('loadedmetadata', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
    });

    function startCamera() {
        navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } })
            .then(stream => {
                mediaStream = stream;
                video.srcObject = stream;
                startBtn.disabled = true;
                detectBtn.disabled = false;
                stopBtn.disabled = false;
                videoWrapper.querySelector('#cameraOverlay').style.display = 'none';
                statusIndicator.classList.add('active');
                recognitionStats.classList.remove('hidden');
                scanningLine.classList.remove('hidden');
                updateFPS();
            })
            .catch(err => {
                Swal.fire('Error Kamera', 'Tidak dapat mengakses kamera.', 'error');
            });
    }

    function stopCamera() {
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
            video.srcObject = null;
            mediaStream = null;
            startBtn.disabled = false;
            detectBtn.disabled = true;
            stopBtn.disabled = true;
            videoWrapper.querySelector('#cameraOverlay').style.display = 'flex';
            statusIndicator.classList.remove('active');
            recognitionStats.classList.add('hidden');
            scanningLine.classList.add('hidden');
            if (detectionInterval) {
                clearInterval(detectionInterval);
                detectionInterval = null;
            }
            clearFaceFrames();

            window.location.reload(); // Reload seluruh halaman
        }
    }


    function updateFPS() {
        if (!mediaStream) return;
        frameCount++;
        const now = Date.now();
        const elapsed = now - lastFrameTime;
        if (elapsed >= 1000) {
            fpsCounter.textContent = Math.round((frameCount * 1000) / elapsed);
            frameCount = 0;
            lastFrameTime = now;
        }
        requestAnimationFrame(updateFPS);
    }

    function clearFaceFrames() {
        const frames = videoWrapper.querySelectorAll('.face-frame');
        frames.forEach(f => f.remove());
    }

    function drawFaceFrame(x, y, width, height, confidence, name = '', status = 'success') {
        clearFaceFrames();
        const frame = document.createElement('div');
        frame.className = 'face-frame ${status}';
        Object.assign(frame.style, {
            left: `${x}px`, top: `${y}px`, width: `${width}px`, height: `${height}px`
        });
        const badge = document.createElement('div');
        badge.className = 'confidence-badge';
        badge.textContent = `${name || 'Terdeteksi'} (Akurasi: ${Math.round(confidence)})`;
        frame.appendChild(badge);
        videoWrapper.appendChild(frame);
        setTimeout(() => frame.remove(), 2000);
    }

    async function detectFace() {
        if (!mediaStream || isProcessing || isCooldown) return;
        isProcessing = true;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = canvas.toDataURL('image/jpeg', 0.8);
        totalDetections++;
        detectionCount.textContent = totalDetections;

        const toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
        toast.fire({ icon: 'info', title: 'Mengenali wajah...' });

        try {
            const response = await fetch(FLASK_API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    image: imageData,
                    kelas_id: selectedKelas,
                })
            });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const data = await response.json();
            toast.close();

            if (data.face_location) {
                const { x, y, width, height } = data.face_location;
                const scaleX = video.offsetWidth / canvas.width;
                const scaleY = video.offsetHeight / canvas.height;
                drawFaceFrame(x * scaleX, y * scaleY, width * scaleX, height * scaleY, data.confidence, data.nama_siswa || '');
            }

            // === Tangani berbagai status ===
            switch (data.status) {
                case 'success':
                    if (data.siswa_nis) {
                        await markAttendance(data.siswa_nis, data.nama_siswa);
                    }
                    break;

                case 'wrong_class':
                    soundWarning.play();
                    Swal.fire(
                        'Wajah Dikenali di Kelas Lain',
                        data.message || 'Wajah dikenali, tapi tidak sesuai kelas.',
                        'warning'
                    );
                    break;

                case 'face_not_found':
                    soundFail.play();
                    Swal.fire('Wajah Tidak Terdeteksi', 'Pastikan wajah terlihat jelas.', 'warning');
                    break;

                case 'not_recognized':
                    soundFail.play();
                    Swal.fire('Wajah Tidak Dikenali', data.message || 'Wajah tidak ditemukan dalam sistem.', 'warning');
                    break;

                default:
                    soundFail.play();
                    Swal.fire('Kesalahan', data.message || 'Terjadi kesalahan.', 'error');
                    break;
            }

        } catch (err) {
            toast.close();
            soundFail.play();
            Swal.fire('Gagal', err.message || 'Gagal menghubungi server.', 'error');
        }

        isProcessing = false;
    }


    async function markAttendance(siswaNis, namaSiswa = '') {
        try {
            const response = await fetch('/mark-attendance', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
                body: JSON.stringify({ siswa_nis: siswaNis, nama_siswa: namaSiswa, jadwal_id: jadwalId })
            });
            const data = await response.json();

            if (data.status === 'success') {
                soundSuccess.play();
                Swal.fire(`${data.data.siswa} tercatat.`, 'Presensi Berhasil', 'success');
                if (typeof updateAttendanceList === 'function') {
                    updateAttendanceList(siswaNis, data.data.siswa);
                }
            } else if (data.status === 'already_marked') {
                soundAlready.play();
                Swal.fire(`${data.data.siswa} sudah tercatat.`, 'Sudah Presensi', 'info');
            } else if (data.status === 'not_allowed') {
                soundWarning.play();
                const siswaName = data.nama_siswa || 'Siswa';
                Swal.fire('Presensi Ditolak', `${siswaName} dikenali, tapi tidak terdaftar di kelas ini.`, 'warning');
            } else {
                soundFail.play();
                Swal.fire('Presensi Gagal', data.message || 'Gagal mencatat presensi.', 'error');
            }
        } catch (err) {
            Swal.fire('Error', 'Gagal menyimpan presensi.', 'error');
        }
    }

    startBtn.addEventListener('click', startCamera);
    detectBtn.addEventListener('click', () => {
        detectFace();
        if (!detectionInterval) {
            detectionInterval = setInterval(() => {
                if (!isProcessing && !isCooldown) detectFace();
            }, 5000);
        }
    });
    stopBtn.addEventListener('click', stopCamera);

    window.addEventListener('beforeunload', stopCamera);

    document.addEventListener('DOMContentLoaded', () => {
        const now = new Date();
        waktuPresensiInput.value = now.toISOString().slice(0, 16);

        navigator.permissions?.query({ name: 'camera' }).then(result => {
            if (result.state === 'denied') {
                Swal.fire('Izin Kamera Dibutuhkan', 'Aktifkan izin kamera di browser.', 'warning');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const waktuInput = document.getElementById('waktu_presensi');
        if (waktuInput) {
            const now = new Date();
            const localTime = now.toISOString().slice(0,16); // format "YYYY-MM-DDTHH:mm"
            waktuInput.value = localTime;
        }
    });
</script>


@endsection
