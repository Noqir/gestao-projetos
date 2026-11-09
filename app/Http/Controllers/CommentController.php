<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Task $task, TaskService $tasks): RedirectResponse
    {
        $tasks->comment($task, $request->user(), $request->validated('body'));

        return redirect()
            ->to(route('projects.tasks.show', [$task->project_id, $task]).'#comentarios')
            ->with('success', 'Comentário publicado.');
    }
}
