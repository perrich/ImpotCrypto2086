<?php

namespace App\Entities\Enums;

enum Currency: string
{
    case ALGO = 'ALGO';
    case BTC = 'BTC';
    case DOGE = 'DOGE';
    case ETH = 'ETH';
    case EUR = 'EUR';
    case GRIN = 'GRIN';
    case LTC = 'LTC';
    case SAND = 'SAND';
    case SOL = 'SOL';
    case USD = 'USD';
    case USDC = 'USDC';
    case USDT = 'USDT';
    case XRP = 'XRP';

    case NONE = ''; // Used for empty values in adjustements
}