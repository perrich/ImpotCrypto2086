<?php

namespace App\Entities;

use \DateTimeImmutable;
use App\Entities\Enums\Currency;

class Sale
{
    private int $sale_amount;
    private int $fee_amount;
    private Currency $currency;
    private DateTimeImmutable $date;
    private int $portfolio_total_amount;
    private int $total_bought;
    private int $total_sold;

    public function __construct(
        int $sale_amount,
        int $fee_amount,
        Currency $currency,
        DateTimeImmutable $date,
        int $portfolio_total_amount,
        int $total_bought,
        int $total_sold
    ) {
        $this->sale_amount = $sale_amount;
        $this->fee_amount = $fee_amount;
        $this->currency = $currency;
        $this->date = $date;
        $this->portfolio_total_amount = $portfolio_total_amount;
        $this->total_bought = $total_bought;
        $this->total_sold = $total_sold;
    }

    public function getSaleAmount(): int
    {
        return $this->sale_amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getPortfolioTotalAmount(): int
    {
        return $this->portfolio_total_amount;
    }

    public function getTotalBought(): int
    {
        return $this->total_bought;
    }

    public function getTotalSold(): int
    {
        return $this->total_sold;
    }

    public function getFeeAmount(): int
    {
        return $this->fee_amount;
    }

    public function getPnl(): int
    {
        $pnl = $this->getSaleAmount() - (
            ($this->getTotalBought() - $this->getTotalSold())
            * ($this->getSaleAmount() + $this->getFeeAmount()) / $this->getPortfolioTotalAmount());

        return round($pnl, 0);
    }
}
