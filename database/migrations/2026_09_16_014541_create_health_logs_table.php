<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_logs', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel users dan health_targets
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('health_target_id')->constrained('health_targets')->cascadeOnDelete();
            
            $table->date('date'); // Tanggal pencatatan
            $table->integer('current_value')->default(0); // Progres saat ini
            $table->boolean('is_completed')->default(false); // Status selesai atau belum
            
            $table->timestamps();

            // Memastikan satu target hanya memiliki satu log per harinya
            $table->unique(['health_target_id', 'date']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_logs');
    }
};