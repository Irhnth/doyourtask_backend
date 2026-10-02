<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'health_target_id',
        'date',
        'current_value',
        'is_completed',
        'last_milestone',
        'earned_xp',
    ];

    protected $casts = [
        'date' => 'date',
        'is_completed' => 'boolean',
        'last_milestone' => 'integer',
        'earned_xp' => 'integer',
    ];

    // Relasi: Log ini milik siapa?
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Log ini untuk target yang mana?
    public function target(): BelongsTo
    {
        return $this->belongsTo(HealthTarget::class, 'health_target_id');
    }
}