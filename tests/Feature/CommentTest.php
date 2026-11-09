<?php

use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\Task;

it('publica comentário e registra no histórico do projeto (RF07/RF08)', function () {
    [$project, , $member] = projectWithTeam();
    $task = Task::factory()->for($project)->create();

    $this->actingAs($member)
        ->post(route('tasks.comments.store', $task), ['body' => 'Pronto para revisão.'])
        ->assertRedirect(route('projects.tasks.show', [$project, $task]).'#comentarios');

    expect(Comment::sole())->body->toBe('Pronto para revisão.')->user_id->toBe($member->id)
        ->and(ActivityLog::where('action', 'comment.created')->exists())->toBeTrue();

    $this->actingAs($member)->get(route('projects.activity', $project))
        ->assertSee("comentou em “{$task->title}”", false);
});

it('rejeita comentário vazio com mensagem em português', function () {
    [$project, $owner] = projectWithTeam();
    $task = Task::factory()->for($project)->create();

    $this->actingAs($owner)
        ->post(route('tasks.comments.store', $task), ['body' => ''])
        ->assertSessionHasErrors(['body' => 'Escreva um comentário antes de enviar.']);
});
