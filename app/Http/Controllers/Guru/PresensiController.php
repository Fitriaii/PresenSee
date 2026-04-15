<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\Siswa_Kelas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class PresensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        $guruId = $guru->id;

        // Ambil semua jadwal yang diajar oleh guru
        $jadwal = Jadwal::whereHas('mapel', function ($query) use ($guruId) {
            $query->where('guru_id', $guruId);
        })->with(['kelas', 'mapel'])->get();

        // Ambil hanya mapel yang diampu guru
        $mapels = Mapel::where('guru_id', $guruId)->get();

        // Ambil daftar kelas dari jadwal yang dimiliki guru
        $kelasIds = $jadwal->pluck('kelas_id')->unique();
        $kelasList = Kelas::whereIn('id', $kelasIds)->get();

        // Variabel hasil
        $selectedJadwal = null;
        $presensi = null;
        $siswaKelas = collect();

        if ($request->has('jadwal_id')) {
            $jadwalId = $request->jadwal_id;

            // Pastikan jadwal tersebut milik guru
            $selectedJadwal = Jadwal::with(['kelas', 'mapel'])
                ->whereHas('mapel', function ($query) use ($guruId) {
                    $query->where('guru_id', $guruId);
                })
                ->where('id', $jadwalId)
                ->first();

            if ($selectedJadwal) {
                // Ambil siswa yang sesuai dengan kelas pada jadwal tersebut
                $siswaKelas = Siswa_Kelas::where('kelas_id', $selectedJadwal->kelas_id)
                    ->with('siswa')
                    ->get();

                // Ambil data presensi dengan paginasi
                $presensi = Presensi::with(['siswa_kelas.siswa', 'jadwal'])
                    ->where('jadwal_id', $jadwalId)
                    ->whereIn('siswa_kelas_id', $siswaKelas->pluck('id'))
                    ->paginate(10)
                    ->appends($request->query());
            }
        } else {
            // Ambil semua siswa dari kelas-kelas yang diampu guru
            $siswaKelas = Siswa_Kelas::whereIn('kelas_id', $kelasIds)->with('siswa')->get();

            // Ambil semua presensi dari jadwal guru dan siswa yang sesuai kelas, dengan paginasi
            $presensi = Presensi::with(['siswa_kelas.siswa', 'jadwal'])
                ->whereIn('jadwal_id', $jadwal->pluck('id'))
                ->whereIn('siswa_kelas_id', $siswaKelas->pluck('id'))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->appends($request->query());
        }

        return view('Guru.Presensi.index', [
            'presensi' => $presensi,
            'jadwal' => $jadwal,
            'mapels' => $mapels,
            'kelasList' => $kelasList,
            'selectedJadwal' => $selectedJadwal,
            'siswaKelas' => $siswaKelas
        ]);
    }

    public function create(){}
    /**
     * Show the form for creating a new resource.
     */
    public function createPresensi($jadwalId)
    {
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        $guruId = $guru->id;

        // Pastikan jadwal milik guru tersebut
        $jadwal = Jadwal::with(['kelas', 'mapel'])
            ->whereHas('mapel', function ($query) use ($guruId) {
                $query->where('guru_id', $guruId);
            })
            ->where('id', $jadwalId)
            ->first();

        if (!$jadwal) {
            return redirect()->route('presensi.index')
                ->with('error', 'Jadwal tidak ditemukan atau tidak sesuai dengan guru yang login.');
        }

        $today = now()->toDateString();

        $presensi = Presensi::with(['siswa_kelas.siswa', 'jadwal'])
            ->where('jadwal_id', $jadwal->id)
            ->whereDate('waktu_presensi', $today)
            ->get();

        $presensiMap = $presensi->keyBy('siswa_kelas_id');

        $siswa = Siswa_Kelas::with('siswa')
            ->where('kelas_id', $jadwal->kelas_id)
            ->get()
            ->map(function ($item) use ($presensiMap) {
                $item->sudah_presensi = $presensiMap->has($item->id);
                $item->presensi_data = $presensiMap->get($item->id);
                return $item;
            });

        $now = now();
        $jamMulai = Carbon::createFromFormat('H:i:s', $jadwal->jam_mulai);
        $jamSelesai = Carbon::createFromFormat('H:i:s', $jadwal->jam_selesai);
        // Cek apakah saat ini berada di luar rentang jam pelajaran
        $showWarning = !($now->between($jamMulai, $jamSelesai));

        $totalSiswa = Siswa_Kelas::where('kelas_id', $jadwal->kelas_id)->count();

        return view('Guru.Presensi.create', [
            'jadwal' => $jadwal,
            'siswa' => $siswa,
            'presensi' => $presensi,
            'showWarning' => $showWarning,
            'totalSiswa' => $totalSiswa,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */

     public function simpanAbsensiManual(Request $request)
     {
         $jadwal_id = $request->input('jadwal_id');
         $tanggal_presensi = now()->toDateString();
         $dataPresensi = $request->input('presensi');

         foreach ($dataPresensi as $data) {
             // Abaikan jika tidak ada siswa_kelas_id (antisipasi input rusak)
             if (empty($data['siswa_kelas_id'])) {
                 continue;
             }

             $siswaKelasId = $data['siswa_kelas_id'];

             // Cek presensi berdasarkan siswa_kelas_id, jadwal_id dan tanggal
             $presensi = Presensi::where('siswa_kelas_id', $siswaKelasId)
                 ->where('jadwal_id', $jadwal_id)
                 ->whereDate('waktu_presensi', $tanggal_presensi)
                 ->first();

             if ($presensi) {
                 // Update jika sudah ada
                 $presensi->update([
                     'status' => $data['status'],
                     'catatan' => $data['catatan'] ?? null,
                 ]);
             } else {
                 // Buat baru jika belum ada
                 Presensi::create([
                     'siswa_kelas_id' => $siswaKelasId,
                     'jadwal_id' => $jadwal_id,
                     'waktu_presensi' => now(),
                     'status' => $data['status'],
                     'catatan' => $data['catatan'] ?? null,
                 ]);
             }
         }

         return redirect()->back()->with('success', 'Presensi manual berhasil disimpan.');
     }

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
    public function edit(Request $request, Presensi $presensi)
    {
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        $jadwal = $presensi->jadwal()->with(['kelas', 'mapel'])->first();

        if (!$jadwal || $jadwal->mapel->guru_id !== $guru->id) {
            return redirect()->route('presensi.index')->with('error', 'Anda tidak berhak mengedit presensi ini.');
        }

        // Ambil siswa_kelas yang berkaitan dengan presensi ini
        $siswa = $presensi->siswa_kelas()->with('siswa')->first();

        if (!$siswa) {
            return redirect()->route('presensi.index')->with('error', 'Data siswa tidak ditemukan.');
        }

        return view('Guru.Presensi.edit', [
            'presensi' => $presensi,
            'siswa' => $siswa->siswa,
            'jadwal' => $jadwal,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Presensi $presensi)
    {
        $request->validate([
            'status' => 'required|in:Hadir,Sakit,Izin,Alpha',
            'catatan' => 'nullable|string',
        ]);

        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Data guru tidak ditemukan.'
            ]);
        }

        $presensi = Presensi::with('jadwal.mapel')->findOrFail($presensi->id);

        // Cek kepemilikan data
        if ($presensi->jadwal->mapel->guru_id !== $guru->id) {
            return redirect()->route('presensi.index')->with([
                'status' => 'error',
                'message' => 'Anda tidak berhak mengedit presensi ini.'
            ]);
        }

        // Update presensi
        $presensi->update([
            'status' => $request->input('status'),
            'catatan' => $request->input('catatan'),
        ]);

        return redirect()->route('presensi.index')->with([
            'status' => 'success',
            'message' => 'Presensi berhasil diperbarui.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presensi $presensi)
    {
        try {
            $presensi->delete();
            return redirect()->route('presensi.index')->with([
                'status' => 'success',
                'message' => 'Presensi berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('presensi.index')->with([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data presensi: ' . $e->getMessage()
            ]);
        }
    }

    public function showCaptureForm($id)
    {
        $presensi = Presensi::findOrFail($id);
        $siswa = Siswa::all();
        return view('Guru.Presensi.create',  compact('presensi', 'siswa'));
    }

    // public function recognizeFace(Request $request)
    // {
    //     $pythonApiUrl = 'http://127.0.0.1:5000/recognize';

    //     try {
    //         if ($request->has('image_url')) {
    //             // ✅ Kasus 1: URL gambar
    //             $imageData = file_get_contents($request->input('image_url'));
    //             $base64 = base64_encode($imageData);

    //         } elseif ($request->hasFile('image_file')) {
    //             // ✅ Kasus 2: Upload file (multipart)
    //             $file = $request->file('image_file');
    //             $imageData = file_get_contents($file->getRealPath());
    //             $base64 = base64_encode($imageData);

    //         } elseif ($request->has('image')) {
    //             // ✅ Kasus 3: Base64 string
    //             $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $request->input('image'));

    //         } else {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'Tidak ada gambar yang dikirim. Gunakan image_url, image_file, atau image (base64).',
    //             ], 422);
    //         }

    //         // Kirim ke Flask
    //         $response = Http::timeout(10)->post($pythonApiUrl, [
    //             'image' => $base64,
    //         ]);

    //         if (!$response->successful()) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'Gagal menghubungi API Python.',
    //                 'error' => $response->body(),
    //             ], 500);
    //         }

    //         $data = $response->json();

    //         // === Respons sukses ===
    //         if ($data['status'] === 'success' && isset($data['nis'])) {
    //             $siswa = Siswa::where('nis', $data['nis'])->first();

    //             if ($siswa) {
    //                 return response()->json([
    //                     'status' => 'success',
    //                     'message' => 'Wajah dikenali.',
    //                     'siswa_nis' => $siswa->nis,
    //                     'nama_siswa' => $siswa->nama_siswa,
    //                     'confidence' => round($data['confidence'] ?? 0, 2),
    //                 ]);
    //             } else {
    //                 return response()->json([
    //                     'status' => 'nis_not_found',
    //                     'message' => 'Wajah dikenali, tapi NIS tidak ditemukan di database.',
    //                     'siswa_nis' => $data['siswa_nis'],
    //                     'confidence' => round($data['confidence'] ?? 0, 2),
    //                 ]);
    //             }
    //         }

    //         // === Wajah tidak dikenali ===
    //         if ($data['status'] === 'fail') {
    //             return response()->json([
    //                 'status' => 'unrecognized',
    //                 'message' => 'Wajah tidak dikenali.',
    //                 'confidence' => round($data['confidence'] ?? 0, 2),
    //             ]);
    //         }

    //         // === Tidak ditemukan wajah ===
    //         if ($data['status'] === 'face_not_found') {
    //             return response()->json([
    //                 'status' => 'face_not_found',
    //                 'message' => $data['message'] ?? 'Tidak ada wajah terdeteksi.',
    //             ]);
    //         }

    //         // === Respons tidak dikenali ===
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Respons tidak dikenali dari API Python.',
    //             'raw_response' => $data,
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Gagal memproses permintaan ke API Python.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
    // public function markAttendance(Request $request)
    // {
    //     $request->validate([
    //         'siswa_nis' => 'required|exists:siswa,nis',
    //         'jadwal_id' => 'required|exists:jadwal,id',
    //         'status' => 'nullable|in:Hadir,Sakit,Izin,Alfa',
    //         'waktu_presensi' => 'nullable',
    //         'catatan' => 'nullable|string',
    //     ]);

    //     // Ambil siswa berdasarkan NIS
    //     $siswa = Siswa::where('nis', $request->siswa_nis)->first();

    //     if (!$siswa) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Siswa tidak ditemukan.'
    //         ]);
    //     }

    //     // Ambil data siswa_kelas aktif
    //     $siswaKelas = Siswa_Kelas::where('siswa_id', $siswa->id)
    //                     ->latest()
    //                     ->first();

    //     if (!$siswaKelas) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Data kelas siswa tidak ditemukan.'
    //         ]);
    //     }

    //     $jadwalId = $request->input('jadwal_id');
    //     $jadwal = Jadwal::with('mapel', 'kelas')->find($jadwalId);

    //     // ✅ Tambahkan validasi apakah siswa ada di kelas sesuai jadwal
    //     if ($siswaKelas->kelas_id !== $jadwal->kelas_id) {
    //         return response()->json([
    //             'status' => 'invalid_class',
    //             'message' => 'Siswa tidak terdaftar di kelas sesuai jadwal.',
    //             'data' => [
    //                 'siswa' => $siswa->nama_siswa,
    //                 'kelas' => $jadwal->kelas->nama_kelas ?? '-'
    //             ]
    //         ]);
    //     }

    //     $sudahPresensi = Presensi::where('siswa_kelas_id', $siswaKelas->id)
    //                             ->where('jadwal_id', $jadwalId)
    //                             ->whereDate('waktu_presensi', now()->toDateString())
    //                             ->exists();

    //     if ($sudahPresensi) {
    //         return response()->json([
    //             'status' => 'already_marked',
    //             'message' => 'Presensi sudah tercatat sebelumnya.',
    //             'data' => [
    //                 'siswa' => $siswa->nama_siswa
    //             ]
    //         ]);
    //     }

    //     // Simpan presensi baru
    //     $presensi = new Presensi();
    //     $presensi->jadwal_id = $jadwalId;
    //     $presensi->siswa_kelas_id = $siswaKelas->id;
    //     $presensi->waktu_presensi = now();
    //     $presensi->status = 'Hadir';
    //     $presensi->catatan = $request->input('catatan') ?? 'Presensi via pengenalan wajah';
    //     $presensi->save();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Presensi berhasil dicatat.',
    //         'data' => [
    //             'siswa' => $siswa->nama_siswa,
    //             'kelas' => $jadwal->kelas->nama_kelas ?? '-',
    //             'mapel' => $jadwal->mapel->nama_mapel ?? '-',
    //             'waktu' => now()->format('d M Y H:i:s'),
    //             'status' => 'Hadir'
    //         ]
    //     ]);
    // }

    // public function recognizeAndMark(Request $request)
    // {
    //     $pythonApiUrl = 'http://127.0.0.1:5000/recognize';

    //     /* ── 1. VALIDASI INPUT ───────────────────────────────────────────── */
    //     $request->validate([
    //         'jadwal_id' => 'required|exists:jadwal,id',
    //         'image'     => 'required|string',
    //     ]);

    //     try {
    //         /* ── 2. KONVERSI GAMBAR ──────────────────────────────────────── */
    //         $base64 = preg_replace('#^data:image/\w+;base64,#', '', $request->image);

    //         /* ── 3. PANGGIL API FLASK ─────────────────────────────────────── */
    //         $flask = Http::timeout(15)
    //             ->post($pythonApiUrl, ['image' => $base64]);

    //         if (!$flask->successful()) {
    //             return response()->json([
    //                 'status'  => 'error',
    //                 'message' => 'Gagal terhubung ke layanan pengenalan wajah.',
    //             ], 500);
    //         }

    //         $res        = $flask->json();                     // {match, nis?, confidence?, message?}
    //         $isMatch    = $res['match'] ?? false;
    //         $nis        = $res['nis'] ?? null;
    //         $confidence = isset($res['confidence']) ? (float) $res['confidence'] : null;
    //         $message    = $res['message'] ?? '';

    //         /* ── 4. TANGANI FACE NOT FOUND / UNRECOGNIZED ────────────────── */
    //         if (!$isMatch) {
    //             if (str_contains(Str::lower($message), 'terdeteksi')) {      // “Wajah tidak terdeteksi”
    //                 return response()->json([
    //                     'status'  => 'face_not_found',
    //                     'message' => $message ?: 'Wajah tidak terdeteksi.',
    //                 ]);
    //             }

    //             return response()->json([
    //                 'status'      => 'unrecognized',
    //                 'message'     => $message ?: 'Wajah tidak dikenali.',
    //                 'confidence'  => $confidence,
    //             ]);
    //         }

    //         /* ── 5. OPSIONAL: LOGIKA LOW CONFIDENCE (jika ingin) ───────────
    //         if ($confidence !== null && $confidence >= 60) {
    //             return response()->json([
    //                 'status'     => 'low_confidence',
    //                 'message'    => 'Confidence rendah.',
    //                 'nis'        => $nis,
    //                 'confidence' => $confidence,
    //             ]);
    //         }
    //         ----------------------------------------------------------------*/

    //         /* ── 6. VALIDASI NIS & RELASI KELAS/JADWAL ───────────────────── */
    //         $siswa = Siswa::where('nis', $nis)->first();
    //         if (!$siswa) {
    //             return response()->json([
    //                 'status'  => 'nis_not_found',
    //                 'message' => "NIS ($nis) tidak ditemukan.",
    //             ], 404);
    //         }

    //         $siswaKelas = Siswa_Kelas::where('siswa_id', $siswa->id)->latest()->first();
    //         if (!$siswaKelas) {
    //             return response()->json([
    //                 'status'  => 'no_class_data',
    //                 'message' => 'Data kelas siswa tidak ditemukan.',
    //             ], 404);
    //         }

    //         $jadwal = Jadwal::with('mapel','kelas')->find($request->jadwal_id);
    //         if (!$jadwal || $siswaKelas->kelas_id !== $jadwal->kelas_id) {
    //             return response()->json([
    //                 'status'  => 'invalid_class',
    //                 'message' => 'Siswa tidak terdaftar di kelas jadwal ini.',
    //             ], 403);
    //         }

    //         /* ── 7. CEK DUPLIKAT PRESENSI ────────────────────────────────── */
    //         $already = Presensi::where('siswa_kelas_id', $siswaKelas->id)
    //             ->where('jadwal_id', $jadwal->id)
    //             ->whereDate('waktu_presensi', today())
    //             ->exists();

    //         if ($already) {
    //             return response()->json([
    //                 'status'  => 'already_marked',
    //                 'message' => 'Presensi sudah dicatat.',
    //             ], 409);
    //         }

    //         /* ── 8. SIMPAN PRESENSI ──────────────────────────────────────── */
    //         $presensi = Presensi::create([
    //             'jadwal_id'      => $jadwal->id,
    //             'siswa_kelas_id' => $siswaKelas->id,
    //             'waktu_presensi' => now(),
    //             'status'         => 'Hadir',
    //             'catatan'        => 'Presensi otomatis via Face Recognition',
    //         ]);

    //         return response()->json([
    //             'status'  => 'success',
    //             'message' => 'Presensi berhasil dicatat!',
    //             'data'    => [
    //                 'siswa'      => $siswa->nama_siswa,
    //                 'kelas'      => $jadwal->kelas->nama_kelas,
    //                 'mapel'      => $jadwal->mapel->nama_mapel,
    //                 'waktu'      => $presensi->waktu_presensi->format('d M Y H:i:s'),
    //                 'confidence' => $confidence,
    //             ],
    //         ], 201);

    //     } catch (\Throwable $e) {
    //         Log::error('Presensi otomatis error', ['msg'=>$e->getMessage()]);
    //         return response()->json([
    //             'status'  => 'error',
    //             'message' => 'Terjadi kesalahan internal.',
    //         ], 500);
    //     }
    // }

    public function markAttendance(Request $request)
    {
        $request->validate([
            'siswa_nis' => 'required|exists:siswa,nis',
            'jadwal_id' => 'required|exists:jadwal,id',
            'status' => 'nullable|in:Hadir,Sakit,Izin,Alfa',
            'waktu_presensi' => 'nullable',
            'catatan' => 'nullable|string',
        ]);

        $jadwalId = $request->input('jadwal_id');
        $jadwal = Jadwal::with('mapel', 'kelas')->find($jadwalId);

        // Cek jika siswa NIS kosong → Wajah tidak dikenali
        if (!$request->filled('siswa_nis')) {
            $presensi = new Presensi();
            $presensi->jadwal_id = $jadwalId;
            $presensi->siswa_kelas_id = null;
            $presensi->waktu_presensi = now();
            $presensi->status = null;
            $presensi->catatan = 'Wajah tidak dikenali';
            $presensi->save();

            return response()->json([
                'status' => 'not_recognized',
                'message' => 'Wajah tidak dikenali.'
            ]);
        }

        $siswa = Siswa::where('nis', $request->siswa_nis)->first();
        if (!$siswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa tidak ditemukan.'
            ]);
        }

        $siswaKelas = Siswa_Kelas::where('siswa_id', $siswa->id)->latest()->first();
        if (!$siswaKelas) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data kelas siswa tidak ditemukan.'
            ]);
        }

        $siswaKelasId = $siswaKelas->id;

        $sudahPresensi = Presensi::where('siswa_kelas_id', $siswaKelasId)
            ->where('jadwal_id', $jadwalId)
            ->whereDate('waktu_presensi', now()->toDateString())
            ->exists();

        if ($sudahPresensi) {
            return response()->json([
                'status' => 'already_marked',
                'message' => 'Presensi sudah tercatat sebelumnya.',
                'data' => [
                    'siswa' => $siswa->nama_siswa
                ]
            ]);
        }

        $isInClass = Siswa_Kelas::where('siswa_id', $siswa->id)
            ->where('kelas_id', $jadwal->kelas_id)
            ->exists();

        // ❌ Salah kelas
        if (!$isInClass) {
            $presensi = new Presensi();
            $presensi->jadwal_id = $jadwalId;
            $presensi->siswa_kelas_id = null;
            $presensi->waktu_presensi = now();
            $presensi->status = null;
            $presensi->catatan = 'Salah kelas';
            $presensi->save();

            return response()->json([
                'status' => 'not_allowed',
                'nama_siswa' => $siswa->nama_siswa,
                'message' => 'Siswa dikenali, tapi tidak terdaftar di kelas pada jadwal ini.'
            ]);
        }

        // ✅ Presensi berhasil
        try {
            $presensi = new Presensi();
            $presensi->jadwal_id = $jadwalId;
            $presensi->siswa_kelas_id = $siswaKelasId;
            $presensi->waktu_presensi = now();
            $presensi->status = 'Hadir';
            $presensi->catatan = $request->input('catatan') ?? 'Presensi via pengenalan wajah';
            $presensi->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Presensi berhasil dicatat.',
                'data' => [
                    'siswa' => $siswa->nama_siswa,
                    'kelas' => $jadwal->kelas->nama_kelas ?? '-',
                    'mapel' => $jadwal->mapel->nama_mapel ?? '-',
                    'waktu' => now()->format('d M Y H:i:s'),
                    'status' => 'Hadir'
                ]
            ]);
        } catch (\Exception $e) {
            // ❌ Gagal menyimpan presensi
            $presensi = new Presensi();
            $presensi->jadwal_id = $jadwalId;
            $presensi->siswa_kelas_id = $siswaKelasId ?? null;
            $presensi->waktu_presensi = now();
            $presensi->status = null;
            $presensi->catatan = 'Error saat mencatat presensi';
            $presensi->save();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan presensi.'
            ]);
        }
    }


}
