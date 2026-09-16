<?php

namespace App\Filament\Resources\HealthLogResource\Pages;

use App\Filament\Resources\HealthLogResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHealthLog extends CreateRecord
{
    protected static string $resource = HealthLogResource::class;
}
