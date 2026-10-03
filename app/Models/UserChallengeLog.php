<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChallengeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_challenge_id',
        'user_id',
        'day_number',
        'log_date',
        'is_completed',
        'xp_earned',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'log_date' => 'date',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function userChallenge()
    {
        return $this->belongsTo(UserChallenge::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
