<?php

namespace App\Entities\Enums;

enum TransactionType: string
{
    case BUY = 'Buy';
    case SELL = 'Sell';
    case SWAP = 'Swap';
    case ADJUSTMENT = 'Adjustment';
}