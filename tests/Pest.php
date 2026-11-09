<?php

use App\Enums\ProjectRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/**
 * Cria um projeto com proprietário e, opcionalmente, membros.
 *
 * @return array{0: Project, 1: User, 2: User} [projeto, proprietário, membro]
 */
function projectWithTeam(array $attributes = []): array
{
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = Project::factory()->for($owner, 'owner')->create($attributes);
    $project->members()->attach($member->id, ['role' => ProjectRole::Member->value]);

    return [$project, $owner, $member];
}
