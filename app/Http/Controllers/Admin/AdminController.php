<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
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

        $query = User::role('admin');

        // Search by name/email/username
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== '') {
            $query->where('is_logged_in', $request->status); // 1 = aktif, 0 = nonaktif
        }

        // Sorting
        switch ($request->sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'created_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'last_login_desc':
                $query->orderBy('last_login_at', 'desc');
                break;
            default:
                $query->latest(); // default: by created_at desc
                break;
        }

        $perPage = $request->perPage ?? 10;
        $admins = $query->paginate($perPage);

        // Tandai admin yang sedang login
        $admins->map(function ($admin) use ($user) {
            $admin->is_logged_in = ($admin->id === $user->id);
        });

        if ($request->ajax()) {
            return response()->json([
                'total' => $admins->total(),
                'from' => $admins->firstItem(),
                'to' => $admins->lastItem(),
                'aktif' => $query->clone()->where('is_logged_in', 1)->count(),
                'nonaktif' => $query->clone()->where('is_logged_in', 0)->count(),
                'updated_at' => now()->diffForHumans(),
            ]);
        }

        return view('Admin.User.Admin.index', compact('admins', 'user'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Admin.User.Admin.create', );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Gunakan Validator manual
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc|unique:users,email',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'required|string|min:8',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'profile_picture.image' => 'File harus berupa gambar.',
            'profile_picture.mimes' => 'Format gambar tidak valid.',
            'profile_picture.max' => 'Ukuran gambar terlalu besar. Maksimal 2MB.',
        ]);

        $validator->after(function ($validator) use ($request) {
        $domain = substr(strrchr($request->email, "@"), 1);

            if (!$domain || (!checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A'))) {
                $validator->errors()->add('email', 'Domain email tidak valid atau tidak ditemukan.');
            }
        });

        // ⛔ STOP otomatis kalau error
        $validator->validate();

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'email_verified_at' => null,
            ];

            // Upload gambar
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $imageData = file_get_contents($file);
                $data['profile_picture'] = base64_encode($imageData);
            }

            $user = User::create($data);
            $user->assignRole('admin');
            $user->sendEmailVerificationNotification();

            return redirect()->route('admin.index')->with([
                'status' => 'success',
                'message' => 'Admin berhasil dibuat. Silakan cek email untuk aktivasi akun.'
            ]);

        } catch (\Exception $e) {
            return redirect()->route('admin.index')->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }




    /**
     * Display the specified resource.
     */
    public function show(User $admin)
    {
        if (!$admin->hasRole('admin')) {
            return redirect()->back()->with([
                'status' => 'error',
                'code' => 403,
                'message' => 'Unauthorized. Hanya pengguna dengan peran admin yang dapat dilihat.'
            ]);
        }

        return view('Admin.User.Admin.show', ['admin' => $admin]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $admin)
    {
        $admins = User::role('admin')->findOrFail($admin->id);
        if (!$admins || !$admins->hasRole('admin')) {
            return redirect()->back()->with([
                'status' => 'error',
                'code' => 403,
                'message' => 'Unauthorized. Only admins can perform this action.'
            ]);
        }
        return view('Admin.User.Admin.edit', compact('admins'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $admin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc|unique:users,email' . $admin->id,
            'password' => 'nullable|string|min:8',
            'profile_picture' => 'nullable|image|max:2048', // max 2MB
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'profile_picture.image' => 'File harus berupa gambar.',
            'profile_picture.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        try {
            $admin->name = $request->name;
            $admin->email = $request->email;

            if ($request->filled('password')) {
                $admin->password = Hash::make($request->password);
            }

            if ($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()) {
                $file = $request->file('profile_picture');
                $imageData = file_get_contents($file->getRealPath());
                $admin->profile_picture = base64_encode($imageData);
            }

            $admin->save();

            return redirect()->route('admin.index')->with([
                'status' => 'success',
                'code' => 200,
                'message' => 'Profil admin berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui profil admin: ' . $e->getMessage());

            return redirect()->back()->withInput()->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $admin)
    {
        try {
            // Cek apakah user memiliki role admin
            if (!$admin->hasRole('admin')) {
                return redirect()->back()->with([
                    'status' => 'error',
                    'message' => 'Unauthorized. Hanya admin yang dapat dihapus.'
                ]);
            }

            // Hapus user
            $admin->delete();

            return redirect()->route('admin.index')->with([
                'status' => 'success',
                'message' => 'Admin berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus admin: ' . $e->getMessage()
            ]);
        }
    }



}
