<?php

use App\Enums\TaskStatus;
use App\Models\ActivityLog;
use App\Models\Task;
use App\Models\User;

it('cria tarefa no fim da coluna e registra a atividade', function () {
    [$project, $owner, $member] = projectWithTeam();
    Task::factory()->for($project)->status(TaskStatus::Todo)->create(['position' => 0]);

    $this->actingAs($member)->post(route('projects.tasks.store', $project), [
        'title' => 'Implementar login',
        'description' => 'Com mensagens em português',
        'assignee_id' => $owner->id,
        'priority' => 'high',
        'due_date' => now()->addWeek()->toDateString(),
    ])->assertRedirect(route('projects.show', $project))->assertSessionHas('success');

    $task = Task::firstWhere('title', 'Implementar login');
    expect($task->status)->toBe(TaskStatus::Todo)
        ->and($task->position)->toBe(1)
        ->and($task->created_by)->toBe($member->id)
        ->and(ActivityLog::where('subject_id', $task->id)->value('action'))->toBe('task.created');
});

it('rejeita tarefa sem título com mensagem em português (RF04)', function () {
    [$project, $owner] = projectWithTeam();

    $this->actingAs($owner)
        ->post(route('projects.tasks.store', $project), ['title' => '', 'priority' => 'medium'])
        ->assertSessionHasErrors(['title' => 'Informe um título para a tarefa.']);

    expect(Task::count())->toBe(0);
});

it('exige que o responsável seja membro do projeto', function () {
    [$project, $owner] = projectWithTeam();
    $outsider = User::factory()->create();

    $this->actingAs($owner)
        ->post(route('projects.tasks.store', $project), ['title' => 'X', 'priority' => 'low', 'assignee_id' => $outsider->id])
        ->assertSessionHasErrors(['assignee_id' => 'O responsável precisa ser membro do projeto.']);
});

it('edita a tarefa e registra a mudança de coluna', function () {
    [$project, $owner] = projectWithTeam();
    $task = Task::factory()->for($project)->status(TaskStatus::Todo)->create();

    $this->actingAs($owner)->put(route('projects.tasks.update', [$project, $task]), [
        'title' => 'Título novo',
        'priority' => 'low',
        'status' => 'review',
    ])->assertRedirect(route('projects.tasks.show', [$project, $task]));

    expect($task->fresh())->title->toBe('Título novo')->status->toBe(TaskStatus::Review)
        ->and(ActivityLog::pluck('action')->all())->toContain('task.updated', 'task.moved');
});

it('permite excluir apenas ao proprietário do projeto ou ao autor da tarefa', function () {
    [$project, $owner, $member] = projectWithTeam();
    $another = User::factory()->create();
    $project->members()->attach($another->id, ['role' => 'member']);
    $task = Task::factory()->for($project)->create(['created_by' => $member->id]);

    $this->actingAs($another)->delete(route('projects.tasks.destroy', [$project, $task]))->assertForbidden();
    $this->actingAs($member)->delete(route('projects.tasks.destroy', [$project, $task]))->assertRedirect(route('projects.show', $project));

    expect(Task::count())->toBe(0)
        ->and(ActivityLog::where('action', 'task.deleted')->exists())->toBeTrue();
});

it('exibe a página da tarefa com comentários', function () {
    [$project, $owner] = projectWithTeam();
    $task = Task::factory()->for($project)->create(['title' => 'Tarefa detalhada']);

    $this->actingAs($owner)->get(route('projects.tasks.show', [$project, $task]))
        ->assertOk()->assertSee('Tarefa detalhada')->assertSee('Nenhum comentário ainda');
});

it('filtra o quadro por responsável, prioridade e texto (RF09)', function () {
    [$project, $owner, $member] = projectWithTeam();
    Task::factory()->for($project)->create(['title' => 'Alpha do membro', 'assignee_id' => $member->id, 'priority' => 'high']);
    Task::factory()->for($project)->create(['title' => 'Beta do dono', 'assignee_id' => $owner->id, 'priority' => 'low']);

    $this->actingAs($owner)->get(route('projects.show', [$project, 'assignee' => $member->id]))
        ->assertSee('Alpha do membro')->assertDontSee('Beta do dono');
    $this->actingAs($owner)->get(route('projects.show', [$project, 'priority' => 'low']))
        ->assertSee('Beta do dono')->assertDontSee('Alpha do membro');
    $this->actingAs($owner)->get(route('projects.show', [$project, 'q' => 'alpha']))
        ->assertSee('Alpha do membro')->assertDontSee('Beta do dono');
});
