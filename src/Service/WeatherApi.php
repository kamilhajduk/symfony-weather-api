<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class WeatherApi
{
    private HttpClientInterface $client;
    private ParameterBagInterface $params;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params)
    {
        $this->client = $client;
        $this->params = $params;
    }

    public function getAverageTempBulk(string $date, array $cities): float
    {
        // Inicializacja zmiennych
        $temperatureSum = 0;
        $temperatureCount = 0;

        // Pobieramy klucz do API ze zmiennych środowiskowych
        $apiKey = $this->params->get('app.api_key');
        if (!$apiKey) {
            throw new \RuntimeException('API Key not found.');
        }

        // Parsujemy string do DateTime w celu walidacji
        $dateTime = \DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateTime) {
            throw new \InvalidArgumentException('Invalid date format, expected Y-m-d.');
        }
        if ($dateTime > new \DateTime()) {
            throw new \InvalidArgumentException('Invalid date, expected date from past.');
        }

        // Przygotowanie tablicy z miastami
        $locations = array_map(fn ($city) => ['q' => $city], $cities);
        if (empty($locations)) {
            throw new \InvalidArgumentException('Invalid cities, expected string array');
        }

        // Request HTTP do Weather API po dane historyczne
        $response = $this->client->request(
            'POST',
            'http://api.weatherapi.com/v1/history.json',
            [
                'query' => [
                    'key' => $apiKey,
                    'q' => 'bulk',
                    'dt' => $dateTime->format('Y-m-d')
                ],
                'json' => ['locations' => $locations]
            ]
        );

        // Konwersja JSON na tablice
        $responseArray = $response->toArray();
        if (!isset($responseArray['bulk']) || !is_array($responseArray['bulk'])) {
            throw new \RuntimeException('Unexpected response structure from API.');
        }

        // Dla każdego miasta pobieramy średnią temperature w ciągu dnia
        foreach ($responseArray['bulk'] as $data) {
            $avgTemp = $data['query']['forecast']['forecastday'][0]['day']['avgtemp_c'] ?? null;

            if (!is_null($avgTemp)) {
                $temperatureSum += $avgTemp;
                $temperatureCount++;
            }
        }

        if ($temperatureCount === 0) {
            throw new \RuntimeException('No valid temperature data found.');
        }

        // Zwracamy średnią arytmetyczną
        return round($temperatureSum / $temperatureCount, 1);
    }
}
