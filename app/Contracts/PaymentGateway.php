<?php

namespace App\Contracts;

interface PaymentGateway
{
    public function key(): string;

    public function createCheckout(array $payload): array;

    public function verifyWebhook(array $payload): bool;
}
