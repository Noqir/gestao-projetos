<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

// RNF05: migrate:fresh --seed deixa o sistema pronto para a demonstração.

it('popula o banco para a demonstração', function () {
    $this->seed();

    $admin = User::firstWhere('email', 'admin@demo.test');
    $campaign = Project::firstWhere('name', 'Campanha de Marketing Q4');

    expect(User::count())->toBe(6)
        ->and(Project::count())->toBe(4)
        ->and(Task::count())->toBe(30)
        ->and(Task::overdue()->count())->toBeGreaterThan(0)
        ->and($campaign->hasMember($admin))->toBeFalse();

    $this->actingAs($admin)->get(route('dashboard'))->assertOk();
    $this->actingAs($admin)->get(route('projects.show', Project::firstWhere('name', 'Lançamento do App Mobile')))->assertOk();
    $this->actingAs($admin)->get(route('projects.show', $campaign))->assertForbidden();
});
