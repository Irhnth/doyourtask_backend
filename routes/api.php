<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

// ========================================================
// TAMBAHAN: Tangkap error jika user tidak bawa token valid
// ========================================================
Route::get('/login', function () {
    return response()->json([
        'message' => 'Unauthenticated. Sesi Anda habis atau belum login.'
    ], 401);
})->name('login');

// Rute Publik (Tidak butuh Token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rute Terlindungi (Wajib pakai Token dari Login)
Route::middleware('auth:sanctum')->group(function () {
    
    // Rute untuk mendapatkan data user yang sedang login
    Route::get('/user', function (Request $request) {
        return $request->user()->load('level', 'badges');
    });

    // Rute CRUD Tugas
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    
    // Rute Eksekusi Gamifikasi
    Route::post('/tasks/{id}/complete', [TaskController::class, 'completeTask']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);

    // BARU: Jalur untuk mengambil data papan peringkat
    Route::get('/leaderboard', [App\Http\Controllers\TaskController::class, 'leaderboard']);
});
