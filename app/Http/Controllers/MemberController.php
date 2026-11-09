<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(private ProjectService $projects) {}

    public function index(Project $project): View
    {
        $this->authorize('view', $project);

        return view('projects.members', [
            'project' => $project->load('members:id,name'),
            'members' => $this->projects->membersOf($project),
        ]);
    }

    public function store(StoreMemberRequest $request, Project $project): RedirectResponse
    {
        $user = $this->projects->addMember($project, $request->user(), $request->validated('email'));

        return back()->with('success', "{$user->name} agora faz parte do projeto.");
    }

    public function destroy(Request $request, Project $project, User $user): RedirectResponse
    {
        $this->authorize('manageMembers', $project);
        $this->projects->removeMember($project, $request->user(), $user);

        return back()->with('success', "{$user->name} foi removido do projeto.");
    }
}
