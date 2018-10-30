<?php

namespace Redmine\Http;

use Redmine\Exception\BadRequestException;
use Redmine\Exception\CurlException;
use Redmine\Exception\InternalServerErrorException;
use Redmine\Response\ApiResponse;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Client
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    protected $defaultHeaders;
    protected $url;

    public function __construct(string $url, array $defaultHeaders = [])
    {
        $this->url = $url;
        $this->defaultHeaders = $defaultHeaders;
    }

    public function makeRequest(string $path, array $options = []): ApiResponse
    {
        $optionsResolver = new OptionsResolver();
        $this->configureRequestOptions($optionsResolver);

        $options = $optionsResolver->resolve($options);

        $url = $this->url . $path;
        if (\count($options['query'])) {
            $url .= '?' . http_build_query($options['query'], '', '&');
        }

        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_URL, $url);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curlHandler, CURLOPT_FAILONERROR, false);
        curl_setopt($curlHandler, CURLOPT_TIMEOUT, $options['timeout']);
        curl_setopt($curlHandler, CURLOPT_CONNECTTIMEOUT, 30);
        if (!empty($options['headers'])) {
            curl_setopt($curlHandler, CURLOPT_HTTPHEADER, $options['headers']);
        }

        if (self::METHOD_POST === $options['method']) {
            curl_setopt($curlHandler, CURLOPT_POST, true);
            curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $options['postFields']);
        }

        $responseBody = curl_exec($curlHandler);
        $statusCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
        $errno = curl_errno($curlHandler);
        $error = curl_error($curlHandler);

        curl_close($curlHandler);

        if ($errno) {
            throw new CurlException($error, $errno);
        }

        if ($statusCode >= 500) {
            throw new InternalServerErrorException('Error in redmine api.', $statusCode);
        }

        if ($statusCode >= 400 && $statusCode < 500) {
            throw new BadRequestException('Error in billing api request: ' . $responseBody, $statusCode);
        }

        return new ApiResponse($statusCode, $responseBody);
    }

    protected function configureRequestOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired([
                'method',
                'timeout'
            ])
            ->setDefined([
                'headers',
                'query',
                'postFields',
                'cacheLifetime'
            ])
            ->setAllowedValues('method', [static::METHOD_POST, static::METHOD_GET])
            ->setAllowedTypes('timeout', 'int')
            ->setAllowedTypes('headers', 'array')
            ->setAllowedTypes('query', 'array')
            ->setAllowedTypes('postFields', ['array', 'string'])
            ->setNormalizer('headers', function (Options $options, $value) {
                return array_merge($this->defaultHeaders, $value);
            })
            ->setDefaults([
                'headers'       => [],
                'method'        => static::METHOD_GET,
                'query'         => [],
                'postFields'    => [],
                'timeout'       => 30,
            ])
        ;
    }
}
