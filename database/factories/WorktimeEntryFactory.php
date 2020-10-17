<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use App\Models\WorktimeEntry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WorktimeEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorktimeEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startedAt = $this->faker->dateTimeBetween('-8 hours', 'now');
        return [
            'user_id' => User::factory(),
            'started_at' => $startedAt,
            'ended_at' => $this->faker->dateTimeBetween($startedAt, 'now'),
            'project_id' => Project::factory()
        ];
    }
}
