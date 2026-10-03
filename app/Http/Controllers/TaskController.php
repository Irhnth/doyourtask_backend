<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Level;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

        // A. Ubah status tugas menjadi selesai & catat waktu penyelesaian aktual
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

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

    // ==========================================
    // 7. FUNGSI ANALISIS PERILAKU PENUNDAAN
    // ==========================================
    public function procrastinationAnalysis(Request $request)
    {
        $user = $request->user();
        $tasks = Task::where('user_id', $user->id)->get();

        $now = now();
        $onTime = 0;
        $lastMinute = 0;
        $lateCompleted = 0;
        $overduePending = 0;

        foreach ($tasks as $task) {
            if (!$task->deadline) continue;
            $deadline = Carbon::parse($task->deadline);

            if ($task->status === 'completed') {
                // Gunakan completed_at aktual, fallback ke updated_at jika completed_at null
                $completedAt = $task->completed_at 
                    ? Carbon::parse($task->completed_at) 
                    : ($task->updated_at ? Carbon::parse($task->updated_at) : $deadline);

                if ($completedAt->gt($deadline)) {
                    $lateCompleted++;
                } else {
                    $diffInHours = $completedAt->diffInHours($deadline, false);
                    if ($diffInHours < 3) {
                        $lastMinute++;
                    } else {
                        $onTime++;
                    }
                }
            } else {
                if ($now->gt($deadline)) {
                    $overduePending++;
                }
            }
        }

        $totalEvaluated = $onTime + $lastMinute + $lateCompleted + overduePending;
        $score = 0;
        if ($totalEvaluated > 0) {
            $rawScore = (($onTime * 1.0 + $lastMinute * 0.5) / $totalEvaluated) * 100;
            $score = (int) max(0, min(100, round($rawScore)));
        }

        if ($totalEvaluated === 0) {
            $archetype = 'Belum Cukup Data';
            $description = 'Selesaikan beberapa quest untuk mulai melihat pola manajemen waktumu.';
            $color = '#8F9BB3';
            $tips = [
                'Tetapkan tenggat waktu yang realistis pada setiap quest baru.',
                'Selesaikan tugas lebih awal untuk membangun ritme kerja yang tenang.',
                'Manfaatkan timer Pomodoro di menu Kesehatan untuk melatih fokus intensif.',
            ];
        } elseif ($score >= 80 && $overduePending === 0) {
            $archetype = 'Eksekutor Proaktif';
            $description = 'Luar biasa! Kamu konsisten menuntaskan quest jauh sebelum batas waktu tanpa menunda.';
            $color = '#00E096';
            $tips = [
                'Pertahankan kebiasaan baik dengan terus memecah quest besar menjadi langkah kecil.',
                'Berikan waktu istirahat yang cukup di sela-sela pencapaian tugasmu agar tidak burnout.',
                'Tantang dirimu dengan quest baru yang lebih menantang untuk memaksimalkan perolehan XP.',
            ];
        } elseif ($score >= 50 || ($lastMinute > $onTime && $overduePending <= 1)) {
            $archetype = 'Pejuang Deadline';
            $description = 'Kamu sering menyelesaikan quest mepet menit-menit akhir menjelang batas waktu.';
            $color = '#FFAA00';
            $tips = [
                'Terapkan "Aturan 5 Menit": paksa dirimu memulai tugas selama 5 menit tanpa distraksi untuk mengatasi rasa malas awal.',
                'Gunakan timer Pomodoro (25 menit kerja, 5 menit istirahat) untuk mencegah stres di akhir.',
                'Buat target selesai pribadi 3-6 jam sebelum tenggat waktu sebenarnya.',
            ];
        } else {
            $archetype = 'Kerap Menunda';
            $description = 'Terdapat beberapa quest yang terlambat atau melewati tenggat waktu. Yuk atur ulang fokusmu!';
            $color = '#FF3D71';
            $tips = [
                'Pilih satu tugas paling kecil dan selesaikan pagi ini juga (konsep Quick Win).',
                'Pecah tugas besar menjadi sub-tugas berdurasi 15-20 menit agar tidak terasa membebani mental.',
                'Singkirkan notifikasi ponsel dan buka mode fokus saat mengerjakan tugas.',
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'on_time_count' => $onTime,
                'last_minute_count' => $lastMinute,
                'late_completed_count' => $lateCompleted,
                'overdue_pending_count' => $overduePending,
                'total_evaluated' => $totalEvaluated,
                'score' => $score,
                'archetype_title' => $archetype,
                'archetype_desc' => $description,
                'archetype_color' => $color,
                'tips' => $tips,
            ]
        ], 200);
    }
}