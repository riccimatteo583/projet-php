<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Thin wrapper around PDO that lazily establishes the connection.
 *
 * The default configuration uses an SQLite database so the project
 * runs out of the box with no external database server required.
 */
final class Database
{
    private ?PDO $pdo = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(private readonly array $config)
    {
    }

    public function connection(): PDO
    {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }

        $dsn = (string) ($this->config['dsn'] ?? 'sqlite::memory:');

        $this->pdo = new PDO(
            $dsn,
            $this->config['username'] ?? null,
            $this->config['password'] ?? null,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ],
        );

        $this->migrate();

        return $this->pdo;
    }

    /**
     * Execute a prepared statement and return all rows.
     *
     * @param array<string, mixed> $bindings
     * @return array<int, array<string, mixed>>
     */
    public function select(string $sql, array $bindings = []): array
    {
        $statement = $this->connection()->prepare($sql);
        $statement->execute($bindings);

        return $statement->fetchAll();
    }

    /**
     * Execute a write statement and return the number of affected rows.
     *
     * @param array<string, mixed> $bindings
     */
    public function statement(string $sql, array $bindings = []): int
    {
        $statement = $this->connection()->prepare($sql);
        $statement->execute($bindings);

        return $statement->rowCount();
    }

    public function lastInsertId(): int
    {
        return (int) $this->connection()->lastInsertId();
    }

    /**
     * Create the schema if it does not yet exist.
     */
    private function migrate(): void
    {
        $this->pdo?->exec(
            'CREATE TABLE IF NOT EXISTS tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                completed INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL DEFAULT (datetime(\'now\'))
            )'
        );
    }
}
