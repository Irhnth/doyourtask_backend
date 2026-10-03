<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'duration_days',
        'total_reward_xp',
        'badge_id',
        'is_active',
    ];

    public function days()
    {
        return $this->hasMany(ChallengeDay::class)->orderBy('day_number', 'asc');
    }

    public function userChallenges()
    {
        return $this->hasMany(UserChallenge::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }
}
