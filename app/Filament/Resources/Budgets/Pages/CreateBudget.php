<?php

namespace App\Filament\Resources\Budgets\Pages;

use App\Filament\Resources\Budgets\BudgetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBudget extends CreateRecord
{
    protected static string $resource = BudgetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $coupleId = auth()->user()->getCoupleId();
        if ($coupleId) {
            $data['couple_id'] = $coupleId;
        }
        return $data;
    }
}
