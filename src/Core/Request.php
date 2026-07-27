<?php

declare(strict_types=1);

namespace App\Core;

/**
 * An immutable value object describing the incoming HTTP request.
 */
final class Request
{
    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $body
     * @param array<string, string> $headers
     */
    private function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $query,
        private readonly array $body,
        private readonly array $headers,
    ) {
    }

    /**
     * Build a Request instance from PHP superglobals.
     */
    public static function capture(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $path   = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
        $path   = '/' . trim($path, '/');

        $body = [];
        $raw  = file_get_contents('php://input') ?: '';

        if ($raw !== '') {
            $decoded = json_decode($raw, true);
            $body    = is_array($decoded) ? $decoded : $_POST;
        } else {
            $body = $_POST;
        }

        return new self($method, $path, $_GET, $body, self::readHeaders());
    }

    /**
     * @return array<string, string>
     */
    private static function readHeaders(): array
    {
        if (function_exists('getallheaders')) {
            return array_change_key_case(getallheaders(), CASE_LOWER);
        }

        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$name] = (string) $value;
            }
        }

        return $headers;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->body;
    }

    public function header(string $key, ?string $default = null): ?string
    {
        return $this->headers[strtolower($key)] ?? $default;
    }
}
