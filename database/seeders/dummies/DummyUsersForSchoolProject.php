<?php

namespace Database\Seeders\dummies;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Models\WorktimeEntry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\Hash;

class DummyUsersForSchoolProject extends Seeder
{
    private $teamMembers = [
        'tommi' => [
            'email' => 'tommi@awesomeworktimetracker.com',
            'password' => 'bAF7k77dADmDS7Vk'
        ],
        'markus' => [
            'email' => 'markus@awesomeworktimetracker.com',
            'password' => 'ysq9x6v49XCqBs8G'
        ],
        'jani' => [
            'email' => 'jani@awesomeworktimetracker.com',
            'password' => 'sFVhXdpr57RqEQuw'
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = FakerFactory::create();

        $supervisor = User::factory()->create([
            'email' => 'supervisor@awesomeworktimetracker.com'
        ]);

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

        $project = Project::factory()->create([
            'founder_id' => $supervisor->id,
            'project_manager_id' => $supervisor->id
        ]);

        DB::transaction(function() use ($faker, $team, $project) {
            foreach ($this->teamMembers as $name => $data) {
                $user = User::create([
                    'name' => $name,
                    'email' => $data['email'],
                    'password' => Hash::make($data['password'])
                ]);

                $user->teams()->attach($team);

                for ($i=-14; $i <= 14; $i++) {
                    if ($i <= 0) {
                        $prefix = "-";
                        $start = $prefix . (8 + 24 * abs($i)) . ' hours';
                        $end = $prefix . (24 * abs($i)) . ' hours';
                    } else {
                        $prefix = "+";
                        $start = $prefix . (24 * abs($i)) . ' hours';
                        $end = $prefix . (8 + 24 * abs($i)) . ' hours';
                    }

                    $startDate = $faker->dateTimeBetween($start, $end);

                    WorktimeEntry::factory()->create([
                        'user_id' => $user->id,
                        'started_at' => $startDate,
                        'ended_at' => $faker->dateTimeBetween($startDate, $end),
                        'project_id' => $project->id
                    ]);
                }
            }
        });

    }
}
