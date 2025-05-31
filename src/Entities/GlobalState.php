<?php

namespace App\Entities;

class GlobalState
{
    private int $bought_amount;
    private int $sold_amount;

    public function __construct(
        int $bought_amount,
        int $sold_amount
    ) {
        $this->bought_amount = $bought_amount;
        $this->sold_amount = $sold_amount;
    }

    public function getBoughtAmount(): int
    {
        return $this->bought_amount;
    }

    public function getSoldAmount(): int
    {
        return $this->sold_amount;
    }
}