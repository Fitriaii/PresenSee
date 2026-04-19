<?php

namespace App\Http\Controllers\guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use Illuminate\Http\Request;

class JadwalMengajarController extends Controller
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

        $guru = Guru::where('user_id', $user->id)->first();

        if (!$guru) {
            return redirect()->back()->with([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data guru tidak ditemukan.'
            ]);
        }

        $query = Jadwal::with(['kelas', 'mapel.guru'])
            ->whereHas('mapel', function ($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->leftJoin('kelas as kls', 'jadwal.kelas_id', '=', 'kls.id')
            ->leftJoin('mapel as mpl', 'jadwal.mapel_id', '=', 'mpl.id')
            ->select('jadwal.*')
            ->orderBy('jadwal.hari')
            ->orderBy('kls.nama_kelas');


        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kls.nama_kelas', 'like', "%$search%")
                ->orWhere('mpl.nama_mapel', 'like', "%$search%");
            });
        }


        if ($request->filled('hari')) {
            $query->where('jadwal.hari', $request->hari);
        }


        if ($request->filled('kelas')) {
            $query->where('jadwal.kelas_id', $request->kelas);
        }


        if ($request->filled('mapel')) {
            $query->where('jadwal.mapel_id', $request->mapel);
        }


        $sort = $request->sort ?? 'created_asc';
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
        }


        $jadwalPaginated = $query->paginate(10)->appends($request->query());
        $jadwalCollection = $jadwalPaginated->getCollection();


        $groupedKelas = $jadwalCollection
            ->groupBy('hari')
            ->map(function ($itemsPerHari) {
                return $itemsPerHari->groupBy('kelas_id');
            });

        return view('Guru.jadwalMengajar', [
            'user' => $user,
            'groupedKelas' => $groupedKelas,
            'jadwal' => $jadwalPaginated,
            'no' => 1,
            'semuaKelas' => Kelas::orderBy('nama_kelas')->get(),
            'semuaMapel' => Mapel::where('guru_id', $guru->id)->orderBy('nama_mapel')->get(),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

    }
}
