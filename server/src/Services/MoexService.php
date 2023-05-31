<?php

namespace App\Services;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

class MoexService
{
    private HttpClientInterface $client;
    private string $basePath = 'https://iss.moex.com';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    // возвращает массив: ключ - это ticker, значение - цена ticker
    public function getTickers(): array
    {
        $path = '/iss/engines/stock/markets/shares/boards/TQBR/securities';
        $parameters['query'] = [
            'iss.meta' => 'off',
            'iss.only' => 'marketdata',
            'marketdata.columns' => 'SECID,LAST',
        ];

        try {
            $response = $this->sendRequest($path, $parameters, 'json');
            $content = $response->toArray();
        } catch (Throwable $err) {
            return [];
        }

        if (empty($content['marketdata']['data'])) {
            return [];
        }
        $result = [];
        foreach ($content['marketdata']['data'] as $tickerInfo) {
            $result[$tickerInfo[0]] = $tickerInfo[1];
        }
        return $result;
    }

    private function sendRequest(string $path, array $parameters, string $format = ''): ResponseInterface
    {
        $response = $this->client->request('GET', $this->createUrl($this->basePath, $path, $format), $parameters);

        $statusCode = $response->getStatusCode();
        if ($statusCode !== Response::HTTP_OK) {
            throw new Exception('Не получены данные от MOEX');
        }
        return $response;
    }

    private function createUrl(string $basePath, string $path, string $format): string
    {
        if (substr($basePath, -1) === '/') {
            $basePath = substr($basePath, 0, -1);
        }
        if (substr($path, 0) !== '/') {
            $path = '/' . $path;
        }
        return $basePath . $path . ($format ? ".$format" : '');
    }

}