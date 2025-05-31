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
     * @var Cession[]
     */
    private array $cessions = [];

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

    public function getCessions(): array
    {
        return $this->cessions;
    }

    public function addCession(Cession $cession): void
    {
        $this->cessions[] = $cession;
    }
}
