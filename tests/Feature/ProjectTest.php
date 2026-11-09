<?php

use App\Enums\ProjectRole;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

it('cria projeto vinculando o criador como proprietário e registra a atividade', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('projects.store'), [
        'name' => 'Novo Sistema',
        'description' => 'Descrição',
        'due_date' => now()->addMonth()->toDateString(),
    ]);

    $project = Project::firstWhere('name', 'Novo Sistema');
    $response->assertRedirect(route('projects.show', $project))->assertSessionHas('success');

    expect($project->owner_id)->toBe($user->id)
        ->and($project->members()->first()->pivot->role)->toBe(ProjectRole::Owner->value)
        ->and(ActivityLog::where('project_id', $project->id)->value('action'))->toBe('project.created');
});

it('rejeita projeto sem nome com mensagem em português', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('projects.store'), ['name' => ''])
        ->assertSessionHasErrors(['name' => 'O campo nome do projeto é obrigatório.']);
});

it('permite apenas ao proprietário editar o projeto', function () {
    [$project, $owner, $member] = projectWithTeam();

    $this->actingAs($member)->put(route('projects.update', $project), ['name' => 'Hackeado'])->assertForbidden();
    $this->actingAs($member)->get(route('projects.edit', $project))->assertForbidden();

    $this->actingAs($owner)->put(route('projects.update', $project), ['name' => 'Renomeado'])
        ->assertRedirect(route('projects.show', $project));
    expect($project->fresh()->name)->toBe('Renomeado');
});

it('arquiva e reativa o projeto, bloqueando novas tarefas enquanto arquivado', function () {
    [$project, $owner, $member] = projectWithTeam();

    $this->actingAs($owner)->patch(route('projects.archive', $project))->assertRedirect(route('projects.index'));
    expect($project->fresh()->archived)->toBeTrue();

    $this->actingAs($member)
        ->post(route('projects.tasks.store', $project), ['title' => 'X', 'priority' => 'low'])
        ->assertForbidden();

    $this->actingAs($owner)->patch(route('projects.restore', $project));
    expect($project->fresh()->archived)->toBeFalse();
});

it('exclui o projeto e, em cascata, suas tarefas', function () {
    [$project, $owner, $member] = projectWithTeam();
    Task::factory()->count(3)->for($project)->create();

    $this->actingAs($member)->delete(route('projects.destroy', $project))->assertForbidden();
    $this->actingAs($owner)->delete(route('projects.destroy', $project))->assertRedirect(route('projects.index'));

    expect(Project::count())->toBe(0)->and(Task::count())->toBe(0);
});

it('impede excluir a conta de quem ainda é dono de projetos', function () {
    [, $owner] = projectWithTeam();

    $this->actingAs($owner)
        ->delete(route('profile.destroy'), ['password' => 'password'])
        ->assertSessionHasErrorsIn('userDeletion', 'password');

    expect($owner->fresh())->not->toBeNull();
});
