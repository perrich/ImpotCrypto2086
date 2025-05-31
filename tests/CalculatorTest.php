<?php

namespace App\Tests;

use App\Calculator;
use App\Entities\Sale;
use App\Entities\GlobalState;
use App\Entities\Position;
use App\Entities\Enums\Currency;
use App\TransactionCsvReader;
use App\PortfolioCalculator;
use App\Provider\PriceProviderFromCsv;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    public function testCalculatePositionsForTransactionWithoutFees()
    {
        $transactionCsvFile = __DIR__ . '/data/transactions without fees test.csv';
        $priceCsvFile = __DIR__ . '/data/prices sample.csv';

        $reader = new TransactionCsvReader();
        $transactions = $reader->readTransactionsFromCsv($transactionCsvFile);

        $csvPriceProvider = new PriceProviderFromCsv($priceCsvFile);

        $portfolioCalculator = new PortfolioCalculator($csvPriceProvider);
        $calculator = new Calculator($transactions, $portfolioCalculator);
        $calculatorResults = $calculator->calculate();
        $calculatedPositions = $calculatorResults->getPositions();

        $expectedPositions = [
            'BTC' => new Position(Currency::BTC, 6.5),
            'GRIN' => new Position(Currency::GRIN, 50.0),
        ];

        $this->assertCount(2, $calculatedPositions);
        $this->assertEquals(array_keys($expectedPositions), array_keys($calculatedPositions));
        $this->assertEquals($expectedPositions['BTC'], $calculatedPositions['BTC']);
        $this->assertEquals($expectedPositions['GRIN'], $calculatedPositions['GRIN']);
    }

    public function testCalculateSalesForTransactionWithoutFees()
    {
        $transactionCsvFile = __DIR__ . '/data/transactions without fees test.csv';
        $priceCsvFile = __DIR__ . '/data/prices sample.csv';

        $reader = new TransactionCsvReader();
        $transactions = $reader->readTransactionsFromCsv($transactionCsvFile);

        $csvPriceProvider = new PriceProviderFromCsv($priceCsvFile);

        $portfolioCalculator = new PortfolioCalculator($csvPriceProvider);
        $calculator = new Calculator($transactions, $portfolioCalculator);
        $calculatorResults = $calculator->calculate();

        $globalState = new GlobalState(34856, 8390);
        $this->assertEquals($globalState, $calculator->getGlobalState());

        $calculatedSales = $calculatorResults->getSales();
        
        $this->assertCount(3, $calculatedSales);
        $this->assertEquals(new Sale(
            3744,
            0,
            Currency::BTC,
            new \DateTimeImmutable('2019-01-09T19:44:35.000000+0000'),
            22464,
            25351,
            0
        ), $calculatedSales[0]);
        $this->assertEquals(new Sale(
            224,
            0,
            Currency::GRIN,
            new \DateTimeImmutable('2019-06-16T19:44:35.000000+0000'),
            63103,
            34856,
            4225
        ), $calculatedSales[1]);
        $this->assertEquals(new Sale(
            10463,
            0,
            Currency::BTC,
            new \DateTimeImmutable('2019-06-25T19:44:35.000000+0000'),
            78745,
            34856,
            4334
        ), $calculatedSales[2]);
    }

    public function testCalculateSalesForTransactionWithFees()
    {
        $transactionCsvFile = __DIR__ . '/data/transactions with fees test.csv';
        $priceCsvFile = __DIR__ . '/data/prices sample.csv';

        $reader = new TransactionCsvReader();
        $transactions = $reader->readTransactionsFromCsv($transactionCsvFile);

        $csvPriceProvider = new PriceProviderFromCsv($priceCsvFile);

        $portfolioCalculator = new PortfolioCalculator($csvPriceProvider);
        $calculator = new Calculator($transactions, $portfolioCalculator);
        $calculatorResults = $calculator->calculate();

        $globalState = new GlobalState(34749, 8338);
        $this->assertEquals($globalState, $calculator->getGlobalState());

        $calculatedSales = $calculatorResults->getSales();
        
        $this->assertCount(2, $calculatedSales);
        $this->assertEquals(new Sale(
            3744,
            37,
            Currency::BTC,
            new \DateTimeImmutable('2019-01-09T19:44:35.000000+0000'),
            22464,
            25376,
            0
        ), $calculatedSales[0]);
        $this->assertEquals(new Sale(
            10463,
            10,
            Currency::BTC,
            new \DateTimeImmutable('2019-06-25T19:44:35.000000+0000'),
            78479,
            34749,
            4271
        ), $calculatedSales[1]);
    }
}
