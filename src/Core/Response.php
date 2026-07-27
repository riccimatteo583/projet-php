<?php

declare(strict_types=1);

namespace App\Core;

/**
 * A simple HTTP response wrapper focused on JSON payloads.
 */
final class Response
{
    /**
     * @param mixed $payload
     * @param array<string, string> $headers
     */
    public function __construct(
        private mixed $payload = null,
        private int $status = 200,
        private array $headers = [],
    ) {
    }

    /**
     * Convenience constructor for a JSON response.
     *
     * @param mixed $payload
     */
    public static function json(mixed $payload, int $status = 200): self
    {
        return new self($payload, $status, ['Content-Type' => 'application/json; charset=utf-8']);
    }

    /**
     * Convenience constructor for an empty "204 No Content" response.
     */
    public static function noContent(): self
    {
        return new self(null, 204);
    }

    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Emit the status line, headers and body to the client.
     */
    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}", true);
        }

        if ($this->status === 204 || $this->payload === null) {
            return;
        }

        echo json_encode(
            $this->payload,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }
}
