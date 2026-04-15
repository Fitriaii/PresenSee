@extends('layouts.app')

@section('content')
<div class="container p-6 mx-auto">
    <div class="mb-4 bg-white rounded-sm shadow-sm">
        <div class="flex items-center justify-between p-6">
            <div>
                <h1 class="text-xl font-bold text-purple-800 font-heading">Jadwal Mengajar</h1>
                <p class="mt-1 text-sm text-gray-600">Kelola informasi jadwal mengajar guru</p>
            </div>
            <nav class="flex space-x-1 font-sans text-xs text-gray-500">
                <a href="{{ route('gurudashboard') }}"
                   class="text-indigo-600 underline underline-offset-2 hover:text-indigo-700">
                    Beranda
                </a>
                <span>/</span>
                <span class="text-gray-400">Jadwal Mengajar</span>
            </nav>
        </div>
    </div>

    <div class="z-50 p-6 mb-6 bg-white rounded-sm shadow-lg  font-heading">

        <div class="mx-auto max-w-7xl">
            <!-- Top Row: Action Buttons -->
            <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
                <!-- Kanan: Search -->
                <form id="searchForm" method="GET" action="{{ route('jadwalAjar.index') }}" class="w-full">
                    <label for="searchInput" class="block mb-1 text-sm font-medium text-gray-700">Pencarian</label>
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            id="searchInput"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari jadwal..."
                            class="w-full py-2 pl-10 pr-4 text-sm border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-white dark:text-gray-900 dark:border-gray-300"
                        >
                    </div>
                </form>
            </div>

            <!-- Filter & Controls -->
            <form method="GET" action="{{ route('jadwalAjar.index') }}" id="filterForm" class="space-y-4">

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">

                    <!-- Hari Filter -->
                    <div class="space-y-2">
                        <label for="hari" class="block text-sm font-semibold text-gray-700">Hari</label>
                        <select name="hari" id="hari"
                            class="w-full px-3 py-2.5 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:outline-none transition-colors duration-200 dark:bg-white dark:text-gray-900 dark:border-gray-300">
                            <option value="">Semua Hari</option>
                            <option value="Senin" {{ request('hari') == 'Senin' ? 'selected' : '' }}>Senin</option>
                            <option value="Selasa" {{ request('hari') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                            <option value="Rabu" {{ request('hari') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                            <option value="Kamis" {{ request('hari') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                            <option value="Jumat" {{ request('hari') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                            <option value="Sabtu" {{ request('hari') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                        </select>
                    </div>

                    <!-- Guru Filter -->
                    <div class="space-y-2">
                        <label for="kelas" class="block text-sm font-semibold text-gray-700">Kelas</label>
                        <select name="kelas" id="kelas"
                            class="w-full px-3 py-2.5 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:outline-none transition-colors duration-200 dark:bg-white dark:text-gray-900 dark:border-gray-300">
                            <option value="">Semua Kelas</option>
                            @foreach ($semuaKelas as $kelas)
                                <option value="{{ $kelas->id }}" {{ request('kelas') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tahun Filter -->
                    <div class="space-y-2">
                        <label for="mapel" class="block text-sm font-semibold text-gray-700">Mata Pelajaran</label>
                        <select name="mapel" id="mapel"
                            class="w-full px-3 py-2.5 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:outline-none transition-colors duration-200 dark:bg-white dark:text-gray-900 dark:border-gray-300">
                            <option value="">Semua Mapel</option>
                            @foreach ($semuaMapel as $mapel)
                                <option value="{{ $mapel->id }}" {{ request('mapel') == $mapel->id ? 'selected' : '' }}>
                                    {{ $mapel->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div class="space-y-2">
                        <label for="sort" class="block text-sm font-semibold text-gray-700">Urutkan Berdasarkan</label>
                        <select id="sort" name="sort" class="w-full px-3 py-2.5 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:outline-none transition-colors duration-200 dark:bg-white dark:text-gray-900 dark:border-gray-300">
                            <option value="created_desc" {{ request('sort') === 'created_desc' ? 'selected' : '' }}>Terbaru Ditambahkan</option>
                            <option value="created_asc" {{ request('sort') === 'created_asc' ? 'selected' : '' }}>Terlama Ditambahkan</option>
                            <option value="jam_mulai_asc" {{ request('sort') == 'jam_mulai_asc' ? 'selected' : '' }}>Jam Mulai (Awal ke Akhir)</option>
                            <option value="jam_mulai_desc" {{ request('sort') == 'jam_mulai_desc' ? 'selected' : '' }}>Jam Mulai (Akhir ke Awal)</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-transparent">Reset</label>
                        <a href="{{ route('jadwalAjar.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 hover:border-gray-400 focus:ring-2 focus:ring-gray-500 focus:outline-none transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset Filter
                        </a>
                    </div>
                </div>
            </form>

            <!-- Results Info -->
            <div class="pt-4 mt-4 border-t border-gray-200">
                <div class="flex flex-col gap-3 text-sm text-gray-600 md:flex-row md:items-center md:justify-between">
                    <!-- Kiri: Info jumlah data -->
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-4">
                        <span>
                            Menampilkan
                            <span class="font-medium text-gray-900">{{ $jadwal->firstItem() ?? 0 }}–{{ $jadwal->lastItem() ?? 0 }}</span>
                            dari
                            <span class="font-medium text-gray-900">{{ $jadwal->total() }}</span> data
                        </span>
                    </div>

                    <!-- Kanan: Info waktu update -->
                    @if ($jadwal->count())
                        <div class="text-gray-500">
                            Terakhir diperbarui:
                            <span class="font-medium">{{ $jadwal->first()->updated_at->diffForHumans() }}</span>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <div class="mt-4 overflow-auto rounded-sm shadow-sm">
            <table class="min-w-full bg-white rounded shadow-md">
                <thead>
                    <tr class="text-sm font-semibold text-gray-700 bg-gray-100 font-heading">
                        <th class="px-4 py-3 text-left whitespace-nowrap">No</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Hari</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Kelas</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Jam Mulai</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Mapel</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse ($groupedKelas as $hari => $kelasGroup)
                        @php $rowspanHari = $kelasGroup->flatten()->count(); @endphp
                        @php $firstHariPrinted = false; @endphp

                        @foreach ($kelasGroup as $kelasId => $j)
                            @foreach ($j as $index => $item)
                                <tr class="text-sm text-gray-700 border-t hover:bg-gray-50">
                                    {{-- Kolom No dan Hari hanya muncul sekali per hari --}}
                                    @if (!$firstHariPrinted)
                                        <td class="px-4 py-3 text-left whitespace-nowrap" rowspan="{{ $rowspanHari }}">{{ $no++ }}</td>
                                        <td class="px-4 py-3 text-left whitespace-nowrap" rowspan="{{ $rowspanHari }}">{{ $hari }}</td>
                                        @php $firstHariPrinted = true; @endphp
                                    @endif

                                    {{-- Kolom Kelas hanya muncul sekali per kelas --}}
                                    @if ($index === 0)
                                        <td class="px-4 py-3 tracking-wider text-left whitespace-nowrap" rowspan="{{ $j->count() }}">
                                            {{ $item->kelas->nama_kelas }}
                                        </td>
                                    @endif

                                    <td class="px-4 py-3 tracking-wider text-left whitespace-nowrap">{{ $item->jam_mulai }}</td>
                                    <td class="px-4 py-3 tracking-wider text-left whitespace-nowrap">{{ $item->mapel->nama_mapel }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @empty
                        <tr class="text-sm text-gray-500">
                            <td colspan="6" class="px-4 py-3 text-center">Tidak ada data jadwal yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
        <div class="mt-4">
            @include('components.pagination', ['data' => $jadwal])
        </div>
    </div>
</div>

<script>
    // Live search debounce
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    let typingTimer;
    const delay = 400;

    if (searchInput && searchForm) {
        searchInput.addEventListener('keyup', () => {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                searchForm.submit();
            }, delay);
        });

        searchInput.addEventListener('keydown', () => clearTimeout(typingTimer));
    }

    // Auto-submit filter form saat filter berubah
    const filterForm = document.getElementById('filterForm');
    ['hari', 'mapel','kelas', 'sort'].forEach(id => {
        document.getElementById(id)?.addEventListener('change', () => {
            filterForm?.submit();
        });
    });

</script>
@endsection
