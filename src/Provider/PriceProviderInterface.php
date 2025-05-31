<?php

namespace App\Provider;

use App\Entities\Enums\Currency;

interface PriceProviderInterface
{
    /**
     * Adds a fallback provider to the price provider.
     */
    public function addfallbackProvider(PriceProviderInterface $provider): void;
    
    public function getPrice(Currency $currency, \DateTimeImmutable $date): float;
}