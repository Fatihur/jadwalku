<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentPortalController;

// Root route - Portal Siswa sebagai halaman utama
Route::get('/', [StudentPortalController::class, 'index'])->name('home');

// Student Portal Routes
Route::name('student.')->group(function () {
    // Public routes (tidak perlu login)
    Route::post('/login', [StudentPortalController::class, 'login'])->name('login');

    // Protected routes (perlu login sebagai siswa)
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/jadwal', [StudentPortalController::class, 'jadwal'])->name('jadwal');
        Route::get('/materi', [StudentPortalController::class, 'materi'])->name('materi');
        Route::get('/materi/{materi}/download/{fileIndex}', [StudentPortalController::class, 'downloadMateri'])->name('download.materi');
        Route::post('/logout', [StudentPortalController::class, 'logout'])->name('logout');
    });
});

// Admin routes tetap menggunakan /admin prefix (handled by Filament)
// Redirect /student ke root untuk backward compatibility
Route::get('/student', function () {
    return redirect('/');
});
