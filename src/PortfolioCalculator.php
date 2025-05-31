<?php

namespace App;

use App\Entities\Position;
use App\Provider\PriceProviderInterface;
use \DateTimeImmutable;

class PortfolioCalculator
{
    private const NEGLIGIBLE_AMOUNT = 0.0001;
    private PriceProviderInterface $priceProvider;

    public function __construct(PriceProviderInterface $priceProvider)
    {
        $this->priceProvider = $priceProvider;
    }

    /**
     * Calculate the portfolio amount at a given date.
     * @param Position[] $positions
     * @param DateTimeImmutable $date
     * @return float
     */
    public function calculatePortfolioAmount(array $positions, DateTimeImmutable $date): float
    {
        $total = 0.0;
        foreach ($positions as $position) {
            $amount = $position->getAmount();
            if ($amount < self::NEGLIGIBLE_AMOUNT) {
                continue;
            }

            $price = $this->priceProvider->getPrice($position->getCurrency(), $date);
            $total += $amount * $price;
        }
        return $total;
    }
}
