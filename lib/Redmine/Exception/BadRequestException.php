<?php

namespace Redmine\Exception;

use Redmine\Response\ApiResponse;

class BadRequestException extends \RuntimeException
{
    protected $response;

    public function __construct($message, $code = 0, $previous = null, ApiResponse $response = null)
    {
        $this->response = $response;

        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): ApiResponse
    {
        return $this->response;
    }
}
