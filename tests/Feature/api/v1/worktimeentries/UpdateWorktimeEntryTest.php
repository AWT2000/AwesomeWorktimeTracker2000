<?php

namespace Tests\Feature\api\v1\worktimeentries;

use App\Actions\AttachCollidingWorktimeEntriesAction;
use App\Models\User;
use App\Models\WorktimeEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdateWorktimeEntryTest extends TestCase
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
        $this->put("/api/v1/worktime-entries/1",[])
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
        $this->put("/api/v1/worktime-entries/1", [
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
    public function it_updates_worktime_entry()
    {
        $wteToBeUpdated = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T06:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ]);

        $this->put("/api/v1/worktime-entries/{$wteToBeUpdated->id}", [
            'started_at' => '2020-10-16T07:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ])
            ->assertStatus(200)
            ->assertJson([
                "id" => $wteToBeUpdated->id,
                "started_at" => "2020-10-16T07:00:00+00:00",
                "ended_at" => "2020-10-16T09:00:00+00:00",
                "collides_with_other_entries" => false,
            ]);

        $this->assertDatabaseHas('worktime_entries', [
            "id" => $wteToBeUpdated->id,
            "user_id" => $this->user->id,
            "started_at" => "2020-10-16 07:00:00",
            "ended_at" => "2020-10-16 09:00:00",
            "project_id" => null
        ]);
    }

    /** @test */
    public function it_updates_worktime_collision_status_to_false()
    {
        // first entry
        WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T05:00:00+00:00',
            'ended_at' => '2020-10-16T07:00:00+00:00',
            'project_id' => null
        ]);

        // entry that will collide
        $wteToBeUpdated = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T06:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ]);

        // update collisions
        (new AttachCollidingWorktimeEntriesAction($wteToBeUpdated, $this->user->id))
            ->execute();

        // fix colliding entry
        $this->put("/api/v1/worktime-entries/{$wteToBeUpdated->id}", [
            'started_at' => '2020-10-16T07:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ])->assertJson(["collides_with_other_entries" => false,]);
    }

    /** @test */
    public function it_updates_worktime_collision_status_to_true()
    {
        // first entry
        WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T05:00:00+00:00',
            'ended_at' => '2020-10-16T07:00:00+00:00',
            'project_id' => null
        ]);

        // second entry
        $wteToBeUpdated = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T07:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ]);

        // update collisions
        (new AttachCollidingWorktimeEntriesAction($wteToBeUpdated, $this->user->id))
            ->execute();

        // fix entries to collide
        $this->put("/api/v1/worktime-entries/{$wteToBeUpdated->id}", [
            'started_at' => '2020-10-16T06:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ])->assertJson(["collides_with_other_entries" => true,]);
    }

    /** @test */
    public function it_updates_existing_entries_colliding_with_entries_when_entry_is_saved()
    {
        // first entry
        $firstEntry = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T05:00:00+00:00',
            'ended_at' => '2020-10-16T07:00:00+00:00',
            'project_id' => null
        ]);

        // second entry
        $wteToBeUpdated = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T07:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ]);

        // update collisions
        (new AttachCollidingWorktimeEntriesAction($wteToBeUpdated, $this->user->id))
            ->execute();

        // fix entries to collide
        $this->put("/api/v1/worktime-entries/{$wteToBeUpdated->id}", [
            'started_at' => '2020-10-16T06:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ]);

        $secondEntry = WorktimeEntry::find($wteToBeUpdated->id);

        $this->assertTrue($firstEntry->entriesCollidingWith->contains($secondEntry));
    }

    /** @test */
    public function it_removes_existing_entries_colliding_with_entries_when_entry_is_saved()
    {
        // first entry
        $firstEntry = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T05:00:00+00:00',
            'ended_at' => '2020-10-16T07:00:00+00:00',
            'project_id' => null
        ]);

        // second entry
        $wteToBeUpdated = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T06:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ]);

        // update collisions
        (new AttachCollidingWorktimeEntriesAction($wteToBeUpdated, $this->user->id))
            ->execute();

        // fix entries not to collide
        $this->put("/api/v1/worktime-entries/{$wteToBeUpdated->id}", [
            'started_at' => '2020-10-16T07:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ]);

        $secondEntry = WorktimeEntry::find($wteToBeUpdated->id);

        $this->assertFalse($firstEntry->entriesCollidingWith->contains($secondEntry));
    }
}
