<?php

declare(strict_types=1);

/**
 * Front controller / application entry point.
 *
 * All HTTP requests are routed through this single file.
 * Configure your web server (or PHP built-in server) to use
 * the "public" directory as the document root.
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Core\App;

$app = App::bootstrap(dirname(__DIR__));
$app->run();
