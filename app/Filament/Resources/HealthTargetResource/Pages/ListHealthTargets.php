<?php

namespace App\Filament\Resources\HealthTargetResource\Pages;

use App\Filament\Resources\HealthTargetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHealthTargets extends ListRecords
{
    protected static string $resource = HealthTargetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
