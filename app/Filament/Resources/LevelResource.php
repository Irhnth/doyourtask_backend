<?php

namespace App\Filament\Resources;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\LevelResource\Pages;
use App\Filament\Resources\LevelResource\RelationManagers;
use App\Models\Level;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LevelResource extends Resource
{
    protected static ?string $model = Level::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('level_number')
                    ->required()
                    ->numeric()
                    ->unique(ignoreRecord: true)
                    ->label('Nomor Level (Misal: 1)'),
                TextInput::make('level_name')
                    ->required()
                    ->maxLength(100)
                    ->label('Nama Level (Misal: Novice)'),
                TextInput::make('xp_required')
                    ->required()
                    ->numeric()
                    ->label('Target XP Dibutuhkan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('level_number')
                    ->sortable()
                    ->label('No. Level')
                    ->badge()
                    ->color('info'),
                TextColumn::make('level_name')
                    ->searchable()
                    ->label('Nama Level'),
                TextColumn::make('xp_required')
                    ->numeric()
                    ->sortable()
                    ->label('Target XP')
                    ->color('warning'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLevels::route('/'),
            'create' => Pages\CreateLevel::route('/create'),
            'edit' => Pages\EditLevel::route('/{record}/edit'),
        ];
    }
}
