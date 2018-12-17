<?php

namespace Redmine\Response;

use Redmine\Exception\InvalidJsonException;

/**
 * Response from billing
 */
class ApiResponse implements \ArrayAccess
{
    // HTTP response status code
    protected $statusCode;

    // response assoc array
    protected $response;

    public function __construct(int $statusCode, ?string $responseBody = null)
    {
        $this->statusCode = $statusCode;

        if (null !== $responseBody && '' !== $responseBody) {
            $response = json_decode($responseBody, true);

            if (!$response && JSON_ERROR_NONE !== ($error = json_last_error())) {
                throw new InvalidJsonException(
                    "Invalid JSON in the API response body:\n\n$responseBody",
                    $error
                );
            }

            $this->response = $response;
        }
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode < 400;
    }

    public function __get($name)
    {
        if (!isset($this->response[$name])) {
            throw new \InvalidArgumentException("Property \"$name\" not found");
        }

        return $this->response[$name];
    }

    public function __isset($name): bool
    {
        return isset($this->response[$name]);
    }

    public function __set($name, $value)
    {
        throw new \BadMethodCallException('This call not allowed');
    }

    public function offsetSet($offset, $value): void
    {
        throw new \BadMethodCallException('This activity not allowed');
    }

    public function offsetUnset($offset): void
    {
        throw new \BadMethodCallException('This call not allowed');
    }

    public function offsetExists($offset): bool
    {
        return isset($this->response[$offset]);
    }

    public function offsetGet($offset)
    {
        if (!isset($this->response[$offset])) {
            throw new \InvalidArgumentException("Property \"$offset\" not found");
        }

        return $this->response[$offset];
    }
}
