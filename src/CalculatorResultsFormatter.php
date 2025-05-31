<?php

namespace App;

use App\Entities\CalculatorResults;

class CalculatorResultsFormatter
{
    private const NEGLIGIBLE_AMOUNT = 0.0001;
    private CalculatorResults $calculatorResults;

    public function __construct(CalculatorResults $calculatorResults)
    {
        $this->calculatorResults = $calculatorResults;
    }

    public function format(): string
    {
        $output = "Positions actuelles :\n";
        foreach ($this->calculatorResults->getPositions() as $position) {
            if ($position->getAmount() < self::NEGLIGIBLE_AMOUNT) {
                continue;
            }

            $output .= sprintf(
                "  %s = %.8f\n",
                $position->getCurrency()->value,
                $position->getAmount()
            );
        }

        $output .= sprintf("\n\n%d cessions :\n", count($this->calculatorResults->getSales()));
        foreach ($this->calculatorResults->getSales() as $sale) {
            $output .= sprintf(
                "Le %s, %s prix (213): %d, frais (214): %d, Valo ptf (212): %d, Total acq (220): %d, Fraction (221): %d, PV: %d\n",
                $sale->getDate()->format('d/m/Y'),
                $sale->getCurrency()->value,
                $sale->getSaleAmount(),
                $sale->getFeeAmount(),
                $sale->getPortfolioTotalAmount(),
                $sale->getTotalBought(),
                $sale->getTotalSold(),
                round($sale->getPnl(), 0)
            );
        }
 
        $pnlByYear = $this->calculateTotalPnlByYear();

        $output .= "\n\nPlus ou moins value totale / an :\n";
       
        foreach ($pnlByYear as $year => $pnl) {
            $output .= sprintf("  Année %s: %d (soit %d d'impots)\n", $year, $pnl, $pnl * 0.3);
        }

        return $output;
    }

    private function calculateTotalPnlByYear(): array
    {
        $totalPnl = [];
        foreach ($this->calculatorResults->getSales() as $sale) {
            if (!isset($totalPnl[$sale->getDate()->format('Y')])) {
                $totalPnl[$sale->getDate()->format('Y')] = 0;
            }
            $totalPnl[$sale->getDate()->format('Y')] += round($sale->getPnl(), 0);
        }
        return $totalPnl;
    }
}
