<?php

namespace App\Filament\Resources;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use App\Filament\Resources\BadgeResource\Pages;
use App\Filament\Resources\BadgeResource\RelationManagers;
use App\Models\Badge;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BadgeResource extends Resource
{
    protected static ?string $model = Badge::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('badge_name')
                    ->required()
                    ->maxLength(100)
                    ->label('Nama Lencana'),
                TextInput::make('requirement_count')
                    ->required()
                    ->numeric()
                    ->label('Syarat Jumlah Tugas Selesai'),
                FileUpload::make('image_icon')
                    ->image()
                    ->directory('badges')
                    ->label('Ikon Lencana')
                    ->nullable(), // Boleh kosong sesuai file migration Anda
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_icon')
                    ->label('Ikon')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-badge.png')), // Ikon default jika kosong
                TextColumn::make('badge_name')
                    ->searchable()
                    ->label('Nama Lencana'),
                TextColumn::make('requirement_count')
                    ->numeric()
                    ->sortable()
                    ->label('Syarat Kelulusan')
                    ->badge()
                    ->color('success'),
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
            'index' => Pages\ListBadges::route('/'),
            'create' => Pages\CreateBadge::route('/create'),
            'edit' => Pages\EditBadge::route('/{record}/edit'),
        ];
    }
}
