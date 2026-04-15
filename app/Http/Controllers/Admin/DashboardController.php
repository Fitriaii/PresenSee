<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $today = Carbon::today();
    //     $bulanIni = Carbon::now()->format('Y-m');
    //     $awalMinggu = Carbon::now()->startOfWeek();

    //     // Statistik Ringkas
    //     $jumlahSiswa = Siswa::count();
    //     $jumlahGuru = Guru::count();
    //     $jumlahKelas = Kelas::count();

    //     // Tambahan: Statistik Baru
    //     $siswaBaruHariIni = Siswa::whereDate('created_at', $today)->count();
    //     $guruBaruMingguIni = Guru::whereDate('created_at', '>=', $awalMinggu)->count();

    //     $jumlahPresensiHariIni = Presensi::whereDate('created_at', $today)->count();
    //     $presentaseKehadiranHariIni = $jumlahPresensiHariIni > 0
    //         ? round((Presensi::whereDate('created_at', $today)->where('status', 'hadir')->count() / $jumlahPresensiHariIni) * 100, 2)
    //         : 0;
    //     $jumlahPresensiBulanIni = Presensi::where('created_at', 'like', "$bulanIni%")->count();

    //     $kelasId = request('kelas_id');

    //     $labels = [];
    //     $dataHadir = [];
    //     $dataTidakHadir = [];

    //     // Ambil 7 hari terakhir
    //     $dates = collect(range(6, 0))->map(function ($i) {
    //         return Carbon::today()->subDays($i);
    //     });

    //     foreach ($dates as $date) {
    //         $query = Presensi::join('siswa_kelas', 'presensi.siswa_kelas_id', '=', 'siswa_kelas.id')
    //             ->whereDate('presensi.waktu_presensi', $date);

    //         if ($kelasId) {
    //             $query->where('siswa_kelas.kelas_id', $kelasId);
    //         }

    //         $data = $query->selectRaw("
    //             SUM(CASE WHEN presensi.status = 'hadir' THEN 1 ELSE 0 END) as hadir,
    //             SUM(CASE WHEN presensi.status != 'hadir' THEN 1 ELSE 0 END) as tidak_hadir
    //         ")->first();

    //         $labels[] = $date->locale('id')->isoFormat('dddd'); // contoh: Senin, Selasa
    //         $dataHadir[] = $data->hadir ?? 0;
    //         $dataTidakHadir[] = $data->tidak_hadir ?? 0;
    //     }

    //     $kelasList = Kelas::all(); // dropdown filter

    //     // Kehadiran Bulan Ini
    //     $totalPresensiBulanIni = Presensi::where('created_at', 'like', "$bulanIni%")->count();
    //     $jumlahHadirBulanIni = Presensi::where('created_at', 'like', "$bulanIni%")
    //         ->where('status', 'hadir')
    //         ->count();
    //     $persentaseKehadiranBulanIni = $totalPresensiBulanIni > 0
    //         ? round(($jumlahHadirBulanIni / $totalPresensiBulanIni) * 100, 2)
    //         : 0;

    //     // Top 5 Kelas Kehadiran Tertinggi
    //     $kelasTop = DB::table('presensi')
    //     ->join('siswa_kelas', 'presensi.siswa_kelas_id', '=', 'siswa_kelas.id')
    //     ->join('kelas', 'siswa_kelas.kelas_id', '=', 'kelas.id')
    //     ->select(
    //         'kelas.id as kelas_id',
    //         'kelas.nama_kelas',
    //         DB::raw('COUNT(presensi.id) as total_presensi'),
    //         DB::raw("SUM(CASE WHEN presensi.status = 'hadir' THEN 1 ELSE 0 END) as total_hadir")
    //     )
    //     ->groupBy('kelas.id', 'kelas.nama_kelas')
    //     ->orderByDesc(DB::raw("SUM(CASE WHEN presensi.status = 'hadir' THEN 1 ELSE 0 END) / COUNT(presensi.id)"))
    //     ->limit(5)
    //     ->get()
    //     ->map(function ($item) {
    //         return [
    //             'nama_kelas' => $item->nama_kelas,
    //             'persentase' => round(($item->total_hadir / $item->total_presensi) * 100, 2),
    //         ];
    //     });



    //     // Log Presensi Terbaru
    //     $logPresensi = Presensi::with(['siswa_kelas', 'jadwal'])
    //         ->orderByDesc('created_at')
    //         ->take(20)
    //         ->get();

    //     return view('admin.dashboard-admin', [
    //         'jumlahSiswa' => $jumlahSiswa,
    //         'jumlahGuru' => $jumlahGuru,
    //         'jumlahKelas' => $jumlahKelas,
    //         'siswaBaruHariIni' => $siswaBaruHariIni,
    //         'guruBaruMingguIni' => $guruBaruMingguIni,
    //         'jumlahPresensiHariIni' => $jumlahPresensiHariIni,
    //         'jumlahPresensiBulanIni' => $jumlahPresensiBulanIni,
    //         'labels' => $labels,
    //         'kelasList' => $kelasList,
    //         'dataHadir' => $dataHadir,
    //         'dataTidakHadir' => $dataTidakHadir,
    //         'presentaseKehadiranHariIni' => $presentaseKehadiranHariIni,
    //         'persentaseKehadiranBulanIni' => $persentaseKehadiranBulanIni,
    //         'kelasTop' => $kelasTop,
    //         'logPresensi' => $logPresensi,
    //         'totalPresensiBulanIni' => $totalPresensiBulanIni,
    //         'jumlahHadirBulanIni' => $jumlahHadirBulanIni,
    //         'kelasAktifHariIni'=> $kelasAktifHariIni->count(),
    //     ]);
    // }

    public function index(Request $request)
    {
        $today = Carbon::today();
        $awalMinggu = Carbon::now()->startOfWeek();
        $akhirMinggu = Carbon::now()->endOfWeek();

        // Statistik Ringkas tetap sama
        $jumlahSiswa = Siswa::count();
        $jumlahGuru = Guru::count();
        $jumlahKelas = Kelas::count();

        // Statistik lainnya tetap sama
        $siswaBaruHariIni = Siswa::whereDate('created_at', $today)->count();
        $guruBaruMingguIni = Guru::whereDate('created_at', '>=', $awalMinggu)->count();

        $jumlahPresensiHariIni = Presensi::whereDate('created_at', $today)->count();
        $presentaseKehadiranHariIni = $jumlahPresensiHariIni > 0
            ? round((Presensi::whereDate('created_at', $today)->where('status', 'hadir')->count() / $jumlahPresensiHariIni) * 100, 2)
            : 0;
        $bulanIni = Carbon::now()->format('Y-m');
        $jumlahPresensiBulanIni = Presensi::where('created_at', 'like', "$bulanIni%")->count();

        $kelasId = request('kelas_id');

        $labels = [];
        $dataHadir = [];
        $dataTidakHadir = [];

        // Buat koleksi tanggal dari Senin sampai Minggu minggu ini
        $dates = collect();
        for ($date = $awalMinggu->copy(); $date->lte($akhirMinggu); $date->addDay()) {
            $dates->push($date->copy());
        }

        foreach ($dates as $date) {
            $query = Presensi::join('siswa_kelas', 'presensi.siswa_kelas_id', '=', 'siswa_kelas.id')
                ->whereDate('presensi.waktu_presensi', $date);

            if ($kelasId) {
                $query->where('siswa_kelas.kelas_id', $kelasId);
            }

            $data = $query->selectRaw("
                SUM(CASE WHEN presensi.status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN presensi.status != 'hadir' THEN 1 ELSE 0 END) as tidak_hadir
            ")->first();

            $labels[] = $date->locale('id')->isoFormat('dddd'); // nama hari, contoh: Senin, Selasa
            $dataHadir[] = $data->hadir ?? 0;
            $dataTidakHadir[] = $data->tidak_hadir ?? 0;
        }

        $kelasList = Kelas::all(); // dropdown filter

        // Statistik lain tetap
        $totalPresensiBulanIni = Presensi::where('created_at', 'like', "$bulanIni%")->count();
        $jumlahHadirBulanIni = Presensi::where('created_at', 'like', "$bulanIni%")
            ->where('status', 'hadir')
            ->count();
        $persentaseKehadiranBulanIni = $totalPresensiBulanIni > 0
            ? round(($jumlahHadirBulanIni / $totalPresensiBulanIni) * 100, 2)
            : 0;

        // Top 5 kelas kehadiran tertinggi
        $kelasTop = DB::table('presensi')
            ->join('siswa_kelas', 'presensi.siswa_kelas_id', '=', 'siswa_kelas.id')
            ->join('kelas', 'siswa_kelas.kelas_id', '=', 'kelas.id')
            ->select(
                'kelas.id as kelas_id',
                'kelas.nama_kelas',
                DB::raw('COUNT(presensi.id) as total_presensi'),
                DB::raw("SUM(CASE WHEN presensi.status = 'hadir' THEN 1 ELSE 0 END) as total_hadir")
            )
            ->groupBy('kelas.id', 'kelas.nama_kelas')
            ->orderByDesc(DB::raw("SUM(CASE WHEN presensi.status = 'hadir' THEN 1 ELSE 0 END) / COUNT(presensi.id)"))
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'nama_kelas' => $item->nama_kelas,
                    'persentase' => round(($item->total_hadir / $item->total_presensi) * 100, 2),
                ];
            });


        // kelas aktif hari ini
        $kelasAktifHariIni = Presensi::whereHas('siswa_kelas',function ($query) use ($today) {
            $query->whereHas('kelas', function ($query) use ($today) {
                $query->whereDate('created_at', $today);
            });
        })->get();

        // Log Presensi Terbaru
        $logPresensi = Presensi::with(['siswa_kelas', 'jadwal'])
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('Admin.dashboard-admin', [
            'jumlahSiswa' => $jumlahSiswa,
            'jumlahGuru' => $jumlahGuru,
            'jumlahKelas' => $jumlahKelas,
            'siswaBaruHariIni' => $siswaBaruHariIni,
            'guruBaruMingguIni' => $guruBaruMingguIni,
            'jumlahPresensiHariIni' => $jumlahPresensiHariIni,
            'jumlahPresensiBulanIni' => $jumlahPresensiBulanIni,
            'labels' => $labels,
            'kelasList' => $kelasList,
            'dataHadir' => $dataHadir,
            'dataTidakHadir' => $dataTidakHadir,
            'presentaseKehadiranHariIni' => $presentaseKehadiranHariIni,
            'persentaseKehadiranBulanIni' => $persentaseKehadiranBulanIni,
            'kelasTop' => $kelasTop,
            'logPresensi' => $logPresensi,
            'totalPresensiBulanIni' => $totalPresensiBulanIni,
            'jumlahHadirBulanIni' => $jumlahHadirBulanIni,
            'awalMinggu' => $awalMinggu->locale('id')->isoFormat('D MMMM YYYY'),
            'akhirMinggu' => $akhirMinggu->locale('id')->isoFormat('D MMMM YYYY'),
            'kelasAktifHariIni' => $kelasAktifHariIni->count(),
        ]);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
