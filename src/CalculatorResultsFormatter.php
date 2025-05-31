<?php

namespace App;

use App\Entities\CalculatorResults;

class CalculatorResultsFormatter
{
    private const NEGLIGIBLE_AMOUNT = 0.0001;
    private CalculatorResults $calculator;

    public function __construct(CalculatorResults $calculator)
    {
        $this->calculator = $calculator;
    }

    public function format(): string
    {
        $output = "Positions actuelles :\n";
        foreach ($this->calculator->getPositions() as $position) {
            if ($position->getAmount() < self::NEGLIGIBLE_AMOUNT) {
                continue;
            }

            $output .= sprintf(
                "  %s = %.8f\n",
                $position->getCurrency()->value,
                $position->getAmount()
            );
        }

        $output .= sprintf("\n\n%d Cessions :\n", count($this->calculator->getCessions()));
        foreach ($this->calculator->getCessions() as $cession) {
            $output .= sprintf(
                "Le %s, %s prix (213): %d, frais (214): %d, Valo ptf (212): %d, Total acq (220): %d, Fraction (221): %d, PV: %d\n",
                $cession->getDate()->format('d/m/Y'),
                $cession->getCurrency()->value,
                $cession->getCessionAmount(),
                $cession->getFeeAmount(),
                $cession->getPortfolioTotalAmount(),
                $cession->getTotalBought(),
                $cession->getTotalSold(),
                round($cession->getPnl(), 0)
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
        foreach ($this->calculator->getCessions() as $cession) {
            if (!isset($totalPnl[$cession->getDate()->format('Y')])) {
                $totalPnl[$cession->getDate()->format('Y')] = 0;
            }
            $totalPnl[$cession->getDate()->format('Y')] += round($cession->getPnl(), 0);
        }
        return $totalPnl;
    }
}
