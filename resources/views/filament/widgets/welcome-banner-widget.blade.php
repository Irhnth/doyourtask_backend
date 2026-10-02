<x-filament-widgets::widget>
    <div style="background: linear-gradient(135deg, #d97706 0%, #ea580c 60%, #c2410c 100%); color: #ffffff; border-radius: 1rem; padding: 1.75rem 2rem; position: relative; overflow: hidden; box-shadow: 0 12px 28px -6px rgba(217, 119, 6, 0.35), 0 4px 12px -2px rgba(0, 0, 0, 0.1);">
        {{-- Background decorative subtle glow --}}
        <div style="pointer-events: none; position: absolute; right: -2rem; top: -3rem; width: 18rem; height: 18rem; border-radius: 9999px; background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, rgba(255,255,255,0) 70%);"></div>
        <div style="pointer-events: none; position: absolute; left: 15%; bottom: -4rem; width: 14rem; height: 14rem; border-radius: 9999px; background: radial-gradient(circle, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0) 70%);"></div>

        <div style="position: relative; z-index: 10;">
            <style>
                .dyt-banner-grid {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 1.75rem;
                    align-items: center;
                }
                @media (min-width: 1024px) {
                    .dyt-banner-grid {
                        grid-template-columns: 1.35fr 1fr;
                    }
                }
                .dyt-stat-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
                    gap: 0.85rem;
                }
                .dyt-stat-card {
                    background: rgba(255, 255, 255, 0.13);
                    border: 1px solid rgba(255, 255, 255, 0.22);
                    border-radius: 0.85rem;
                    padding: 1rem 1.15rem;
                    backdrop-filter: blur(10px);
                    transition: all 0.2s ease-in-out;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                }
                .dyt-stat-card:hover {
                    background: rgba(255, 255, 255, 0.2);
                    transform: translateY(-2px);
                    box-shadow: 0 6px 16px -2px rgba(0, 0, 0, 0.15);
                }
                .dyt-btn-primary {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    border-radius: 0.65rem;
                    background: #ffffff;
                    color: #c2410c;
                    padding: 0.6rem 1.2rem;
                    font-size: 0.85rem;
                    font-weight: 700;
                    text-decoration: none;
                    box-shadow: 0 4px 8px -2px rgba(0, 0, 0, 0.15);
                    transition: all 0.2s ease;
                }
                .dyt-btn-primary:hover {
                    background: #fff7ed;
                    color: #9a3412;
                    transform: translateY(-1px);
                    box-shadow: 0 6px 14px -3px rgba(0, 0, 0, 0.2);
                }
                .dyt-btn-secondary {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    border-radius: 0.65rem;
                    background: rgba(255, 255, 255, 0.18);
                    color: #ffffff;
                    border: 1px solid rgba(255, 255, 255, 0.32);
                    padding: 0.6rem 1.15rem;
                    font-size: 0.85rem;
                    font-weight: 600;
                    text-decoration: none;
                    backdrop-filter: blur(8px);
                    transition: all 0.2s ease;
                }
                .dyt-btn-secondary:hover {
                    background: rgba(255, 255, 255, 0.28);
                    border-color: rgba(255, 255, 255, 0.45);
                    transform: translateY(-1px);
                }
                .dyt-btn-subtle {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    border-radius: 0.65rem;
                    background: rgba(0, 0, 0, 0.15);
                    color: #ffedd5;
                    border: 1px solid rgba(255, 255, 255, 0.18);
                    padding: 0.6rem 1.1rem;
                    font-size: 0.85rem;
                    font-weight: 500;
                    text-decoration: none;
                    backdrop-filter: blur(8px);
                    transition: all 0.2s ease;
                }
                .dyt-btn-subtle:hover {
                    background: rgba(0, 0, 0, 0.25);
                    color: #ffffff;
                    border-color: rgba(255, 255, 255, 0.3);
                    transform: translateY(-1px);
                }
            </style>

            <div class="dyt-banner-grid">
                {{-- Sisi Kiri: Sapaan, Info & Tombol Aksi --}}
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    {{-- Header Pill --}}
                    <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                        <span style="display: inline-flex; align-items: center; gap: 0.4rem; border-radius: 9999px; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); padding: 0.25rem 0.8rem; font-size: 0.75rem; font-weight: 600; color: #ffffff; backdrop-filter: blur(8px); letter-spacing: 0.02em;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                            DoYourTask Admin
                        </span>
                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8rem; font-weight: 500; color: #fed7aa;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </span>
                    </div>

                    {{-- Title --}}
                    <h2 style="font-size: 1.85rem; font-weight: 800; color: #ffffff; line-height: 1.2; margin: 0; letter-spacing: -0.01em;">
                        {{ $this->getGreeting() }}, {{ auth()->user()->name ?? 'Administrator' }}! 👋
                    </h2>

                    {{-- Description --}}
                    <p style="margin: 0; font-size: 0.925rem; line-height: 1.55; color: #ffedd5; max-width: 38rem;">
                        Pantau produktivitas tugas, perkembangan gamifikasi XP & level, serta kepatuhan target kesehatan pengguna secara real-time.
                    </p>

                    {{-- Quick Action Buttons --}}
                    <div style="display: flex; flex-wrap: wrap; gap: 0.65rem; padding-top: 0.35rem; align-items: center;">
                        <a href="{{ $this->getNewTaskUrl() }}" class="dyt-btn-primary">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>
                            Tambah Tugas
                        </a>

                        <a href="{{ $this->getNewHealthTargetUrl() }}" class="dyt-btn-secondary">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                            Target Kesehatan
                        </a>

                        <a href="{{ $this->getUsersUrl() }}" class="dyt-btn-subtle">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            Kelola Pengguna
                        </a>
                    </div>
                </div>

                {{-- Sisi Kanan: 3 Kartu Mini Ringkasan Statistik --}}
                <div class="dyt-stat-grid">
                    {{-- Card 1: Tugas Menunggu --}}
                    <div class="dyt-stat-card">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.775rem; font-weight: 600; color: #fed7aa; text-transform: uppercase; letter-spacing: 0.03em;">Tugas Pending</span>
                            <div style="width: 2rem; height: 2rem; border-radius: 0.5rem; background: rgba(254, 240, 138, 0.2); display: flex; align-items: center; justify-content: center; color: #fef08a;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/></svg>
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 1.85rem; font-weight: 800; color: #ffffff; line-height: 1;">
                                {{ $this->getPendingTasksCount() }}
                            </div>
                            <span style="font-size: 0.725rem; color: #ffedd5; opacity: 0.9; margin-top: 0.25rem; display: block;">
                                Menunggu dikerjakan
                            </span>
                        </div>
                    </div>

                    {{-- Card 2: Log Kesehatan Hari Ini --}}
                    <div class="dyt-stat-card">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.775rem; font-weight: 600; color: #fed7aa; text-transform: uppercase; letter-spacing: 0.03em;">Log Hari Ini</span>
                            <div style="width: 2rem; height: 2rem; border-radius: 0.5rem; background: rgba(254, 202, 202, 0.2); display: flex; align-items: center; justify-content: center; color: #fca5a5;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 1.85rem; font-weight: 800; color: #ffffff; line-height: 1;">
                                {{ $this->getTodayHealthLogsCount() }}
                            </div>
                            <span style="font-size: 0.725rem; color: #ffedd5; opacity: 0.9; margin-top: 0.25rem; display: block;">
                                Catatan kesehatan harian
                            </span>
                        </div>
                    </div>

                    {{-- Card 3: Total Pengguna --}}
                    <div class="dyt-stat-card">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.775rem; font-weight: 600; color: #fed7aa; text-transform: uppercase; letter-spacing: 0.03em;">Pengguna</span>
                            <div style="width: 2rem; height: 2rem; border-radius: 0.5rem; background: rgba(187, 247, 208, 0.2); display: flex; align-items: center; justify-content: center; color: #86efac;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 1.85rem; font-weight: 800; color: #ffffff; line-height: 1;">
                                {{ $this->getTotalUsersCount() }}
                            </div>
                            <span style="font-size: 0.725rem; color: #ffedd5; opacity: 0.9; margin-top: 0.25rem; display: block;">
                                Akun terdaftar
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
