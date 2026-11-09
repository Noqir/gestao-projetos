<?php

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(private TaskService $tasks) {}

    public function create(Request $request, Project $project): View
    {
        $this->authorize('createTask', $project);

        return view('tasks.create', [
            'project' => $project->load('members:id,name'),
            'task' => new Task(['status' => $request->query('status', TaskStatus::Todo->value)]),
            'priorities' => TaskPriority::options(),
            'statuses' => TaskStatus::options(),
        ]);
    }

    public function store(StoreTaskRequest $request, Project $project): RedirectResponse
    {
        $task = $this->tasks->create($project, $request->user(), $request->validated());

        return redirect()->route('projects.show', $project)
            ->with('success', "Tarefa “{$task->title}” criada.");
    }

    public function show(Project $project, Task $task): View
    {
        $this->authorize('view', $task);

        return view('tasks.show', ['project' => $project, 'task' => $this->tasks->details($task)]);
    }

    public function edit(Project $project, Task $task): View
    {
        $this->authorize('update', $task);

        return view('tasks.edit', [
            'project' => $project->load('members:id,name'),
            'task' => $task,
            'priorities' => TaskPriority::options(),
            'statuses' => TaskStatus::options(),
        ]);
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task): RedirectResponse
    {
        $this->tasks->update($task, $request->user(), $request->validated());

        return redirect()->route('projects.tasks.show', [$project, $task])->with('success', 'Tarefa atualizada.');
    }

    public function destroy(Request $request, Project $project, Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);
        $this->tasks->delete($task, $request->user());

        return redirect()->route('projects.show', $project)->with('success', "Tarefa “{$task->title}” excluída.");
    }

    /**
     * PATCH /tasks/{task}/status: chamado pelo arrastar e soltar (JSON)
     * ou pelo seletor de status da página da tarefa (redirect).
     */
    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): JsonResponse|RedirectResponse
    {
        $status = TaskStatus::from($request->validated('status'));
        $task = $this->tasks->moveTo($task, $request->user(), $status, $request->validated('position'));
        $message = "“{$task->title}” movida para {$status->label()}.";

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'task' => [
                    'id' => $task->id,
                    'status' => $task->status->value,
                    'status_label' => $task->status->label(),
                    'position' => $task->position,
                ],
            ]);
        }

        return back()->with('success', $message);
    }
}
