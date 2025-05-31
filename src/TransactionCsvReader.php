<?php

namespace App;

use App\Entities\Enums\Currency;
use App\Entities\Transaction;
use App\Entities\Enums\TransactionType;

class TransactionCsvReader
{
    public function readTransactionsFromCsv(string $filename, string $delimiter = ','): array
    {
        $transactions = [];
        if (!file_exists($filename) || !is_readable($filename)) {
            return $transactions;
        }

        $handle = fopen($filename, 'r');
        if ($handle === false) {
            throw new \RuntimeException("Could not open file: $filename");
        }

        $header = null;
        while (($row = fgetcsv($handle, 1000, $delimiter, '"', "\\")) !== false) {
            if (!$header) {
                $header = $row;
                continue;
            }
            $data = array_combine($header, $row);
            $transactions[] = $this->convertToTransaction($data);
        }
        fclose($handle);

        return $transactions;
    }

    private function convertToTransaction(array $data)
    {
        if (!isset($data['description']) || !isset($data['date'])) {
            throw new \InvalidArgumentException('Missing required fields in transaction data.');
        }

        $type = TransactionType::tryFrom($data['description'] ?? '');
        if ($type === null) {
            throw new \InvalidArgumentException('Invalid transaction type: ' . ($data['description'] ?? ''));
        }

        return new Transaction(
            type: $type,
            date: \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, substr($data['date'], 0, 19) . '+00:00', new \DateTimeZone('UTC')),
            received_amount: isset($data['received_amount']) ? (float)$data['received_amount'] : 0.0,
            received_currency: $this->convertToCurrency($data['received_currency']),
            sent_amount: isset($data['sent_amount']) ? (float)$data['sent_amount'] : 0.0,
            sent_currency: $this->convertToCurrency($data['sent_currency']),
            fee_amount: isset($data['fee_amount']) ? (float)$data['fee_amount'] : 0.0,
            fee_currency: $this->convertToCurrency($data['fee_currency'])
        );
        /*
        $this->type = $data['type'] ?? null;
        $this->address = $data['address'] ?? null;
        $this->transaction_hash = $data['transaction_hash'] ?? null;
        $this->external_id = $data['external_id'] ?? null;
        */
    }

    private function convertToCurrency(string $currencyAsString): Currency
    {
        $currencyAsString = strtoupper($currencyAsString);
        $currency = Currency::tryFrom($currencyAsString);
        if ($currency === null) {
            throw new \InvalidArgumentException("Invalid currency: $currency");
        }
        return $currency;
    }
}
