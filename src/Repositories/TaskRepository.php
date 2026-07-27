<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Task;

/**
 * Data access layer for the Task entity.
 *
 * All queries use prepared statements to guard against SQL injection.
 */
final class TaskRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    /**
     * @return array<int, Task>
     */
    public function all(): array
    {
        $rows = $this->db->select('SELECT * FROM tasks ORDER BY id ASC');

        return array_map(static fn (array $row): Task => Task::fromArray($row), $rows);
    }

    public function find(int $id): ?Task
    {
        $rows = $this->db->select('SELECT * FROM tasks WHERE id = :id', ['id' => $id]);

        return $rows === [] ? null : Task::fromArray($rows[0]);
    }

    public function create(string $title, bool $completed = false): Task
    {
        $this->db->statement(
            'INSERT INTO tasks (title, completed) VALUES (:title, :completed)',
            ['title' => $title, 'completed' => $completed ? 1 : 0],
        );

        return $this->find($this->db->lastInsertId());
    }

    public function update(int $id, string $title, bool $completed): Task
    {
        $this->db->statement(
            'UPDATE tasks SET title = :title, completed = :completed WHERE id = :id',
            ['id' => $id, 'title' => $title, 'completed' => $completed ? 1 : 0],
        );

        return $this->find($id);
    }

    public function delete(int $id): void
    {
        $this->db->statement('DELETE FROM tasks WHERE id = :id', ['id' => $id]);
    }
}
