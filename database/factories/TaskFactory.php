<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'created_by' => fn (array $attributes) => Project::find($attributes['project_id'])->owner_id,
            'assignee_id' => null,
            'title' => ucfirst(fake()->words(4, true)),
            'description' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(TaskStatus::cases()),
            'priority' => fake()->randomElement(TaskPriority::cases()),
            'due_date' => fake()->optional()->dateTimeBetween('-1 week', '+1 month'),
            'position' => 0,
        ];
    }

    public function status(TaskStatus $status): static
    {
        return $this->state(['status' => $status]);
    }

    public function overdue(): static
    {
        return $this->state(['status' => TaskStatus::Todo, 'due_date' => now()->subDays(3)]);
    }
}
