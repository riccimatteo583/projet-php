<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\HomeController;
use App\Controllers\TaskController;

/**
 * The core application container.
 *
 * Wires together the router, request and a tiny service container,
 * registers the application routes and dispatches the incoming request.
 */
final class App
{
    private Router $router;
    private Container $container;
    private string $basePath;

    private function __construct(string $basePath)
    {
        $this->basePath  = $basePath;
        $this->container = new Container();
        $this->router    = new Router();
    }

    /**
     * Build and configure the application.
     */
    public static function bootstrap(string $basePath): self
    {
        $app = new self($basePath);
        $app->registerServices();
        $app->registerRoutes();

        return $app;
    }

    /**
     * Register shared services in the container.
     */
    private function registerServices(): void
    {
        $this->container->bind(Database::class, function (): Database {
            $config = require $this->basePath . '/config/database.php';

            return new Database($config);
        });
    }

    /**
     * Map HTTP verbs and paths to controller actions.
     */
    private function registerRoutes(): void
    {
        $this->router->get('/', [HomeController::class, 'index']);
        $this->router->get('/health', [HomeController::class, 'health']);

        $this->router->get('/tasks', [TaskController::class, 'index']);
        $this->router->get('/tasks/{id}', [TaskController::class, 'show']);
        $this->router->post('/tasks', [TaskController::class, 'store']);
        $this->router->put('/tasks/{id}', [TaskController::class, 'update']);
        $this->router->delete('/tasks/{id}', [TaskController::class, 'destroy']);
    }

    /**
     * Handle the current request and emit the response.
     */
    public function run(): void
    {
        $request  = Request::capture();
        $response = $this->router->dispatch($request, $this->container);
        $response->send();
    }
}
