<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'user_id' => fn (array $attributes) => Project::find($attributes['project_id'])->owner_id,
            'action' => 'project.updated',
            'properties' => [],
            'created_at' => now(),
        ];
    }
}
