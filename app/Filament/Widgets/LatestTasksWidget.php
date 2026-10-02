<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestTasksWidget extends BaseWidget
{
    protected static ?string $heading = 'Tugas Terbaru & Status Tenggat';

    protected static ?int $sort = 5;

    protected static ?string $pollingInterval = '30s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()->with('user')->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->limit(15),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Tugas')
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->title),

                Tables\Columns\TextColumn::make('deadline')
                    ->label('Tenggat')
                    ->dateTime('d M, H:i')
                    ->description(fn (Task $record): ?string => $record->deadline ? $record->deadline->diffForHumans() : null)
                    ->color(function (Task $record): string {
                        if ($record->status === 'completed') {
                            return 'gray';
                        }
                        return ($record->deadline && $record->deadline->isPast()) ? 'danger' : 'warning';
                    }),

                Tables\Columns\TextColumn::make('reward_xp')
                    ->label('Reward')
                    ->formatStateUsing(fn ($state) => "+{$state} XP")
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'completed' => 'Selesai',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'completed' => 'success',
                        default => 'primary',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->tooltip('Buka Tugas')
                    ->url(fn (Task $record): string => TaskResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }
}

