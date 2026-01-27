<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Barcode;
use App\Models\User;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalKaryawan = User::where('role', 'karyawan')->whereNull('tanggal_keluar')->count();

        $totalHadir = Absensi::whereDate('tanggal_absen', today())
            ->where('status', 'hadir')
            ->count();

        $totalTerlambat = Absensi::whereDate('tanggal_absen', today())
            ->where('status', 'terlambat')
            ->count();

        $absensiHariIni = Absensi::with(['user', 'shift'])
            ->whereDate('tanggal_absen', today())
            ->orderBy('waktu_absen', 'desc')
            ->limit(10)
            ->get();

        // Statistics for chart (last 7 days) - FIXED
        $dates = [];
        $hadirData = [];
        $terlambatData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[] = now()->subDays($i)->translatedFormat('D, d M');

            // Count attendance for this day
            $hadir = Absensi::whereDate('tanggal_absen', $date)
                ->where('status', 'hadir')
                ->count();

            $terlambat = Absensi::whereDate('tanggal_absen', $date)
                ->where('status', 'terlambat')
                ->count();

            $hadirData[] = $hadir;
            $terlambatData[] = $terlambat;
        }

        return view('admin.dashboard', compact(
            'totalKaryawan',
            'totalHadir',
            'totalTerlambat',
            'absensiHariIni',
            'dates',
            'hadirData',
            'terlambatData'
        ));
    }

    // Data Karyawan
    public function dataKaryawan(Request $request)
    {
        $query = User::where('role', 'karyawan');

        // Filter berdasarkan status
        if ($request->has('status') && in_array($request->status, ['aktif', 'nonaktif'])) {
            if ($request->status == 'aktif') {
                $query->whereNull('tanggal_keluar');
            } else {
                $query->whereNotNull('tanggal_keluar');
            }
        }

        // Filter berdasarkan departemen
        if ($request->has('departemen') && $request->departemen) {
            $query->where('departemen', 'like', "%{$request->departemen}%");
        }

        // Pencarian
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('nip', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('departemen', 'like', "%$search%")
                  ->orWhere('jabatan', 'like', "%$search%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Get distinct departemen untuk suggestions
        $departemenList = User::where('role', 'karyawan')
            ->whereNotNull('departemen')
            ->select('departemen')
            ->distinct()
            ->orderBy('departemen')
            ->pluck('departemen');

        $perPage = $request->get('per_page', 15);
        $karyawan = $query->paginate($perPage);

        return view('admin.data-karyawan', compact('karyawan', 'departemenList'));
    }

    /**
     * Menyimpan data karyawan baru
     */
    public function tambahKaryawan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|max:100',
            'nip' => 'nullable|unique:users,nip|max:20',
            'username' => 'required|unique:users,username|max:50|regex:/^[a-zA-Z0-9_]+$/',
            'email' => 'nullable|email|unique:users,email|max:100',
            'password' => 'required|min:6|confirmed',
            'jabatan' => 'nullable|max:100',
            'departemen' => 'nullable|max:100',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir' => 'nullable|max:100',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable',
            'no_telepon' => 'nullable|max:20',
            'status_karyawan' => 'nullable|in:tetap,kontrak,probation',
            'tanggal_masuk' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ], [
            'username.regex' => 'Username hanya boleh berisi huruf, angka dan underscore',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'foto.max' => 'Ukuran foto maksimal 2MB'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'uuid_user' => Str::uuid(),
                'nama' => $request->nama,
                'nip' => $request->nip,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'karyawan',
                'jabatan' => $request->jabatan,
                'departemen' => $request->departemen,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'no_telepon' => $request->no_telepon,
                'status_karyawan' => $request->status_karyawan ?? 'probation',
                'tanggal_masuk' => $request->tanggal_masuk ?? now(),
            ];

            // Handle foto upload
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = 'foto-' . time() . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('foto-karyawan', $filename, 'public');
                $data['foto'] = $path;
            }

            User::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail karyawan
     */
    public function detailKaryawan($id)
    {
        try {
            $karyawan = User::where('id', $id)
                ->where('role', 'karyawan')
                ->first();

            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan tidak ditemukan'
                ], 404);
            }

            // Hitung usia
            $usia = null;
            if ($karyawan->tanggal_lahir) {
                $usia = Carbon::parse($karyawan->tanggal_lahir)->age;
            }

            // Hitung masa kerja
            $masaKerja = null;
            if ($karyawan->tanggal_masuk) {
                $tanggalMasuk = Carbon::parse($karyawan->tanggal_masuk);
                $now = Carbon::now();

                $tahun = $now->diffInYears($tanggalMasuk);
                $bulan = $now->diffInMonths($tanggalMasuk) % 12;
                $hari = $now->diffInDays($tanggalMasuk) % 30;

                if ($tahun > 0) {
                    $masaKerja = $tahun . ' tahun';
                    if ($bulan > 0) {
                        $masaKerja .= ' ' . $bulan . ' bulan';
                    }
                } elseif ($bulan > 0) {
                    $masaKerja = $bulan . ' bulan';
                    if ($hari > 0) {
                        $masaKerja .= ' ' . $hari . ' hari';
                    }
                } else {
                    $masaKerja = $hari . ' hari';
                }
            }

            // Format data untuk response
            $data = [
                'id' => $karyawan->id,
                'uuid_user' => $karyawan->uuid_user,
                'nama' => $karyawan->nama,
                'nip' => $karyawan->nip,
                'username' => $karyawan->username,
                'email' => $karyawan->email,
                'jabatan' => $karyawan->jabatan,
                'departemen' => $karyawan->departemen,
                'jenis_kelamin' => $karyawan->jenis_kelamin,
                'tempat_lahir' => $karyawan->tempat_lahir,
                'tanggal_lahir' => $karyawan->tanggal_lahir ? $karyawan->tanggal_lahir->format('Y-m-d') : null,
                'usia' => $usia,
                'alamat' => $karyawan->alamat,
                'no_telepon' => $karyawan->no_telepon,
                'status_karyawan' => $karyawan->status_karyawan,
                'tanggal_masuk' => $karyawan->tanggal_masuk ? $karyawan->tanggal_masuk->format('Y-m-d') : null,
                'tanggal_keluar' => $karyawan->tanggal_keluar ? $karyawan->tanggal_keluar->format('Y-m-d') : null,
                'masa_kerja' => $masaKerja,
                'foto' => $karyawan->foto,
                'created_at' => $karyawan->created_at->format('d F Y H:i'),
                'updated_at' => $karyawan->updated_at->format('d F Y H:i'),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error in detailKaryawan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan form edit karyawan
     */
     public function editKaryawan($id)
    {
        try {
            $karyawan = User::where('id', $id)
                ->where('role', 'karyawan')
                ->first();

            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan tidak ditemukan'
                ], 404);
            }

            // Format dates for input fields
            $karyawan->tanggal_lahir_formatted = $karyawan->tanggal_lahir ? $karyawan->tanggal_lahir->format('Y-m-d') : null;
            $karyawan->tanggal_masuk_formatted = $karyawan->tanggal_masuk ? $karyawan->tanggal_masuk->format('Y-m-d') : null;
            $karyawan->tanggal_keluar_formatted = $karyawan->tanggal_keluar ? $karyawan->tanggal_keluar->format('Y-m-d') : null;

            return response()->json([
                'success' => true,
                'data' => $karyawan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memperbarui data karyawan (FIXED)
     */
    public function updateKaryawan(Request $request, $id)
    {
        try {
            $karyawan = User::where('id', $id)
                ->where('role', 'karyawan')
                ->first();

            if (!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama' => 'required|max:100',
                'nip' => [
                    'nullable',
                    'max:20',
                    Rule::unique('users', 'nip')->ignore($karyawan->id)
                ],
                'username' => [
                    'required',
                    'max:50',
                    'regex:/^[a-zA-Z0-9_]+$/',
                    Rule::unique('users', 'username')->ignore($karyawan->id)
                ],
                'email' => [
                    'nullable',
                    'email',
                    'max:100',
                    Rule::unique('users', 'email')->ignore($karyawan->id)
                ],
                'password' => 'nullable|min:6|confirmed',
                'jabatan' => 'nullable|max:100',
                'departemen' => 'nullable|max:100',
                'jenis_kelamin' => 'nullable|in:L,P',
                'tempat_lahir' => 'nullable|max:100',
                'tanggal_lahir' => 'nullable|date',
                'alamat' => 'nullable',
                'no_telepon' => 'nullable|max:20',
                'status_karyawan' => 'nullable|in:tetap,kontrak,probation',
                'tanggal_masuk' => 'nullable|date',
                'tanggal_keluar' => 'nullable|date',
                'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'hapus_foto' => 'nullable|boolean'
            ], [
                'username.regex' => 'Username hanya boleh berisi huruf, angka dan underscore',
                'password.confirmed' => 'Konfirmasi password tidak cocok',
                'foto.max' => 'Ukuran foto maksimal 2MB'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'nama' => $request->nama,
                'nip' => $request->nip,
                'username' => $request->username,
                'email' => $request->email,
                'jabatan' => $request->jabatan,
                'departemen' => $request->departemen,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'no_telepon' => $request->no_telepon,
                'status_karyawan' => $request->status_karyawan,
                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_keluar' => $request->tanggal_keluar,
            ];

            // Update password jika diisi
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // Handle foto upload
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($karyawan->foto) {
                    Storage::disk('public')->delete($karyawan->foto);
                }

                $file = $request->file('foto');
                $filename = 'foto-' . time() . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('foto-karyawan', $filename, 'public');
                $data['foto'] = $path;
            } elseif ($request->has('hapus_foto') && $request->hapus_foto == '1' && $karyawan->foto) {
                // Hapus foto jika diminta
                Storage::disk('public')->delete($karyawan->foto);
                $data['foto'] = null;
            }

            $karyawan->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menonaktifkan karyawan
     */
    public function nonaktifkanKaryawan(Request $request, $id)
    {
        try {
            $karyawan = User::where('id', $id)
                ->where('role', 'karyawan')
                ->firstOrFail();

            $validator = Validator::make($request->all(), [
                'tanggal_keluar' => 'required|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $karyawan->update([
                'tanggal_keluar' => $request->tanggal_keluar
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dinonaktifkan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan atau gagal dinonaktifkan: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mengaktifkan kembali karyawan
     */
    public function aktifkanKaryawan(Request $request, $id)
    {
        try {
            $karyawan = User::where('id', $id)
                ->where('role', 'karyawan')
                ->whereNotNull('tanggal_keluar')
                ->firstOrFail();

            $karyawan->update([
                'tanggal_keluar' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil diaktifkan kembali.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan atau sudah aktif: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Menghapus data karyawan permanen
     */
    public function hapusKaryawan($id)
    {
        try {
            $karyawan = User::where('id', $id)
                ->where('role', 'karyawan')
                ->firstOrFail();

            // Cek apakah karyawan memiliki data absensi
            $hasAbsensi = \App\Models\Absensi::where('id_user', $id)->exists();

            if ($hasAbsensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus karyawan karena memiliki data absensi. Nonaktifkan terlebih dahulu.'
                ], 400);
            }

            // Hapus foto jika ada
            if ($karyawan->foto) {
                Storage::disk('public')->delete($karyawan->foto);
            }

            // Hapus data
            $nama = $karyawan->nama;
            $karyawan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Karyawan ' . $nama . ' berhasil dihapus permanen.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan atau gagal dihapus: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Export data karyawan
     */
    public function exportKaryawan(Request $request)
    {
        $query = User::where('role', 'karyawan');

        if ($request->has('departemen') && $request->departemen) {
            $query->where('departemen', 'like', "%{$request->departemen}%");
        }

        $karyawan = $query->get();

        // TODO: Implement export to Excel/PDF
        return response()->json([
            'success' => true,
            'data' => $karyawan
        ]);
    }

    /**
     * Get departemen suggestions
     */
    public function getDepartemenSuggestions(Request $request)
    {
        $search = $request->get('search', '');

        $departemen = User::where('role', 'karyawan')
            ->whereNotNull('departemen')
            ->where('departemen', 'like', "%{$search}%")
            ->select('departemen')
            ->distinct()
            ->orderBy('departemen')
            ->limit(10)
            ->pluck('departemen');

        return response()->json([
            'success' => true,
            'data' => $departemen
        ]);
    }

    // Data Shift
    public function dataShift(Request $request)
    {
        // Dapatkan parameter pagination
        $perPage = $request->get('per_page', 10);

        // Query shift dengan pagination
        $shifts = Shift::withCount('absensi')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('admin.data-shift', compact('shifts'));
    }

    public function simpanShift(Request $request)
    {
        $request->validate([
            'nama_shift' => 'required|string|max:100|unique:shifts,nama_shift',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_akhir' => 'required|date_format:H:i',
            'batas_telat' => 'required|integer|min:0|max:120'
        ]);

        // Validasi tambahan: jam akhir tidak boleh sama dengan jam mulai
        if ($request->jam_mulai === $request->jam_akhir) {
            return response()->json([
                'errors' => [
                    'jam_akhir' => ['Jam akhir tidak boleh sama dengan jam mulai']
                ]
            ], 422);
        }

        Shift::create([
            'nama_shift' => $request->nama_shift,
            'jam_mulai' => $request->jam_mulai,
            'jam_akhir' => $request->jam_akhir,
            'batas_telat' => $request->batas_telat
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shift "' . $request->nama_shift . '" berhasil ditambahkan.'
        ]);
    }

    /**
     * Edit data shift
     */
    public function editShift(Request $request, $id)
    {
        try {
            $shift = Shift::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $shift
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shift tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update data shift
     */
    public function updateShift(Request $request, $id)
    {
        try {
            $shift = Shift::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nama_shift' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('shifts', 'nama_shift')->ignore($shift->id)
                ],
                'jam_mulai' => 'required|date_format:H:i',
                'jam_akhir' => 'required|date_format:H:i',
                'batas_telat' => 'required|integer|min:0|max:120'
            ], [
                'jam_mulai.date_format' => 'Format jam mulai tidak valid',
                'jam_akhir.date_format' => 'Format jam akhir tidak valid',
            ]);

            // Validasi tambahan: jam akhir tidak boleh sama dengan jam mulai
            if ($request->jam_mulai === $request->jam_akhir) {
                return response()->json([
                    'errors' => [
                        'jam_akhir' => ['Jam akhir tidak boleh sama dengan jam mulai']
                    ]
                ], 422);
            }

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Simpan nama shift lama untuk pesan response
            $oldName = $shift->nama_shift;

            $shift->update([
                'nama_shift' => $request->nama_shift,
                'jam_mulai' => $request->jam_mulai,
                'jam_akhir' => $request->jam_akhir,
                'batas_telat' => $request->batas_telat
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Shift "' . $oldName . '" berhasil diperbarui menjadi "' . $request->nama_shift . '".'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus data shift
     */
    public function hapusShift($id)
    {
        try {
            $shift = Shift::findOrFail($id);

            // Cek apakah shift memiliki data absensi
            $hasAbsensi = Absensi::where('id_shift', $id)->exists();

            if ($hasAbsensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus shift karena sudah digunakan dalam data absensi.'
                ], 400);
            }

            $shiftName = $shift->nama_shift;
            $shift->delete();

            return response()->json([
                'success' => true,
                'message' => 'Shift "' . $shiftName . '" berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shift tidak ditemukan atau gagal dihapus: ' . $e->getMessage()
            ], 404);
        }
    }

    // QR Code
    public function showGenerateBarcode()
    {
        $shifts = Shift::all();
        return view('admin.generate-barcode', compact('shifts'));
    }

    public function generateBarcode(Request $request)
    {
        $request->validate([
            'id_shift' => 'required|exists:shifts,id',
            'durasi_menit' => 'required|integer|min:1|max:60'
        ]);

        $shift = Shift::findOrFail($request->id_shift);
        $kode = Str::uuid();

        $waktuMulai = now();
        $waktuAkhir = now()->addMinutes((int)$request->durasi_menit);

        $barcode = Barcode::create([
            'id_shift' => $request->id_shift,
            'kode_barcode' => $kode,
            'waktu_mulai' => $waktuMulai,
            'waktu_akhir' => $waktuAkhir,
            'status' => 'aktif'
        ]);

        return response()->json([
            'success' => true,
            'barcode' => $barcode,
            'shift' => $shift,
            'redirect' => route('admin.barcode-display', $barcode->id)
        ]);
    }

    public function showBarcode($id)
    {
        $barcode = Barcode::with('shift')->findOrFail($id);
        return view('admin.barcode-display', compact('barcode'));
    }

    // LAPORAN ABSENSI PROFESIONAL - FLEKSIBEL UNTUK SEMUA TAHUN
 public function laporanAbsensi(Request $request)
    {
        // Set waktu Indonesia (WIB)
        date_default_timezone_set('Asia/Jakarta');

        // Query dasar
        $query = Absensi::with(['user', 'shift']);

        // Default ke bulan ini jika tidak ada filter
        $bulan = $request->has('bulan') && $request->bulan
            ? Carbon::parse($request->bulan)->format('Y-m')
            : now()->format('Y-m');

        $tahun = $request->has('tahun') && $request->tahun
            ? $request->tahun
            : now()->format('Y');

        // Tentukan mode periode
        $periodeMode = 'monthly';
        if ($request->has('tanggal_dari') && $request->tanggal_dari) {
            $query->whereDate('tanggal_absen', '>=', $request->tanggal_dari);
            $periodeMode = 'range';
        } else {
            // Default ke bulan ini
            $yearMonth = explode('-', $bulan);
            $tahun = $yearMonth[0];
            $bulanAngka = (int)$yearMonth[1];
            $query->whereYear('tanggal_absen', $tahun)
                  ->whereMonth('tanggal_absen', $bulanAngka);
        }

        if ($request->has('tanggal_sampai') && $request->tanggal_sampai) {
            $query->whereDate('tanggal_absen', '<=', $request->tanggal_sampai);
        }

        if ($request->has('karyawan') && $request->karyawan) {
            $query->where('id_user', $request->karyawan);
        }

        // Tambahkan filter departemen
        if ($request->has('departemen') && $request->departemen) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('departemen', $request->departemen);
            });
        }

        $absensi = $query->orderBy('tanggal_absen', 'desc')
            ->orderBy('waktu_absen', 'desc')
            ->paginate(20);

        $karyawanList = User::where('role', 'karyawan')->get();

        // Hitung persentase absensi per bulan per karyawan
        $persentasePerKaryawan = collect();
        $persentaseAbsensi = 0;
        $hariKerja = 0;
        $periodeLabel = '';
        $totalKaryawanAktif = 0;
        $totalHariBulan = 30; // TETAP 30 HARI UNTUK KAFE

        // Jika menggunakan filter bulan
        if ($periodeMode == 'monthly') {
            $yearMonth = explode('-', $bulan);
            $tahun = $yearMonth[0];
            $bulanAngka = (int)$yearMonth[1];

            // UNTUK KAFE: Tetap 30 hari per bulan
            $hariKerja = 30; // Standard 30 hari untuk pekerja kafe

            // Format label periode Indonesia
            $periodeLabel = $this->formatPeriodeIndonesia($tahun, $bulanAngka);

            // Total karyawan aktif
            $totalKaryawanAktif = User::where('role', 'karyawan')
                ->whereNull('tanggal_keluar')
                ->count();

            if ($request->karyawan) {
                // Hitung untuk karyawan tertentu
                $karyawan = User::find($request->karyawan);
                if ($karyawan) {
                    $totalHadir = Absensi::where('id_user', $request->karyawan)
                        ->whereYear('tanggal_absen', $tahun)
                        ->whereMonth('tanggal_absen', $bulanAngka)
                        ->count();

                    // Hitung keterlambatan untuk karyawan tertentu
                    $totalTerlambat = Absensi::where('id_user', $request->karyawan)
                        ->whereYear('tanggal_absen', $tahun)
                        ->whereMonth('tanggal_absen', $bulanAngka)
                        ->where('status', 'terlambat')
                        ->count();

                    // Persentase berdasarkan 30 hari
                    $persentaseAbsensi = round(($totalHadir / $hariKerja) * 100, 2);

                    // Data khusus untuk karyawan tertentu
                    $persentasePerKaryawan->push([
                        'id' => $karyawan->id,
                        'nama' => $karyawan->nama,
                        'nip' => $karyawan->nip ?? '-',
                        'departemen' => $karyawan->departemen ?? '-',
                        'jabatan' => $karyawan->jabatan ?? '-',
                        'total_hadir' => $totalHadir,
                        'total_terlambat' => $totalTerlambat,
                        'hari_kerja' => $hariKerja,
                        'total_hari_bulan' => $totalHariBulan,
                        'persentase' => $persentaseAbsensi,
                        'status' => $this->getStatusPersentaseKafe($persentaseAbsensi),
                        'rata_rata_terlambat' => $totalHadir > 0 ? round($totalTerlambat / $totalHadir * 100, 1) : 0,
                        'alpha' => max(0, $hariKerja - $totalHadir)
                    ]);
                }
            } else {
                // Hitung persentase untuk setiap karyawan
                $karyawanAktif = User::where('role', 'karyawan')
                    ->whereNull('tanggal_keluar')
                    ->get();

                foreach ($karyawanAktif as $karyawan) {
                    $totalHadir = Absensi::where('id_user', $karyawan->id)
                        ->whereYear('tanggal_absen', $tahun)
                        ->whereMonth('tanggal_absen', $bulanAngka)
                        ->count();

                    // Hitung keterlambatan
                    $totalTerlambat = Absensi::where('id_user', $karyawan->id)
                        ->whereYear('tanggal_absen', $tahun)
                        ->whereMonth('tanggal_absen', $bulanAngka)
                        ->where('status', 'terlambat')
                        ->count();

                    // Persentase berdasarkan 30 hari
                    $persentase = round(($totalHadir / $hariKerja) * 100, 2);

                    $persentasePerKaryawan->push([
                        'id' => $karyawan->id,
                        'nama' => $karyawan->nama,
                        'nip' => $karyawan->nip ?? '-',
                        'departemen' => $karyawan->departemen ?? '-',
                        'jabatan' => $karyawan->jabatan ?? '-',
                        'total_hadir' => $totalHadir,
                        'total_terlambat' => $totalTerlambat,
                        'hari_kerja' => $hariKerja,
                        'total_hari_bulan' => $totalHariBulan,
                        'persentase' => $persentase,
                        'status' => $this->getStatusPersentaseKafe($persentase),
                        'rata_rata_terlambat' => $totalHadir > 0 ? round($totalTerlambat / $totalHadir * 100, 1) : 0,
                        'alpha' => max(0, $hariKerja - $totalHadir)
                    ]);
                }

                // Urutkan berdasarkan persentase tertinggi
                $persentasePerKaryawan = $persentasePerKaryawan->sortByDesc('persentase');

                // Hitung rata-rata semua karyawan
                if ($totalKaryawanAktif > 0) {
                    $totalAbsensi = Absensi::whereYear('tanggal_absen', $tahun)
                        ->whereMonth('tanggal_absen', $bulanAngka)
                        ->count();

                    $totalHarapanAbsensi = $totalKaryawanAktif * $hariKerja;
                    if ($totalHarapanAbsensi > 0) {
                        $persentaseAbsensi = round(($totalAbsensi / $totalHarapanAbsensi) * 100, 2);
                    }
                }
            }
        } else {
            // Jika menggunakan filter range tanggal
            if ($request->has('tanggal_dari') && $request->tanggal_dari &&
                $request->has('tanggal_sampai') && $request->tanggal_sampai) {

                $tanggalDari = Carbon::parse($request->tanggal_dari);
                $tanggalSampai = Carbon::parse($request->tanggal_sampai);

                // Untuk rentang tanggal, hitung total hari dalam rentang
                $hariKerja = $tanggalDari->diffInDays($tanggalSampai) + 1;
                $totalHariBulan = $hariKerja;
                $periodeLabel = $tanggalDari->translatedFormat('d F Y') . ' - ' . $tanggalSampai->translatedFormat('d F Y');

                if ($request->karyawan) {
                    // Hitung untuk karyawan tertentu
                    $karyawan = User::find($request->karyawan);
                    if ($karyawan) {
                        $totalHadir = Absensi::where('id_user', $request->karyawan)
                            ->whereDate('tanggal_absen', '>=', $request->tanggal_dari)
                            ->whereDate('tanggal_absen', '<=', $request->tanggal_sampai)
                            ->count();

                        // Hitung keterlambatan untuk karyawan tertentu
                        $totalTerlambat = Absensi::where('id_user', $request->karyawan)
                            ->whereDate('tanggal_absen', '>=', $request->tanggal_dari)
                            ->whereDate('tanggal_absen', '<=', $request->tanggal_sampai)
                            ->where('status', 'terlambat')
                            ->count();

                        $persentaseAbsensi = $hariKerja > 0 ? round(($totalHadir / $hariKerja) * 100, 2) : 0;

                        $persentasePerKaryawan->push([
                            'id' => $karyawan->id,
                            'nama' => $karyawan->nama,
                            'nip' => $karyawan->nip ?? '-',
                            'departemen' => $karyawan->departemen ?? '-',
                            'jabatan' => $karyawan->jabatan ?? '-',
                            'total_hadir' => $totalHadir,
                            'total_terlambat' => $totalTerlambat,
                            'hari_kerja' => $hariKerja,
                            'total_hari_bulan' => $totalHariBulan,
                            'persentase' => $persentaseAbsensi,
                            'status' => $this->getStatusPersentaseKafe($persentaseAbsensi),
                            'rata_rata_terlambat' => $totalHadir > 0 ? round($totalTerlambat / $totalHadir * 100, 1) : 0,
                            'alpha' => max(0, $hariKerja - $totalHadir)
                        ]);
                    }
                } else {
                    // Hitung persentase untuk setiap karyawan
                    $karyawanAktif = User::where('role', 'karyawan')
                        ->whereNull('tanggal_keluar')
                        ->get();

                    foreach ($karyawanAktif as $karyawan) {
                        $totalHadir = Absensi::where('id_user', $karyawan->id)
                            ->whereDate('tanggal_absen', '>=', $request->tanggal_dari)
                            ->whereDate('tanggal_absen', '<=', $request->tanggal_sampai)
                            ->count();

                        // Hitung keterlambatan untuk rentang tanggal
                        $totalTerlambat = Absensi::where('id_user', $karyawan->id)
                            ->whereDate('tanggal_absen', '>=', $request->tanggal_dari)
                            ->whereDate('tanggal_absen', '<=', $request->tanggal_sampai)
                            ->where('status', 'terlambat')
                            ->count();

                        $persentase = $hariKerja > 0 ? round(($totalHadir / $hariKerja) * 100, 2) : 0;

                        $persentasePerKaryawan->push([
                            'id' => $karyawan->id,
                            'nama' => $karyawan->nama,
                            'nip' => $karyawan->nip ?? '-',
                            'departemen' => $karyawan->departemen ?? '-',
                            'jabatan' => $karyawan->jabatan ?? '-',
                            'total_hadir' => $totalHadir,
                            'total_terlambat' => $totalTerlambat,
                            'hari_kerja' => $hariKerja,
                            'total_hari_bulan' => $totalHariBulan,
                            'persentase' => $persentase,
                            'status' => $this->getStatusPersentaseKafe($persentase),
                            'rata_rata_terlambat' => $totalHadir > 0 ? round($totalTerlambat / $totalHadir * 100, 1) : 0,
                            'alpha' => max(0, $hariKerja - $totalHadir)
                        ]);
                    }

                    // Urutkan berdasarkan persentase tertinggi
                    $persentasePerKaryawan = $persentasePerKaryawan->sortByDesc('persentase');

                    // Hitung rata-rata semua karyawan
                    $totalKaryawanAktif = User::where('role', 'karyawan')->whereNull('tanggal_keluar')->count();
                    if ($totalKaryawanAktif > 0) {
                        $totalAbsensi = Absensi::whereDate('tanggal_absen', '>=', $request->tanggal_dari)
                            ->whereDate('tanggal_absen', '<=', $request->tanggal_sampai)
                            ->count();

                        $totalHarapanAbsensi = $totalKaryawanAktif * $hariKerja;
                        if ($totalHarapanAbsensi > 0) {
                            $persentaseAbsensi = round(($totalAbsensi / $totalHarapanAbsensi) * 100, 2);
                        }
                    }
                }
            } else {
                // Jika tidak ada filter tanggal range yang lengkap
                $periodeLabel = 'Semua Periode';
            }
        }

        // Get list tahun untuk dropdown (dari data absensi yang ada)
        $tahunList = Absensi::selectRaw('YEAR(tanggal_absen) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Tambahkan tahun sekarang jika belum ada
        $currentYear = now()->year;
        if (!$tahunList->contains($currentYear)) {
            $tahunList->prepend($currentYear);
        }

        // Generate range tahun dari 2020 sampai tahun sekarang + 2 tahun ke depan
        $startYear = 2020;
        $endYear = $currentYear + 2;
        $allYears = collect(range($startYear, $endYear))->reverse();

        // Generate bulan list untuk dropdown (format Indonesia)
        $bulanList = $this->getBulanListIndonesia();

        // Get departemen list untuk filter
        $departemenList = User::where('role', 'karyawan')
            ->whereNotNull('departemen')
            ->select('departemen')
            ->distinct()
            ->orderBy('departemen')
            ->pluck('departemen');

        // Statistik tambahan untuk kartu
        $stats = $this->calculateStatsKafe($persentasePerKaryawan, $absensi, $periodeMode);

        return view('admin.laporan-absensi', compact(
            'absensi',
            'karyawanList',
            'persentaseAbsensi',
            'persentasePerKaryawan',
            'hariKerja',
            'periodeLabel',
            'tahunList',
            'allYears',
            'bulanList',
            'bulan',
            'tahun',
            'periodeMode',
            'departemenList',
            'totalKaryawanAktif',
            'totalHariBulan',
            'stats'
        ));
    }

    /**
     * Helper method untuk menentukan status persentase untuk kafe
     * Standar lebih fleksibel karena 30 hari kerja penuh
     */
    private function getStatusPersentaseKafe($persentase)
    {
        if ($persentase >= 90) {
            return 'sangat-baik'; // ≥ 27 hari dari 30
        } elseif ($persentase >= 80) {
            return 'baik'; // 24-26 hari dari 30
        } elseif ($persentase >= 70) {
            return 'cukup'; // 21-23 hari dari 30
        } elseif ($persentase >= 60) {
            return 'kurang'; // 18-20 hari dari 30
        } else {
            return 'sangat-kurang'; // < 18 hari dari 30
        }
    }

    /**
     * Format periode dalam bahasa Indonesia
     */
    private function formatPeriodeIndonesia($tahun, $bulan)
    {
        $namaBulan = $this->getBulanListIndonesia();

        return $namaBulan[$bulan] . ' ' . $tahun;
    }

    /**
     * Daftar bulan dalam bahasa Indonesia
     */
    private function getBulanListIndonesia()
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
    }

    /**
     * Hitung statistik tambahan untuk laporan kafe
     */
    private function calculateStatsKafe($persentasePerKaryawan, $absensi, $periodeMode)
    {
        $stats = [
            'total_karyawan' => $persentasePerKaryawan->count(),
            'rata_persentase' => $persentasePerKaryawan->avg('persentase') ?? 0,
            'total_absensi' => $absensi->total(),
            'top_performers' => $persentasePerKaryawan->where('persentase', '>=', 90)->count(),
            'need_improvement' => $persentasePerKaryawan->where('persentase', '<', 70)->count(),
            'total_terlambat' => $persentasePerKaryawan->sum('total_terlambat'),
            'total_alpha' => $persentasePerKaryawan->sum('alpha'),
            'persentase_terlambat' => $persentasePerKaryawan->sum('total_hadir') > 0
                ? round($persentasePerKaryawan->sum('total_terlambat') / $persentasePerKaryawan->sum('total_hadir') * 100, 1)
                : 0,
            'rata_hadir_per_karyawan' => $persentasePerKaryawan->count() > 0
                ? round($persentasePerKaryawan->sum('total_hadir') / $persentasePerKaryawan->count(), 1)
                : 0,
        ];

        return $stats;
    }

    /**
     * Method untuk statistik bulanan (API)
     */
    public function getMonthlyStats(Request $request)
    {
        $bulan = $request->bulan ?: now()->format('Y-m');
        $yearMonth = explode('-', $bulan);
        $tahun = $yearMonth[0];
        $bulanAngka = $yearMonth[1];

        $stats = [
            'hari_kerja' => 30, // TETAP 30 HARI UNTUK KAFE
            'total_karyawan' => User::where('role', 'karyawan')->whereNull('tanggal_keluar')->count(),
            'rata_persentase' => 0,
            'top_karyawan' => [],
            'need_attention' => []
        ];

        // Hitung rata-rata persentase
        $totalPersentase = 0;
        $karyawanCount = 0;

        $karyawanAktif = User::where('role', 'karyawan')->whereNull('tanggal_keluar')->get();

        foreach ($karyawanAktif as $karyawan) {
            $totalHadir = Absensi::where('id_user', $karyawan->id)
                ->whereYear('tanggal_absen', $tahun)
                ->whereMonth('tanggal_absen', $bulanAngka)
                ->count();

            $persentase = round(($totalHadir / 30) * 100, 2); // 30 hari tetap
            $totalPersentase += $persentase;
            $karyawanCount++;

            // Top karyawan (≥ 90%)
            if ($persentase >= 90) {
                $stats['top_karyawan'][] = [
                    'nama' => $karyawan->nama,
                    'persentase' => $persentase
                ];
            }

            // Perlu perhatian (< 70%)
            if ($persentase < 70) {
                $stats['need_attention'][] = [
                    'nama' => $karyawan->nama,
                    'persentase' => $persentase
                ];
            }
        }

        if ($karyawanCount > 0) {
            $stats['rata_persentase'] = round($totalPersentase / $karyawanCount, 2);
        }

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Export laporan absensi untuk kafe
     */
    public function exportLaporan(Request $request)
    {
        // Set waktu Indonesia
        date_default_timezone_set('Asia/Jakarta');

        $query = Absensi::with(['user', 'shift']);

        // Filter berdasarkan periode
        if ($request->has('bulan') && $request->bulan) {
            $yearMonth = explode('-', $request->bulan);
            $query->whereYear('tanggal_absen', $yearMonth[0])
                  ->whereMonth('tanggal_absen', $yearMonth[1]);
            $periode = $this->formatPeriodeIndonesia($yearMonth[0], (int)$yearMonth[1]);
        } elseif ($request->has('tanggal_dari') && $request->tanggal_dari) {
            $query->whereDate('tanggal_absen', '>=', $request->tanggal_dari);
            if ($request->has('tanggal_sampai') && $request->tanggal_sampai) {
                $query->whereDate('tanggal_absen', '<=', $request->tanggal_sampai);
                $periode = Carbon::parse($request->tanggal_dari)->translatedFormat('d F Y') .
                          ' - ' . Carbon::parse($request->tanggal_sampai)->translatedFormat('d F Y');
            } else {
                $periode = Carbon::parse($request->tanggal_dari)->translatedFormat('d F Y') . ' - Sekarang';
            }
        } else {
            // Default bulan ini
            $query->whereYear('tanggal_absen', now()->year)
                  ->whereMonth('tanggal_absen', now()->month);
            $periode = $this->formatPeriodeIndonesia(now()->year, now()->month);
        }

        if ($request->has('karyawan') && $request->karyawan) {
            $query->where('id_user', $request->karyawan);
        }

        if ($request->has('departemen') && $request->departemen) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('departemen', $request->departemen);
            });
        }

        $absensi = $query->orderBy('tanggal_absen', 'desc')
            ->orderBy('waktu_absen', 'desc')
            ->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=laporan-absensi-" . now()->format('Y-m-d') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($absensi, $periode) {
            $file = fopen('php://output', 'w');

            // Header dengan informasi perusahaan
            fputcsv($file, ['LAPORAN REKAPITULASI ABSENSI KARYAWAN KAFE']);
            fputcsv($file, ['Periode: ' . $periode]);
            fputcsv($file, ['Catatan: Standar kehadiran 30 hari per bulan']);
            fputcsv($file, ['Tanggal Cetak: ' . now()->translatedFormat('l, d F Y H:i:s') . ' WIB']);
            fputcsv($file, ['']); // Blank line

            // Header kolom
            fputcsv($file, [
                'No',
                'Tanggal',
                'Hari',
                'Nama Karyawan',
                'Departemen',
                'Jabatan',
                'Shift',
                'Jam Mulai',
                'Jam Absen',
                'Status',
                'Keterangan'
            ]);

            $counter = 1;
            foreach ($absensi as $item) {
                $jamMulai = Carbon::parse($item->shift->jam_mulai);
                $waktuAbsen = Carbon::parse($item->waktu_absen->format('H:i:s'));
                $selisih = $waktuAbsen->diffInMinutes($jamMulai, false);

                $keterangan = '';
                if ($item->status == 'terlambat') {
                    $keterangan = 'Terlambat ' . abs((int)$selisih) . ' menit';
                } else {
                    if ($selisih <= 0) {
                        $keterangan = 'Tepat waktu';
                    } else {
                        $keterangan = 'Lebih awal ' . (int)$selisih . ' menit';
                    }
                }

                fputcsv($file, [
                    $counter++,
                    $item->tanggal_absen->format('d/m/Y'),
                    $item->tanggal_absen->translatedFormat('l'),
                    $item->user->nama,
                    $item->user->departemen ?? '-',
                    $item->user->jabatan ?? '-',
                    $item->shift->nama_shift,
                    $item->shift->jam_mulai,
                    $item->waktu_absen->format('H:i:s'),
                    $item->status == 'hadir' ? 'Hadir' : 'Terlambat',
                    $keterangan
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export rekap bulanan per karyawan untuk kafe
     */
    public function exportRekapBulanan(Request $request)
    {
        // Set waktu Indonesia
        date_default_timezone_set('Asia/Jakarta');

        $bulan = $request->bulan ?: now()->format('Y-m');
        $yearMonth = explode('-', $bulan);
        $tahun = $yearMonth[0];
        $bulanAngka = (int)$yearMonth[1];

        $hariKerja = 30; // TETAP 30 HARI UNTUK KAFE
        $totalHariBulan = 30;
        $periode = $this->formatPeriodeIndonesia($tahun, $bulanAngka);

        // Hitung data per karyawan
        $karyawanAktif = User::where('role', 'karyawan')
            ->whereNull('tanggal_keluar')
            ->orderBy('departemen')
            ->orderBy('nama')
            ->get();

        $rekapData = [];

        foreach ($karyawanAktif as $karyawan) {
            $totalHadir = Absensi::where('id_user', $karyawan->id)
                ->whereYear('tanggal_absen', $tahun)
                ->whereMonth('tanggal_absen', $bulanAngka)
                ->count();

            $totalTerlambat = Absensi::where('id_user', $karyawan->id)
                ->whereYear('tanggal_absen', $tahun)
                ->whereMonth('tanggal_absen', $bulanAngka)
                ->where('status', 'terlambat')
                ->count();

            $persentase = round(($totalHadir / $hariKerja) * 100, 2);
            $alpha = max(0, $hariKerja - $totalHadir);

            $rekapData[] = [
                'nama' => $karyawan->nama,
                'departemen' => $karyawan->departemen ?? '-',
                'jabatan' => $karyawan->jabatan ?? '-',
                'hari_kerja' => $hariKerja,
                'total_hadir' => $totalHadir,
                'total_terlambat' => $totalTerlambat,
                'alpha' => $alpha,
                'persentase' => $persentase,
                'status' => $this->getStatusLabelKafe($persentase)
            ];
        }

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=rekap-absensi-kafe-" . str_replace(' ', '-', strtolower($periode)) . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($rekapData, $periode) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['REKAPITULASI ABSENSI KARYAWAN KAFE']);
            fputcsv($file, ['Periode: ' . $periode]);
            fputcsv($file, ['Standar: 30 hari kerja per bulan']);
            fputcsv($file, ['Tanggal Cetak: ' . now()->translatedFormat('l, d F Y H:i:s') . ' WIB']);
            fputcsv($file, ['']); // Blank line

            // Header kolom
            fputcsv($file, [
                'No',
                'Nama Karyawan',
                'Departemen',
                'Jabatan',
                'Hari Kerja',
                'Hadir',
                'Terlambat',
                'Alpha',
                'Persentase (%)',
                'Status'
            ]);

            $counter = 1;
            foreach ($rekapData as $data) {
                fputcsv($file, [
                    $counter++,
                    $data['nama'],
                    $data['departemen'],
                    $data['jabatan'],
                    $data['hari_kerja'],
                    $data['total_hadir'],
                    $data['total_terlambat'],
                    $data['alpha'],
                    $data['persentase'] . '%',
                    $data['status']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getStatusLabelKafe($persentase)
    {
        if ($persentase >= 90) return 'Sangat Baik (≥ 27 hari)';
        if ($persentase >= 80) return 'Baik (24-26 hari)';
        if ($persentase >= 70) return 'Cukup (21-23 hari)';
        if ($persentase >= 60) return 'Kurang (18-20 hari)';
        return 'Sangat Kurang (< 18 hari)';
    }

    // Tambahkan method ini di AdminController.php
    public function dashboardStats()
    {
        $totalKaryawan = User::where('role', 'karyawan')->whereNull('tanggal_keluar')->count();

        $totalHadir = Absensi::whereDate('tanggal_absen', today())
            ->where('status', 'hadir')
            ->count();

        $totalTerlambat = Absensi::whereDate('tanggal_absen', today())
            ->where('status', 'terlambat')
            ->count();

        $qrAktif = Barcode::where('status', 'aktif')
            ->where('waktu_akhir', '>', now())
            ->count();

        // Count new absensi in last 5 minutes
        $newAbsensi = Absensi::whereDate('tanggal_absen', today())
            ->where('created_at', '>', now()->subMinutes(5))
            ->count();

        return response()->json([
            'totalKaryawan' => $totalKaryawan,
            'totalHadir' => $totalHadir,
            'totalTerlambat' => $totalTerlambat,
            'qrAktif' => $qrAktif,
            'newAbsensi' => $newAbsensi
        ]);
    }
}
