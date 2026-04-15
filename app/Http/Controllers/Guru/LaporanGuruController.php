<?php

namespace App\Http\Controllers\guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Presensi;
use App\Models\TahunAjaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class LaporanGuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('guru')) {
            return redirect()->back()->with([
                'status' => 'error',
                'code' => 403,
                'message' => 'Unauthorized. Only guru can perform this action.'
            ]);
        }

        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        $query = Presensi::with([
                'siswa_kelas.siswa',
                'siswa_kelas.kelas',
                'siswa_kelas.tahunAjaran',
                'jadwal.kelas',
                'jadwal.mapel.guru'
            ])
            ->whereHas('jadwal.mapel', fn($q) => $q->where('guru_id', $guru->id));

        // Filter
        if ($tahunAjaranId = $request->input('tahun_ajaran_id')) {
            $query->whereHas('siswa_kelas.tahunAjaran', fn($q) => $q->where('id', $tahunAjaranId));
        }

        if ($kelasId = $request->input('kelas_id')) {
            $query->whereHas('jadwal.kelas', fn($q) => $q->where('id', $kelasId));
        }

        if ($mapelId = $request->input('mapel_id')) {
            $query->whereHas('jadwal.mapel', fn($q) => $q->where('id', $mapelId));
        }

        if ($jenisKelas = $request->input('jenis_kelas')) {
            $query->whereHas('jadwal.kelas', fn($q) => $q->where('jenis_kelas', $jenisKelas));
        }

        switch ($request->input('periode')) {
            case 'harian':
                if ($tanggal = $request->input('tanggal')) {
                    $query->whereDate('waktu_presensi', $tanggal);
                }
                break;
            case 'mingguan':
            case 'custom':
                $start = $request->input('start_date');
                $end = $request->input('end_date');
                if ($start && $end) {
                    $query->whereBetween('waktu_presensi', [$start, $end]);
                }
                break;
            case 'bulanan':
                if ($bulan = $request->input('bulan')) {
                    $query->whereMonth('waktu_presensi', $bulan);
                }
                if ($tahun = $request->input('tahun')) {
                    $query->whereYear('waktu_presensi', $tahun);
                }
                break;
            case 'tahunan':
                if ($tahun = $request->input('tahun')) {
                    $query->whereYear('waktu_presensi', $tahun);
                }
                break;
        }

        if ($keyword = $request->input('search')) {
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('siswa_kelas.siswa', function ($q) use ($keyword) {
                    $q->where('nama_siswa', 'like', "%{$keyword}%")
                        ->orWhere('nis', 'like', "%{$keyword}%");
                })->orWhereHas('jadwal.mapel', function ($q) use ($keyword) {
                    $q->where('nama_mapel', 'like', "%{$keyword}%");
                })->orWhereHas('jadwal.kelas', function ($q) use ($keyword) {
                    $q->where('nama_kelas', 'like', "%{$keyword}%");
                });
            });
        }

        // Ambil semua data presensi terlebih dahulu
        $allPresensi = $query->orderBy('waktu_presensi', 'desc')->get();

        // Rekap presensi per siswa
        $rekapCollection = $allPresensi->groupBy('siswa_kelas_id')->map(function ($presensis) {
            $siswaKelas = $presensis->first()->siswa_kelas;
            $siswa = $siswaKelas->siswa ?? null;
            $kelas = $presensis->first()->jadwal->kelas ?? null;

            $hadir = $presensis->where('status', 'Hadir')->count();
            $izin = $presensis->where('status', 'Izin')->count();
            $sakit = $presensis->where('status', 'Sakit')->count();
            $alpha = $presensis->where('status', 'Alpha')->count();
            $total = $presensis->count();

            $presentase = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;

            return [
                'nama' => $siswa?->nama_siswa ?? '-',
                'nis' => $siswa?->nis ?? '-',
                'kelas' => $kelas?->nama_kelas ?? '-',
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpha' => $alpha,
                'total' => $total,
                'presentase' => $presentase,
            ];
        })->values();

        // Paginate rekap per siswa
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = $rekapCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $rekapPerSiswa = new LengthAwarePaginator(
            $currentItems,
            $rekapCollection->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('Guru.Laporan.laporanGuru', [
            'user' => $user,
            'data' => $rekapPerSiswa,
            'rekapPerSiswa' => $rekapPerSiswa,
            'semuaKelas' => Kelas::whereHas('jadwal.mapel', fn($q) => $q->where('guru_id', $guru->id))->orderBy('nama_kelas')->get(),
            'semuaMapel' => Mapel::where('guru_id', $guru->id)->orderBy('nama_mapel')->get(),
            'semuaTahunAjaran' => TahunAjaran::orderBy('tahun_ajaran', 'desc')->get(),
        ]);
    }

    public function exportPdf(Request $request)
    {
        $user = $request->user();

        // 🔒 Pastikan hanya guru yang bisa akses
        if (!$user || !$user->hasRole('guru')) {
            return redirect()->back()->with([
                'status' => 'error',
                'code' => 403,
                'message' => 'Unauthorized. Only guru can perform this action.'
            ]);
        }

        // Ambil data guru dari user
        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        // Ambil presensi yang hanya sesuai jadwal guru ini
        $query = Presensi::with([
            'siswa_kelas.siswa',
            'siswa_kelas.kelas',
            'siswa_kelas.tahunAjaran',
            'jadwal.kelas',
            'jadwal.mapel.guru'
        ])
        ->whereHas('jadwal.mapel', function ($q) use ($guru) {
            $q->where('guru_id', $guru->id);
        })
        ->join('jadwal', 'presensi.jadwal_id', '=', 'jadwal.id')
        ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
        ->join('siswa_kelas', 'presensi.siswa_kelas_id', '=', 'siswa_kelas.id')
        ->select('presensi.*');

        // Filter dinamis
        if ($request->filled('tahun_ajaran_id')) {
            $query->where('siswa_kelas.tahun_ajaran_id', $request->tahun_ajaran_id);
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas.id', $request->kelas_id);
        }

        // Validasi wajib: mapel harus dipilih
        if (!$request->filled('mapel_id')) {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Export gagal. Silakan pilih mata pelajaran terlebih dahulu.'
            ]);
        }

        if ($request->filled('search')) {
            $keyword = $request->search;

            $query->where(function ($q) use ($keyword) {
                $q->whereHas('siswa_kelas.siswa', function ($q) use ($keyword) {
                    $q->where('nama_siswa', 'like', "%{$keyword}%")
                    ->orWhere('nis', 'like', "%{$keyword}%");
                });

                $q->orWhereHas('jadwal.mapel', function ($q) use ($keyword) {
                    $q->where('nama_mapel', 'like', "%{$keyword}%");
                });

                $q->orWhereHas('jadwal.kelas', function ($q) use ($keyword) {
                    $q->where('nama_kelas', 'like', "%{$keyword}%");
                });
            });
        }

        // Filter periode
        switch ($request->periode) {
            case 'harian':
                if ($request->filled('tanggal')) {
                    $query->whereDate('waktu_presensi', $request->tanggal);
                }
                break;
            case 'bulanan':
                if ($request->filled('bulan')) {
                    $query->whereMonth('waktu_presensi', $request->bulan);
                }
                if ($request->filled('tahun')) {
                    $query->whereYear('waktu_presensi', $request->tahun);
                }
                break;
            case 'tahunan':
                if ($request->filled('tahun')) {
                    $query->whereYear('waktu_presensi', $request->tahun);
                }
                break;
            case 'custom':
                if ($request->filled(['start_date', 'end_date'])) {
                    $query->whereBetween('waktu_presensi', [$request->start_date, $request->end_date]);
                }
                break;
        }

        // Ambil data
        $presensi = $query->orderBy('waktu_presensi', 'asc')->get();

        // Rekap per siswa
        $rekapPerSiswa = $presensi->groupBy('siswa_kelas_id')->map(function ($presensis) {
            $siswaKelas = $presensis->first()->siswa_kelas;
            $siswa = $siswaKelas->siswa ?? null;
            $kelas = $presensis->first()->jadwal->kelas ?? null;

            $hadir = $presensis->where('status', 'Hadir')->count();
            $izin = $presensis->where('status', 'Izin')->count();
            $sakit = $presensis->where('status', 'Sakit')->count();
            $alpha = $presensis->where('status', 'Alpha')->count();
            $total = $presensis->count();

            $presentase = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;

            return [
                'nama' => $siswa?->nama_siswa ?? '-',
                'nis' => $siswa?->nis ?? '-',
                'kelas' => $kelas?->nama_kelas ?? '-',
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpha' => $alpha,
                'total' => $total,
                'presentase' => $presentase,
            ];
        })->values();

        $filter = [
            'periode' => $request->periode,
            'tanggal' => $request->tanggal,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'search' => $request->search,
            'tahun_ajaran' => TahunAjaran::find($request->tahun_ajaran_id)?->nama ?? null,
            'kelas' => Kelas::find($request->kelas_id)?->nama_kelas ?? null,
            'mapel' => Mapel::find($request->mapel_id)?->nama_mapel ?? null,
        ];

        // Buat PDF
        $pdf = Pdf::loadView('Guru.Laporan.laporanpdf', [
            'rekap' => $rekapPerSiswa,
            'presensi' => $presensi,
            'filter' => $filter,
        ]);

        $kelas = Str::slug($filter['kelas'] ?? 'semua-kelas');
        $mapel = Str::slug($filter['mapel'] ?? 'semua-mapel');
        $tahun = $filter['tahun'] ?? now()->format('Y');
        $periode = $filter['periode'] ?? 'semua';
        $tanggal = now()->format('Ymd-His');

        // Buat nama file
        $filename = "presensi_{$kelas}_{$mapel}_{$periode}_{$tahun}_{$tanggal}.pdf";
        return $pdf->download($filename);
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
