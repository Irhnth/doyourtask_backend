<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HealthTargetResource\Pages;
use App\Models\HealthTarget;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HealthTargetResource extends Resource
{
    protected static ?string $model = HealthTarget::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Manajemen Kesehatan';
    protected static ?string $navigationLabel = 'Target Kesehatan';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required()
                    ->label('Pengguna (User)'),
                
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Misal: Minum Air Putih')
                    ->label('Nama Target'),
                
                Forms\Components\Select::make('type')
                    ->options([
                        'food' => 'Makanan (Food)',
                        'drink' => 'Minuman (Drink)',
                        'exercise' => 'Olahraga (Exercise)',
                        'other' => 'Lainnya (Other)',
                    ])
                    ->required()
                    ->native(false)
                    ->label('Tipe'),
                
                Forms\Components\TextInput::make('target_value')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->placeholder('Misal: 8')
                    ->label('Nilai Target'),
                
                Forms\Components\TextInput::make('unit')
                    ->maxLength(255)
                    ->placeholder('Misal: Gelas, Menit, Porsi')
                    ->label('Satuan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Pengguna'),
                
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->label('Nama Target'),
                
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'food' => 'warning',
                        'drink' => 'info',
                        'exercise' => 'success',
                        'other' => 'gray',
                    })
                    ->label('Tipe'),
                
                Tables\Columns\TextColumn::make('target_value')
                    ->numeric()
                    ->sortable()
                    ->label('Target'),
                
                Tables\Columns\TextColumn::make('unit')
                    ->searchable()
                    ->label('Satuan'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tambahkan filter jika diperlukan nanti
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHealthTargets::route('/'),
            'create' => Pages\CreateHealthTarget::route('/create'),
            'edit' => Pages\EditHealthTarget::route('/{record}/edit'),
        ];
    }
}