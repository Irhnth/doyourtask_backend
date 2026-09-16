<?php

namespace App\Filament\Resources\HealthLogResource\Pages;

use App\Filament\Resources\HealthLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHealthLog extends EditRecord
{
    protected static string $resource = HealthLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
