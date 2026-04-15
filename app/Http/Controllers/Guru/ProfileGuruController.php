<?php

namespace App\Http\Controllers\guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileGuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Pastikan user adalah guru
        if (!$user || !$user->hasRole('guru')) {
            return redirect()->back()->with([
                'status' => 'error',
                'code' => 403,
                'message' => 'Unauthorized. Hanya pengguna dengan peran guru yang dapat melihat profil ini.'
            ]);
        }

        // Ambil data guru berdasarkan user yang login
        $guru = Guru::with('mapel')->where('user_id', $user->id)->firstOrFail();

        // Tambahkan informasi tambahan
        $guru->email = $user->email;
        $guru->roles = $user->getRoleNames();
        $guru->is_logged_in = true;
        $guru->profile_picture;

        return view('guru.profile.index', compact('guru', 'user'));
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
    public function edit(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('guru')) {
            return redirect()->back()->with([
                'status' => 'error',
                'code' => 403,
                'message' => 'Unauthorized. Hanya pengguna dengan peran guru yang dapat mengakses halaman ini.'
            ]);
        }

        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        return view('guru.profile.edit', compact('guru', 'user'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('guru')) {
            return redirect()->back()->with([
                'status' => 'error',
                'code' => 403,
                'message' => 'Unauthorized. Hanya pengguna dengan peran guru yang dapat memperbarui data ini.'
            ]);
        }

        $guru = Guru::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'nama_guru' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email,{$user->id}",
            'profile_picture' => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
            'nip' => "required|string|max:255|unique:guru,nip,{$guru->id}",
            'alamat' => 'required|string|max:255',
            'no_hp' => 'required|string|max:15',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'status_keaktifan' => 'required|in:Aktif,Tidak Aktif',
            'password' => 'nullable|string|min:8',
        ]);

        DB::beginTransaction();

        try {
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $path = $file->store('profile_picture', 'public');

                $user->profile_picture = $path;
            }

            // Update data user
            $user->name = $validated['nama_guru'];
            $user->email = $validated['email'];
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();

            // Update data guru
            $guru->nama_guru = $validated['nama_guru'];
            $guru->nip = $validated['nip'];
            $guru->alamat = $validated['alamat'];
            $guru->no_hp = $validated['no_hp'];
            $guru->jenis_kelamin = $validated['jenis_kelamin'];
            $guru->status_keaktifan = $validated['status_keaktifan'];
            $guru->save();

            DB::commit();

            return redirect()->route('profileGuru.index')->with([
                'status' => 'success',
                'code' => 200,
                'message' => "Profil berhasil diperbarui."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat memperbarui profil guru: ' . $e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'error' => 'Gagal memperbarui profil: ' . $e->getMessage()
            ]);
        }
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
