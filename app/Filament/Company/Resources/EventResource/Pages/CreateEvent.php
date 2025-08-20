<?php

namespace App\Filament\Company\Resources\EventResource\Pages;

use App\Filament\Company\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;
}
