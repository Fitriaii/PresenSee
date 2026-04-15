<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Hanya admin yang diizinkan
        if (!$user || !$user->hasRole('admin')) {
            return redirect()->back()->with([
                'status' => 'error',
                'code' => 403,
                'message' => 'Unauthorized. Only admins can perform this action.'
            ]);
        }

        // Query kelas dengan relasi siswa_kelas dihitung
        $kelasQuery = Kelas::withCount('siswa_kelas');

        // Filter pencarian
        if ($request->filled('search')) {
            $kelasQuery->where(function ($query) use ($request) {
                $query->where('nama_kelas', 'like', '%' . $request->search . '%')
                    ->orWhere('tingkatan_kelas', 'like', '%' . $request->search . '%')
                    ->orWhereHas('guru', function ($subQuery) use ($request) {
                        $subQuery->where('nama_guru', 'like', '%' . $request->search . '%');
                    });
            });
        }

        // Filter berdasarkan jenis_kelas
        if ($request->filled('jenis_kelas')) {
            $kelasQuery->where('jenis_kelas', $request->jenis_kelas);
        }

        // Filter berdasarkan tingkatan_kelas
        if ($request->filled('tingkatan_kelas')) {
            $kelasQuery->where('tingkatan_kelas', $request->tingkatan_kelas);
        }

        // Sorting
        switch ($request->sort) {
            case 'nama_kelas_asc':
                $kelasQuery->orderBy('nama_kelas', 'asc');
                break;
            case 'nama_kelas_desc':
                $kelasQuery->orderBy('nama_kelas', 'desc');
                break;
            case 'created_asc':
                $kelasQuery->orderBy('created_at', 'asc');
                break;
            case 'created_desc':
                $kelasQuery->orderBy('created_at', 'desc');
                break;
            default:
                $kelasQuery->latest();
                break;
        }

        // Paginate hasil akhir (sudah termasuk jumlah siswa per kelas)
        $room = $kelasQuery->paginate(10)->appends($request->all());

        return view('Admin.Akademik.Kelas.index', compact('user', 'room'))
            ->with('search', $request->search)
            ->with('jenis_kelas', $request->jenis_kelas)
            ->with('tingkatan_kelas', $request->tingkatan_kelas);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $guruList = Guru::all();
        return view('Admin.Akademik.Kelas.create', compact('guruList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'jenis_kelas' => 'required|string|max:255',
            'tingkatan_kelas' => 'required|string|max:255',
            'guru' => 'required|exists:guru,id',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'jenis_kelas.required' => 'Jenis kelas wajib diisi.',
            'jenis_kelas.unique' => 'Jenis kelas sudah digunakan.',
            'tingkatan_kelas.required' => 'Tingkatan kelas wajib diisi.',
            'guru.required' => 'Guru pengampu wajib dipilih.',
            'guru.exists' => 'Guru yang dipilih tidak ditemukan.',
        ]);

        try {
            $room = new Kelas();
            $room->nama_kelas = $request->nama_kelas;
            $room->jenis_kelas = $request->jenis_kelas;
            $room->tingkatan_kelas = $request->tingkatan_kelas;
            $room->guru_id = $request->guru;
            $room->save();
            return redirect()->route('room.index')->with([
                'status' => 'success',
                'message' => 'Kelas berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('room.index')->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menambahkan kelas. Silakan coba lagi.'
            ]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Kelas $room)
    {
        // Menampilkan detail kelas
        return view('Admin.Akademik.Kelas.show', compact('room'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $room)
    {
        $guruList = Guru::all();
        return view('Admin.Akademik.Kelas.edit', [
            'room' => $room,
            'guruList' => $guruList,
            'selectedGuru' => $room->guru_id,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $room)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'jenis_kelas' => 'required|string|max:255', // pengecualian untuk kelas yang sedang diupdate
            'tingkatan_kelas' => 'required|string|max:255',
            'guru' => 'required|exists:guru,id',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            // 'jenis_kelas.required' => 'Jenis kelas wajib diisi.',,
            'tingkatan_kelas.required' => 'Tingkatan kelas wajib diisi.',
            'guru.required' => 'Guru pengampu wajib dipilih.',
            'guru.exists' => 'Guru yang dipilih tidak ditemukan.',
        ]);

        try {
            $guru = Guru::findOrFail($request->guru);
            $room->nama_kelas = $request->nama_kelas;
            $room->jenis_kelas = $request->jenis_kelas;
            $room->tingkatan_kelas = $request->tingkatan_kelas;
            $room->guru_id = $guru->id;  // Menggunakan ID guru, bukan nama_guru
            $room->save();
            return redirect()->route('room.index')->with([
                'status' => 'success',
                'message' => 'Kelas berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('room.index')->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $room)
    {
        try {
            $room->delete();

            return redirect()->route('room.index')->with([
                'status' => 'success',
                'code' => 200,
                'message' => 'Kelas berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('room.index')->with([
                'status' => 'error',
                'code' => 500,
                'message' => 'Terjadi kesalahan saat menghapus kelas: ' . $e->getMessage()
            ]);
        }
    }



}
