<?php

namespace Database\Seeders\dummies;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Models\WorktimeEntry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = FakerFactory::create();

        DB::transaction(function() use ($faker) {
            for ($i=0; $i < 5; $i++) {
                $supervisor = User::factory()->create();

                $supervisor->assignRole('supervisor');

                $project = Project::factory()->create([
                    'founder_id' => $supervisor->id,
                    'project_manager_id' => $supervisor->id
                ]);

                $team = Team::factory()->create([
                    'supervisor_id' => $supervisor->id
                ]);

                $team->projects()->attach($project);

                $supervisor->teams()->attach($team);

                for ($j=0; $j < 3; $j++) {
                    $user = User::factory()->create();

                    $user->teams()->attach($team);

                    $personalProject = Project::factory()->create([
                        'founder_id' => $user->id,
                        'project_manager_id' => $user->id
                    ]);

                    $user->personalProjects()->attach($personalProject);

                    for ($k=0; $k < 3; $k++) {

                        $start = '-' . (8 + 24 * $k) . ' hours';
                        $end = '-' . (24 * $k) . ' hours';

                        $startDate = $faker->dateTimeBetween($start, $end);

                        WorktimeEntry::factory()->create([
                            'user_id' => $user->id,
                            'started_at' => $startDate,
                            'ended_at' => $faker->dateTimeBetween($startDate, $end),
                            'project_id' => $project->id
                        ]);
                    }
                }
            }
        });
    }
}
