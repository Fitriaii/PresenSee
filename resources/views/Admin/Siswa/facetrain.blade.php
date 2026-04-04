@extends('layouts.app')

@section('content')
<div class="container p-6 mx-auto">

    {{-- Header Section --}}
    <div class="z-50 p-4 mb-4 bg-white rounded-sm shadow font-heading">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-purple-800 font-heading">Capture & Train Wajah Siswa</h2>
            <div class="flex space-x-1 font-sans text-xs text-gray-500">
                <a href="{{ route('admindashboard') }}" class="text-indigo-600 underline underline-offset-2 hover:text-indigo-700">Beranda</a>
                <span>/</span>
                <a href="{{ route('siswa.index') }}" class="text-indigo-600 underline underline-offset-2 hover:text-indigo-700">Siswa</a>
                <span>/</span>
                <span class="text-gray-400">Capture & Train</span>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        {{-- Camera Section --}}
        <div class="lg:col-span-2">
            <div class="overflow-hidden bg-white border border-gray-100 rounded-sm shadow-lg">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="flex items-center text-lg font-semibold text-gray-800">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Live Camera
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">Pastikan wajah terlihat jelas dan pencahayaan cukup</p>
                </div>

                <div class="p-4 text-center">
                    {{-- Video Preview --}}
                    <div class="relative inline-block">
                        <video id="video" width="640" height="480" autoplay muted playsinline
                            class="bg-gray-900 border-4 border-gray-100 rounded-lg shadow-lg"></video>
                        <canvas id="canvas" width="640" height="480" class="hidden"></canvas>

                        {{-- Camera Overlay --}}
                        <div class="absolute inset-0 border-2 border-purple-300 border-dashed rounded-lg opacity-50 pointer-events-none"></div>

                        {{-- Countdown Overlay --}}
                        <div id="countdown-overlay"
                            class="absolute inset-0 flex items-center justify-center transition-opacity duration-300 rounded-lg opacity-0 pointer-events-none bg-black/50">
                            <span id="countdown-number" class="font-extrabold text-white text-8xl"
                                style="text-shadow: 0 0 40px rgba(124,58,237,0.9);">3</span>
                        </div>

                        {{-- Flash Overlay --}}
                        <div id="flash-overlay" class="absolute inset-0 bg-white rounded-lg opacity-0 pointer-events-none"></div>
                    </div>

                    {{-- Control Buttons --}}
                    <div class="mt-6 space-y-3">
                        <div class="flex flex-col items-center justify-center gap-3 sm:flex-row">
                            <button id="captureBtn"
                                    class="inline-flex items-center px-8 py-3 font-semibold text-white transition-all duration-200 transform rounded-lg shadow-lg bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                                    disabled>
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <circle cx="12" cy="13" r="3"></circle>
                                </svg>
                                Ambil Foto
                            </button>

                            <a href="{{ route('siswa.index') }}"
                                class="px-6 py-3 text-sm font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400">
                                Batal
                            </a>
                        </div>
                        <p class="text-sm text-gray-500">Foto wajah untuk pendaftaran sistem presensi</p>
                    </div>

                    {{-- Loading State --}}
                    <div id="loading" class="hidden mt-6">
                        <div class="flex items-center justify-center space-x-3">
                            <div class="w-8 h-8 border-b-2 border-purple-600 rounded-full animate-spin"></div>
                            <span class="text-lg font-semibold text-purple-600">Memproses & menyimpan...</span>
                        </div>
                        <div class="h-2 mt-4 overflow-hidden bg-gray-200 rounded-full">
                            <div id="progressBar" class="h-full transition-all duration-300 rounded-full bg-gradient-to-r from-purple-600 to-blue-600" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Preview & Instructions Section --}}
        <div class="space-y-4">
            {{-- Instructions Card --}}
            <div class="bg-white border border-gray-100 rounded-sm shadow-lg">
                <div class="px-6 py-4 border-b border-gray-100 bg-blue-50">
                    <h3 class="flex items-center text-lg font-semibold text-gray-800">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Petunjuk
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex items-center justify-center flex-shrink-0 w-6 h-6 text-sm font-bold text-purple-600 bg-purple-100 rounded-full">1</div>
                        <p class="text-sm text-gray-700">Pastikan wajah terlihat jelas di dalam frame kamera</p>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="flex items-center justify-center flex-shrink-0 w-6 h-6 text-sm font-bold text-purple-600 bg-purple-100 rounded-full">2</div>
                        <div class="flex-1">
                            <p class="mb-2 text-sm text-gray-700">Hadapkan wajah tepat ke depan kamera</p>
                            <div class="flex items-center gap-3 p-3 border border-purple-100 rounded-lg bg-purple-50">
                                <span class="text-xl">👤</span>
                                <div>
                                    <p class="text-xs font-bold text-purple-700">Posisi: Depan</p>
                                    <p class="text-xs text-purple-600">Lihat lurus ke kamera, wajah tegak</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="flex items-center justify-center flex-shrink-0 w-6 h-6 text-sm font-bold text-purple-600 bg-purple-100 rounded-full">3</div>
                        <p class="text-sm text-gray-700">Klik tombol <strong>Ambil Foto</strong> — akan ada hitung mundur 3 detik</p>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="flex items-center justify-center flex-shrink-0 w-6 h-6 text-sm font-bold text-purple-600 bg-purple-100 rounded-full">4</div>
                        <p class="text-sm text-gray-700">Tetap diam hingga foto berhasil diambil</p>
                    </div>
                </div>
            </div>

            {{-- Preview Card --}}
            <div class="bg-white border border-gray-100 rounded-sm shadow-lg">
                <div class="px-6 py-4 border-b border-gray-100 bg-green-50">
                    <h3 class="flex items-center text-lg font-semibold text-gray-800">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Preview Gambar
                    </h3>
                </div>
                <div class="p-6">
                    <div id="emptyPreview" class="py-8 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm">Gambar akan muncul di sini</p>
                    </div>

                    <div id="preview" class="flex-col items-center hidden gap-3">
                        <img id="previewImg" src="" alt="Preview wajah"
                            class="object-cover w-full border-2 border-gray-100 shadow-sm rounded-xl">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            Foto berhasil diambil
                        </span>
                        <button id="retakeBtn" class="text-xs text-gray-400 underline transition-colors underline-offset-2 hover:text-purple-600">
                            Ambil ulang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const video            = document.getElementById('video');
        const canvas           = document.getElementById('canvas');
        const captureBtn       = document.getElementById('captureBtn');
        const countdownOverlay = document.getElementById('countdown-overlay');
        const countdownNum     = document.getElementById('countdown-number');
        const flashOverlay     = document.getElementById('flash-overlay');
        const loading          = document.getElementById('loading');
        const progressBar      = document.getElementById('progressBar');
        const emptyPreview     = document.getElementById('emptyPreview');
        const preview          = document.getElementById('preview');
        const previewImg       = document.getElementById('previewImg');
        const retakeBtn        = document.getElementById('retakeBtn');

        let currentStream = null;

        // Start camera
        function initializeCamera() {
            navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } })
                .then(stream => {
                    currentStream = stream;
                    video.srcObject = stream;
                    captureBtn.disabled = false;
                })
                .catch(err => console.error('Camera error:', err));
        }

        function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

        // Capture & send
        async function startCapture() {
            captureBtn.disabled = true;

            // Countdown 3..2..1
            countdownOverlay.style.opacity = '1';
            for (let i = 3; i >= 1; i--) {
                countdownNum.textContent = i;
                await sleep(1000);
            }
            countdownOverlay.style.opacity = '0';

            // Flash
            flashOverlay.style.opacity = '1';
            setTimeout(() => flashOverlay.style.opacity = '0', 120);

            // Take photo
            canvas.getContext('2d').drawImage(video, 0, 0, 640, 480);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.9);

            // Show preview
            emptyPreview.classList.add('hidden');
            previewImg.src = dataUrl;
            preview.classList.remove('hidden');
            preview.classList.add('flex');

            // Loading
            loading.classList.remove('hidden');
            let w = 0;
            const prog = setInterval(() => {
                w += 5;
                progressBar.style.width = Math.min(w, 90) + '%';
                if (w >= 90) clearInterval(prog);
            }, 60);

            // Send to server
            try {
                await sendImagesToServer([dataUrl]);
                progressBar.style.width = '100%';
                await sleep(200);
            } catch (err) {
                showErrorAlert('Gagal!', err.message);
                captureBtn.disabled = false;
            } finally {
                loading.classList.add('hidden');
            }
        }

        // Retake
        retakeBtn.addEventListener('click', () => {
            preview.classList.add('hidden');
            preview.classList.remove('flex');
            emptyPreview.classList.remove('hidden');
            previewImg.src = '';
            progressBar.style.width = '0%';
            captureBtn.disabled = false;
        });

        // Send to server
        async function sendImagesToServer(images) {
            const response = await fetch("{{ route('siswa.capture-train', $siswa->id) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ images })
            });

            const data = await response.json();
            if (response.ok && data.status === 'success') {
                showSuccessAlert('Berhasil! 🎉', data.message, () => {
                    if (data.redirect) window.location.href = data.redirect;
                });
            } else {
                throw new Error(data.message || 'Gagal proses training.');
            }
        }

        function showSuccessAlert(title, message, callback) {
            Swal.fire({
                icon: 'success',
                title, text: message,
                confirmButtonColor: '#6B46C1',
                customClass: { popup: 'rounded-xl', confirmButton: 'rounded-lg' }
            }).then(callback);
        }

        function showErrorAlert(title, message) {
            Swal.fire({
                icon: 'error',
                title, text: message,
                confirmButtonColor: '#e53e3e',
                customClass: { popup: 'rounded-xl', confirmButton: 'rounded-lg' }
            });
        }

        function cleanup() {
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
                currentStream = null;
            }
        }

        // Init
        captureBtn.addEventListener('click', startCapture);
        window.addEventListener('beforeunload', cleanup);
        initializeCamera();

        window.cameraTraining = { startCapture, initializeCamera, cleanup };
    });
</script>


@endsection
