<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChallengeResource\Pages;
use App\Filament\Resources\ChallengeResource\RelationManagers;
use App\Models\Challenge;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChallengeResource extends Resource
{
    protected static ?string $model = Challenge::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Gamifikasi & Kebiasaan';
    protected static ?string $navigationLabel = 'Tantangan 28 Hari';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul Program Tantangan')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi Program')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'habit' => 'Pembentukan Kebiasaan (Habit)',
                        'productivity' => 'Produktivitas & Fokus',
                        'health' => 'Kebugaran & Kesehatan',
                    ])
                    ->default('habit')
                    ->required(),
                Forms\Components\TextInput::make('duration_days')
                    ->label('Durasi (Hari)')
                    ->numeric()
                    ->default(28)
                    ->required(),
                Forms\Components\TextInput::make('total_reward_xp')
                    ->label('Total Hadiah XP')
                    ->numeric()
                    ->default(1545)
                    ->required(),
                Forms\Components\Select::make('badge_id')
                    ->label('Hadiah Lencana Juara')
                    ->relationship('badge', 'badge_name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true)
                    ->inline(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Tantangan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('duration_days')
                    ->label('Durasi')
                    ->suffix(' Hari')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_reward_xp')
                    ->label('Total Hadiah')
                    ->badge()
                    ->color('warning')
                    ->suffix(' XP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('badge.badge_name')
                    ->label('Grand Badge')
                    ->badge()
                    ->color('success'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DaysRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChallenges::route('/'),
            'create' => Pages\CreateChallenge::route('/create'),
            'edit' => Pages\EditChallenge::route('/{record}/edit'),
        ];
    }
}

