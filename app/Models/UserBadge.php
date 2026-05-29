<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

// Meng-extend Pivot, bukan Model biasa
class UserBadge extends Pivot
{
    protected $table = 'user_badges';

    protected $guarded = ['id'];

    protected $casts = [
        'achieved_at' => 'datetime',
    ];
}