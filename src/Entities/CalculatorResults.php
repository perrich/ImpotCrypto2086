<?php

namespace App\Entities;

use App\Entities\Enums\Currency;

class CalculatorResults
{
    /**
     * @var Position[]
     */
    private array $positions = [];

    /**
     * @var Sale[]
     */
    private array $sales = [];

    public function getPositions(): array
    {
        return $this->positions;
    }

    public function getPosition(Currency $currency): Position
    {
        if (!array_key_exists($currency->value, $this->positions)) {
            $this->positions[$currency->value] = new Position($currency, 0.0);
        }

        return $this->positions[$currency->value];
    }
    
    public function updatePosition(Position $position): void
    {
        $this->positions[$position->getCurrency()->value] = $position;
    }

    public function getSales(): array
    {
        return $this->sales;
    }

    public function addSale(Sale $sale): void
    {
        $this->sales[] = $sale;
    }
}
