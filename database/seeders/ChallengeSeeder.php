<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Badge;
use App\Models\Challenge;
use App\Models\ChallengeDay;

class ChallengeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Lencana Milestone jika belum ada
        $badgesData = [
            ['badge_name' => 'Week 1 Survivor', 'requirement_count' => 7, 'image_icon' => 'badge_week1.png'],
            ['badge_name' => 'Halfway Hero', 'requirement_count' => 14, 'image_icon' => 'badge_week2.png'],
            ['badge_name' => 'Discipline Master', 'requirement_count' => 21, 'image_icon' => 'badge_week3.png'],
            ['badge_name' => '28-Day Champion', 'requirement_count' => 28, 'image_icon' => 'badge_week4.png'],
        ];

        $createdBadges = [];
        foreach ($badgesData as $b) {
            $badge = Badge::firstOrCreate(
                ['badge_name' => $b['badge_name']],
                ['requirement_count' => $b['requirement_count'], 'image_icon' => $b['image_icon']]
            );
            $createdBadges[$b['badge_name']] = $badge;
        }

        // 2. Buat Master Program Tantangan 28 Hari
        $grandChampionBadge = $createdBadges['28-Day Champion'] ?? null;
        $challenge = Challenge::firstOrCreate(
            ['title' => 'Tantangan 28 Hari: Bangun Kebiasaan Produktif & Sehat'],
            [
                'description' => 'Program 4 minggu terstruktur untuk melatih konsistensi, menaklukkan penundaan, serta menyelaraskan produktivitas harian dengan kesehatan fisik dan mental.',
                'category' => 'habit',
                'duration_days' => 28,
                'total_reward_xp' => 1400,
                'badge_id' => $grandChampionBadge ? $grandChampionBadge->id : null,
                'is_active' => true,
            ]
        );

        // 3. Buat 28 Hari Misi
        $missions = [
            // ================= MINGGU 1: INISIASI & PONDASE =================
            [
                'day' => 1, 'week' => 1,
                'title' => 'Evaluasi & Rencana Prioritas',
                'desc' => 'Tuliskan minimal 2 target atau quest tugas penting yang ingin kamu selesaikan dalam minggu ini.',
                'type' => 'task', 'target' => 2, 'xp' => 25, 'bonus' => 0,
            ],
            [
                'day' => 2, 'week' => 1,
                'title' => 'Hidrasi Optimal',
                'desc' => 'Mulai kebiasaan minum air putih secara teratur, penuhi minimal 6-8 gelas hari ini.',
                'type' => 'health', 'target' => 6, 'xp' => 25, 'bonus' => 0,
            ],
            [
                'day' => 3, 'week' => 1,
                'title' => 'Sesi Fokus Pomodoro',
                'desc' => 'Selesaikan minimal 1 sesi Pomodoro (25 menit) penuh fokus tanpa membuka media sosial.',
                'type' => 'focus', 'target' => 25, 'xp' => 25, 'bonus' => 0,
            ],
            [
                'day' => 4, 'week' => 1,
                'title' => 'Aktif Melangkah',
                'desc' => 'Gerakkan tubuhmu! Capai minimal 3.000 langkah kaki sepanjang hari ini.',
                'type' => 'health', 'target' => 3000, 'xp' => 25, 'bonus' => 0,
            ],
            [
                'day' => 5, 'week' => 1,
                'title' => 'Taklukkan Tugas Pertama',
                'desc' => 'Pilih 1 tugas yang paling sering kamu tunda dan selesaikan sebelum sore hari.',
                'type' => 'task', 'target' => 1, 'xp' => 25, 'bonus' => 0,
            ],
            [
                'day' => 6, 'week' => 1,
                'title' => 'Kerapian Ruang Kerja',
                'desc' => 'Rapikan meja belajar/kerjamu dan periksa daftar tugas yang masih pending.',
                'type' => 'custom', 'target' => 1, 'xp' => 25, 'bonus' => 0,
            ],
            [
                'day' => 7, 'week' => 1,
                'title' => 'Milestone Pekan 1: Evaluasi & Syukuri',
                'desc' => 'Selamat menuntaskan minggu pertama! Evaluasi pencapaianmu dan klaim Lencana Week 1 Survivor.',
                'type' => 'custom', 'target' => 1, 'xp' => 50, 'bonus' => 50,
            ],

            // ================= MINGGU 2: MEMBANGUN RITME & KETAHANAN =================
            [
                'day' => 8, 'week' => 2,
                'title' => 'Ritme Baru Dimulai',
                'desc' => 'Awali minggu kedua dengan menuntaskan 2 quest penting di aplikasi DoYourTask.',
                'type' => 'task', 'target' => 2, 'xp' => 30, 'bonus' => 0,
            ],
            [
                'day' => 9, 'week' => 2,
                'title' => 'Fokus Ganda (50 Menit)',
                'desc' => 'Tuntaskan 2 putaran sesi fokus Pomodoro untuk pekerjaan yang menuntut konsentrasi tinggi.',
                'type' => 'focus', 'target' => 50, 'xp' => 30, 'bonus' => 0,
            ],
            [
                'day' => 10, 'week' => 2,
                'title' => 'Langkah Sehat 5.000',
                'desc' => 'Tingkatkan kebugaran dengan berjalan kaki minimal 5.000 langkah hari ini.',
                'type' => 'health', 'target' => 5000, 'xp' => 30, 'bonus' => 0,
            ],
            [
                'day' => 11, 'week' => 2,
                'title' => 'Metode Eat That Frog',
                'desc' => 'Kerjakan tugas paling berat atau mendesak pertama kali di pagi hari.',
                'type' => 'task', 'target' => 1, 'xp' => 30, 'bonus' => 0,
            ],
            [
                'day' => 12, 'week' => 2,
                'title' => 'Bebas Distraksi',
                'desc' => 'Matikan notifikasi non-esensial selama 1 jam dan selesaikan satu pekerjaan tuntas.',
                'type' => 'custom', 'target' => 1, 'xp' => 30, 'bonus' => 0,
            ],
            [
                'day' => 13, 'week' => 2,
                'title' => 'Peregangan & Postur Tubuh',
                'desc' => 'Luangkan 15 menit untuk stretching postur leher, bahu, dan punggung setelah duduk lama.',
                'type' => 'health', 'target' => 15, 'xp' => 30, 'bonus' => 0,
            ],
            [
                'day' => 14, 'week' => 2,
                'title' => 'Milestone Pekan 2: Paruh Waktu (Halfway Hero)',
                'desc' => 'Kamu sudah menempuh separuh perjalanan! 14 hari konsisten membuktikan tekadmu yang kuat.',
                'type' => 'custom', 'target' => 1, 'xp' => 60, 'bonus' => 75,
            ],

            // ================= MINGGU 3: MENEMBUS BATAS RESISTENSI =================
            [
                'day' => 15, 'week' => 3,
                'title' => 'Zona Konsistensi',
                'desc' => 'Minggu ketiga adalah saat kebiasaan lama mencoba kembali. Pertahankan dengan menyelesaikan 2 tugas hari ini.',
                'type' => 'task', 'target' => 2, 'xp' => 35, 'bonus' => 0,
            ],
            [
                'day' => 16, 'week' => 3,
                'title' => 'Hidrasi Penuh 8 Gelas',
                'desc' => 'Penuhi target hidrasi 8 gelas air untuk menjaga daya fokus otak tetap prima.',
                'type' => 'health', 'target' => 8, 'xp' => 35, 'bonus' => 0,
            ],
            [
                'day' => 17, 'week' => 3,
                'title' => 'Target 6.000 Langkah',
                'desc' => 'Capai target optimal 6.000 langkah kaki harian.',
                'type' => 'health', 'target' => 6000, 'xp' => 35, 'bonus' => 0,
            ],
            [
                'day' => 18, 'week' => 3,
                'title' => 'Time Boxing Terjadwal',
                'desc' => 'Tetapkan jam pasti kapan kamu akan mengerjakan tiap tugas dan tepati jadwal tersebut.',
                'type' => 'task', 'target' => 1, 'xp' => 35, 'bonus' => 0,
            ],
            [
                'day' => 19, 'week' => 3,
                'title' => 'Deep Work 3 Sesi',
                'desc' => 'Lakukan 3 sesi fokus tanpa terganggu sama sekali.',
                'type' => 'focus', 'target' => 75, 'xp' => 35, 'bonus' => 0,
            ],
            [
                'day' => 20, 'week' => 3,
                'title' => 'Istirahat Berkualitas',
                'desc' => 'Hindari layar gadget 30 menit sebelum tidur agar pemulihan tubuh maksimal.',
                'type' => 'custom', 'target' => 1, 'xp' => 35, 'bonus' => 0,
            ],
            [
                'day' => 21, 'week' => 3,
                'title' => 'Milestone Pekan 3: 21-Day Habit Milestone!',
                'desc' => 'Riset ilmiah membuktikan 21 hari membentuk jalur saraf kebiasaan baru. Kamu adalah Discipline Master!',
                'type' => 'custom', 'target' => 1, 'xp' => 80, 'bonus' => 100,
            ],

            // ================= MINGGU 4: PENGUASAAN & GAYA HIDUP BARU =================
            [
                'day' => 22, 'week' => 4,
                'title' => 'Gaya Hidup Otomatis',
                'desc' => 'Kebiasaanmu kini sudah mengakar. Selesaikan 2 quest utama dengan penuh percaya diri.',
                'type' => 'task', 'target' => 2, 'xp' => 40, 'bonus' => 0,
            ],
            [
                'day' => 23, 'week' => 4,
                'title' => 'Kebugaran Prima',
                'desc' => 'Jaga kebugaran dengan mencapai minimal 6.000 langkah dan penuhi kebutuhan cairan tubuh.',
                'type' => 'health', 'target' => 6000, 'xp' => 40, 'bonus' => 0,
            ],
            [
                'day' => 24, 'week' => 4,
                'title' => 'Pembersihan Tugas Tertunda',
                'desc' => 'Babat habis sisa-sisa pekerjaan kecil yang masih menggantung.',
                'type' => 'task', 'target' => 2, 'xp' => 40, 'bonus' => 0,
            ],
            [
                'day' => 25, 'week' => 4,
                'title' => 'Master Pomodoro',
                'desc' => 'Gunakan teknik Pomodoro untuk menyelesaikan proyek pentingmu secara tuntas.',
                'type' => 'focus', 'target' => 50, 'xp' => 40, 'bonus' => 0,
            ],
            [
                'day' => 26, 'week' => 4,
                'title' => 'Pertahankan Api Streak',
                'desc' => 'Hampir di garis finish! Pertahankan rekor tanpa putus sampai hari terakhir.',
                'type' => 'custom', 'target' => 1, 'xp' => 40, 'bonus' => 0,
            ],
            [
                'day' => 27, 'week' => 4,
                'title' => 'Refleksi Perubahan Diri',
                'desc' => 'Tuliskan satu hal positif terbesar yang kamu rasakan setelah 27 hari berproses.',
                'type' => 'custom', 'target' => 1, 'xp' => 40, 'bonus' => 0,
            ],
            [
                'day' => 28, 'week' => 4,
                'title' => 'Grand Finale: 28-Day Habit Champion!',
                'desc' => 'LUAR BIASA! Kamu telah menaklukkan tantangan 28 hari penuh! Kamu resmi menjadi Habit Champion!',
                'type' => 'custom', 'target' => 1, 'xp' => 150, 'bonus' => 200,
            ],
        ];

        foreach ($missions as $m) {
            ChallengeDay::updateOrCreate(
                ['challenge_id' => $challenge->id, 'day_number' => $m['day']],
                [
                    'week_number' => $m['week'],
                    'title' => $m['title'],
                    'description' => $m['desc'],
                    'mission_type' => $m['type'],
                    'target_metric' => $m['target'],
                    'reward_xp' => $m['xp'],
                    'milestone_bonus_xp' => $m['bonus'],
                ]
            );
        }

        $this->command->info('Data Tantangan 28 Hari dan Misi 1-28 berhasil di-generate!');
    }
}
