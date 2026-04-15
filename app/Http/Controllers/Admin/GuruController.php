<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Pastikan hanya admin yang boleh akses
        if (!$user || !$user->hasRole('admin')) {
            return redirect()->back()->with([
                'status' => 'error',
                'code' => 403,
                'message' => 'Unauthorized. Only admins can perform this action.'
            ]);
        }

        // Query guru dengan relasi user
        $query = Guru::with('user');

        // Filter by search (cari di user terkait nama/email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('nip', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status is_logged_in (berdasarkan relasi user)
        if ($request->filled('status') && $request->status !== '') {
            $status = (int) $request->status; // 1 or 0
            $query->whereHas('user', function ($q) use ($status) {
                $q->where('is_logged_in', $status);
            });
        }

        // Sorting berdasarkan request param
        switch ($request->sort) {
            case 'name_asc':
                $query->whereHas('user')->orderBy(User::select('name')->whereColumn('users.id', 'guru.user_id'), 'asc');
                break;
            case 'name_desc':
                $query->whereHas('user')->orderBy(User::select('name')->whereColumn('users.id', 'guru.user_id'), 'desc');
                break;
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'created_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'last_login_desc':
                // Sorting by related user's last_login_at
                $query->whereHas('user')->orderBy(User::select('last_login_at')->whereColumn('users.id', 'guru.user_id'), 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Pagination
        $perPage = $request->perPage ?? 10;
        $userGuru = $query->paginate($perPage);

        // Tambahkan atribut tambahan di setiap model guru
        $userGuru->getCollection()->transform(function ($guru) use ($user) {
            $guru->is_logged_in = $guru->user && $guru->user->id === $user->id ? 1 : 0;
            if ($guru->user) {
                $guru->email = $guru->user->email;
                $guru->roles = $guru->user->getRoleNames();
            }
            return $guru;
        });

        return view('Admin.User.Guru.index', compact('userGuru', 'user'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Admin.User.Guru.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_guru' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
            'nip' => 'required|string|max:255|unique:guru,nip',
            'alamat' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'status_keaktifan' => 'required|in:Aktif,Tidak Aktif',
            'no_hp' => 'required|string|max:15',
        ], [
            'nama_guru.required' => 'Nama guru wajib diisi.',
            'nama_guru.max' => 'Nama guru maksimal 255 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',

            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi harus mengandung setidaknya satu huruf besar, satu huruf kecil, satu angka, dan satu karakter khusus.',

            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah digunakan.',
            'nip.max' => 'NIP maksimal 255 karakter.',

            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.max' => 'Alamat maksimal 255 karakter.',

            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin harus Laki-Laki atau Perempuan.',

            'status_keaktifan.required' => 'Status keaktifan wajib dipilih.',
            'status_keaktifan.in' => 'Status keaktifan harus Aktif atau Tidak Aktif.',

            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.max' => 'Nomor HP maksimal 15 digit.',
        ]);

        DB::beginTransaction();

        try {

            $email = $request->email;
            $domain = substr(strrchr($email, "@"), 1);

            if (!checkdnsrr($domain, 'MX')) {
                return back()->withErrors([
                    'email' => 'Domain email tidak ditemukan atau tidak valid.'
                ])->withInput();
            };

            // Simpan ke tabel users
            $user = new User();
            $user->name = $validated['nama_guru'];
            $user->email = $validated['email'];
            $user->password = Hash::make($validated['password']);
            $user->email_verified_at = null;
            $user->save();

            // Berikan role guru
            $user->assignRole('guru');

            // Simpan ke tabel guru
            $guru = new Guru();
            $guru->user_id = $user->id;
            $guru->nama_guru = $validated['nama_guru'];
            $guru->nip = $validated['nip'];
            $guru->alamat = $validated['alamat'];
            $guru->no_hp = $validated['no_hp'];
            $guru->jenis_kelamin = $validated['jenis_kelamin'];
            $guru->status_keaktifan = $validated['status_keaktifan'];
            $guru->save();

            DB::commit();

            $user->sendEmailVerificationNotification();

            return redirect()->route('guru.index')->with([
                'status' => 'success',
                'code' => 200,
                'message' => "Guru {$validated['nama_guru']} berhasil ditambahkan. Silakan minta guru cek email untuk aktivasi akun."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan data guru: ' . $e->getMessage());

            return redirect()->back()->withInput()->with([
                'status' => 'error',
                'code' => 500,
                'message' => 'Gagal menyimpan data guru. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Guru $guru)
    {
        $user = $guru->user;
        if (!$user || !$user->hasRole('guru')) {
            return redirect()->back()->with([
                'status' => 'error',
                'code' => 403,
                'message' => 'Unauthorized. Hanya pengguna dengan peran guru yang dapat dilihat.'
            ]);
        }

        $guru->is_logged_in = ($guru->user_id === $user->id);
        if ($guru->user) {
            $guru->email = $guru->user->email;
            $guru->roles = $guru->user->getRoleNames();
        }

        return view('Admin.User.Guru.show', compact('guru', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guru $guru)
    {
        $user = $guru->user;
        if (!$user || !$user->hasRole('guru')) {
            return redirect()->back()->with([
                'status' => 'error',
                'code' => 403,
                'message' => 'Unauthorized. Hanya pengguna dengan peran guru yang dapat diedit.'
            ]);
        }

        return view('Admin.User.Guru.edit', compact('guru', 'user'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'nama_guru' => 'required|string|max:255',
            'email' => "required|email:rfc,dns|unique:users,email,{$guru->user->id}", // Abaikan email user yang sedang diedit
            'nip' => 'required|string|max:255|unique:guru,nip,' . $guru->id, // Abaikan NIP guru yang sedang diedit
            'alamat' => 'required|string|max:255',
            'no_hp' => 'required|string|max:15',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'status_keaktifan' => 'required|in:Aktif,Tidak Aktif',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
        ], [
            'nama_guru.required' => 'Nama guru wajib diisi.',
            'nama_guru.max' => 'Nama guru maksimal 255 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',

            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi harus mengandung setidaknya satu huruf besar, satu huruf kecil, satu angka, dan satu karakter khusus.',

            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah digunakan.',
            'nip.max' => 'NIP maksimal 255 karakter.',

            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.max' => 'Alamat maksimal 255 karakter.',

            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin harus Laki-Laki atau Perempuan.',

            'status_keaktifan.required' => 'Status keaktifan wajib dipilih.',
            'status_keaktifan.in' => 'Status keaktifan harus Aktif atau Tidak Aktif.',

            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.max' => 'Nomor HP maksimal 15 digit.',
        ]);

        DB::beginTransaction();

        try {
            $guru = Guru::findOrFail($guru->id);
            $user = $guru->user; // Relasi ke user dari model guru
            $user->name = $validated['nama_guru'];
            $user->email = $validated['email']; // Email tetap sama jika tidak diubah
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();
            $guru->nama_guru = $validated['nama_guru'];
            $guru->nip = $validated['nip'];
            $guru->alamat = $validated['alamat'];
            $guru->no_hp = $validated['no_hp'];
            $guru->jenis_kelamin = $validated['jenis_kelamin'];
            $guru->status_keaktifan = $validated['status_keaktifan'];
            $guru->save();

            DB::commit();

            return redirect()->route('guru.index')->with([
                'status' => 'success',
                'code' => 200,
                'message' => "Data guru {$validated['nama_guru']} berhasil diperbarui."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat mengupdate data guru: ' . $e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'error' => 'Gagal memperbarui data guru: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guru $guru)
    {
        try {
            // Hapus relasi user jika ada
            if ($guru->user) {
                $guru->user->delete();
            }

            // Hapus data guru
            $guru->delete();

            return redirect()->route('guru.index')->with([
                'status' => 'success',
                'code' => 200,
                'message' => 'Guru berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('guru.index')->with([
                'status' => 'error',
                'code' => 500,
                'message' => 'Terjadi kesalahan saat menghapus guru: ' . $e->getMessage()
            ]);
        }
    }




}
