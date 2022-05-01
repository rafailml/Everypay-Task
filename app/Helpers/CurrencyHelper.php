<?php
declare(strict_types=1);

namespace App\Helpers;

class CurrencyHelper
{
    public static function resolveFactor(string $currency)
    {
        $zeroDecimalCurrencies = ['JPY'];

        if (in_array(strtoupper($currency), $zeroDecimalCurrencies)) {
            return 1;
        }

        return 100;
    }
}
