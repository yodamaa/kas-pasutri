<?php

namespace App\Filament\Superadmin\Resources\ActivityLogResource\Pages;

use App\Filament\Superadmin\Resources\ActivityLogResource;
use Filament\Resources\Pages\ListRecords;

class ListActivityLogs extends ListRecords
{
    protected static string $resource = ActivityLogResource::class;
}
