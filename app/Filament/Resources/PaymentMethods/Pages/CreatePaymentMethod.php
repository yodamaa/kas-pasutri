<?php

namespace App\Filament\Resources\PaymentMethods\Pages;

use App\Filament\Resources\PaymentMethods\PaymentMethodResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentMethod extends CreateRecord
{
    protected static string $resource = PaymentMethodResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $coupleId = auth()->user()->getCoupleId();
        if ($coupleId) {
            $data['couple_id'] = $coupleId;
        }
        return $data;
    }
}
