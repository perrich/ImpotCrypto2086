<?php

namespace App\Entities;

use App\Entities\Enums\Currency;

class Position
{
    private Currency $currency;
    private float $amount;

    public function __construct(
        Currency $currency,
        float $amount
    ) {
        $this->currency = $currency;
        $this->amount = $amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}