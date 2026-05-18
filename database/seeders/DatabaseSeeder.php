<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;
use App\Models\Badge;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Membuat Data Master Level
        $levels = [
            ['level_number' => 1, 'level_name' => 'Novice', 'xp_required' => 0],
            ['level_number' => 2, 'level_name' => 'Apprentice', 'xp_required' => 100],
            ['level_number' => 3, 'level_name' => 'Warrior', 'xp_required' => 300],
            ['level_number' => 4, 'level_name' => 'Elite', 'xp_required' => 600],
            ['level_number' => 5, 'level_name' => 'Master', 'xp_required' => 1000],
        ];

        foreach ($levels as $level) {
            Level::create($level);
        }

        // 2. Membuat Data Master Badge (Lencana)
        $badges = [
            [
                'badge_name' => 'First Blood', 
                'requirement_count' => 1, 
                'image_icon' => 'first_blood.png'
            ],
            [
                'badge_name' => 'Consistent 5', 
                'requirement_count' => 5, 
                'image_icon' => 'consistent_5.png'
            ],
            [
                'badge_name' => 'Productivity Machine', 
                'requirement_count' => 10, 
                'image_icon' => 'machine_10.png'
            ],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }

        echo "Data Level dan Badge berhasil di-generate!\n";
    }
}