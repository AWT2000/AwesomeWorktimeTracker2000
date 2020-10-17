<?php

namespace Tests\Feature\api\v1\worktimeentries;

use App\Actions\AttachCollidingWorktimeEntriesAction;
use App\Models\User;
use App\Models\WorktimeEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DeleteWorktimeEntryTest extends TestCase
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
    public function it_deletes_entry()
    {
        $entry = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T06:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ]);

        $this->delete("api/v1/worktime-entries/{$entry->id}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('worktime_entries', [
            'id' => $entry->id,
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_deletes_entry_even_when_there_are_colliding_entries()
    {
        $firstEntry = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T06:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ]);

        $secondEntry = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T06:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ]);

        // update collisions
        (new AttachCollidingWorktimeEntriesAction($secondEntry, $this->user->id))
            ->execute();

        $this->delete("api/v1/worktime-entries/{$secondEntry->id}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('worktime_entries', [
            'id' => $secondEntry->id,
            'user_id' => $this->user->id,
        ]);

        $firstEntry->load("entriesCollidingWith");

        $this->assertFalse($firstEntry->entriesCollidingWith->contains($secondEntry));
    }
}
