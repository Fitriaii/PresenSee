<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Sorting
        switch ($request->sort) {
            case 'created_asc':
                $tahunAjaranQuery->orderBy('created_at', 'asc');
                break;
            case 'created_desc':
                $tahunAjaranQuery->orderBy('created_at', 'desc');
                break;
            default:
                $tahunAjaranQuery->latest();
                break;
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $tahunAjaranQuery->where('status', $request->status);
        }

        // Filter berdasarkan tahun_mulai dari kolom tahun_ajaran (format: 2024/2025)
        if ($request->filled('tahun_mulai')) {
            $tahunAjaranQuery->whereRaw('SUBSTRING_INDEX(tahun_ajaran, "/", 1) = ?', [$request->tahun_mulai]);
        }

        // Pencarian
        if ($request->filled('search')) {
            $tahunAjaranQuery->where(function ($query) use ($request) {
                $query->where('tahun_ajaran', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%');
            });
        }

        $tahunAjaran = $tahunAjaranQuery->paginate(10);
        $tahunAjaran->appends($request->only(['status', 'tahun_mulai', 'search', 'sort']));

        // Ambil list tahun_mulai dari tahun_ajaran
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

            // Validasi unik manual karena tahun_ajaran gabungan dari 2 input
            $exists = TahunAjaran::where('tahun_ajaran', $tahun_ajaran)->exists();
            if ($exists) {
                return redirect()->back()->withInput()->with([
                    'status' => 'error',
                    'message' => 'Tahun ajaran sudah ada.'
                ]);
            }

            // Set semua tahun ajaran lain menjadi Tidak Aktif
            TahunAjaran::where('status', 'Aktif')->update(['status' => 'Tidak Aktif']);

            // Simpan tahun ajaran baru sebagai Aktif
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
        // Cek apakah pengguna adalah admin
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

            // Cek apakah kombinasi tahun ajaran sudah digunakan oleh entri lain
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
            // Status tidak diubah saat update
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
            // Hapus tahun ajaran
            $tahunajaran->delete();

            // Menggunakan SweetAlert untuk sukses
            return redirect()->route('tahunajaran.index')->with([
                'status' => 'success',
                'message' => 'Tahun ajaran berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            // Menggunakan SweetAlert untuk error
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
            // Nonaktifkan semua dulu
            TahunAjaran::where('status', 'Aktif')->update(['status' => 'Tidak Aktif']);

            // Aktifkan tahun ajaran yang dipilih
            $ta->status = 'Aktif';
        } else {
            // Nonaktifkan tahun ajaran ini
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
