<?php

namespace App\Provider;

trait FallbackPriceProviderTrait
{
    protected ?PriceProviderInterface $fallbackProvider = null;

    /**
     * Adds a fallback provider to the price provider.
     */
    public function addFallbackProvider(PriceProviderInterface $provider): void
    {
        if (isset($this->fallbackProvider)) {
            throw new \LogicException('Fallback provider already set.');
        }
        $this->fallbackProvider = $provider;    
    }
}