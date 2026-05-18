<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi Many-to-Many ke tabel users melalui user_badges
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
                    ->withPivot('achieved_at')
                    ->withTimestamps();
    }
}