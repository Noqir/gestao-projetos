<?php

use App\Models\Task;
use App\Models\User;

// RF03 / RNF02: quem não é membro recebe 403 em qualquer página do projeto.

it('redireciona visitantes para o login', function () {
    [$project] = projectWithTeam();

    $this->get(route('projects.show', $project))->assertRedirect(route('login'));
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('permite que membros vejam o quadro, os membros e as atividades', function () {
    [$project, , $member] = projectWithTeam();

    $this->actingAs($member);
    $this->get(route('projects.show', $project))->assertOk()->assertSee($project->name);
    $this->get(route('projects.members.index', $project))->assertOk();
    $this->get(route('projects.activity', $project))->assertOk();
});

it('retorna 403 para usuário que não é membro', function () {
    [$project] = projectWithTeam();
    $task = Task::factory()->for($project)->create();
    $outsider = User::factory()->create();

    $this->actingAs($outsider);
    $this->get(route('projects.show', $project))->assertForbidden()->assertSee('Você não é membro deste projeto.');
    $this->get(route('projects.members.index', $project))->assertForbidden();
    $this->get(route('projects.activity', $project))->assertForbidden();
    $this->get(route('projects.tasks.show', [$project, $task]))->assertForbidden();
    $this->patchJson(route('tasks.status', $task), ['status' => 'done'])->assertForbidden();
    $this->post(route('tasks.comments.store', $task), ['body' => 'Oi'])->assertForbidden();
});

it('retorna 404 quando a tarefa não pertence ao projeto da URL', function () {
    [$project, $owner] = projectWithTeam();
    [$other] = projectWithTeam();
    $foreignTask = Task::factory()->for($other)->create();

    $this->actingAs($owner)
        ->get(route('projects.tasks.show', [$project, $foreignTask]))
        ->assertNotFound();
});

it('lista apenas os projetos dos quais o usuário participa', function () {
    [$mine, , $member] = projectWithTeam(['name' => 'Projeto Visível']);
    projectWithTeam(['name' => 'Projeto Alheio']);

    $this->actingAs($member)
        ->get(route('projects.index'))
        ->assertOk()
        ->assertSee('Projeto Visível')
        ->assertDontSee('Projeto Alheio');
});
