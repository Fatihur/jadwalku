<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Materi;
use Carbon\Carbon;

class StudentPortalController extends Controller
{
    public function index()
    {
        return view('student.landing');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            // Cek apakah user adalah siswa
            if ($user->hasRole('siswa')) {
                return redirect()->route('student.dashboard');
            } else {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun ini bukan akun siswa.']);
            }
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    public function dashboard()
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole('siswa')) {
            return redirect()->route('home');
        }

        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            Auth::logout();
            return redirect()->route('home')->withErrors(['error' => 'Data siswa tidak ditemukan.']);
        }

        // Get jadwal hari ini
        $today = Carbon::now()->locale('id');
        $hariIni = strtolower($today->dayName);

        $jadwalHariIni = Jadwal::with(['mataPelajaran', 'guru', 'ruangan'])
            ->where('kelas_id', $siswa->kelas_id)
            ->where('hari', $hariIni)
            ->where('is_active', true)
            ->orderBy('jam_mulai')
            ->get();

        // Get jadwal minggu ini
        $jadwalMingguIni = Jadwal::with(['mataPelajaran', 'guru', 'ruangan'])
            ->where('kelas_id', $siswa->kelas_id)
            ->where('is_active', true)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        // Get materi terbaru untuk kelas siswa
        $materiTerbaru = Materi::with(['guru', 'mataPelajaran'])
            ->where('kelas_id', $siswa->kelas_id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('student.dashboard', compact('siswa', 'jadwalHariIni', 'jadwalMingguIni', 'materiTerbaru', 'hariIni'));
    }

    public function jadwal()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return redirect()->route('student.dashboard');
        }

        $jadwalMingguIni = Jadwal::with(['mataPelajaran', 'guru', 'ruangan'])
            ->where('kelas_id', $siswa->kelas_id)
            ->where('is_active', true)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        return view('student.jadwal', compact('siswa', 'jadwalMingguIni'));
    }

    public function materi()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return redirect()->route('student.dashboard');
        }

        $materiList = Materi::with(['guru', 'mataPelajaran'])
            ->where('kelas_id', $siswa->kelas_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.materi', compact('siswa', 'materiList'));
    }

    public function downloadMateri($materiId, $fileIndex)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            abort(403, 'Akses ditolak');
        }

        // Get materi dan pastikan siswa bisa mengakses (sesuai kelas)
        $materi = Materi::with(['guru', 'mataPelajaran', 'kelas'])
            ->where('id', $materiId)
            ->where('kelas_id', $siswa->kelas_id)
            ->where('is_published', true)
            ->firstOrFail();

        // Check if files exist
        if (!$materi->files || !is_array($materi->files)) {
            abort(404, 'File tidak ditemukan');
        }

        // Validate file index
        if (!isset($materi->files[$fileIndex])) {
            abort(404, 'File tidak ditemukan');
        }

        $filePath = $materi->files[$fileIndex];
        $fullPath = storage_path('app/public/' . $filePath);

        // Check if file exists on disk
        if (!file_exists($fullPath)) {
            abort(404, 'File tidak ditemukan di server');
        }

        // Get original filename
        $fileName = basename($filePath);

        // Log download activity (optional)
        Log::info('File downloaded', [
            'user_id' => $user->id,
            'siswa_id' => $siswa->id,
            'materi_id' => $materiId,
            'file_index' => $fileIndex,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'downloaded_at' => now()
        ]);

        // Return file download response
        return response()->download($fullPath, $fileName);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }
}
