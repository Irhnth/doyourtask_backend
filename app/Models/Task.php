<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Casting agar tipe data waktu (datetime) dibaca dengan benar
    protected $casts = [
        'deadline' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relasi ke tabel users
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}