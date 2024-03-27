<?php

namespace Redmine;

use Redmine\Response\ApiResponse;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Redmine\Exception\BadRequestException;
use Redmine\Exception\InternalServerErrorException;

class ApiClient
{
    protected ClientInterface $client;
    protected RequestFactoryInterface $requestFactory;
    protected StreamFactoryInterface $streamFactory;
    protected string $url;
    protected string $apiKey;

    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        string $url,
        string $apiKey
    ) {
        if ('/' !== $url[\strlen($url) - 1]) {
            $url .= '/';
        }

        $this->url = $url;
        $this->apiKey = $apiKey;

        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }

    public function getBaseUrl(): string
    {
        return $this->url;
    }

    public function requestGet(string $path, ?array $queryParameters = []): ApiResponse
    {
        return $this->makeRequest(
            $this->baseRequest(
                'GET',
                $path,
                $queryParameters
            )
        );
    }

    public function requestDelete(string $path, ?array $queryParameters = []): ApiResponse
    {
        return $this->makeRequest(
            $this->baseRequest(
                'DELETE',
                $path,
                $queryParameters
            )
        );
    }

    public function requestPost(string $path, array $postData = [], array $queryParameters = []): ApiResponse
    {
        return $this->makeRequest(
            $this->baseRequest(
                'POST',
                $path,
                $queryParameters
            )->withBody(
                $this->streamFactory->createStream(json_encode($postData))
            )
        );
    }

    public function requestPut(string $path, array $putData = [], array $queryParameters = []): ApiResponse
    {
        return $this->makeRequest(
            $this->baseRequest(
                'PUT',
                $path,
                $queryParameters
            )->withBody(
                $this->streamFactory->createStream(json_encode($putData))
            )
        );
    }

    /** @param array<string, string> $queryParameters */
    protected function baseRequest(
        string $method,
        string $path,
        array $queryParameters = []
    ): RequestInterface {
        $uri = $this->url . $path .  '.json';
        if (\count($queryParameters) > 0) {
            $uri .= '?' . http_build_query($queryParameters, '', '&');
        }

        $request = $this->requestFactory
            ->createRequest($method, $uri)
            ->withHeader('Content-type', 'application/json')
            ->withHeader('X-Redmine-API-Key', $this->apiKey)
        ;

        return $request;
    }

    protected function makeRequest(RequestInterface $request): ApiResponse
    {
        $response = $this->client->sendRequest($request);
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 500) {
            throw new InternalServerErrorException(
                sprintf('Error in redmine api on %s %s', $request->getMethod(), $request->getUri()),
                $statusCode
            );
        }

        $responseBody = $response->getBody()->getContents();
        if ($statusCode >= 400 && $statusCode < 500) {
            throw new BadRequestException(
                new ApiResponse(
                    $statusCode,
                    $responseBody
                ),
                sprintf('Error in redmine api request %s %s: %s', $request->getMethod(), $request->getUri(), $responseBody),
                $statusCode
            );
        }

        return new ApiResponse($statusCode, $responseBody);
    }
}
