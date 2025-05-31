<?php

namespace App;

use App\Entities\Sale;
use App\Entities\Position;
use App\Entities\Transaction;
use App\Entities\Enums\TransactionType;
use App\Entities\GlobalState;
use App\Entities\CalculatorResults;

class Calculator
{
    private array $transactions = [];
    private GlobalState $globalState;
    private PortfolioCalculator $portfolioCalculator;
    private CalculatorResults $calculatorResults;

    public function __construct(array $transactions, PortfolioCalculator $portfolioCalculator)
    {
        $this->transactions = $transactions;
        $this->portfolioCalculator = $portfolioCalculator;
        $this->globalState = new GlobalState(0, 0);
        $this->calculatorResults = new CalculatorResults();
    }

    public function getGlobalState(): GlobalState
    {
        return $this->globalState;
    }

    public function calculate(): CalculatorResults
    {
        foreach ($this->transactions as $transaction) {
            $this->createSaleForSellTransaction($transaction);
            $this->managePosition($transaction);
            $this->manageGlobalstate($transaction);
        }

        return $this->calculatorResults;
    }

    private function managePosition(Transaction $transaction): void
    {
        if ($transaction->getType() === TransactionType::BUY || $transaction->getType() === TransactionType::SWAP) {
            $currency = $transaction->getReceivedCurrency();
            $position = $this->calculatorResults->getPosition($currency);

            $totalAmount = $position->getAmount() + $transaction->getReceivedAmount();
            $this->calculatorResults->updatePosition(new Position($currency, $totalAmount));
        }

        if ($transaction->getType() === TransactionType::SELL || $transaction->getType() === TransactionType::SWAP) {
            $currency = $transaction->getSentCurrency();
            $amount = $transaction->getSentAmount();
            $position = $this->calculatorResults->getPosition($currency);

            $this->calculatorResults->updatePosition(new Position(
                $currency,
                $position->getAmount() - $amount
            ));
        }
    }

    private function manageGlobalstate(Transaction $transaction): void
    {
        if ($transaction->getType() === TransactionType::BUY) {
            $this->globalState = new GlobalState(
                $this->globalState->getBoughtAmount() + round($transaction->getSentAmount(), 0) + round($transaction->getFeeAmount(), 0),
                $this->globalState->getSoldAmount()
            );
        }

        if ($transaction->getType() === TransactionType::SELL) {
            $this->globalState = new GlobalState(
                $this->globalState->getBoughtAmount(),
                $this->globalState->getSoldAmount() + round($transaction->getReceivedAmount(), 0)
            );
        }
    }

    private function createSaleForSellTransaction(Transaction $transaction): void
    {
        if ($transaction->getType() !== TransactionType::SELL) {
            return;
        }

        $portfolioAmount = $this->portfolioCalculator->calculatePortfolioAmount(
            $this->calculatorResults->getPositions(),
            $transaction->getDate()
        );

        $sale = new Sale(
            round($transaction->getReceivedAmount(), 0),
            round($transaction->getFeeAmount(), 0),
            $transaction->getSentCurrency(),
            $transaction->getDate(),
            round($portfolioAmount, 0),
            $this->globalState->getBoughtAmount(),
            $this->globalState->getSoldAmount()
        );

        // Update global state to include the sale
        $this->globalState = new GlobalState(
            $this->globalState->getBoughtAmount(),
            $this->globalState->getSoldAmount() - round($sale->getPnl(), 0)
        );

        $this->calculatorResults->addSale($sale);
    }
}
