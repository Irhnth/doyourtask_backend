<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopUsersLeaderboardWidget extends BaseWidget
{
    protected static ?string $heading = 'Papan Peringkat Gamifikasi (Top 5)';

    protected static ?int $sort = 6;

    protected static ?string $pollingInterval = '30s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->with('level')
                    ->withCount('badges')
                    ->orderByDesc('current_xp')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->rowIndex()
                    ->label('#')
                    ->formatStateUsing(function ($state) {
                        return match ((int) $state) {
                            1 => '🥇 1',
                            2 => '🥈 2',
                            3 => '🥉 3',
                            default => "#{$state}",
                        };
                    })
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Pengguna')
                    ->description(fn (User $record): string => $record->email)
                    ->limit(18),

                Tables\Columns\TextColumn::make('level.level_name')
                    ->label('Level')
                    ->badge()
                    ->color('success')
                    ->default('Novice'),

                Tables\Columns\TextColumn::make('current_xp')
                    ->label('XP')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' XP'),

                Tables\Columns\TextColumn::make('badges_count')
                    ->label('Lencana')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => "{$state} 🏆"),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('')
                    ->icon('heroicon-m-eye')
                    ->tooltip('Lihat Detail Pengguna')
                    ->url(fn (User $record): string => UserResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }
}

