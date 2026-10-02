<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HealthLogResource\Pages;
use App\Models\HealthLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HealthLogResource extends Resource
{
    protected static ?string $model = HealthLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Manajemen Kesehatan';
    protected static ?string $navigationLabel = 'Log Harian';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required()
                    ->label('Pengguna'),
                
                Forms\Components\Select::make('health_target_id')
                    ->relationship('target', 'title') // Mengacu pada relasi target() di model HealthLog
                    ->searchable()
                    ->required()
                    ->label('Target Kesehatan'),
                
                Forms\Components\DatePicker::make('date')
                    ->required()
                    ->native(false)
                    ->label('Tanggal Catatan'),
                
                Forms\Components\TextInput::make('current_value')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Progres Tercapai'),
                
                Forms\Components\Toggle::make('is_completed')
                    ->required()
                    ->label('Status Selesai?'),
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
                
                Tables\Columns\TextColumn::make('target.title')
                    ->searchable()
                    ->sortable()
                    ->label('Target Kesehatan'),
                
                Tables\Columns\TextColumn::make('date')
                    ->date('d M Y')
                    ->sortable()
                    ->label('Tanggal'),
                
                Tables\Columns\TextColumn::make('current_value')
                    ->numeric()
                    ->sortable()
                    ->label('Progres Saat Ini'),
                
                Tables\Columns\IconColumn::make('is_completed')
                    ->boolean()
                    ->label('Selesai?'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter berdasarkan tanggal atau status selesai bisa ditambahkan di sini
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
            'index' => Pages\ListHealthLogs::route('/'),
            'create' => Pages\CreateHealthLog::route('/create'),
            'edit' => Pages\EditHealthLog::route('/{record}/edit'),
        ];
    }
}