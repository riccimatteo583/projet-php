<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

/**
 * Handles the landing and health-check endpoints.
 */
final class HomeController extends Controller
{
    /**
     * @param array<string, string> $params
     */
    public function index(Request $request, array $params): Response
    {
        return $this->ok([
            'application' => 'projet-php',
            'description' => 'An advanced PHP MVC REST API skeleton.',
            'version'     => '1.0.0',
            'endpoints'   => [
                'GET    /health',
                'GET    /tasks',
                'GET    /tasks/{id}',
                'POST   /tasks',
                'PUT    /tasks/{id}',
                'DELETE /tasks/{id}',
            ],
        ]);
    }

    /**
     * @param array<string, string> $params
     */
    public function health(Request $request, array $params): Response
    {
        return $this->ok([
            'status'    => 'ok',
            'timestamp' => gmdate('c'),
        ]);
    }
}
