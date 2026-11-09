<?php

use App\Enums\TaskStatus;
use App\Models\ActivityLog;
use App\Models\Task;

// RF05: mover um cartão persiste status e posição no banco.

it('move a tarefa para outra coluna, persiste e renumera as posições', function () {
    [$project, , $member] = projectWithTeam();
    [$a, $b] = Task::factory()->count(2)->for($project)->status(TaskStatus::Todo)
        ->sequence(['position' => 0], ['position' => 1])->create();
    [$c, $d] = Task::factory()->count(2)->for($project)->status(TaskStatus::Doing)
        ->sequence(['position' => 0], ['position' => 1])->create();

    $this->actingAs($member)
        ->patchJson(route('tasks.status', $a), ['status' => 'doing', 'position' => 1])
        ->assertOk()
        ->assertJsonPath('task.status', 'doing')
        ->assertJsonPath('message', "“{$a->title}” movida para Em andamento.");

    expect($a->fresh())->status->toBe(TaskStatus::Doing)->position->toBe(1)
        ->and($c->fresh()->position)->toBe(0)
        ->and($d->fresh()->position)->toBe(2)
        ->and($b->fresh()->position)->toBe(0); // coluna de origem renumerada

    $log = ActivityLog::where('action', 'task.moved')->sole();
    expect($log->properties)->toMatchArray(['from' => 'todo', 'to' => 'doing']);

    // A página recarregada mostra o cartão na nova coluna
    $this->actingAs($member)->get(route('projects.show', $project))
        ->assertSeeInOrder(['data-column="doing"', $c->title, $a->title, $d->title], false);
});

it('reordena dentro da mesma coluna sem registrar movimentação', function () {
    [$project, $owner] = projectWithTeam();
    [$a, $b, $c] = Task::factory()->count(3)->for($project)->status(TaskStatus::Todo)
        ->sequence(['position' => 0], ['position' => 1], ['position' => 2])->create();

    $this->actingAs($owner)->patchJson(route('tasks.status', $c), ['status' => 'todo', 'position' => 0])->assertOk();

    expect([$c->fresh()->position, $a->fresh()->position, $b->fresh()->position])->toBe([0, 1, 2])
        ->and(ActivityLog::where('action', 'task.moved')->exists())->toBeFalse();
});

it('rejeita status inválido com 422 e mensagem em português', function () {
    [$project, $owner] = projectWithTeam();
    $task = Task::factory()->for($project)->status(TaskStatus::Todo)->create();

    $this->actingAs($owner)->patchJson(route('tasks.status', $task), ['status' => 'perdida'])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Status de tarefa inválido.');

    expect($task->fresh()->status)->toBe(TaskStatus::Todo);
});

it('não permite mover tarefas de projeto arquivado', function () {
    [$project, $owner] = projectWithTeam(['archived' => true]);
    $task = Task::factory()->for($project)->status(TaskStatus::Todo)->create();

    $this->actingAs($owner)->patchJson(route('tasks.status', $task), ['status' => 'done'])->assertForbidden();
});

it('também aceita a troca de status por formulário (sem JavaScript)', function () {
    [$project, $owner] = projectWithTeam();
    $task = Task::factory()->for($project)->status(TaskStatus::Todo)->create();

    $this->actingAs($owner)
        ->from(route('projects.tasks.show', [$project, $task]))
        ->patch(route('tasks.status', $task), ['status' => 'done'])
        ->assertRedirect(route('projects.tasks.show', [$project, $task]))
        ->assertSessionHas('success');

    expect($task->fresh()->status)->toBe(TaskStatus::Done);
});
