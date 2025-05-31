<?php

require_once __DIR__ . '../vendor/autoload.php';

$transactionCsvFile = __DIR__ . '/data/transactions.csv';
$priceCsvFile = __DIR__ . '/data/prices.csv';

$reader = new App\TransactionCsvReader();
$transactions = $reader->readTransactionsFromCsv($transactionCsvFile);

$apiPriceProvider = new App\Provider\PriceProviderFromCoinGeckoApi();
$csvPriceProvider = new App\Provider\PriceProviderFromCsv($priceCsvFile);
$csvPriceProvider->addFallbackProvider($apiPriceProvider);

$portfolioCalculator = new App\PortfolioCalculator($csvPriceProvider);
$calculator = new App\Calculator($transactions, $portfolioCalculator);
$calculatorResults = $calculator->calculate();

$formatter = new App\CalculatorResultsFormatter($calculatorResults);

print($formatter->format());
