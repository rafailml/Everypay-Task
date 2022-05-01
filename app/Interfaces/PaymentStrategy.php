<?php
declare(strict_types=1);

namespace App\Interfaces;

interface PaymentStrategy
{
    public function handlePayment(
        int    $cardNumber,
        string $cardExpirationDate,
        int    $cardCvc,
        string $cardholderName,
        float  $amount,
        string $currency,
        string $cardAddress = null,
        string $cardCity = null,
        string $cardCountry = null,
        string $email = null,
        string $orderDescription = null,
    );
}
