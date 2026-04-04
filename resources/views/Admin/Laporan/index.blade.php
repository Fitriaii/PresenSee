@extends('layouts.app')

@section('content')
<div class="container p-6 mx-auto">
    <!-- Header dan Breadcrumb -->
    <div class="mb-4 bg-white rounded-sm shadow-sm">
        <div class="flex items-center justify-between p-6">
            <div>
                <h1 class="text-xl font-bold text-purple-800 font-heading">Laporan Presensi</h1>
                <p class="mt-1 text-sm text-gray-600">Kelola dan filter laporan presensi siswa</p>
            </div>
            <nav class="flex space-x-1 font-sans text-xs text-gray-500">
                <a href="{{ route('admindashboard') }}"
                   class="text-indigo-600 underline underline-offset-2 hover:text-indigo-700">
                    Beranda
                </a>
                <span>/</span>
                <span class="text-gray-400">Laporan Presensi</span>
            </nav>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-sm shadow-sm">

        <div class="p-6">

            <div x-data="{ show: true }" x-show="show" x-transition class="p-4 mb-6 text-blue-500 bg-blue-100 border border-blue-200 rounded-lg">
                <div class="flex items-start justify-between">
                    <div class="w-full">
                        <h1 class="mb-1 text-base font-bold text-blue-600 font-heading">Export Laporan Presensi</h1>
                        <hr class="mb-2 border-blue-300">
                        <div class="flex items-start gap-2 font-sans text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 stroke-current shrink-0" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p>Export laporan hanya akan mempertimbangkan <span class="font-semibold text-blue-700">filter mata pelajaran</span> yang dipilih.</p>
                                <p class="mt-1 text-blue-600">Meskipun Anda menerapkan filter lainnya seperti kelas, tahun ajaran, atau periode, data yang diekspor tetap akan mencakup seluruh data berdasarkan mata pelajaran yang dipilih.</p>
                                <p class="mt-1 text-xs italic text-blue-500">Pastikan Anda memilih mata pelajaran sebelum menekan tombol export.</p>
                            </div>
                        </div>
                    </div>
                    <button @click="show = false" class="ml-4 text-blue-600 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mt-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <!-- Filter Header -->
            <div class="flex items-center pb-4 mb-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-purple-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M6 13.5V3.75m0 9.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 3.75V16.5m12-3V3.75m0 9.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 3.75V16.5m-6-9V3.75m0 3.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 9.75V10.5" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-purple-800 font-heading">Filter Laporan</h2>
                        <p class="font-sans text-sm text-gray-600">Pilih kriteria untuk menampilkan laporan</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('laporan.index') }}" method="GET" class="space-y-6">
                <!-- Filter Grid -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Periode -->
                    <div class="space-y-2">
                        <label for="periode" class="block text-sm font-medium text-gray-700">
                            Periode Laporan
                        </label>
                        <select id="periode" name="periode"
                                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 dark:bg-white dark:text-gray-900 dark:border-gray-300 focus:ring-purple-500/20 focus:border-purple-500">
                            <option value="harian" {{ request('periode') == 'harian' ? 'selected' : '' }}>Harian</option>
                            <option value="mingguan" {{ request('periode') == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                            <option value="bulanan" {{ request('periode') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                            <option value="tahunan" {{ request('periode') == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                            <option value="custom" {{ request('periode') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>

                    <!-- Tahun Ajaran -->
                    <div class="space-y-2">
                        <label for="tahun_ajaran" class="block text-sm font-medium text-gray-700">
                            Tahun Ajaran
                        </label>
                        <select id="tahun_ajaran" name="tahun_ajaran_id"
                                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 dark:bg-white dark:text-gray-900 dark:border-gray-300 focus:ring-purple-500/20 focus:border-purple-500">
                            <option value="" {{ !request('tahun_ajaran_id') ? 'selected' : '' }}>
                                Semua Tahun Ajaran
                            </option>
                            @foreach ($semuaTahunAjaran as $ta)
                                <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->tahun_ajaran }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kelas -->
                    <div class="space-y-2">
                        <label for="kelas" class="block text-sm font-medium text-gray-700">
                            Kelas
                        </label>
                        <select id="kelas" name="kelas_id"
                                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 dark:bg-white dark:text-gray-900 dark:border-gray-300 focus:ring-purple-500/20 focus:border-purple-500">
                            <option value="" {{ !request('kelas_id') ? 'selected' : '' }}>
                                Semua Kelas
                            </option>
                            @foreach ($semuaKelas as $k)
                                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mata Pelajaran -->
                    <div class="space-y-2">
                        <label for="mapel" class="block text-sm font-medium text-gray-700">
                            Mata Pelajaran
                        </label>
                        <select id="mapel" name="mapel_id"
                                class="w-full px-3 py-2 text-sm text-gray-900 transition-colors bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 dark:bg-white dark:text-gray-900 dark:border-gray-300 focus:ring-purple-500/20 focus:border-purple-500">
                            <option value="" {{ !request('mapel_id') ? 'selected' : '' }}>
                                Semua Mata Pelajaran
                            </option>
                            @foreach ($semuaMapel as $m)
                                <option value="{{ $m->id }}" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Dynamic Fields -->
                <div id="dynamic-fields" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Dynamic fields will be inserted here by JavaScript -->
                </div>

                <!-- Search Section -->
                <div class="pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 gap-4 mb-6">
                        <label for="search" class="text-sm font-medium text-gray-700">Pencarian</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </span>
                            <input
                                type="text"
                                id="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Cari nama siswa, NIS, atau keterangan..."
                                class="w-full py-2 pl-10 pr-4 text-sm border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 dark:bg-white dark:text-gray-900 dark:border-gray-300 focus:ring-purple-500/20 focus:border-purple-500"
                            >
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col pt-6 space-y-4 border-t border-gray-200 sm:flex-row sm:items-center sm:justify-between sm:space-y-0 sm:space-x-3">
                    <div class="text-sm text-gray-600">
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            Pilih periode untuk menampilkan filter tambahan
                        </span>
                    </div>

                    <div class="flex flex-col items-stretch w-full space-y-2 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-3 sm:w-auto">
                        <a href="{{ route('laporan.index') }}" type="reset"
                           class="inline-flex items-center justify-center flex-1 w-1/2 px-6 py-2 text-sm font-medium text-gray-700 transition-all duration-200 border border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 hover:border-gray-400 focus:ring-2 focus:ring-gray-500 focus:outline-none">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset
                        </a>

                        <button type="submit"
                                class="inline-flex items-center justify-center flex-1 w-full px-6 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Tampilkan
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <div class="mt-1 bg-white rounded-sm shadow-sm">
        <div class="p-4 bg-white rounded-sm shadow-sm">
            <h3 class="mb-2 text-lg font-semibold text-purple-800">Laporan Detail Kehadiran</h3>
            <p class="text-sm text-gray-600">Berikut adalah detail laporan kehadiran berdasarkan filter yang telah Anda pilih.</p>
            <div class="mt-4 overflow-auto">
                <table class="min-w-full bg-white rounded shadow-md">
                    <thead>
                        <tr class="text-sm text-gray-700 bg-gray-100">
                            <th class="p-3 text-left uppercase">No</th>
                            <th class="p-3 text-left uppercase">NIS</th>
                            <th class="p-3 text-left uppercase">Nama Siswa</th>
                            <th class="p-3 text-left uppercase">Kelas</th>
                            <th class="p-3 text-center uppercase">Hadir</th>
                            <th class="p-3 text-center uppercase">Izin</th>
                            <th class="p-3 text-center uppercase">Sakit</th>
                            <th class="p-3 text-center uppercase">Alpha</th>
                            <th class="p-3 text-center uppercase">Total</th>
                            <th class="p-3 text-center uppercase">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rekapPerSiswa as $index => $siswa)
                            <tr class="text-sm text-gray-700 border-t hover:bg-gray-50">
                                <td class="p-3">{{ $data->firstItem() + $index }}</td>
                                <td class="p-3">{{ $siswa['nis'] }}</td>
                                <td class="p-3">{{ $siswa['nama'] }}</td>
                                <td class="p-3">{{ $siswa['kelas'] }}</td>
                                <td class="p-3 text-center">{{ $siswa['hadir'] }}</td>
                                <td class="p-3 text-center">{{ $siswa['izin'] }}</td>
                                <td class="p-3 text-center">{{ $siswa['sakit'] }}</td>
                                <td class="p-3 text-center">{{ $siswa['alpha'] }}</td>
                                <td class="p-3 text-center">{{ $siswa['total'] }}</td>
                                <td class="p-3 text-center">
                                    <span class="font-semibold {{ $siswa['presentase'] >= 90 ? 'text-green-600' : ($siswa['presentase'] >= 80 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $siswa['presentase'] }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-4 text-center text-gray-500">
                                    Tidak ada data laporan yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">

                @include('components.pagination', ['data' => $data])
            </div>
        </div>
    </div>

    <!-- Export Actions -->
    <div class="mt-1 bg-white rounded-sm shadow-sm">
        <div class="p-6">
            <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <div>
                <h3 class="text-lg font-semibold text-purple-800">Export Laporan Presensi</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Laporan yang akan diunduh hanya berdasarkan <span class="font-semibold text-purple-900">mata pelajaran</span> yang dipilih.
                </p>
                <p class="mt-1 text-xs italic text-red-600">
                    *Filter lain seperti kelas, tahun ajaran, dan periode tidak memengaruhi isi laporan.
                </p>
            </div>

            <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-3">
                <form id="exportPdfForm" action="{{ route('laporan.exportPdf') }}" method="POST" class="inline-block">
                    @csrf

                    <!-- Hanya mapel_id -->
                    <input type="hidden" name="mapel_id" value="{{ request('mapel_id') }}">

                    <button type="submit"
                        class="w-full px-4 py-2 text-red-700 transition-colors border border-red-200 rounded-lg sm:w-auto bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <span class="flex items-center justify-center text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            Export PDF
                        </span>
                    </button>
                </form>
            </div>
        </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const periodeSelect = document.getElementById('periode');
        const dynamicFields = document.getElementById('dynamic-fields');
        const form = document.querySelector('form#searchForm'); // Ganti dari getElementById ke querySelector untuk memastikan cocok

        // Live search
        const searchInput = document.getElementById('searchInput');
        if (searchInput && form) {
            let typingTimer;
            const delay = 400;
            searchInput.addEventListener('input', () => {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    form.submit();
                }, delay);
            });
        }

        // Dynamic fields berdasarkan periode
        function updateDynamicFields() {
            if (!periodeSelect || !dynamicFields) return;

            const periode = periodeSelect.value;
            dynamicFields.innerHTML = '';

            const bulanOptions = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];

            const currentYear = new Date().getFullYear();
            const tahunOptions = Array.from({ length: 5 }, (_, i) => currentYear - 2 + i);

            const inputClass = "w-full text-sm px-3 py-2 bg-white text-gray-900 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 dark:bg-white dark:text-gray-900 dark:border-gray-300 focus:ring-purple-500/20 focus:border-purple-500 transition-colors";
            const labelClass = "block text-sm font-medium text-gray-700 mb-2";

            const getParam = (param) => new URLSearchParams(window.location.search).get(param);

            if (periode === 'harian') {
                dynamicFields.innerHTML = `
                    <div class="space-y-2">
                        <label class="${labelClass}">Tanggal</label>
                        <div class="relative">
                            <input
                                type="date"
                                name="tanggal"
                                id="tanggal"
                                value="${getParam('tanggal') || ''}"
                                class="${inputClass}"
                            />
                            <button
                                type="button"
                                onclick="document.getElementById('tanggal').showPicker()"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 transition-colors hover:text-purple-600"
                            >
                                <!-- Heroicons Calendar -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1
                                        2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18
                                        0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0
                                        21 18.75m-18 0v-7.5A2.25 2.25 0 0 1
                                        5.25 9h13.5A2.25 2.25 0 0 1 21
                                        11.25v7.5m-9-6h.008v.008H12v-.008ZM12
                                        15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75
                                        15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5
                                        15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0
                                        2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0
                                        2.25h.008v.008H16.5V15Z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                `;

                // Tambahkan listener showPicker jika didukung
                setTimeout(() => {
                    const inputTanggal = document.getElementById('tanggal');
                    if (inputTanggal && typeof inputTanggal.showPicker === 'function') {
                        inputTanggal.addEventListener('focus', () => {
                            inputTanggal.showPicker();
                        });
                    }
                }, 10); // Tunggu elemen dirender terlebih dahulu

            } else if (periode === 'mingguan' || periode === 'custom') {
                dynamicFields.innerHTML = `
                    <div class="space-y-2">
                        <label class="${labelClass}">Dari Tanggal</label>
                        <div class="relative">
                            <input
                                type="date"
                                id="start_date"
                                name="start_date"
                                value="${getParam('start_date') || ''}"
                                class="${inputClass}"
                            />
                            <button
                                type="button"
                                onclick="document.getElementById('start_date').showPicker()"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 transition-colors hover:text-purple-600"
                            >
                                <!-- Heroicons Calendar -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1
                                        2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18
                                        0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0
                                        21 18.75m-18 0v-7.5A2.25 2.25 0 0 1
                                        5.25 9h13.5A2.25 2.25 0 0 1 21
                                        11.25v7.5m-9-6h.008v.008H12v-.008ZM12
                                        15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75
                                        15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5
                                        15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0
                                        2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0
                                        2.25h.008v.008H16.5V15Z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="${labelClass}">Sampai Tanggal</label>
                        <div class="relative">
                            <input
                                type="date"
                                id="end_date"
                                name="end_date"
                                value="${getParam('end_date') || ''}"
                                class="${inputClass}"
                            />
                            <button
                                type="button"
                                onclick="document.getElementById('end_date').showPicker()"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 transition-colors hover:text-purple-600"
                            >
                                <!-- Heroicons Calendar -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1
                                        2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18
                                        0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0
                                        21 18.75m-18 0v-7.5A2.25 2.25 0 0 1
                                        5.25 9h13.5A2.25 2.25 0 0 1 21
                                        11.25v7.5m-9-6h.008v.008H12v-.008ZM12
                                        15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75
                                        15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5
                                        15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0
                                        2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0
                                        2.25h.008v.008H16.5V15Z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                `;

                // Tambahkan showPicker() saat input difokuskan
                setTimeout(() => {
                    const inputStart = document.getElementById('start_date');
                    const inputEnd = document.getElementById('end_date');

                    if (inputStart && typeof inputStart.showPicker === 'function') {
                        inputStart.addEventListener('focus', () => inputStart.showPicker());
                    }
                    if (inputEnd && typeof inputEnd.showPicker === 'function') {
                        inputEnd.addEventListener('focus', () => inputEnd.showPicker());
                    }
                }, 10);
            } else if (periode === 'bulanan') {
                const selectedBulan = getParam('bulan') || String(new Date().getMonth() + 1).padStart(2, '0');
                const selectedTahun = getParam('tahun') || currentYear;

                dynamicFields.innerHTML = `
                    <div class="space-y-2">
                        <label class="${labelClass}">Bulan</label>
                        <select name="bulan" class="${inputClass}">
                            ${bulanOptions.map((b, i) => {
                                const val = String(i + 1).padStart(2, '0');
                                return `<option value="${val}" ${val === selectedBulan ? 'selected' : ''}>${b}</option>`;
                            }).join('')}
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="${labelClass}">Tahun</label>
                        <select name="tahun" class="${inputClass}">
                            ${tahunOptions.map(t => `<option value="${t}" ${t == selectedTahun ? 'selected' : ''}>${t}</option>`).join('')}
                        </select>
                    </div>
                `;
            } else if (periode === 'tahunan') {
                const selectedTahun = getParam('tahun') || currentYear;
                dynamicFields.innerHTML = `
                    <div class="space-y-2">
                        <label class="${labelClass}">Tahun</label>
                        <select name="tahun" class="${inputClass}">
                            ${tahunOptions.map(t => `<option value="${t}" ${t == selectedTahun ? 'selected' : ''}>${t}</option>`).join('')}
                        </select>
                    </div>
                `;
            }
        }

        if (periodeSelect) {
            periodeSelect.addEventListener('change', updateDynamicFields);
            updateDynamicFields();
        }

        // Reset button
        const resetButton = document.querySelector('button[type="reset"]');
        if (resetButton && form) {
            resetButton.addEventListener('click', function (e) {
                e.preventDefault();
                form.reset();
                window.location.href = window.location.pathname;
            });
        }

        // Export confirmation
        document.querySelectorAll('form[action*="export"]').forEach(exportForm => {
            exportForm.addEventListener('submit', function (e) {
                e.preventDefault(); // Cegah submit dulu

                const format = this.action.includes('excel') ? 'Excel' : 'PDF';

                Swal.fire({
                    title: 'Export PDF?',
                    text: 'Laporan hanya akan berisi data berdasarkan mata pelajaran yang dipilih. Filter lain tidak digunakan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Lanjutkan Export',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('exportPdfForm').submit();
                    }
                });
            });
        });

    });
</script>
@if (session('status') === 'success' && session('message'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: @json(session('message')),
            showConfirmButton: false,
            timer: 2000
        });
    </script>
@endif

@if (session('status') === 'error' && session('message'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: @json(session('message')),
            showConfirmButton: true
        });
    </script>
@endif


@endsection
