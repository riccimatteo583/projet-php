<?php

declare(strict_types=1);

/**
 * Database configuration.
 *
 * By default the application uses a file-based SQLite database
 * stored in the "storage" directory, which keeps the project
 * self-contained and easy to run locally. To use MySQL/MariaDB
 * or PostgreSQL, provide the appropriate DSN, username and
 * password below (values can also be read from environment
 * variables).
 */

$storage = dirname(__DIR__) . '/storage';

if (!is_dir($storage)) {
    @mkdir($storage, 0o775, true);
}

return [
    'dsn'      => getenv('DB_DSN') ?: 'sqlite:' . $storage . '/database.sqlite',
    'username' => getenv('DB_USERNAME') ?: null,
    'password' => getenv('DB_PASSWORD') ?: null,
];
