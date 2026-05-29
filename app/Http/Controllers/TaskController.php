<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Level;
use App\Models\Badge;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // 1. Mengambil semua tugas milik user yang sedang login
    public function index(Request $request)
    {
        $tasks = Task::where('user_id', $request->user()->id)
                     ->orderBy('deadline', 'asc')
                     ->get();
                     
        return response()->json(['tasks' => $tasks], 200);
    }

    // 2. Menyimpan tugas baru
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'deadline' => 'required|date'
        ]);

        $task = Task::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'reward_xp' => 50 // Nilai default XP setiap tugas
        ]);

        return response()->json(['message' => 'Tugas berhasil dibuat', 'task' => $task], 201);
    }

    // 3. LOGIKA UTAMA: Menyelesaikan tugas & Proses Gamifikasi
    public function completeTask(Request $request, $id)
    {
        $user = $request->user();
        $task = Task::where('id', $id)->where('user_id', $user->id)->first();

        if (!$task) {
            return response()->json(['message' => 'Tugas tidak ditemukan'], 404);
        }
        if ($task->status === 'completed') {
            return response()->json(['message' => 'Tugas ini sudah diselesaikan sebelumnya'], 400);
        }

        // A. Ubah status tugas menjadi selesai
        $task->update(['status' => 'completed']);

        // B. Tambahkan XP ke User
        $user->current_xp += $task->reward_xp;
        
        // Variabel untuk melacak perubahan (untuk memicu animasi di Flutter nanti)
        $isLevelUp = false;
        $newBadges = [];

        // C. Logika Naik Level (Level Up)
        $currentLevel = $user->level;
        if ($currentLevel) {
            // Cari level selanjutnya
            $nextLevel = Level::where('level_number', '>', $currentLevel->level_number)
                              ->orderBy('level_number', 'asc')
                              ->first();
                              
            // Jika ada level selanjutnya dan XP user sudah mencukupi
            if ($nextLevel && $user->current_xp >= $nextLevel->xp_required) {
                $user->level_id = $nextLevel->id;
                $isLevelUp = true;
            }
        }

        // D. Logika Pencapaian Lencana (Badges)
        $completedTasksCount = Task::where('user_id', $user->id)
                                   ->where('status', 'completed')
                                   ->count();
        
        // Cari badge yang syarat jumlah tugasnya terpenuhi
        $eligibleBadges = Badge::where('requirement_count', '<=', $completedTasksCount)->get();
        
        foreach ($eligibleBadges as $badge) {
            // Cek apakah user belum memiliki badge ini di tabel user_badges
            if (!$user->badges->contains($badge->id)) {
                $user->badges()->attach($badge->id);
                $newBadges[] = $badge->badge_name;
            }
        }

        // Simpan perubahan data User (XP dan Level)
        $user->save();

        return response()->json([
            'message' => 'Tugas diselesaikan! Anda mendapatkan ' . $task->reward_xp . ' XP.',
            'new_total_xp' => $user->current_xp,
            'is_level_up' => $isLevelUp,
            'new_level' => $isLevelUp ? $user->level->level_name : $currentLevel->level_name,
            'new_badges_unlocked' => $newBadges
        ], 200);
    }

    // ==========================================
    // 4. FUNGSI EDIT TUGAS (UPDATE)
    // ==========================================
    public function update(Request $request, $id)
    {
        // Cari tugas berdasarkan ID dan pastikan itu milik user yang sedang login
        $task = Task::where('user_id', $request->user()->id)->findOrFail($id);
        
        // Update data
        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
        ]);

        return response()->json([
            'message' => 'Quest berhasil diperbarui!',
            'task' => $task
        ], 200);
    }

    // ==========================================
    // 5. FUNGSI HAPUS TUGAS (DELETE)
    // ==========================================
    public function destroy(Request $request, $id)
    {
        // Cari tugas dan pastikan itu milik user yang sedang login
        $task = Task::where('user_id', $request->user()->id)->findOrFail($id);
        
        // Hapus tugas dari database
        $task->delete();

        return response()->json([
            'message' => 'Quest berhasil dihapus!'
        ], 200);
    }
<<<<<<< HEAD
    
    // ==========================================
    // 6. FUNGSI PAPAN PERINGKAT (LEADERBOARD)
    // ==========================================
    public function leaderboard(Request $request)
    {
        // Ambil 10 user dengan XP terbanyak, urutkan dari yang terbesar
        $users = \App\Models\User::with('level')
                    ->orderBy('current_xp', 'desc')
                    ->take(10)
                    ->get();

        return response()->json([
            'leaderboard' => $users
        ], 200);
    }
=======
>>>>>>> 8d13f21794e470fcfd26f6ee165fae9db6dedf07
}