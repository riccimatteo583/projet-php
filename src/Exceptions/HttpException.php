<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * Represents an error that maps directly to an HTTP status code.
 */
class HttpException extends RuntimeException
{
    public function __construct(
        private readonly int $statusCode,
        string $message = '',
    ) {
        parent::__construct($message);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }
}
