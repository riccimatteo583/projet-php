<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Thrown when request input fails validation. Maps to HTTP 422.
 */
final class ValidationException extends HttpException
{
    /**
     * @param array<string, array<int, string>> $errors
     */
    public function __construct(private readonly array $errors)
    {
        parent::__construct(422, 'The given data was invalid.');
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
