<?php

declare(strict_types=1);

namespace App\Models;

/**
 * A plain data object representing a single task entity.
 */
final class Task
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly bool $completed,
        public readonly string $createdAt,
    ) {
    }

    /**
     * Rehydrate a Task from a database row.
     *
     * @param array<string, mixed> $row
     */
    public static function fromArray(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            title: (string) $row['title'],
            completed: (bool) $row['completed'],
            createdAt: (string) $row['created_at'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'completed'  => $this->completed,
            'created_at' => $this->createdAt,
        ];
    }
}
