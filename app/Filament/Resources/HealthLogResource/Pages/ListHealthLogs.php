<?php

namespace App\Filament\Resources\HealthLogResource\Pages;

use App\Filament\Resources\HealthLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHealthLogs extends ListRecords
{
    protected static string $resource = HealthLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
