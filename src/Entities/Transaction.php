<?php

namespace App\Entities;

use \DateTimeImmutable;
use App\Entities\Enums\TransactionType;
use App\Entities\Enums\Currency;

class Transaction
{
    private TransactionType $type;
    private DateTimeImmutable $date;
    private float $received_amount;
    private Currency $received_currency;
    private float $sent_amount;
    private Currency $sent_currency;
    private float $fee_amount;
    private Currency $fee_currency;

    public function __construct(
        TransactionType $type,
        DateTimeImmutable $date,
        float $received_amount,
        Currency $received_currency,
        float $sent_amount,
        Currency $sent_currency,
        float $fee_amount,
        Currency $fee_currency
    ) {
        $this->type = $type;
        $this->date = $date;
        $this->received_amount = $received_amount;
        $this->received_currency = $received_currency;
        $this->sent_amount = $sent_amount;
        $this->sent_currency = $sent_currency;
        $this->fee_amount = $fee_amount;
        $this->fee_currency = $fee_currency;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getReceivedAmount(): float
    {
        return $this->received_amount;
    }

    public function getReceivedCurrency(): Currency
    {
        return $this->received_currency;
    }

    public function getSentAmount(): float
    {
        return $this->sent_amount;
    }

    public function getSentCurrency(): Currency
    {
        return $this->sent_currency;
    }

    public function getFeeAmount(): float
    {
        return $this->fee_amount;
    }

    public function getFeeCurrency(): Currency
    {
        return $this->fee_currency;
    }
}
