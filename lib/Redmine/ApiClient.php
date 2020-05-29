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

    public function getBaseUrl(): string
    {
        return $this->client->getBaseUrl();
    }

    public function requestGet(string $path, ?array $queryParameters = []): ApiResponse
    {
        return $this->client->makeRequest($path . '.json', [
            'query' => $queryParameters,
        ]);
    }

    public function requestDelete(string $path, ?array $queryParameters = []): ApiResponse
    {
        return $this->client->makeRequest($path . '.json', [
            'method' => Client::METHOD_DELETE,
            'query' => $queryParameters,
        ]);
    }

    public function requestPost(string $path, array $postData = [], array $queryParameters = []): ApiResponse
    {
        return $this->client->makeRequest($path . '.json', [
            'method' => Client::METHOD_POST,
            'postFields' => json_encode($postData),
            'query' => $queryParameters,
        ]);
    }

    public function requestPut(string $path, array $putData = [], array $queryParameters = []): ApiResponse
    {
        return $this->client->makeRequest($path . '.json', [
            'method' => Client::METHOD_PUT,
            'postFields' => json_encode($putData),
            'query' => $queryParameters,
        ]);
    }
}
