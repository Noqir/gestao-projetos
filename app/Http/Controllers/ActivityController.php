<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function __invoke(Project $project, ProjectService $projects): View
    {
        $this->authorize('view', $project);

        return view('projects.activity', [
            'project' => $project->load('members:id,name'),
            'activities' => $projects->activityOf($project),
        ]);
    }
}
