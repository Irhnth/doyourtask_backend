<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChallenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'challenge_id',
        'start_date',
        'target_end_date',
        'current_day',
        'status',
        'current_streak',
        'total_xp_earned',
        'last_completed_date',
        'completed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'target_end_date' => 'date',
        'last_completed_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    public function logs()
    {
        return $this->hasMany(UserChallengeLog::class)->orderBy('day_number', 'asc');
    }
}
