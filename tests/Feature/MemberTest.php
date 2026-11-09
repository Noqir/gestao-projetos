<?php

use App\Models\ActivityLog;
use App\Models\Task;
use App\Models\User;

it('adiciona um usuário cadastrado pelo e-mail', function () {
    [$project, $owner] = projectWithTeam();
    $newcomer = User::factory()->create(['email' => 'nova@demo.test']);

    $this->actingAs($owner)
        ->post(route('projects.members.store', $project), ['email' => 'NOVA@demo.test '])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('success');

    expect($project->hasMember($newcomer))->toBeTrue()
        ->and(ActivityLog::where('action', 'member.added')->exists())->toBeTrue();
});

it('informa em português quando o e-mail não está cadastrado ou já é membro', function () {
    [$project, $owner, $member] = projectWithTeam();

    $this->actingAs($owner)
        ->post(route('projects.members.store', $project), ['email' => 'ninguem@demo.test'])
        ->assertSessionHasErrors(['email' => 'Nenhum usuário cadastrado com este e-mail. Peça para a pessoa criar uma conta primeiro.']);

    $this->actingAs($owner)
        ->post(route('projects.members.store', $project), ['email' => $member->email])
        ->assertSessionHasErrors(['email' => "{$member->name} já faz parte deste projeto."]);
});

it('não permite que membros comuns gerenciem a equipe', function () {
    [$project, $owner, $member] = projectWithTeam();
    $other = User::factory()->create();

    $this->actingAs($member)->post(route('projects.members.store', $project), ['email' => $other->email])->assertForbidden();
    $this->actingAs($member)->delete(route('projects.members.destroy', [$project, $owner]))->assertForbidden();
});

it('remove o membro e libera as tarefas atribuídas a ele', function () {
    [$project, $owner, $member] = projectWithTeam();
    $task = Task::factory()->for($project)->create(['assignee_id' => $member->id]);

    $this->actingAs($owner)->delete(route('projects.members.destroy', [$project, $member]))->assertSessionHas('success');

    expect($project->hasMember($member))->toBeFalse()
        ->and($task->fresh()->assignee_id)->toBeNull();

    // Depois de removido, perde o acesso
    $this->actingAs($member)->get(route('projects.show', $project))->assertForbidden();
});

it('não remove o proprietário do projeto', function () {
    [$project, $owner] = projectWithTeam();

    $this->actingAs($owner)
        ->delete(route('projects.members.destroy', [$project, $owner]))
        ->assertSessionHasErrors('member');

    expect($project->hasMember($owner))->toBeTrue();
});
