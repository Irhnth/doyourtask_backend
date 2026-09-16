<?php

namespace App\Filament\Resources\HealthTargetResource\Pages;

use App\Filament\Resources\HealthTargetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHealthTarget extends EditRecord
{
    protected static string $resource = HealthTargetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
