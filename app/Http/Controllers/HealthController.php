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

        // Cek apakah target langkah sudah ada, jika belum otomatis buatkan default 6.000 langkah
        $hasStepTarget = $targets->contains(function ($t) {
            $unit = strtolower($t->unit ?? '');
            $title = strtolower($t->title ?? '');
            return $unit === 'langkah' || str_contains($title, 'langkah');
        });

        if (!$hasStepTarget) {
            $newTarget = HealthTarget::create([
                'user_id' => $user->id,
                'title' => 'Langkah Kaki',
                'type' => 'other',
                'target_value' => 6000,
                'unit' => 'Langkah',
            ]);
            $newTarget->setRelation('logs', collect());
            $targets->push($newTarget);
        }

        // Format data agar mudah dibaca oleh Flutter
        $data = $targets->map(function ($target) use ($today, $user) {
            $log = $target->logs->first();
            
            return [
                'target_id' => $target->id,
                'title' => $target->title,
                'type' => $target->type,
                'target_value' => $target->target_value,
                'unit' => $target->unit,
                'current_value' => $log ? $log->current_value : 0,
                'is_completed' => $log ? (bool)$log->is_completed : false,
                'last_milestone' => $log ? (int)($log->last_milestone ?? 0) : 0,
                'total_xp' => $log ? (int)($log->earned_xp ?? 0) : 0,
                'max_xp' => 50,
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

    // 3. Memperbarui progres harian manual (Inkremental biasa)
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
            ['current_value' => 0, 'is_completed' => false, 'last_milestone' => 0, 'earned_xp' => 0]
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
            $defaultValues = [
                'drink' => 8,
                'exercise' => 30,
                'food' => 3,
                'other' => 1
            ];
            $defaultValue = $defaultValues[$target->type] ?? 1;
            $diff = $target->target_value - $defaultValue;

            $earnedXp = 50 + ($diff * 10);
            if ($earnedXp < 10) {
                $earnedXp = 10; 
            }

            $user->current_xp += $earnedXp;

            // Cek Naik Level
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

        $log->update([
            'current_value' => $newValue,
            'is_completed' => $isCompleted,
            'earned_xp' => ($log->earned_xp ?? 0) + $earnedXp
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

    // 4. Memperbarui progres langkah (Step Counter dengan Milestone XP Maksimal 50 XP)
    public function updateStepProgress(Request $request, $targetId)
    {
        $request->validate([
            'current_steps' => 'required|integer|min:0'
        ]);

        $user = Auth::user();
        $target = HealthTarget::where('user_id', $user->id)->findOrFail($targetId);
        $today = Carbon::today()->toDateString();

        $log = HealthLog::firstOrCreate(
            ['user_id' => $user->id, 'health_target_id' => $target->id, 'date' => $today],
            ['current_value' => 0, 'is_completed' => false, 'last_milestone' => 0, 'earned_xp' => 0]
        );

        $targetValue = max(1, (int)$target->target_value);
        $currentSteps = max(0, (int)$request->input('current_steps'));
        $percentage = (int)floor(($currentSteps / $targetValue) * 100);

        // Aturan Milestone XP:
        // 20%  -> +5 XP
        // 40%  -> +5 XP
        // 60%  -> +10 XP
        // 80%  -> +10 XP
        // 100% -> +20 XP
        // Total maksimal: 50 XP
        $milestones = [
            20 => 5,
            40 => 5,
            60 => 10,
            80 => 10,
            100 => 20,
        ];

        $lastMilestone = (int)($log->last_milestone ?? 0);
        $currentTotalXp = (int)($log->earned_xp ?? 0);
        $earnedXp = 0;
        $newLastMilestone = $lastMilestone;
        $milestoneReached = false;

        if ($currentTotalXp < 50) {
            foreach ($milestones as $ms => $xpReward) {
                if ($percentage >= $ms && $ms > $lastMilestone) {
                    $remainingQuota = 50 - ($currentTotalXp + $earnedXp);
                    $reward = min($xpReward, $remainingQuota);
                    if ($reward > 0) {
                        $earnedXp += $reward;
                        $newLastMilestone = $ms;
                        $milestoneReached = true;
                    }
                }
            }
        }

        $newTotalXp = min(50, $currentTotalXp + $earnedXp);
        $isCompleted = ($percentage >= 100) || (bool)$log->is_completed;

        // Tambahkan XP ke pengguna jika ada XP baru
        $isLevelUp = false;
        $newLevelName = null;

        if ($earnedXp > 0) {
            $user->current_xp += $earnedXp;

            // Cek Naik Level
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

        // Perbarui log kesehatan
        $log->current_value = $currentSteps;
        $log->is_completed = $isCompleted;
        $log->last_milestone = max($lastMilestone, $newLastMilestone);
        $log->earned_xp = $newTotalXp;
        $log->save();

        return response()->json([
            'status' => 'success',
            'success' => true,
            'message' => 'Progres langkah berhasil disinkronkan',
            'data' => [
                'current_value' => $currentSteps,
                'target_value' => $targetValue,
                'progress_percentage' => min(100, $percentage),
                'milestone' => $log->last_milestone,
                'milestone_reached' => $milestoneReached,
                'earned_xp' => $earnedXp,
                'total_xp' => $newTotalXp,
                'max_xp' => 50,
                'is_completed' => $isCompleted,
                'is_level_up' => $isLevelUp,
                'new_level' => $newLevelName,
            ],
            // Kompatibilitas level atas
            'current_value' => $currentSteps,
            'target_value' => $targetValue,
            'progress_percentage' => min(100, $percentage),
            'milestone' => $log->last_milestone,
            'milestone_reached' => $milestoneReached,
            'earned_xp' => $earnedXp,
            'total_xp' => $newTotalXp,
            'max_xp' => 50,
            'is_completed' => $isCompleted,
            'is_level_up' => $isLevelUp,
            'new_level' => $newLevelName,
        ]);
    }

    // 5. Memperbarui informasi target kesehatan (Edit Target)
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