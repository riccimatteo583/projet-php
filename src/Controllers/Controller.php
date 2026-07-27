<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;

/**
 * Base controller providing small helpers shared by all controllers.
 */
abstract class Controller
{
    /**
     * @param mixed $data
     */
    protected function ok(mixed $data): Response
    {
        return Response::json($data, 200);
    }

    /**
     * @param mixed $data
     */
    protected function created(mixed $data): Response
    {
        return Response::json($data, 201);
    }

    protected function noContent(): Response
    {
        return Response::noContent();
    }
}
