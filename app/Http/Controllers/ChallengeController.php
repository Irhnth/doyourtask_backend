<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Challenge;
use App\Models\ChallengeDay;
use App\Models\Level;
use App\Models\UserChallenge;
use App\Models\UserChallengeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ChallengeController extends Controller
{
    /**
     * 1. Mengambil daftar program tantangan yang tersedia
     */
    public function index()
    {
        $challenges = Challenge::where('is_active', true)->with('badge')->get();
        return response()->json([
            'status' => 'success',
            'data' => $challenges
        ]);
    }

    /**
     * 2. Mengambil data tantangan aktif milik user yang sedang login
     */
    public function getActiveChallenge()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $userChallenge = UserChallenge::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['challenge.badge', 'logs'])
            ->first();

        // Jika belum ada tantangan aktif, kirimkan template rekomendasi tantangan default
        if (!$userChallenge) {
            $defaultChallenge = Challenge::where('is_active', true)->with('days')->first();

            return response()->json([
                'status' => 'success',
                'has_active_challenge' => false,
                'available_challenge' => $defaultChallenge,
            ]);
        }

        // Ambil semua misi 28 hari untuk challenge ini
        $allDays = ChallengeDay::where('challenge_id', $userChallenge->challenge_id)
            ->orderBy('day_number', 'asc')
            ->get();

        $logsMap = $userChallenge->logs->keyBy('day_number');

        // Hitung hari kalender relatif terhadap start_date
        $startDate = Carbon::parse($userChallenge->start_date);
        $daysSinceStart = (int) $startDate->diffInDays(Carbon::today(), false); // Bisa 0 jika hari pertama
        $calendarDay = max(1, min(28, $daysSinceStart + 1));

        // Format 28 hari untuk UI Flutter
        $formattedDays = $allDays->map(function ($day) use ($logsMap, $calendarDay, $startDate) {
            $dayNum = $day->day_number;
            $log = $logsMap->get($dayNum);
            $isCompleted = $log ? (bool)$log->is_completed : false;

            // Tanggal target untuk hari ini
            $scheduledDate = $startDate->copy()->addDays($dayNum - 1)->toDateString();

            // Status gembok/ketersediaan:
            // Selesai -> Completed
            // Hari ini & belum selesai -> Available / Active
            // Sudah lewat & belum selesai -> Missed (bisa di-catchup atau ditandai)
            // Belum sampai harinya -> Locked
            $status = 'locked';
            if ($isCompleted) {
                $status = 'completed';
            } elseif ($dayNum == $calendarDay) {
                $status = 'today';
            } elseif ($dayNum < $calendarDay) {
                $status = 'missed';
            } else {
                $status = 'locked';
            }

            return [
                'day_number' => $dayNum,
                'week_number' => $day->week_number,
                'title' => $day->title,
                'description' => $day->description,
                'mission_type' => $day->mission_type,
                'target_metric' => $day->target_metric,
                'reward_xp' => $day->reward_xp,
                'milestone_bonus_xp' => $day->milestone_bonus_xp,
                'total_xp' => $day->reward_xp + $day->milestone_bonus_xp,
                'is_milestone' => in_array($dayNum, [7, 14, 21, 28]),
                'is_completed' => $isCompleted,
                'status' => $status,
                'scheduled_date' => $scheduledDate,
                'completed_at' => $log ? $log->completed_at?->toDateTimeString() : null,
                'notes' => $log ? $log->notes : null,
            ];
        });

        // Cek apakah hari ini sudah diselesaikan
        $todayLog = $logsMap->get($calendarDay);
        $isTodayCompleted = $todayLog ? (bool)$todayLog->is_completed : false;

        // Hitung total hari selesai
        $completedDaysCount = $userChallenge->logs->where('is_completed', true)->count();
        $progressPercentage = round(($completedDaysCount / 28) * 100);

        return response()->json([
            'status' => 'success',
            'has_active_challenge' => true,
            'challenge' => [
                'id' => $userChallenge->challenge->id,
                'title' => $userChallenge->challenge->title,
                'description' => $userChallenge->challenge->description,
                'category' => $userChallenge->challenge->category,
                'duration_days' => $userChallenge->challenge->duration_days,
            ],
            'progress' => [
                'user_challenge_id' => $userChallenge->id,
                'start_date' => $userChallenge->start_date->toDateString(),
                'target_end_date' => $userChallenge->target_end_date?->toDateString(),
                'current_day' => $calendarDay,
                'completed_days_count' => $completedDaysCount,
                'progress_percentage' => $progressPercentage,
                'current_streak' => $userChallenge->current_streak,
                'total_xp_earned' => $userChallenge->total_xp_earned,
                'is_today_completed' => $isTodayCompleted,
            ],
            'days' => $formattedDays,
        ]);
    }

    /**
     * 3. Memulai tantangan 28 hari baru
     */
    public function joinChallenge(Request $request, $id)
    {
        $user = Auth::user();
        $challenge = Challenge::findOrFail($id);

        // Periksa apakah sudah ada tantangan aktif
        $existing = UserChallenge::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 'success',
                'message' => 'Anda sudah memiliki tantangan yang sedang aktif.',
                'data' => $existing
            ]);
        }

        $startDate = Carbon::today()->toDateString();
        $targetEndDate = Carbon::today()->addDays($challenge->duration_days - 1)->toDateString();

        $userChallenge = UserChallenge::create([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
            'start_date' => $startDate,
            'target_end_date' => $targetEndDate,
            'current_day' => 1,
            'status' => 'active',
            'current_streak' => 0,
            'total_xp_earned' => 0,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Selamat datang di Tantangan 28 Hari! Langkah awal kebiasaan barumu dimulai hari ini.',
            'data' => $userChallenge
        ], 201);
    }

    /**
     * 4. Selesaikan tantangan harian (Check-in)
     */
    public function completeDay(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $todayStr = $today->toDateString();

        $userChallenge = UserChallenge::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$userChallenge) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada tantangan yang sedang aktif.'
            ], 404);
        }

        $startDate = Carbon::parse($userChallenge->start_date);
        $daysSinceStart = (int) $startDate->diffInDays($today, false);
        $calendarDay = max(1, min(28, $daysSinceStart + 1));

        // Pengguna dapat mengirim day_number khusus jika menyelesaikan hari ini atau hari missed
        $dayNumber = $request->input('day_number', $calendarDay);
        $dayNumber = max(1, min(28, (int)$dayNumber));

        // 1. Validasi: Kunci hari di masa depan agar tidak bisa dicurangi
        if ($dayNumber > $calendarDay) {
            return response()->json([
                'status' => 'error',
                'message' => "Hari ke-$dayNumber belum dibuka! Kamu hanya dapat menyelesaikan misi hari ini (Hari ke-$calendarDay) atau hari sebelumnya yang terlewat.",
            ], 422);
        }

        // Cek apakah hari ini sudah pernah selesai
        $existingLog = UserChallengeLog::where('user_challenge_id', $userChallenge->id)
            ->where('day_number', $dayNumber)
            ->where('is_completed', true)
            ->first();

        if ($existingLog) {
            return response()->json([
                'status' => 'error',
                'message' => "Tantangan Hari ke-$dayNumber sudah pernah kamu selesaikan!",
            ], 400);
        }

        // Ambil data master misi
        $mission = ChallengeDay::where('challenge_id', $userChallenge->challenge_id)
            ->where('day_number', $dayNumber)
            ->first();

        // 2. Verifikasi pemenuhan misi aktual dari aktivitas pengguna
        if ($mission) {
            $scheduledDate = $startDate->copy()->addDays($dayNumber - 1)->toDateString();
            $targetMetric = (int) $mission->target_metric;

            switch ($mission->mission_type) {
                case 'task':
                    // Verifikasi jumlah quest/tugas yang diselesaikan pengguna
                    $completedTasksCount = \App\Models\Task::where('user_id', $user->id)
                        ->where('status', 'completed')
                        ->where(function ($q) use ($todayStr, $scheduledDate) {
                            $hasCompletedAt = \Illuminate\Support\Facades\Schema::hasColumn('tasks', 'completed_at');
                            if ($hasCompletedAt) {
                                $q->whereDate('completed_at', $todayStr)
                                  ->orWhereDate('completed_at', $scheduledDate)
                                  ->orWhereDate('updated_at', $todayStr)
                                  ->orWhereDate('updated_at', $scheduledDate);
                            } else {
                                $q->whereDate('updated_at', $todayStr)
                                  ->orWhereDate('updated_at', $scheduledDate);
                            }
                        })
                        ->count();

                    if ($completedTasksCount < $targetMetric) {
                        return response()->json([
                            'status' => 'error',
                            'message' => "Misi belum terpenuhi! Kamu baru menyelesaikan $completedTasksCount dari target $targetMetric quest. Selesaikan quest di menu Tugas terlebih dahulu.",
                            'current_metric' => $completedTasksCount,
                            'target_metric' => $targetMetric,
                        ], 422);
                    }
                    break;

                case 'health':
                    // Jika target metrik >= 1000, ini adalah misi Langkah Kaki
                    if ($targetMetric >= 1000) {
                        $stepTarget = \App\Models\HealthTarget::where('user_id', $user->id)
                            ->where(function ($q) {
                                $q->where('unit', 'like', '%langkah%')
                                  ->orWhere('title', 'like', '%langkah%');
                            })
                            ->first();

                        $currentSteps = 0;
                        if ($stepTarget) {
                            $healthLog = \App\Models\HealthLog::where('health_target_id', $stepTarget->id)
                                ->where(function ($q) use ($todayStr, $scheduledDate) {
                                    $q->where('date', $todayStr)
                                      ->orWhere('date', $scheduledDate);
                                })
                                ->orderByDesc('current_value')
                                ->first();
                            $currentSteps = $healthLog ? (int)$healthLog->current_value : 0;
                        }

                        if ($currentSteps < $targetMetric) {
                            $formattedSteps = number_format($currentSteps, 0, ',', '.');
                            $formattedTarget = number_format($targetMetric, 0, ',', '.');
                            return response()->json([
                                'status' => 'error',
                                'message' => "Misi belum terpenuhi! Langkah kamu baru tercatat $formattedSteps dari target $formattedTarget langkah hari ini. Buka menu Langkah untuk menyinkronkan!",
                                'current_metric' => $currentSteps,
                                'target_metric' => $targetMetric,
                            ], 422);
                        }
                    } else {
                        // Misi hidrasi air putih atau peregangan fisik
                        $healthSatisfied = \App\Models\HealthTarget::where('user_id', $user->id)
                            ->whereHas('logs', function ($q) use ($targetMetric, $todayStr, $scheduledDate) {
                                $q->where(function ($d) use ($todayStr, $scheduledDate) {
                                    $d->where('date', $todayStr)->orWhere('date', $scheduledDate);
                                })->where('current_value', '>=', $targetMetric);
                            })
                            ->exists();

                        if (!$healthSatisfied) {
                            return response()->json([
                                'status' => 'error',
                                'message' => "Misi belum terpenuhi! Target kesehatan harianmu belum mencapai $targetMetric. Catat progresmu di menu Kesehatan!",
                                'target_metric' => $targetMetric,
                            ], 422);
                        }
                    }
                    break;

                case 'focus':
                    // Verifikasi sesi fokus/Pomodoro
                    $focusSatisfied = \App\Models\HealthTarget::where('user_id', $user->id)
                        ->where(function ($q) {
                            $q->where('type', 'exercise')
                              ->orWhere('type', 'other')
                              ->orWhere('title', 'like', '%pomodoro%')
                              ->orWhere('title', 'like', '%fokus%');
                        })
                        ->whereHas('logs', function ($q) use ($targetMetric, $todayStr, $scheduledDate) {
                            $q->where(function ($d) use ($todayStr, $scheduledDate) {
                                $d->where('date', $todayStr)->orWhere('date', $scheduledDate);
                            })->where('current_value', '>=', $targetMetric);
                        })
                        ->exists();

                    if (!$focusSatisfied) {
                        return response()->json([
                            'status' => 'error',
                            'message' => "Misi belum terpenuhi! Kamu belum menyelesaikan sesi fokus minimal $targetMetric menit hari ini. Mulai sesi di timer Pomodoro!",
                            'target_metric' => $targetMetric,
                        ], 422);
                    }
                    break;

                case 'custom':
                default:
                    // Misi kustom/refleksi: verifikasi selesai melalui check-in dan catatan
                    break;
            }
        }

        $earnedXp = $mission ? ($mission->reward_xp + $mission->milestone_bonus_xp) : 25;

        // Hitung Streak
        $lastCompletedDate = $userChallenge->last_completed_date 
            ? Carbon::parse($userChallenge->last_completed_date) 
            : null;

        $newStreak = $userChallenge->current_streak;
        if (!$lastCompletedDate) {
            $newStreak = 1;
        } elseif ($lastCompletedDate->isYesterday()) {
            $newStreak += 1;
        } elseif ($lastCompletedDate->isToday()) {
            // Sudah check-in hari ini untuk misi lain, streak tetap
        } else {
            // Terlewat lebih dari 1 hari, mulai streak baru
            $newStreak = 1;
        }

        // Simpan log penyelesaian
        $log = UserChallengeLog::create([
            'user_challenge_id' => $userChallenge->id,
            'user_id' => $user->id,
            'day_number' => $dayNumber,
            'log_date' => $todayStr,
            'is_completed' => true,
            'xp_earned' => $earnedXp,
            'notes' => $request->input('notes', ''),
            'completed_at' => now(),
        ]);

        // Update User Challenge
        $userChallenge->current_streak = $newStreak;
        $userChallenge->total_xp_earned += $earnedXp;
        $userChallenge->last_completed_date = $todayStr;

        // Cek apakah semua 28 hari sudah selesai
        $totalCompleted = UserChallengeLog::where('user_challenge_id', $userChallenge->id)
            ->where('is_completed', true)
            ->count();

        $isChallengeCompleted = ($totalCompleted >= 28);
        if ($isChallengeCompleted) {
            $userChallenge->status = 'completed';
            $userChallenge->completed_at = now();
        }
        $userChallenge->save();

        // Tambah XP ke User
        $user->current_xp += $earnedXp;

        // Logika Level Up
        $isLevelUp = false;
        $newLevelName = null;
        $levels = Level::orderBy('xp_required', 'asc')->get();
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

        // Logika Milestone Badges
        $unlockedBadge = null;
        $badgeMap = [
            7 => 'Week 1 Survivor',
            14 => 'Halfway Hero',
            21 => 'Discipline Master',
            28 => '28-Day Champion',
        ];

        if (isset($badgeMap[$dayNumber])) {
            $badgeTitle = $badgeMap[$dayNumber];
            $badge = Badge::where('badge_name', $badgeTitle)->first();
            if ($badge && !$user->badges->contains($badge->id)) {
                $user->badges()->attach($badge->id, ['achieved_at' => now()]);
                $unlockedBadge = [
                    'id' => $badge->id,
                    'name' => $badge->badge_name,
                    'icon' => $badge->image_icon,
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "Hebat! Misi Hari ke-$dayNumber Selesai! Kamu mendapatkan +$earnedXp XP.",
            'data' => [
                'day_number' => $dayNumber,
                'earned_xp' => $earnedXp,
                'new_total_xp' => $user->current_xp,
                'current_streak' => $newStreak,
                'is_level_up' => $isLevelUp,
                'new_level' => $newLevelName,
                'unlocked_badge' => $unlockedBadge,
                'is_challenge_completed' => $isChallengeCompleted,
            ]
        ]);
    }

    /**
     * 5. Membatalkan / Reset tantangan aktif untuk mengulang dari Hari 1
     */
    public function abandonChallenge()
    {
        $user = Auth::user();
        $userChallenge = UserChallenge::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($userChallenge) {
            $userChallenge->update(['status' => 'abandoned']);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Tantangan sebelumnya telah direset. Kamu bisa memulai tantangan baru kapan saja!'
        ]);
    }
}
