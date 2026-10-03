<?php

namespace App\Filament\Resources\ChallengeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DaysRelationManager extends RelationManager
{
    protected static string $relationship = 'days';
    protected static ?string $title = 'Daftar Misi Harian (Hari 1 - 28)';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('day_number')
                    ->label('Hari Ke-')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->maxValue(28),
                Forms\Components\TextInput::make('week_number')
                    ->label('Minggu Ke-')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->maxValue(4),
                Forms\Components\TextInput::make('title')
                    ->label('Judul Misi')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Select::make('mission_type')
                    ->label('Tipe Misi')
                    ->options([
                        'task' => 'Quest Tugas (Task)',
                        'health' => 'Kesehatan / Langkah (Health)',
                        'focus' => 'Fokus Pomodoro (Focus)',
                        'custom' => 'Kustom / Refleksi (Custom)',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('target_metric')
                    ->label('Target Metrik (Jumlah/Langkah/Menit)')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('reward_xp')
                    ->label('Hadiah XP Harian')
                    ->numeric()
                    ->required()
                    ->default(25),
                Forms\Components\TextInput::make('milestone_bonus_xp')
                    ->label('Bonus Milestone XP')
                    ->numeric()
                    ->required()
                    ->default(0),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi Petunjuk Misi')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('day_number', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('day_number')
                    ->label('Hari')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('week_number')
                    ->label('Minggu')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Misi')
                    ->searchable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('mission_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'task' => 'warning',
                        'health' => 'success',
                        'focus' => 'danger',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('target_metric')
                    ->label('Target'),
                Tables\Columns\TextColumn::make('reward_xp')
                    ->label('XP')
                    ->badge()
                    ->color('secondary')
                    ->suffix(' XP'),
                Tables\Columns\TextColumn::make('milestone_bonus_xp')
                    ->label('Bonus XP')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray')
                    ->suffix(' XP'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

