<?php

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projects) {}

    public function index(Request $request): View
    {
        $archived = $request->query('filter') === 'archived';

        return view('projects.index', [
            'projects' => $this->projects->listFor($request->user(), $archived),
            'counts' => $this->projects->countsFor($request->user()),
            'showingArchived' => $archived,
        ]);
    }

    public function create(): View
    {
        return view('projects.create', ['project' => new Project]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = $this->projects->create($request->user(), $request->validated());

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projeto criado! Agora adicione tarefas e convide a equipe.');
    }

    /** O quadro Kanban do projeto. */
    public function show(Request $request, Project $project, TaskService $tasks): View
    {
        $this->authorize('view', $project);

        $filters = $request->only(['assignee', 'priority', 'q']);
        $project->load('members:id,name');

        return view('projects.show', [
            'project' => $project,
            'columns' => $tasks->board($project, $filters),
            'filters' => $filters,
            'statuses' => TaskStatus::cases(),
            'priorities' => TaskPriority::options(),
        ]);
    }

    public function edit(Project $project): View
    {
        $this->authorize('update', $project);

        return view('projects.edit', ['project' => $project]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->projects->update($project, $request->user(), $request->validated());

        return redirect()->route('projects.show', $project)->with('success', 'Projeto atualizado.');
    }

    public function archive(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('archive', $project);
        $this->projects->setArchived($project, $request->user(), true);

        return redirect()->route('projects.index')->with('success', "Projeto “{$project->name}” arquivado.");
    }

    public function restore(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('archive', $project);
        $this->projects->setArchived($project, $request->user(), false);

        return redirect()->route('projects.show', $project)->with('success', 'Projeto reativado.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);
        $this->projects->delete($project);

        return redirect()->route('projects.index')->with('success', "Projeto “{$project->name}” excluído.");
    }
}
