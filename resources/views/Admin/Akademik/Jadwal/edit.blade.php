@extends('layouts.app')

@section('content')

<div class="container p-6 mx-auto">
    {{-- Header Section --}}
    <div class="z-50 p-4 mb-4 bg-white rounded-sm shadow font-heading">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-purple-800 font-heading">Edit Data Kelas</h2>
            <div class="flex space-x-1 font-sans text-xs text-gray-500">
                <a href="{{ route('admindashboard') }}" class="text-indigo-600 underline underline-offset-2 hover:text-indigo-700">Beranda</a>
                <span>/</span>
                <a href="{{ route('jadwal.index') }}" class="text-indigo-600 underline underline-offset-2 hover:text-indigo-700">Jadwal</a>
                <span>/</span>
                <span class="text-gray-400">Edit Jadwal</span>
            </div>
        </div>
    </div>

    {{-- Input Form --}}
    <div class="w-full p-6 mb-8 bg-white border border-gray-200 rounded-sm shadow-sm drop-shadow">

        <form id="userForm" action="{{ route('jadwal.update', $jadwal) }}" method="POST" class="space-y-3">
            @csrf
            @method('PATCH')

            <div class="space-y-6">
                <h3 class="pb-2 text-lg font-semibold text-purple-800 border-b border-gray-200 font-heading">Informasi Jadwal</h3>

                <!-- Hari -->
                <div class="grid items-start grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:text-right">
                        <label for="hari" class="block mb-1 text-sm font-semibold text-gray-700 font-heading">
                            Hari <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <div class="lg:col-span-2">
                        <select
                            name="hari"
                            id="hari"
                            required
                            class="w-full px-4 py-3 text-sm transition-colors duration-200 border border-gray-300 rounded-lg outline-none hover:border-purple-400 dark:bg-white dark:text-gray-900 dark:border-gray-300 focus:ring-purple-500/20 focus:border-purple-500"
                        >
                            <option value="" disabled {{ old('hari', $jadwal->hari ?? '') == '' ? 'selected' : '' }}>Pilih Hari</option>
                            <option value="Senin" {{ old('hari', $jadwal->hari ?? '') == 'Senin' ? 'selected' : '' }}>Senin</option>
                            <option value="Selasa" {{ old('hari', $jadwal->hari ?? '') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                            <option value="Rabu" {{ old('hari', $jadwal->hari ?? '') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                            <option value="Kamis" {{ old('hari', $jadwal->hari ?? '') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                            <option value="Jumat" {{ old('hari', $jadwal->hari ?? '') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                            <option value="Sabtu" {{ old('hari', $jadwal->hari ?? '') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                        </select>
                        @error('hari')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Jam Mulai -->
                <div class="grid items-start grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:text-right">
                        <label for="jam_mulai" class="block mb-1 text-sm font-semibold text-gray-700 font-heading">
                            Jam Mulai <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <div class="lg:col-span-2">
                        <div class="relative">
                            <input
                                type="time"
                                id="jam_mulai"
                                name="jam_mulai"
                                value="{{ old('jam_mulai', $jadwal ?? '') }}"
                                required
                                class="w-full px-4 py-3 text-sm transition-colors duration-200 border border-gray-300 rounded-lg outline-none hover:border-purple-400 dark:bg-white dark:text-gray-900 dark:border-gray-300 focus:ring-purple-500/20 focus:border-purple-500"
                            />
                            <button
                                type="button"
                                onclick="document.getElementById('jam_mulai').showPicker()"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 transition-colors hover:text-purple-600"
                            >
                                <!-- Heroicons Jam -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                        </div>
                        @error('jam_mulai')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Jam Selesai -->
                <div class="grid items-start grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:text-right">
                        <label for="jam_selesai" class="block mb-1 text-sm font-semibold text-gray-700 font-heading">
                            Jam Selesai <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <div class="lg:col-span-2">
                        <div class="relative">
                            <input
                            type="time"
                            id="jam_selesai"
                            name="jam_selesai"
                            value="{{ old('jam_selesai', $jadwal ?? '') }}"
                            required
                            class="w-full px-4 py-3 text-sm transition-colors duration-200 border border-gray-300 rounded-lg outline-none hover:border-purple-400 dark:bg-white dark:text-gray-900 dark:border-gray-300 focus:ring-purple-500/20 focus:border-purple-500"
                        />
                        <button
                                type="button"
                                onclick="document.getElementById('jam_selesai').showPicker()"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 transition-colors hover:text-purple-600"
                            >
                                <!-- Heroicons Jam -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                        </div>
                        @error('jam_selesai')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Kelas -->
                <div class="grid items-start grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:text-right">
                        <label for="kelas" class="block mb-1 text-sm font-semibold text-gray-700 font-heading">
                            Kelas <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <div class="lg:col-span-2">
                        <select
                            name="kelas_id"
                            id="kelas"
                            required
                            class="w-full px-4 py-3 text-sm transition-colors duration-200 border border-gray-300 rounded-lg outline-none hover:border-purple-400 dark:bg-white dark:text-gray-900 dark:border-gray-300 focus:ring-purple-500/20 focus:border-purple-500"
                        >
                            <option value="" disabled>Pilih Kelas</option>
                            @foreach ($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : ''}}>
                                    {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                        @error('kelas')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Mata Pelajaran -->
                <div class="grid items-start grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:text-right">
                        <label for="mapel" class="block mb-1 text-sm font-semibold text-gray-700 font-heading">
                            Mata Pelajaran <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <div class="lg:col-span-2">
                        <select
                            id="mapel"
                            name="mapel_id"
                            class="w-full px-4 py-3 text-sm transition-colors duration-200 border border-gray-300 rounded-lg outline-none hover:border-purple-400 dark:bg-white dark:text-gray-900 dark:border-gray-300 focus:ring-purple-500/20 focus:border-purple-500">
                            <option value="" disabled {{ old('mapel_id') ? '' : 'selected' }}>Pilih Mapel</option>
                            @foreach ($mapelList as $mapel)
                               <option value="{{ $mapel->id }}"
                                    data-guru="{{ $mapel->guru->nama_guru ?? '' }}"
                                    {{ old('mapel_id', $jadwal->mapel_id) == $mapel->id ? 'selected' : '' }}>
                                    {{ $mapel->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                        @error('mapel_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Guru Pengampu -->
                <div class="grid items-start grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:text-right">
                        <label for="guru_pengampu" class="block mb-1 text-sm font-semibold text-gray-700 font-heading">
                            Guru Pengampu <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <div class="lg:col-span-2">
                        <input type="hidden" name="guru_pengampu" id="guru_pengampu_hidden" value="{{ old('guru_pengampu', $jadwal->guru_pengampu) }}">
                        <input
                            type="text"
                            id="guru_pengampu"
                            class="w-full px-4 py-3 mb-4 text-sm transition-colors duration-200 border border-gray-300 rounded-lg outline-none hover:border-purple-400 dark:bg-white dark:text-gray-900 dark:border-gray-300 focus:ring-purple-500/20 focus:border-purple-500"
                            readonly
                            placeholder="Guru Pengampu akan tampil di sini"
                            value="{{ old('guru_pengampu', $jadwal->guru_pengampu) }}">

                        @error('guru_pengampu')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex flex-col justify-end gap-4 pt-6 border-t border-gray-200 sm:flex-row">
                <a href="{{ route('jadwal.index') }}" type="button"
                    class="px-6 py-3 text-sm font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-3 text-sm font-semibold text-white transition-colors duration-200 bg-purple-600 rounded-lg shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-200 hover:shadow-md">
                    <span class="flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Data Jadwal
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    window.addEventListener('load', function () {
        const mapel = document.getElementById('mapel');
        const guruInput = document.getElementById('guru_pengampu');
        const guruHidden = document.getElementById('guru_pengampu_hidden');

        const selectedOption = mapel.options[mapel.selectedIndex];
        const guru = selectedOption.getAttribute('data-guru');

        // ✅ hanya set kalau ADA nilainya
        if (guru && guru.trim() !== '') {
            guruInput.value = guru;
            guruHidden.value = guru;
        }
    });

    const inputJamMulai = document.getElementById('jam_mulai');
    if (inputJamMulai && typeof inputJamMulai.showPicker === 'function') {
        inputJamMulai.addEventListener('focus', () => {
            inputJamMulai.showPicker();
        });
    }
    const inputJamSelesai = document.getElementById('jam_selesai');
    if (inputJamSelesai && typeof inputJamSelesai.showPicker === 'function') {
        inputJamSelesai.addEventListener('focus', () => {
            inputJamSelesai.showPicker();
        });
    }


</script>



@endsection
