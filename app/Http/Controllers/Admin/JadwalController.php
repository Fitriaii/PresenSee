<?php

namespace App\Http\Controllers\Admin;

use App\Exports\JadwalExport;
use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class JadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $user = request()->user();

        if (!$user || !$user->hasRole('admin')) {
            return redirect()->back()->with([
                'status' => 'error',
                'code' => 403,
                'message' => 'Unauthorized. Only admins can perform this action.'
            ]);
        }

        $query = Jadwal::with(['kelas', 'mapel.guru'])
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->join('guru', 'mapel.guru_id', '=', 'guru.id')
            ->select('jadwal.*')
            ->orderBy('hari')
            ->orderBy('kelas.nama_kelas');

        // 🔍 Filter: Pencarian bebas
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kelas.nama_kelas', 'like', "%$search%")
                ->orWhere('mapel.nama_mapel', 'like', "%$search%")
                ->orWhere('guru.nama_guru', 'like', "%$search%");
            });
        }

        // Filter: Hari
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        // Filter: Kelas
        if ($request->filled('kelas')){
            $query->where('kelas_id', $request->kelas);
        }

        // Filter: Mape
        if ($request->filled('mapel')) {
            $query->where('mapel_id', $request->mapel);
        }

        // 🔀 Sorting berdasarkan tanggal dibuat (opsional, kalau ada field created_at)
        if ($sort = request('sort')) {
            switch ($sort) {
                case 'created_asc':
                    $query->orderBy('jadwal.created_at', 'asc');
                    break;
                case 'created_desc':
                    $query->orderBy('jadwal.created_at', 'desc');
                    break;
                case 'jam_mulai_asc':
                    $query->orderBy('jadwal.jam_mulai', 'asc');
                    break;
                case 'jam_mulai_desc':
                    $query->orderBy('jadwal.jam_mulai', 'desc');
                    break;
                default:
                    $query->latest('jadwal.created_at');
                    break;
            }
        }


        // 📄 Pagination dan pelengkap filter
        $jadwalPaginated = $query->paginate(10)->appends(request()->all());
        $jadwalCollection = $jadwalPaginated->getCollection();

        // 📊 Group by Hari > Kelas
        $groupedKelas = $jadwalCollection
            ->groupBy('hari')
            ->map(function ($itemsPerHari) {
                return $itemsPerHari->groupBy('kelas_id');
            });

        return view('Admin.Akademik.Jadwal.index', [
            'user' => $user,
            'groupedKelas' => $groupedKelas,
            'jadwal' => $jadwalPaginated,
            'no' => 1,
            'semuaKelas' => Kelas::orderBy('nama_kelas')->get(),
            'semuaMapel' => Mapel::orderBy('nama_mapel')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelasList = Kelas::all();
        $mapelList = Mapel::with('guru')->get(); // eager load guru dari mapel

        return view('Admin.Akademik.Jadwal.create', compact('kelasList', 'mapelList'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapel,id',
            'hari' => 'required|string|max:255',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $guruId = auth()->user->guru->id;

        // CEK DUPLIKAT
        $duplikat = Jadwal::where('guru_id', $guruId)
            ->where('kelas_id', $request->kelas_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('hari', $request->hari)
            ->where('jam_mulai', $request->jam_mulai)
            ->where('jam_selesai', $request->jam_selesai)
            ->exists();

        if ($duplikat) {
            return back()->with([
                'status' => 'error',
                'message' => 'Jadwal ini sudah ada.'
            ]);
        }

        // BENTROK GURU
        $bentrokGuru = Jadwal::where('guru_id', $guruId)
            ->where('hari', $request->hari)
            ->where(function ($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('jam_mulai', '<=', $request->jam_mulai)
                        ->where('jam_selesai', '>=', $request->jam_selesai);
                    });
            })
            ->exists();

        if ($bentrokGuru) {
            return back()->with([
                'status' => 'error',
                'message' => 'Guru sudah memiliki jadwal di waktu yang sama.'
            ]);
        }

        // BENTROK KELAS
        $bentrokKelas = Jadwal::where('kelas_id', $request->kelas_id)
            ->where('hari', $request->hari)
            ->where(function ($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('jam_mulai', '<=', $request->jam_mulai)
                        ->where('jam_selesai', '>=', $request->jam_selesai);
                    });
            })
            ->exists();

        if ($bentrokKelas) {
            return back()->with([
                'status' => 'error',
                'message' => 'Kelas sudah memiliki jadwal di waktu yang sama.'
            ]);
        }

        try {
            $jadwal = new Jadwal();
            $jadwal->guru_id = $guruId;
            $jadwal->kelas_id = $request->kelas_id;
            $jadwal->mapel_id = $request->mapel_id;
            $jadwal->hari = $request->hari;
            $jadwal->jam_mulai = $request->jam_mulai;
            $jadwal->jam_selesai = $request->jam_selesai;
            $jadwal->save();

            return redirect()->route('jadwal.index')->with([
                'status' => 'success',
                'message' => 'Jadwal berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Jadwal $jadwal)
    {
        // Jika ada logika khusus untuk menampilkan detail jadwal, bisa ditambahkan di sini
        return view('Admin.Akademik.Jadwal.show', compact('jadwal'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jadwal $jadwal)
    {
        $kelasList = Kelas::all();
        $mapelList = Mapel::with('guru')->get(); // eager load guru dari mapel

        return view('Admin.Akademik.Jadwal.edit', [
            'jadwal' => $jadwal,
            'kelasList' => $kelasList,
            'mapelList' => $mapelList,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jadwal $jadwal)
    {
        $validatedData = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapel,id',
            'hari' => 'required|string|max:255',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $guruId = $jadwal->guru_id; 

        // 🔴 CEK DUPLIKAT (exclude diri sendiri)
        $duplikat = Jadwal::where('guru_id', $guruId)
            ->where('kelas_id', $request->kelas_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('hari', $request->hari)
            ->where('jam_mulai', $request->jam_mulai)
            ->where('jam_selesai', $request->jam_selesai)
            ->where('id', '!=', $jadwal->id)
            ->exists();

        if ($duplikat) {
            return back()->withInput()->with([
                'status' => 'error',
                'message' => 'Jadwal ini sudah ada.'
            ]);
        }

        // 🔴 CEK BENTROK GURU
        $bentrokGuru = Jadwal::where('guru_id', $guruId)
            ->where('hari', $request->hari)
            ->where('id', '!=', $jadwal->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('jam_mulai', '<=', $request->jam_mulai)
                        ->where('jam_selesai', '>=', $request->jam_selesai);
                    });
            })
            ->exists();

        if ($bentrokGuru) {
            return back()->withInput()->with([
                'status' => 'error',
                'message' => 'Guru sudah memiliki jadwal di waktu yang sama.'
            ]);
        }

        // 🔴 CEK BENTROK KELAS
        $bentrokKelas = Jadwal::where('kelas_id', $request->kelas_id)
            ->where('hari', $request->hari)
            ->where('id', '!=', $jadwal->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('jam_mulai', '<=', $request->jam_mulai)
                        ->where('jam_selesai', '>=', $request->jam_selesai);
                    });
            })
            ->exists();

        if ($bentrokKelas) {
            return back()->withInput()->with([
                'status' => 'error',
                'message' => 'Kelas sudah memiliki jadwal di waktu yang sama.'
            ]);
        }

        try {
            $jadwal->kelas_id = $request->kelas_id;
            $jadwal->mapel_id = $request->mapel_id;
            $jadwal->hari = $request->hari;
            $jadwal->jam_mulai = $request->jam_mulai;
            $jadwal->jam_selesai = $request->jam_selesai;
            $jadwal->save();

            return redirect()->route('jadwal.index')->with([
                'status' => 'success',
                'message' => 'Jadwal berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jadwal $jadwal)
    {
        try {
            $jadwal->delete();

            return redirect()->route('jadwal.index')->with([
                'status' => 'success',
                'code' => 200,
                'message' => 'Jadwal berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('jadwal.index')->with([
                'status' => 'error',
                'code' => 500,
                'message' => 'Terjadi kesalahan saat menghapus jadwal: ' . $e->getMessage()
            ]);
        }
    }


    public function exportJadwal()
    {
        return Excel::download(new JadwalExport, 'jadwal_' . date('Ymd') . '.xlsx');
    }

}
