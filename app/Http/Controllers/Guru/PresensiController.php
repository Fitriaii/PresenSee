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
use Illuminate\Support\Facades\Http;
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


        $jadwal = Jadwal::whereHas('mapel', function ($query) use ($guruId) {
            $query->where('guru_id', $guruId);
        })->with(['kelas', 'mapel'])->get();


        $mapels = Mapel::where('guru_id', $guruId)->get();


        $kelasIds = $jadwal->pluck('kelas_id')->unique();
        $kelasList = Kelas::whereIn('id', $kelasIds)->get();


        $selectedJadwal = null;
        $presensi = null;
        $siswaKelas = collect();

        if ($request->has('jadwal_id')) {
            $jadwalId = $request->jadwal_id;

            $selectedJadwal = Jadwal::with(['kelas', 'mapel'])
                ->whereHas('mapel', function ($query) use ($guruId) {
                    $query->where('guru_id', $guruId);
                })
                ->where('id', $jadwalId)
                ->first();

            if ($selectedJadwal) {

                $siswaKelas = Siswa_Kelas::where('kelas_id', $selectedJadwal->kelas_id)
                    ->with('siswa')
                    ->get();


                $presensi = Presensi::with(['siswa_kelas.siswa', 'jadwal'])
                    ->where('jadwal_id', $jadwalId)
                    ->whereIn('siswa_kelas_id', $siswaKelas->pluck('id'))
                    ->paginate(10)
                    ->appends($request->query());
            }
        } else {

            $siswaKelas = Siswa_Kelas::whereIn('kelas_id', $kelasIds)->with('siswa')->get();


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

             if (empty($data['siswa_kelas_id'])) {
                 continue;
             }

             $siswaKelasId = $data['siswa_kelas_id'];


             $presensi = Presensi::where('siswa_kelas_id', $siswaKelasId)
                 ->where('jadwal_id', $jadwal_id)
                 ->whereDate('waktu_presensi', $tanggal_presensi)
                 ->first();

             if ($presensi) {

                 $presensi->update([
                     'status' => $data['status'],
                     'catatan' => $data['catatan'] ?? null,
                 ]);
             } else {

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


        if ($presensi->jadwal->mapel->guru_id !== $guru->id) {
            return redirect()->route('presensi.index')->with([
                'status' => 'error',
                'message' => 'Anda tidak berhak mengedit presensi ini.'
            ]);
        }


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
