<?php

namespace Redmine\Exception;

use Redmine\Response\ApiResponse;

class BadRequestException extends \RuntimeException
{
    protected ApiResponse $response;

    public function __construct(
        ApiResponse $response,
        string $message, int $code = 0, \Throwable $previous = null
    ) {
        $this->response = $response;

        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): ApiResponse
    {
        return $this->response;
    }
}
