<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
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

        $tahunAjaranQuery = TahunAjaran::query();


        $sort = $request->sort ?? 'created_asc';
        switch ($sort) {
            case 'created_asc':
                $tahunAjaranQuery->orderBy('created_at', 'asc');
                break;
            case 'created_desc':
                $tahunAjaranQuery->orderBy('created_at', 'desc');
                break;
        }

        if ($request->filled('status')) {
            $tahunAjaranQuery->where('status', $request->status);
        }

        if ($request->filled('tahun_mulai')) {
            $tahunAjaranQuery->whereRaw('SUBSTRING_INDEX(tahun_ajaran, "/", 1) = ?', [$request->tahun_mulai]);
        }

        if ($request->filled('search')) {
            $tahunAjaranQuery->where(function ($query) use ($request) {
                $query->where('tahun_ajaran', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%');
            });
        }

        $tahunAjaran = $tahunAjaranQuery->paginate(10);
        $tahunAjaran->appends($request->only(['status', 'tahun_mulai', 'search', 'sort']));


        $tahunList = TahunAjaran::selectRaw('DISTINCT SUBSTRING_INDEX(tahun_ajaran, "/", 1) AS tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $activeCount = TahunAjaran::where('status', 'Aktif')->count();
        return view('Admin.Akademik.TAjaran.index', compact('tahunAjaran', 'user', 'tahunList', 'activeCount'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ta = TahunAjaran::all();
        return view('Admin.Akademik.TAjaran.create', compact('ta'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun_mulai' => 'required|integer|min:2000|max:2099',
            'tahun_akhir' => 'required|integer|min:2001|max:2100|gt:tahun_mulai',
        ], [
            'tahun_mulai.required' => 'Tahun mulai wajib diisi.',
            'tahun_akhir.required' => 'Tahun akhir wajib diisi.',
            'tahun_akhir.gt' => 'Tahun akhir harus lebih besar dari tahun mulai.',
        ]);

        try {
            $tahun_ajaran = $request->tahun_mulai . '/' . $request->tahun_akhir;


            $exists = TahunAjaran::where('tahun_ajaran', $tahun_ajaran)->exists();
            if ($exists) {
                return redirect()->back()->withInput()->with([
                    'status' => 'error',
                    'message' => 'Tahun ajaran sudah ada.'
                ]);
            }


            TahunAjaran::where('status', 'Aktif')->update(['status' => 'Tidak Aktif']);


            $tahunajaran = new TahunAjaran();
            $tahunajaran->tahun_ajaran = $tahun_ajaran;
            $tahunajaran->status = 'Aktif';
            $tahunajaran->save();

            return redirect()->route('tahunajaran.index')->with([
                'status' => 'success',
                'message' => 'Tahun ajaran berhasil ditambahkan dan diatur sebagai aktif.'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tahunajaran.index')->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menambahkan tahun ajaran.'
            ]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(TahunAjaran $tahunajaran)
    {

        return view('Admin.Akademik.TAjaran.show', compact('tahunajaran'));
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, TahunAjaran $tahunajaran)
    {
        $ta = TahunAjaran::findOrFail($tahunajaran->id);

        $user = $request->user();

        if (!$user || !$user->hasRole('admin')) {
            return redirect()->back()->with([
                'status' => 'error',
                'code' => 403,
                'message' => 'Unauthorized. Only admins can perform this action.'
            ]);
        }

        return view('Admin.Akademik.TAjaran.edit', compact('ta'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TahunAjaran $tahunajaran)
    {
        $request->validate([
            'tahun_mulai' => 'required|integer|min:2000|max:2099',
            'tahun_akhir' => 'required|integer|min:2001|max:2100|gt:tahun_mulai',
        ], [
            'tahun_mulai.required' => 'Tahun mulai wajib diisi.',
            'tahun_akhir.required' => 'Tahun akhir wajib diisi.',
            'tahun_akhir.gt' => 'Tahun akhir harus lebih besar dari tahun mulai.',
        ]);

        try {
            $tahun_ajaran = $request->tahun_mulai . '/' . $request->tahun_akhir;


            $exists = TahunAjaran::where('tahun_ajaran', $tahun_ajaran)
                ->where('id', '!=', $tahunajaran->id)
                ->exists();

            if ($exists) {
                return redirect()->back()->withInput()->with([
                    'status' => 'error',
                    'message' => 'Tahun ajaran sudah ada.'
                ]);
            }

            $tahunajaran->tahun_ajaran = $tahun_ajaran;

            $tahunajaran->save();

            return redirect()->route('tahunajaran.index')->with([
                'status' => 'success',
                'message' => 'Tahun ajaran berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tahunajaran.index')->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui tahun ajaran.'
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TahunAjaran $tahunajaran)
    {
        try {

            $tahunajaran->delete();


            return redirect()->route('tahunajaran.index')->with([
                'status' => 'success',
                'message' => 'Tahun ajaran berhasil dihapus.'
            ]);
        } catch (\Exception $e) {

            return redirect()->route('tahunajaran.index')->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus tahun ajaran.'
            ]);
        }
    }


    public function toggleStatus($id)
    {
        $ta = TahunAjaran::findOrFail($id);

        if ($ta->status === 'Tidak Aktif') {

            TahunAjaran::where('status', 'Aktif')->update(['status' => 'Tidak Aktif']);


            $ta->status = 'Aktif';
        } else {

            $ta->status = 'Tidak Aktif';
        }

        $ta->save();

        return response()->json([
            'success' => true,
            'message' => 'Status tahun ajaran berhasil diperbarui.',
            'status' => $ta->status
        ]);
    }


}
