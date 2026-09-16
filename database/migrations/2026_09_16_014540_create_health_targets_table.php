<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_targets', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel users
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); 
            
            $table->string('title'); // Contoh: 'Minum Air Putih', 'Lari Pagi'
            $table->enum('type', ['food', 'drink', 'exercise', 'other'])->default('other');
            $table->integer('target_value'); // Contoh: 8, 30
            $table->string('unit')->nullable(); // Contoh: 'Gelas', 'Menit', 'Porsi'
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_targets');
    }
};
