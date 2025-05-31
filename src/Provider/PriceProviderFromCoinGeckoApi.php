<?php

namespace App\Provider;

use Exception;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use App\Entities\Enums\Currency;

class PriceProviderFromCoinGeckoApi implements PriceProviderInterface
{
    use FallbackPriceProviderTrait;

    private array $tickers = [
        'btc' => 'bitcoin',
        'eth' => 'ethereum',
        'xrp' => 'ripple',
        'ltc' => 'litecoin',
        'sol' => 'solana',
        'doge' => 'dogecoin',
        'usdt' => 'tether',
        'usdc' => 'usd-coin',
        'sand' => 'the-sandbox',
        'algo' => 'algorand',

        // Add more tickers as needed (see https://api.coingecko.com/api/v3/coins/list)
    ];
    private ClientInterface $client;

    private array $prices = [];

    public function __construct(?ClientInterface $client = null)
    {
        // If a client is provided, it can be used for making API requests.
        // Otherwise, you can use a default client or throw an exception.
        if ($client === null) {
            $client = new \GuzzleHttp\Client(['verify' => false]); // Disable SSL verification for testing purposes
        }

        $this->client = $client;

        print("Powered by CoinGecko API\n");
    }

    public function getPrice(Currency $currency, \DateTimeImmutable $date): float
    {
        if (array_key_exists($currency->value, $this->prices) && array_key_exists($date->format('Y-m-d'), $this->prices[$currency->value])) {
            return $this->prices[$currency->value][$date->format('Y-m-d')];
        }

        $dateInString = $date->format('d-m-Y');
        $id = $this->tickers[strtolower($currency->value)] ?? null;
        $price = null;

        if ($id === null) {
            throw new \InvalidArgumentException(sprintf('Ticker for currency "%s" not found.', $currency->value));
        }

        $request = new Request('GET', 'https://api.coingecko.com/api/v3/coins/' . $id . '/history?date=' . $dateInString . '&localization=fr', [
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);

        try {
            $response = $this->client->sendRequest($request);

            if ($response->getStatusCode() === 429) {
                throw new \RuntimeException(sprintf('Rate limit reached for currency "%s" on date "%s", try again in 1 min.', $currency->value, $dateInString));
            }

            $coin = json_decode($response->getBody()->getContents(), true);

            if (!isset($coin['market_data']['current_price']['eur'])) {
                throw new \RuntimeException(sprintf('Price for currency "%s" not found on date "%s".', $currency->value, $dateInString));
            }

            $price = (float)$coin['market_data']['current_price']['eur'];

            $this->prices[$currency->value][$date->format('Y-m-d')] = $price;
        } catch (\Exception $e) {
            printf("Error fetching price for currency '%s' on date '%s': %s\n", $currency->value, $dateInString, $e->getMessage());
            return $this->getPriceFromFallBackProvider($currency, $date);
        }

        return (float)$price;
    }

    private function getPriceFromFallBackProvider(Currency $currency, \DateTimeImmutable $date): float
    {
        $dateInString = $date->format('Y-m-d');
        
        if ($this->fallbackProvider === null) {
            throw new \InvalidArgumentException(sprintf('Price for currency "%s" not found on date "%s".', $currency->value, $dateInString));
        }

        $price = $this->fallbackProvider->getPrice($currency, $date);
        $this->prices[$currency][$dateInString] = $price;

        return $price;
    }
}
