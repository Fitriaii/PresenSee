<?php

use App\Exports\JadwalExport;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\MapelController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\FaceRecognitionController;
use App\Http\Controllers\guru\DaftarSiswaController;
use App\Http\Controllers\guru\GuruDashboardController;
use App\Http\Controllers\guru\JadwalMengajarController;
use App\Http\Controllers\guru\LaporanGuruController;
use App\Http\Controllers\Guru\PresensiController;
use App\Http\Controllers\guru\ProfileGuruController;
use App\Http\Controllers\ProfileController;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/test-404', function () {
        abort(404);
    })->name('test.404');

    Route::get('/dashboard', function (Request $request) {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admindashboard'); // Route untuk admin
        } elseif ($user->hasRole('guru')) {
            return redirect()->route('gurudashboard'); // Route untuk penduduk
        }
        // Redirect default jika role tidak dikenali
        return redirect()->route('login'); // Sesuaikan dengan route default Anda
    })->name('dashboard');

    Route::middleware('role:admin')->group(function(){
        Route::get('/admindashboard', [DashboardController::class, 'index'])->name('admindashboard');
        Route::resource('admin', AdminController::class);
        Route::resource('guru', GuruController::class);
        Route::resource('tahunajaran', TahunAjaranController::class);
        Route::patch('/tahunajaran/{id}/toggle', [TahunAjaranController::class, 'toggleStatus'])->name('tahunajaran.toggleStatus');
        Route::resource('mapel', MapelController::class);
        Route::resource('room',  KelasController::class);
        Route::resource('jadwal',  JadwalController::class);
        Route::get('/export-jadwal', [JadwalController::class, 'exportJadwal'])->name('exportJadwal');

        Route::resource('siswa',  SiswaController::class);

        Route::get('/siswa/{id}/capture', [SiswaController::class, 'showCaptureForm'])->name('siswa.capture');
        Route::post('/siswa/{id}/capture-train', [SiswaController::class, 'captureAndTrain'])->name('siswa.capture-train');

        Route::resource('laporan', LaporanController::class);
        Route::post('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.exportPdf');


        // Route::post('/siswa/capture', [SiswaController::class, 'captureFace'])->name('siswa.capture.run');
        // Route::get('/siswa/{id}/capture', [FaceRecognitionController::class, 'captureForm'])->name('siswa.capture');
        //Route::post('/siswa/capture/run', [FaceRecognitionController::class, 'runCapture'])->name('siswa.capture.run');

    });

    Route::middleware('role:guru')->group(function(){
        Route::get('/gurudashboard', [GuruDashboardController::class, 'index'])->name('gurudashboard');

        Route::resource('presensi',  PresensiController::class);
        Route::get('/guru/presensi/{jadwalId}/create', [PresensiController::class, 'createPresensi'])->name('presensiJadwal.create');
        Route::post('/presensi/manual/simpan', [PresensiController::class, 'simpanAbsensiManual'])->name('presensi.manual.simpan');

        Route::get('/presensi/{id}/capture', [PresensiController::class, 'showCaptureForm'])->name('presensi.capture');
        Route::post('/siswa-recognize', [PresensiController::class, 'recognizeAndMark'])->name('siswa.recognize');
        Route::post('/mark-attendance', [PresensiController::class, 'markAttendance'])->name('mark.attendance');

        Route::resource('daftarSiswa', DaftarSiswaController::class);
        Route::resource('jadwalAjar', JadwalMengajarController::class);
        Route::resource('laporanGuru', LaporanGuruController::class);
        Route::post('/laporanGuru/export-pdf', [LaporanGuruController::class, 'exportPdf'])->name('laporanguru.exportPdf');
        Route::resource('profileGuru', ProfileGuruController::class);
        // Tambahkan route untuk guru di sini
    });

});

require __DIR__.'/auth.php';
