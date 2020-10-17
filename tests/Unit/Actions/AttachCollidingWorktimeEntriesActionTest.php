<?php

namespace Tests\Unit\Actions;

use App\Actions\AttachCollidingWorktimeEntriesAction;
use App\Models\User;
use App\Models\WorktimeEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttachCollidingWorktimeEntriesActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\User
     */
    private $user;

    /** @test */
    public function it_attaches_colliding_entries()
    {
        $this->user = User::factory()->create();

        // entry 1: does not stop before entry starts
        $collidingEntry1 = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T06:00:00+00:00',
            'ended_at' => '2020-10-16T09:00:00+00:00',
            'project_id' => null
        ]);

        // entry 2: starts and ends within entry
        $collidingEntry2 = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T09:00:00+00:00',
            'ended_at' => '2020-10-16T10:00:00+00:00',
            'project_id' => null
        ]);

        // entry 3: starts before entry stops
        $collidingEntry3 = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T10:00:00+00:00',
            'ended_at' => '2020-10-16T11:00:00+00:00',
            'project_id' => null
        ]);

        $entry = WorktimeEntry::factory()->create([
            'user_id' => $this->user->id,
            'started_at' => '2020-10-16T08:00:00+00:00',
            'ended_at' => '2020-10-16T10:30:00+00:00',
            'project_id' => null
        ]);

        (new AttachCollidingWorktimeEntriesAction($entry, $this->user->id))->execute();

        $collidingEntries = $entry->collidingEntries()->get();

        $this->assertEquals(3, $collidingEntries->count());
        $this->assertTrue($collidingEntries->contains($collidingEntry1));
        $this->assertTrue($collidingEntries->contains($collidingEntry2));
        $this->assertTrue($collidingEntries->contains($collidingEntry3));
    }
}
