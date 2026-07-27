<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\HttpException;

/**
 * A small regex based HTTP router.
 *
 * Routes may contain named placeholders such as "{id}" which are
 * extracted from the URI and passed to the controller action as
 * an associative array of parameters.
 */
final class Router
{
    /** @var array<string, array<int, array{pattern: string, handler: array, params: array<int, string>}>> */
    private array $routes = [
        'GET'    => [],
        'POST'   => [],
        'PUT'    => [],
        'PATCH'  => [],
        'DELETE' => [],
    ];

    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function put(string $path, array $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    public function patch(string $path, array $handler): void
    {
        $this->add('PATCH', $path, $handler);
    }

    public function delete(string $path, array $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    private function add(string $method, string $path, array $handler): void
    {
        $params  = [];
        $pattern = preg_replace_callback('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', static function (array $m) use (&$params): string {
            $params[] = $m[1];

            return '([^/]+)';
        }, $path);

        $this->routes[$method][] = [
            'pattern' => '#^' . $pattern . '$#',
            'handler' => $handler,
            'params'  => $params,
        ];
    }

    /**
     * Match the request against the route table and invoke the handler.
     */
    public function dispatch(Request $request, Container $container): Response
    {
        $method = $request->method();
        $uri    = $request->path();

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches);
                $params = array_combine($route['params'], $matches) ?: [];

                [$class, $action] = $route['handler'];
                $controller = $container->make($class);

                return $controller->{$action}($request, $params);
            }
        }

        throw new HttpException(404, 'The requested resource was not found.');
    }
}
