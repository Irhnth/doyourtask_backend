<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengeDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'challenge_id',
        'day_number',
        'week_number',
        'title',
        'description',
        'mission_type',
        'target_metric',
        'reward_xp',
        'milestone_bonus_xp',
    ];

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }
}
