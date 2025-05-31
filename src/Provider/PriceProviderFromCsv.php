<?php

namespace App\Provider;

use App\Entities\Enums\Currency;

class PriceProviderFromCsv implements PriceProviderInterface
{
    use FallbackPriceProviderTrait;

    private bool $isInitialized = false;
    private array $prices = [];
    private string $filename;
    private string $delimiter;

    /**
     * PriceProviderFromCsv constructor.
     * @param PriceProviderInterface|null $priceProvider Optional price provider for fallback or additional functionality.
     * @param string $filename Path to the CSV file containing currency prices.
     * @param string $delimiter Delimiter used in the CSV file, default is ','.
     */
    public function __construct(string $filename, string $delimiter = ',')
    {
        $this->filename = $filename;
        $this->delimiter = $delimiter;
        $this->isInitialized = false;
    }

    public function getPrice(Currency $currency, \DateTimeImmutable $date): float
    {
        $this->readPricesFromCsv();

        $dateInString = $date->format('Y-m-d');

        try {

            if (array_key_exists($currency->value, $this->prices) === false || array_key_exists($dateInString, $this->prices[$currency->value]) === false) {
                throw new \InvalidArgumentException(sprintf('Price for currency "%s" not found on date "%s".', $currency->value, $dateInString));
            }

            return $this->prices[$currency->value][$dateInString];
        } catch (\Exception) {
            return $this->getPriceFromFallBackProvider($currency, $date);
        }
    }

    private function getPriceFromFallBackProvider(Currency $currency, \DateTimeImmutable $date): float
    {
        $dateInString = $date->format('Y-m-d');

        if ($this->fallbackProvider === null) {
            throw new \InvalidArgumentException(sprintf('Price for currency "%s" not found on date "%s".', $currency->value, $dateInString));
        }

        $price = $this->fallbackProvider->getPrice($currency, $date);

        $this->savePriceToCsv($currency, $dateInString, $price);
        $this->prices[$currency->value][$dateInString] = $price;

        return $price;
    }

    private function savePriceToCsv(Currency $currency, string $date, float $price): void
    {
        $fp = fopen($this->filename, 'a');
        fputcsv($fp, [$currency->value, $date, $price], $this->delimiter, '"', "\\");
        fclose($fp);
    }

    private function readPricesFromCsv()
    {
        if ($this->isInitialized) {
            return; // Prices already loaded
        }
        $this->isInitialized = true;

        if (!file_exists($this->filename) || !is_readable($this->filename)) {
            $this->initPricesFile();
            return;
        }

        $handle = fopen($this->filename, 'r');
        if ($handle === false) {
            throw new \RuntimeException("Could not open file: {$this->filename}");
        }

        $header = null;
        while (($row = fgetcsv($handle, 1000, $this->delimiter, '"', "\\")) !== false) {
            if (!$header) {
                $header = $row;
                continue;
            }

            if (count($row) !== count($header)) {
                continue; // Skip rows that do not match header length
            }

            $data = array_combine($header, $row);

            if (!$data || !isset($data['currency'], $data['date'], $data['price'])) {
                continue; // Skip rows with missing data
            }

            if (!isset($this->prices[$data['currency']])) {
                $this->prices[$data['currency']] = [];
            }

            $this->prices[$data['currency']][$data['date']] = $data['price'];
        }
        fclose($handle);
    }

    private function initPricesFile(): void
    {
        $fp = fopen($this->filename, 'c');
        fputcsv($fp, ['currency', 'date', 'price'], $this->delimiter);
        fclose($fp);
    }
}
