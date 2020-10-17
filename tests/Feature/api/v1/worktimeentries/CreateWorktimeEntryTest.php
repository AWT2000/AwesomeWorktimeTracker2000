<?php

namespace Tests\Feature\api\v1\worktimeentries;

use App\Models\User;
use App\Models\WorktimeEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CreateWorktimeEntryTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Passport::actingAs($this->user);
    }

    /** @test */
    public function it_validates_required_attributes_from_request()
    {
        $this->post("/api/v1/worktime-entries/",[])
            ->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "started_at" => [
                        "The started at field is required."
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_validates_attribute_types_from_request()
    {
        $this->post("/api/v1/worktime-entries/", [
            'started_at' => '5',
            'ended_at' => '2020-10-16',
            'project_id' => 'as'
        ])
            ->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "started_at" => [
                      "The started at does not match the format Y-m-d\TH:i:sP.",
                      "The started at must be a date before or equal to ended at.",
                    ],
                    "ended_at" => [
                      "The ended at does not match the format Y-m-d\TH:i:sP.",
                      "The ended at must be a date after or equal to started at.",
                    ],
                    "project_id" => [
                      "The selected project id is invalid.",
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_stores_worktime_entry()
    {
        $this->post("/api/v1/worktime-entries/", [
            'started_at' => '2020-10-16T06:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ])
            ->assertStatus(200)
            ->assertJson([
                "started_at" => "2020-10-16T06:00:00+00:00",
                "ended_at" => "2020-10-16T09:00:00+00:00",
                "collides_with_other_entries" => false,
            ]);

        $this->assertDatabaseHas('worktime_entries', [
            "user_id" => $this->user->id,
            "started_at" => "2020-10-16 06:00:00",
            "ended_at" => "2020-10-16 09:00:00",
            "project_id" => null
        ]);
    }

    /** @test */
    public function it_stores_worktime_entry_even_if_it_collides()
    {
        WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T06:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ]);

        $this->post("/api/v1/worktime-entries/", [
            'started_at' => '2020-10-16T07:00:00+00:00',
            'ended_at' => '2020-10-16T10:00:00+00:00',
            'project_id' => null
        ])
            ->assertJson(["collides_with_other_entries" => true,]);

        $this->assertDatabaseHas('worktime_entries', [
            "user_id" => $this->user->id,
            "started_at" => "2020-10-16 07:00:00",
            "ended_at" => "2020-10-16 10:00:00",
            "project_id" => null
        ]);
    }

    /** @test */
    public function it_updates_existing_entries_colliding_with_entries_when_entry_is_saved()
    {
        $firstEntry = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T06:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ]);

        $this->post("/api/v1/worktime-entries/", [
            'started_at' => '2020-10-16T07:00:00+00:00',
            'ended_at' => '2020-10-16T10:00:00+00:00',
            'project_id' => null
        ]);

        $secondEntry = WorktimeEntry::where('started_at', '2020-10-16 07:00:00')
            ->first();

        $this->assertTrue($firstEntry->entriesCollidingWith->contains($secondEntry));
    }
}
