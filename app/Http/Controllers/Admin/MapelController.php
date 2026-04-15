<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Mapel;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('admin')) {
            return redirect()->back()->with([
                'status' => 'error',
                'code' => 403,
                'message' => 'Unauthorized. Only admins can perform this action.'
            ]);
        }

    // Query dasar
    $mapelQuery = Mapel::with('guru');

    // Search by kode_mapel atau nama_mapel
    if ($request->filled('search')) {
        $mapelQuery->where(function ($query) use ($request) {
            $query->where('kode_mapel', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_mapel', 'like', '%' . $request->search . '%');
        });
    }

    // Filter by guru_id
    if ($request->filled('guru')) {
        $mapelQuery->where('guru_id', $request->guru);
    }

    // Filter by tahun (created_at)
    if ($request->filled('tahun')) {
        $mapelQuery->whereYear('created_at', $request->tahun);
    }

    // Sorting
    switch ($request->sort) {
        case 'nama_mapel_asc':
            $mapelQuery->orderBy('nama_mapel', 'asc');
            break;
        case 'nama_mapel_desc':
            $mapelQuery->orderBy('nama_mapel', 'desc');
            break;
        case 'created_asc':
            $mapelQuery->orderBy('created_at', 'asc');
            break;
        case 'created_desc':
            $mapelQuery->orderBy('created_at', 'desc');
            break;
        default:
            $mapelQuery->latest();
            break;
    }

    // Data untuk dropdown filter
    $guruList = Guru::orderBy('nama_guru')->get(); // untuk dropdown guru
    $tahunList = Mapel::selectRaw('YEAR(created_at) as tahun')
                    ->distinct()
                    ->orderBy('tahun', 'desc')
                    ->pluck('tahun'); // untuk dropdown tahun

    // Paginate
    $mapel = $mapelQuery->paginate(10);

    return view('Admin.Akademik.Mapel.index', compact('user', 'mapel', 'guruList', 'tahunList'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $guruList = Guru::all();
        return view('Admin.Akademik.Mapel.create', compact('guruList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|string|max:255|unique:mapel,kode_mapel',
            'guru' => 'required|exists:guru,id',
        ], [
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi.',
            'kode_mapel.required' => 'Kode mata pelajaran wajib diisi.',
            'kode_mapel.unique' => 'Kode mata pelajaran sudah digunakan.',
            'guru.required' => 'Guru pengampu wajib dipilih.',
            'guru.exists' => 'Guru yang dipilih tidak ditemukan.',
        ]);

        try {
            $mapel = new Mapel();
            $mapel->nama_mapel = $request->nama_mapel;
            $mapel->kode_mapel = $request->kode_mapel;
            $mapel->guru_id = $request->guru;
            $mapel->save();
            return redirect()->route('mapel.index')->with([
                'status' => 'success',
                'message' => 'Mata pelajaran berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('mapel.index')->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menambahkan mata pelajaran. Silakan coba lagi.'
            ]);
        }
}



    /**
     * Display the specified resource.
     */
    public function show(Mapel $mapel)
    {
        // Show the details of the specified mapel
        return view('Admin.Akademik.Mapel.show', compact('mapel'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mapel $mapel)
    {
        $guruList = Guru::all();
        return view('Admin.Akademik.Mapel.edit', [
            'mapel' => $mapel,
            'guruList' => $guruList,
            'selectedGuru' => $mapel->guru_id,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mapel $mapel)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|string|max:255',
            'guru' => 'required|exists:guru,id',
        ], [
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi.',
            'kode_mapel.required' => 'Kode mata pelajaran wajib diisi.',
            'kode_mapel.unique' => 'Kode mata pelajaran sudah digunakan.',
            'guru.required' => 'Guru pengampu wajib dipilih.',
            'guru.exists' => 'Guru yang dipilih tidak ditemukan.',
        ]);

        try {
            $guru = Guru::findOrFail($request->guru);
            $mapel->nama_mapel = $request->nama_mapel;
            $mapel->kode_mapel = $request->kode_mapel;
            $mapel->guru_id = $guru->id;
            $mapel->save();
            return redirect()->route('mapel.index')->with([
                'status' => 'success',
                'message' => 'Mata pelajaran berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('mapel.index')->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui mata pelajaran. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mapel $mapel)
    {
        $mapel->delete();

        return redirect()->route('mapel.index')->with([
            'status' => 'success',
            'message' => 'Mata pelajaran berhasil dihapus.'
        ]);
    }




}
