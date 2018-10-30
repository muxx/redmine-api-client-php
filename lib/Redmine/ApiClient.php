<?php

namespace Redmine;

use Redmine\Http\Client;
use Redmine\Response\ApiResponse;

class ApiClient
{
    protected $client;

    public function __construct(string $url, string $apiKey)
    {
        if ('/' !== $url[\strlen($url) - 1]) {
            $url .= '/';
        }

        $this->client = new Client($url, ['X-Redmine-API-Key: ' . $apiKey]);
    }

    public function requestGet(string $path, ?array $queryParameters = []): ApiResponse
    {
        return $this->client->makeRequest($path . '.json', [
            'query' => $queryParameters,
        ]);
    }

    public function requestPost(string $path, array $postData = [], array $queryParameters = []): ApiResponse
    {
        return $this->client->makeRequest($path . 'json', [
            'method' => Client::METHOD_POST,
            'postFields' => json_encode($postData),
            'query' => $queryParameters,
        ]);
    }
}
