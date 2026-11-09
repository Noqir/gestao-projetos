<?php

namespace Database\Factories;

use App\Enums\ProjectRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'name' => ucfirst(fake()->words(3, true)),
            'description' => fake()->sentence(12),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+3 months'),
            'archived' => false,
        ];
    }

    /** O proprietário também entra no pivô project_user, com papel owner. */
    public function configure(): static
    {
        return $this->afterCreating(function (Project $project) {
            $project->members()->syncWithoutDetaching([
                $project->owner_id => ['role' => ProjectRole::Owner->value],
            ]);
        });
    }

    public function archived(): static
    {
        return $this->state(['archived' => true]);
    }
}
