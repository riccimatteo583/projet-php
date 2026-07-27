<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Exceptions\HttpException;
use App\Models\Task;
use App\Repositories\TaskRepository;
use App\Support\Validator;

/**
 * RESTful controller exposing CRUD operations for tasks.
 */
final class TaskController extends Controller
{
    public function __construct(private readonly TaskRepository $tasks)
    {
    }

    /**
     * @param array<string, string> $params
     */
    public function index(Request $request, array $params): Response
    {
        $tasks = $this->tasks->all();

        return $this->ok(array_map(static fn (Task $task): array => $task->toArray(), $tasks));
    }

    /**
     * @param array<string, string> $params
     */
    public function show(Request $request, array $params): Response
    {
        $task = $this->tasks->find((int) $params['id']);

        if ($task === null) {
            throw new HttpException(404, 'Task not found.');
        }

        return $this->ok($task->toArray());
    }

    /**
     * @param array<string, string> $params
     */
    public function store(Request $request, array $params): Response
    {
        $data = Validator::make($request->all(), [
            'title'     => 'required|string|max:255',
            'completed' => 'boolean',
        ]);

        $task = $this->tasks->create(
            title: (string) $data['title'],
            completed: (bool) ($data['completed'] ?? false),
        );

        return $this->created($task->toArray());
    }

    /**
     * @param array<string, string> $params
     */
    public function update(Request $request, array $params): Response
    {
        $task = $this->tasks->find((int) $params['id']);

        if ($task === null) {
            throw new HttpException(404, 'Task not found.');
        }

        $data = Validator::make($request->all(), [
            'title'     => 'required|string|max:255',
            'completed' => 'boolean',
        ]);

        $updated = $this->tasks->update(
            id: $task->id,
            title: (string) $data['title'],
            completed: (bool) ($data['completed'] ?? false),
        );

        return $this->ok($updated->toArray());
    }

    /**
     * @param array<string, string> $params
     */
    public function destroy(Request $request, array $params): Response
    {
        $task = $this->tasks->find((int) $params['id']);

        if ($task === null) {
            throw new HttpException(404, 'Task not found.');
        }

        $this->tasks->delete($task->id);

        return $this->noContent();
    }
}
