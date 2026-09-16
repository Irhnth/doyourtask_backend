<?php

namespace App\Http\Controllers;

use App\Models\HealthTarget;
use App\Models\HealthLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class HealthController extends Controller
{
    // 1. Mengambil semua target beserta progres HARI INI
    public function getTodayProgress()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Ambil target dan sertakan log khusus hari ini
        $targets = HealthTarget::where('user_id', $user->id)
            ->with(['logs' => function ($query) use ($today) {
                $query->where('date', $today);
            }])
            ->get();

        // Format data agar mudah dibaca oleh Flutter
        $data = $targets->map(function ($target) use ($today, $user) {
            // Jika belum ada log hari ini, buat nilai default 0
            $log = $target->logs->first();
            
            return [
                'target_id' => $target->id,
                'title' => $target->title,
                'type' => $target->type,
                'target_value' => $target->target_value,
                'unit' => $target->unit,
                'current_value' => $log ? $log->current_value : 0,
                'is_completed' => $log ? $log->is_completed : false,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'date' => $today
        ]);
    }

    // 2. Membuat target kesehatan baru
    public function storeTarget(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'type' => 'required|in:food,drink,exercise,other',
            'target_value' => 'required|integer|min:1',
            'unit' => 'nullable|string'
        ]);

        $target = HealthTarget::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'type' => $request->type,
            'target_value' => $request->target_value,
            'unit' => $request->unit,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Target berhasil dibuat',
            'data' => $target
        ]);
    }

    // Memperbarui progres harian dan memberikan EXP
    public function updateProgress(Request $request, $targetId)
    {
        $request->validate([
            'increment_value' => 'required|integer|min:1'
        ]);

        $user = Auth::user();
        $target = HealthTarget::where('user_id', $user->id)->findOrFail($targetId);
        $today = Carbon::today()->toDateString();

        $log = HealthLog::firstOrCreate(
            ['user_id' => $user->id, 'health_target_id' => $target->id, 'date' => $today],
            ['current_value' => 0, 'is_completed' => false]
        );

        // Jika sudah selesai sebelumnya hari ini, jangan proses lagi
        if ($log->is_completed) {
            return response()->json([
                'status' => 'success',
                'message' => 'Sudah selesai hari ini',
                'data' => $log,
                'earned_xp' => 0
            ]);
        }

        $newValue = $log->current_value + $request->increment_value;
        $isCompleted = $newValue >= $target->target_value;

        $earnedXp = 0;
        $isLevelUp = false;
        $newLevelName = null;

        // JIKA TARGET TERCAPAI HARI INI
        if ($isCompleted) {
            // 1. Tentukan nilai default untuk referensi
            $defaultValues = [
                'drink' => 8,
                'exercise' => 30,
                'food' => 3,
                'other' => 1
            ];
            $defaultValue = $defaultValues[$target->type] ?? 1;

            // 2. Hitung selisih dari default
            $diff = $target->target_value - $defaultValue;

            // 3. Kalkulasi XP: Base 50 + (Selisih * 10)
            $earnedXp = 50 + ($diff * 10);
            
            // Jangan sampai XP minus jika user menurunkan target terlalu jauh
            if ($earnedXp < 10) {
                $earnedXp = 10; 
            }

            // 4. Tambahkan XP ke pengguna
            $user->current_xp += $earnedXp;

           // 5. Cek Naik Level
            $levels = \App\Models\Level::orderBy('xp_required', 'asc')->get();
            foreach ($levels as $level) {
                if ($user->current_xp >= $level->xp_required) {
                    if ($user->level_id !== $level->id) {
                        $user->level_id = $level->id;
                        $isLevelUp = true;
                        $newLevelName = $level->level_name;
                    }
                }
            }
            $user->save();
        }

        // Simpan pembaruan progres
        $log->update([
            'current_value' => $newValue,
            'is_completed' => $isCompleted
        ]);

        return response()->json([
            'status' => 'success',
            'message' => $isCompleted ? 'Target Selesai! +'.$earnedXp.' XP' : 'Progres diperbarui',
            'data' => $log,
            'earned_xp' => $earnedXp,
            'is_level_up' => $isLevelUp,
            'new_level' => $newLevelName
        ]);
    }
    // Memperbarui informasi target kesehatan (Edit Target)
    public function updateTarget(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'target_value' => 'required|integer|min:1',
            'unit' => 'nullable|string|max:50'
        ]);

        $target = HealthTarget::where('user_id', Auth::id())->findOrFail($id);
        
        $target->update([
            'title' => $request->title,
            'target_value' => $request->target_value,
            'unit' => $request->unit,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Target berhasil diperbarui',
            'data' => $target
        ]);
    }
}