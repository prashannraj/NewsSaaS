<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGateway;

class ManualGateway implements PaymentGateway
{
    public function key(): string
    {
        return 'manual';
    }

    public function createCheckout(array $payload): array
    {
        return ['url' => null, 'reference' => 'manual-'.uniqid()];
    }

    public function verifyWebhook(array $payload): bool
    {
        return true;
    }
}
